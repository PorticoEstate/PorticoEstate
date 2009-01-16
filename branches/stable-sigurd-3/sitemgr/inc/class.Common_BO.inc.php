<?php

	class Common_BO
	{
		var $sites,$acl,$theme,$pages,$cats,$content,$modules;
		var $state,$visiblestates;
		var $sitemenu,$othermenu;
		function Common_BO()
		{
			$this->sites = CreateObject('sitemgr.Sites_BO',True);
			$this->acl = CreateObject('sitemgr.ACL_BO',True);
			$this->theme = CreateObject('sitemgr.Theme_BO',True);
			$this->pages = CreateObject('sitemgr.Pages_BO',True);
			$this->cats = CreateObject('sitemgr.Categories_BO',True);
			$this->content = CreateObject('sitemgr.Content_BO',True);
			$this->modules = CreateObject('sitemgr.Modules_BO',True);
			$this->state = array(
				SITEMGR_STATE_DRAFT => lang('draft'),
				SITEMGR_STATE_PREPUBLISH => lang('prepublished'),
				SITEMGR_STATE_PUBLISH => lang('published'),
				SITEMGR_STATE_PREUNPUBLISH => lang('preunpublished'),
				SITEMGR_STATE_ARCHIVE => lang('archived'),
			);
		}

		function setvisiblestates($mode)
		{
			$this->visiblestates = $this->getstates($mode);
		}

		function getstates($mode)
		{
			switch ($mode)
			{
				case 'Administration' :
					return array(SITEMGR_STATE_DRAFT,SITEMGR_STATE_PREPUBLISH,SITEMGR_STATE_PUBLISH,SITEMGR_STATE_PREUNPUBLISH);
				case 'Production' :
					return array(SITEMGR_STATE_PUBLISH,SITEMGR_STATE_PREUNPUBLISH);
				case 'Draft' :
				case 'Edit' :
					return array(SITEMGR_STATE_PREPUBLISH,SITEMGR_STATE_PUBLISH);
				case 'Commit' :
					return array(SITEMGR_STATE_PREPUBLISH,SITEMGR_STATE_PREUNPUBLISH);
				case 'Archive' :
					return array(SITEMGR_STATE_ARCHIVE);
			}
		}

		function globalize($varname)
		{
			if (is_array($varname))
			{
				foreach($varname as $var)
				{
					$GLOBALS[$var] = $_POST[$var];
				}
			}
			else
			{
				$GLOBALS[$varname] = $_POST[$varname];
			}
		}

		function getlangname($lang)
		{
			$GLOBALS['phpgw']->db->query("select lang_name from phpgw_languages where lang_id = '$lang'",__LINE__,__FILE__);
			$GLOBALS['phpgw']->db->next_record();
			return $GLOBALS['phpgw']->db->f('lang_name');
		}

		function inputstateselect($default)
		{
			$returnValue = '';
			foreach($this->state as $value => $display)
			{
				$selected = ($default == $value) ? $selected = 'selected="selected" ' : '';
				$returnValue.='<option '.$selected.'value="'.$value.'">'.
					$display.'</option>'."\n";
			}
			return $returnValue;
		}

		function set_menus()
		{
			$this->sitemenu = $this->get_sitemenu();
			$this->othermenu = $this->get_othermenu();
		}

		function get_sitemenu()
		{
			if ($GLOBALS['Common_BO']->acl->is_admin())
			{
				$file[] = array('text'  => 'Configure Website',
						'url'	=> $GLOBALS['phpgw']->link('/index.php','menuaction=sitemgr.Common_UI.DisplayPrefs'));
				$link_data['cat_id'] = CURRENT_SITE_ID;
				$link_data['menuaction'] = "sitemgr.Modules_UI.manage";
				$file[] = array('text' => 'Manage site-wide module properties',
						'url'	=> $GLOBALS['phpgw']->link('/index.php',$link_data));
						
				$link_data['page_id'] = 0;
				$link_data['menuaction'] = "sitemgr.Content_UI.manage";
				$file[] = array('text' => 'Manage site-wide content',
						'url'	=> $GLOBALS['phpgw']->link('/index.php',$link_data));
			}
			$file[] = array('text' => 'Manage Categories and pages',
					'url'	=> $GLOBALS['phpgw']->link('/index.php', 'menuaction=sitemgr.Outline_UI.manage'));
			$file[] = array('text' => 'Manage Translations', 
					'url' => $GLOBALS['phpgw']->link('/index.php', 'menuaction=sitemgr.Translations_UI.manage'));
			$file[] = array('text' => 'Commit Changes', 
					'url' => $GLOBALS['phpgw']->link('/index.php', 'menuaction=sitemgr.Content_UI.commit'));
			$file[] = array('text' => 'Manage archived content',
					'url' => $GLOBALS['phpgw']->link('/index.php', 'menuaction=sitemgr.Content_UI.archive'));
			$file[] = array('text' => '_NewLine_');
			$file[] = array('text' => 'View Generated Site', 
					'url' => $GLOBALS['phpgw']->link('/sitemgr-link/', array('site_id' => CURRENT_SITE_ID)));
			return $file;
		}

		function get_othermenu()
		{
			$numberofsites = $GLOBALS['Common_BO']->sites->getnumberofsites();
			$isadmin = $GLOBALS['phpgw']->acl->check('run',1,'admin');
			if ($numberofsites < 2 && !$isadmin)
			{
				return false;
			}
			$menu_title = lang('Other websites');
			if ($numberofsites > 1)
			{
				$link_data['menuaction'] = 'sitemgr.Common_UI.DisplayMenu';
				$sites = $GLOBALS['Common_BO']->sites->list_sites(False);
				while(list($site_id,$site) = @each($sites))
				{
					if ($site_id != CURRENT_SITE_ID)
					{
						$link_data['siteswitch'] = $site_id;
						$file[] = array('text' => $site['site_name'],
								'url' => $GLOBALS['phpgw']->link('/index.php',$link_data));
					}
				}
			}
			if ($numberofsites > 1 && $isadmin)
			{
				$file[] = array ('text' => '_NewLine_');
			}
			if ($isadmin)
			{
				$file[] = array('text' => 'Define websites',
						'url'  => $GLOBALS['phpgw']->link('/index.php','menuaction=sitemgr.Sites_UI.list_sites'));
			}
			return $file;
		}			
	}
?>
