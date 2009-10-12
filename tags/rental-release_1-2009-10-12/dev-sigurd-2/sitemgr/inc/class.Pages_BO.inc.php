<?php
	class Pages_BO
	{
		var $so;

		function Pages_BO()
		{
			$this->so = CreateObject('sitemgr.Pages_SO',True);
		}

		function getPageOptionList()
		{
			$pagelist = $this->so->getPageIDList(0,$GLOBALS['Common_BO']->getstates('Production'));
			$retval[]=array('value'=>0,'display'=>'[' .lang('Show Site Index') . ']');
			foreach($pagelist as $page_id)
			{
				$page = $this->so->getPage($page_id);
				$retval[]=array('value'=>$page_id,'display'=>$page->name);
			}
			return $retval;
		}

		function getpageIDListCommitable()
		{
			//only retrieve commitable pages from writeable categories
			return $this->so->getPageIDList($GLOBALS['Common_BO']->cats->getpermittedcatsWrite(),$GLOBALS['Common_BO']->getstates('Commit'));
		}

		function getpageIDListArchived()
		{
			//only retrieve archived pages from writeable categories
			return $this->so->getPageIDList($GLOBALS['Common_BO']->cats->getpermittedcatsWrite(),$GLOBALS['Common_BO']->getstates('Archive'));
		}

		function getPageIDList($cat_id=0,$states=false)
		{
			return $this->so->getPageIDList($cat_id,$states);	
		}

		function addPage($cat_id)
		{
			if ($GLOBALS['Common_BO']->acl->can_write_category($cat_id))
			{
				return $this->so->addPage($cat_id);
			}
			else
			{
				return false;
			}
		}

		function removePagesInCat($cat_id,$force=False)
		{
			if (!($force || $GLOBALS['Common_BO']->acl->can_write_category($cat_id)))
			{
				return false;
			}
			$pages = $this->so->getPageIDList($cat_id);
			while(list(,$page_id) = each($pages))
			{
				$this->removePage($page_id,True);
			}
		}

		function removePage($page_id,$force=False)
		{
			$cat_id = $this->so->getcatidforpage($page_id);
			if (!$force)
			{
				if (!$GLOBALS['Common_BO']->acl->can_write_category($cat_id))
				{
					return false;
				}
			}
			$this->so->removePage($page_id);
			//since we already did the ACL we force
			$GLOBALS['Common_BO']->content->removeBlocksInPageOrCat($cat_id,$page_id,True);
		}

		function getPage($page_id,$lang=False)
		{
			$page = $this->so->getPage($page_id,$lang);
			if ($page && in_array($page->cat_id,$GLOBALS['Common_BO']->cats->readablecats))
			{
				//if the page is not in published status we maintain its name so that switching from edit to prodcution mode works
				if (!in_array($page->state,$GLOBALS['Common_BO']->visiblestates))
				{
					$page->title = lang('Error accessing page');
					$page->subtitle = '';
					$page->id = 0;
					$page->cat_id = 0;
				}
				return $page;
			}
			else
			{
				$page = CreateObject('sitemgr.Page_SO');
				$page->name = 'Error';
				$page->title = lang('Error accessing page');
				$page->subtitle = '';
//				$page->content = lang('There was an error accessing the requested page. Either you do not have permission to view this page, or the page does not exist.');
				return $page;
			}
		}

		function getlangarrayforpage($page_id)
		{
			return $this->so->getlangarrayforpage($page_id);
		}

		function savePageInfo($page_Info,$lang)
		{
			$oldpage = $this->getpage($page_Info->id);

			if(!($GLOBALS['Common_BO']->acl->can_write_category($page_Info->cat_id) && 
				$GLOBALS['Common_BO']->acl->can_write_category($oldpage->cat_id)))
			{
				return lang("You don't have permission to write to that category.");
			}

			$fixed_name = strtr($page_Info->name, '!@#$%^&*()=+	/?><,.\\\'":;|`~{}[]','                               ');
			$fixed_name = str_replace(' ', '', $fixed_name);
			if ($fixed_name != $page_Info->name)
			{
				$page_Info->name = $fixed_name;
				$this->so->savePageInfo($page_Info);
				$this->so->savePageLang($page_Info,$lang);
				return lang('The Name field cannot contain punctuation or spaces (field modified).');
			}

			if ($this->so->pageExists($page_Info->name,$page_Info->id))
			{
				$page_Info->name .= '--FIX-DUPLICATE-NAME';
				$this->so->savePageInfo($page_Info);
				$this->so->savePageLang($page_Info,$lang);
				return lang('The page name must be unique.');
			}
			$this->so->savePageInfo($page_Info);
		  	$this->so->savePageLang($page_Info,$lang);
			return True;
		}

		function savePageLang($page_Info,$lang)
		{
		    $this->so->savePageLang($page_Info,$lang);
		}

		function removealllang($lang)
		{
			$this->so->removealllang($lang);
		}

		function migratealllang($oldlang,$newlang)
		{
			$this->so->migratealllang($oldlang,$newlang);
		}

		function commit($page_id)
		{
			$cat_id = $this->so->getcatidforpage($page_id);
			if ($GLOBALS['Common_BO']->acl->can_write_category($cat_id))
			{
				$this->so->commit($page_id);
			}
		}

		function reactivate($page_id)
		{
			$cat_id = $this->so->getcatidforpage($page_id);
			if ($GLOBALS['Common_BO']->acl->can_write_category($cat_id))
			{
				$this->so->reactivate($page_id);
			}
		}
	}
?>
