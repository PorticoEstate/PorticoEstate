<?php
	/*************************************************************************\
	* phpGroupWare - Web Content Manager                                      *
	* http://www.phpgroupware.org                                             *
	* -------------------------------------------------                       *
	* This program is free software; you can redistribute it and/or modify it *
	* under the terms of the GNU General Public License as published by the   *
	* Free Software Foundation; either version 2 of the License, or (at your  *
	* option) any later version.                                              *
	\*************************************************************************/
	/* $Id: class.sitebo.inc.php 15965 2005-05-15 02:16:27Z skwashd $ */

	class sitebo
	{
		var $pages_bo;
		var $catbo;
		var $acl;

		function sitebo()
		{
			$this->catbo = &$GLOBALS['Common_BO']->cats;
			$this->pages_bo = &$GLOBALS['Common_BO']->pages;
			$this->acl = &$GLOBALS['Common_BO']->acl;
			$this->isadmin = $this->acl->is_admin;
			//$anonymous_user is globally set in config.inc.php
			$this->isuser = ($GLOBALS['phpgw_info']['user']['account_lid'] != $GLOBALS['anonymous_user']);
		}

		function getcatwrapper($cat_id)
		{
			$availablelangsforcat = $this->catbo->getlangarrayforcategory($cat_id);
			if (in_array($GLOBALS['sitemgr_info']['userlang'],$availablelangsforcat))
			{
				return $this->catbo->getCategory($cat_id,$GLOBALS['sitemgr_info']['userlang']);
			}
			else
			{
				foreach ($GLOBALS['sitemgr_info']['sitelanguages'] as $lang)
				{
					if (in_array($lang,$availablelangsforcat))
					{
						return $this->catbo->getCategory($cat_id,$lang);
					}
				}
			}
			//fall back to a category in "default" lang
			return $this->catbo->getCategory($cat_id);
		}

		function getpagewrapper($page_id)
		{
			$availablelangsforpage = $this->pages_bo->getlangarrayforpage($page_id);
			if (in_array($GLOBALS['sitemgr_info']['userlang'],$availablelangsforpage))
			{
				return $this->pages_bo->GetPage($page_id,$GLOBALS['sitemgr_info']['userlang']);
			}
			else
			{
				foreach ($GLOBALS['sitemgr_info']['sitelanguages'] as $lang)
				{
					if (in_array($lang,$availablelangsforpage))
					{
						return $this->pages_bo->GetPage($page_id,$lang);
					}
				}
			}
			//fall back to a page in "default" lang
			return $this->pages_bo->GetPage($page_id);
		}

		function loadPage($page_id)
		{
			global $page;
			$page = $this->getpagewrapper($page_id);
		}

		function loadIndex()
		{
			global $page;
			$page->title = lang('Site Index');
			$page->subtitle = '';
			$page->index = True;
			$page->block = CreateObject('sitemgr.Block_SO',True);
			$page->block->module_name = 'index';
			$page->block->module_id = $GLOBALS['Common_BO']->modules->getmoduleid('index');
			$page->block->view = SITEMGR_VIEWABLE_EVERBODY;
			$page->block->status = SITEMGR_STATE_PUBLISH;
			$page->cat_id = $GLOBALS['Common_BO']->current_site['site_id'];
			return true;
		}

		function getIndex($showhidden=true, $rootonly=false)
		{
			$cats = $this->getCatLinks(0,!$rootonly);
			$index = array();

			if (count($cats)>0)
			{
				reset($cats);
				$content = "\n".'<ul>';
				while(list($cat_id,$cat) = each($cats))
				{
					$pages = $this->getPageLinks($cat_id,$showhidden);
					if (count($pages)>0)
					{
						foreach($pages as $link)
						{
							$index[] = array(
								'catname'=>$cat['name'],
								'catdepth'=>$cat['depth'],
								'catlink'=>$cat['link'],
								'catdescrip'=>$cat['description'],
								'pagename'=>$link['name'],
								'pagelink'=>$link['link'],
								'pagetitle'=>$link['title'],
								'pagesubtitle'=>$link['subtitle']
							);
						}
					}
					else
					{
						$index[] = array(
							'catname'=>$cat['name'],
							'catdepth'=>$cat['depth'],
							'catdescrip'=>$cat['description'],
							'catlink'=>$cat['link'],
							'pagelink'=>lang('No pages available')
						);
					}
				}
			}
			return $index;
		}

		function loadTOC($category_id=false)
		{
			global $page;

			$page->title = lang('Table of Contents');
			$page->subtitle = '';
			$page->toc = True;
			$page->cat_id = $category_id ? $category_id : CURRENT_SITE_ID;
			$page->block = CreateObject('sitemgr.Block_SO',True);
			$page->block->module_name = 'toc';
			$page->block->arguments = array('category_id' => $category_id);
			$page->block->module_id = $GLOBALS['Common_BO']->modules->getmoduleid('toc');
			$page->block->view = SITEMGR_VIEWABLE_EVERBODY;
			$page->block->state = SITEMGR_STATE_PUBLISH;
			return true;
		}

		function getPageLinks($category_id, $showhidden=true)
		{
			$pages=$this->pages_bo->getPageIDList($category_id);
			foreach($pages as $page_id)
			{
				$page=$this->getpagewrapper($page_id);
				if ($showhidden || !$page->hidden)
				{
					$pglinks[$page_id] = array(
						'name'=>$page->name,
						'link'=>'<a href="'.sitemgr_link('page_name='.$page->name).'">'.$page->title.'</a>',
						'title'=>$page->title,
						'subtitle'=>$page->subtitle
					);
				}
			}
			return $pglinks;
		}

		function getCatLinks($cat_id=0,$recurse=true)
		{
			$catlinks = array();
			$cat_list = $this->catbo->getpermittedcatsRead($cat_id,$recurse);
			foreach($cat_list as $cat_id)
			{
				$category = $this->getcatwrapper($cat_id);
				$catlinks[$cat_id] = array(
					'name'=>$category->name,
					'link'=>'<a href="'.sitemgr_link('category_id='.$cat_id).'">'.$category->name.'</a>',
					'description'=>$category->description,
					'depth'=>$category->depth
				);
			}
			return $catlinks;
		}

		//like $GLOBALS['phpgw']->common->getPreferredLanguage,
		//but compares languages accepted by the user 
		//to the languages the website is configured for
		//instead of the languages installed in phpgroupware
		function setsitemgrPreferredLanguage()
		{
			//since there are lang calls in the API, and the first lang call builds $GLOBAL['lang'], wet have to unset it, if
			//the change of $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'] should have any effect.
			//is there a more efficient way to do the same thing?
			unset($GLOBALS['lang']);
			$supportedLanguages = $GLOBALS['sitemgr_info']['sitelanguages'] ? $GLOBALS['sitemgr_info']['sitelanguages'] : array('en');
			$postlang = trim($_GET['lang']);
			if ($postlang && in_array($postlang,$supportedLanguages))
			{
				$GLOBALS['phpgw_info']['user']['preferences']['common']['lang'] = $postlang;
				$GLOBALS['sitemgr_info']['userlang'] = $postlang;
				$GLOBALS['phpgw']->session->appsession('language','sitemgr-site',$postlang);
				return;
			}
		
			$sessionlang = $GLOBALS['phpgw']->session->appsession('language','sitemgr-site');
			if ($sessionlang)
			{
				$GLOBALS['phpgw_info']['user']['preferences']['common']['lang'] = $sessionlang;
				$GLOBALS['sitemgr_info']['userlang'] = $sessionlang;
				return;
			}

			$client_langs = split('[,;]', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			if(is_array($client_langs))
			{
				foreach($client_langs as $lang)
				{
					if(strlen($lang) != 2)
					{
						list($lang) = explode('-', $lang);
					}
					if(in_array($lang, $supportedLanguages))
					{
						$GLOBALS['phpgw_info']['user']['preferences']['common']['lang'] = $lang;
						$GLOBALS['sitemgr_info']['userlang'] = $lang;
						$GLOBALS['phpgw']->session->appsession('language','sitemgr-site',$lang);
						return;
					}
				}
			}
			
			if ($this->isuser)
			{
				$userlang = $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'];
				if (in_array($userlang,$supportedLanguages))
				{
				//we do not touch $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'] if
				//the user is registered and his lang preference is supported by the website,
				//but save it to the appsession for quicker retrieval
				$GLOBALS['phpgw']->session->appsession('language','sitemgr-site',$userlang);
				$GLOBALS['sitemgr_info']['userlang'] = $userlang;
			return;
				}
			}
				
			// create a array of languages the user is accepting
			$userLanguages = explode(',',$GLOBALS['HTTP_ACCEPT_LANGUAGE']);
		
			// find usersupported language
			while (list($key,$value) = each($userLanguages))
			{
				// remove everything behind '-' example: de-de
				$value = trim($value);
				$pieces = explode('-', $value);
				$value = $pieces[0];
				//print "current lang $value<br>";
				if (in_array($value,$supportedLanguages))
				{
					$browserlang = $value;
					break;
				}
			}

			// no usersupported language found -> return the first entry of sitelanguages
			if (empty($browserlang))
			{
				$browserlang = $supportedLanguages[0];
			}
		
			$GLOBALS['phpgw_info']['user']['preferences']['common']['lang'] = $browserlang;
			$GLOBALS['sitemgr_info']['userlang'] = $browserlang;
			$GLOBALS['phpgw']->session->appsession('language','sitemgr-site',$browserlang);
		}

		function getmode()
		{
			if ($this->isuser)
			{
				$postmode = $_GET['administration']['mode'];
				if ($postmode)
				{
					$GLOBALS['phpgw']->session->appsession('mode','sitemgr-site',$postmode);
					return $postmode;
				}
				$sessionmode = $GLOBALS['phpgw']->session->appsession('mode','sitemgr-site');
				if($sessionmode)
				{
					return $sessionmode;
				}
			}
			return 'Production';
		}
	}
?>
