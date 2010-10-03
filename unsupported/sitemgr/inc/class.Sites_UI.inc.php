<?php
  /**************************************************************************\
  * phpGroupWare - phpgroupware SiteMgr                                      *
  * http://www.phpgroupware.org                                              *
  * -----------------------------------------------                          *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id$ */

	//copied from class admin.uiservers
	class Sites_UI
	{
		var $common_ui;
		var $public_functions = array(
			'list_sites' => True,
			'edit'         => True,
			'delete'       => True
		);

		var $start = 0;
		var $query = '';
		var $sort  = '';
		var $order = '';

		var $debug = False;

		var $bo = '';
		var $nextmatchs = '';

		function Sites_UI()
		{
			$this->common_ui = CreateObject('sitemgr.Common_UI',True);
			$this->bo = &$GLOBALS['Common_BO']->sites;
			$this->nextmatchs = createobject('phpgwapi.nextmatchs');

			$this->start = $this->bo->start;
			$this->query = $this->bo->query;
			$this->order = $this->bo->order;
			$this->sort = $this->bo->sort;
			if($this->debug) { $this->_debug_sqsof(); }
			/* _debug_array($this); */
		}

		function _debug_sqsof()
		{
			$data = array(
				'start' => $this->start,
				'query' => $this->query,
				'sort'  => $this->sort,
				'order' => $this->order
			);
			echo '<br>UI:';
			_debug_array($data);
		}

		function save_sessiondata()
		{
			$data = array(
				'start' => $this->start,
				'query' => $this->query,
				'sort'  => $this->sort,
				'order' => $this->order
			);
			$this->bo->save_sessiondata($data);
		}

		function list_sites()
		{
			$this->common_ui->DisplayHeader();

			if (!$GLOBALS['phpgw']->acl->check('run',1,'admin'))
			{
				$this->deny();
			}

			$GLOBALS['phpgw']->template->set_file(array('site_list_t' => 'listsites.tpl'));
			$GLOBALS['phpgw']->template->set_block('site_list_t','site_list','list');

			$GLOBALS['phpgw']->template->set_var('add_action',$GLOBALS['phpgw']->link('/index.php','menuaction=sitemgr.Sites_UI.edit'));
			$GLOBALS['phpgw']->template->set_var('lang_add',lang('Add'));
			$GLOBALS['phpgw']->template->set_var('title_sites',lang('Sitemgr Websites'));
			$GLOBALS['phpgw']->template->set_var('lang_search',lang('Search'));
			$GLOBALS['phpgw']->template->set_var('actionurl',$GLOBALS['phpgw']->link('/index.php','menuaction=sitemgr.Sites_UI.list_sites'));
			$GLOBALS['phpgw']->template->set_var('lang_done',lang('Done'));
			$GLOBALS['phpgw']->template->set_var('doneurl',$GLOBALS['phpgw']->link('/admin/index.php'));

			if(!$this->start)
			{
				$this->start = 0;
			}

			$this->save_sessiondata();
			$sites = $this->bo->list_sites();

			$left  = $this->nextmatchs->left('/index.php',$this->start,$this->bo->total,'menuaction=sitemgr.Sites_UI.list_sites');
			$right = $this->nextmatchs->right('/index.php',$this->start,$this->bo->total,'menuaction=sitemgr.Sites_UI.list_sites');

			$GLOBALS['phpgw']->template->set_var(array(
				'left' => $left,
				'right' => $right,
				'lang_showing' => $this->nextmatchs->show_hits($this->bo->total,$this->start),
				'th_bg' => $GLOBALS['phpgw_info']['theme']['th_bg'],
				'lang_edit' => lang('Edit'),
				'lang_delete' => lang('Delete'),
				'sort_name' => $this->nextmatchs->show_sort_order(
					$this->sort,'site_name',$this->order,'/index.php',lang('Name'),'&menuaction=sitemgr.Sites_UI.list_sites'
				),
				'sort_url' => $this->nextmatchs->show_sort_order(
					$this->sort,'site_url',$this->order,'/index.php',lang('URL'),'&menuaction=sitemgr.Sites_UI.list_sites'
				)
			));

			while(list($site_id,$site) = @each($sites))
			{
				$tr_color = $this->nextmatchs->alternate_row_color($tr_color);

				$GLOBALS['phpgw']->template->set_var(array(
					'tr_color' => $tr_color,
					'site_name' => $GLOBALS['phpgw']->strip_html($site['site_name']),
					'site_url' => $site['site_url'],
					'edit' => $GLOBALS['phpgw']->link('/index.php','menuaction=sitemgr.Sites_UI.edit&site_id=' . $site_id),
					'lang_edit_entry' => lang('Edit'),
					'delete' => $GLOBALS['phpgw']->link('/index.php','menuaction=sitemgr.Sites_UI.delete&site_id=' . $site_id),
					'lang_delete_entry' => lang('Delete')
				));
				$GLOBALS['phpgw']->template->parse('list','site_list',True);
			}

			$GLOBALS['phpgw']->template->parse('out','site_list_t',True);
			$GLOBALS['phpgw']->template->p('out');
			$this->common_ui->DisplayFooter();
		}

		/* This function handles add or edit */
		function edit()
		{
			if ($_POST['done'])
			{
				return $this->list_sites();
			}
			$this->common_ui->DisplayHeader();

			if (!$GLOBALS['phpgw']->acl->check('run',1,'admin'))
			{
				$this->deny();
			}
			if ($_POST['delete'])
			{
				return $this->delete();
			}

			$site_id = get_var('site_id',array('POST','GET'));
			
			$GLOBALS['phpgw']->template->set_file(array('form' => 'site_form.tpl'));
			$GLOBALS['phpgw']->template->set_block('form','add','addhandle');
			$GLOBALS['phpgw']->template->set_block('form','edit','edithandle');

			if ($_POST['save'])
			{
				if (!$_POST['site']['name'])
				{
					$GLOBALS['phpgw']->template->set_var('message',lang('Please enter a name for that site !'));
				}
				elseif ($site_id)
				{
					$this->bo->update($site_id,$_POST['site']);
					$GLOBALS['phpgw']->template->set_var('message',lang('Site %1 has been updated',$_POST['site']['_name']));
				}
				else
				{
					$site_id = $this->bo->add($_POST['site']);
					$GLOBALS['phpgw']->template->set_var('message',lang('Site %1 has been added',$_POST['site']['_name']));
				}
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('message','');
			}
			if ($site_id)
			{
				$site = $this->bo->read($site_id);
			}

			$GLOBALS['phpgw']->template->set_var('title_sites',$site_id ? lang('Edit Website') : lang('Add Website'));
			
			$GLOBALS['phpgw']->template->set_var('actionurl',$GLOBALS['phpgw']->link('/index.php','menuaction=sitemgr.Sites_UI.edit'));

			$GLOBALS['phpgw']->template->set_var(array(
				'lang_name' => lang('Site name'),
				'lang_sitedir' => lang('Filesystem path to sitemgr-site directory'),
				'lang_siteurl' => lang('URL to sitemgr-site'),
				'lang_anonuser' => lang('Anonymous user\'s username'),
				'lang_anonpasswd' => lang('Anonymous user\'s password'),
				'note_name' => lang('This is only used as an internal name for the website.'),
				'note_dir' => lang('This must be an absolute directory location.  <b>No trailing slash</b>.'),
				'note_url' => lang('The URL must be absolute and end in a slash, for example http://mydomain.com/mysite/'),
				'note_anonuser' => lang('If you haven\'t done so already, create a user that will be used for public viewing of the site.  Recommended name: anonymous.'),
				'note_anonpasswd' => lang('Password that you assigned for the anonymous user account.'),
				'note_adminlist' => lang('Select persons and groups that are entitled to configure the website.')
			));

			$GLOBALS['phpgw']->template->set_var('lang_adminlist',lang('Site administrators'));
			$GLOBALS['phpgw']->template->set_var('lang_save',lang('Save'));
			$GLOBALS['phpgw']->template->set_var('lang_add',lang('Add'));
			$GLOBALS['phpgw']->template->set_var('lang_default',lang('Default'));
			$GLOBALS['phpgw']->template->set_var('lang_reset',lang('Clear Form'));
			$GLOBALS['phpgw']->template->set_var('lang_done',lang('Cancel'));
			$GLOBALS['phpgw']->template->set_var('lang_delete',lang('Delete'));

			$GLOBALS['phpgw']->template->set_var(array(
				'site_name' => $site['site_name'],
				'site_dir' => $site['site_dir'],
				'site_url' => $site['site_url'],
				'site_anonuser' => $site['anonymous_user'],
				'site_anonpasswd' => $site['anonymous_passwd']
			));
			$GLOBALS['phpgw']->template->set_var('site_adminlist',$this->adminselectlist($site_id));
			$GLOBALS['phpgw']->template->set_var('site_id',$site_id);

			$GLOBALS['phpgw']->template->set_var(array(
				'th'      => $GLOBALS['phpgw_info']['theme']['th_bg'],
				'row_on'  => $GLOBALS['phpgw_info']['theme']['row_on'],
				'row_off' => $GLOBALS['phpgw_info']['theme']['row_off']
			));
			if ($site_id)
			{
				$GLOBALS['phpgw']->template->parse('edithandle','edit');
				$GLOBALS['phpgw']->template->set_var('addhandle','');
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('edithandle','');
				$GLOBALS['phpgw']->template->parse('addhandle','add');
			}
			$GLOBALS['phpgw']->template->pparse('phpgw_body','form');
			$this->common_ui->DisplayFooter();

		}

		function adminselectlist($site_id)
		{
			$accounts = $GLOBALS['phpgw']->accounts->get_list();
			$admin_list = $this->bo->get_adminlist($site_id);

			while (list($null,$account) = each($accounts))
			{
				$selectlist .= '<option value="' . $account['account_id'] . '"';
 				if($admin_list[$account['account_id']] == SITEMGR_ACL_IS_ADMIN)
				{
					$selectlist .= ' selected="selected"';
				}
				$selectlist .= '>' . $account['account_firstname'] . ' ' . $account['account_lastname']
										. ' [ ' . $account['account_lid'] . ' ]' . '</option>' . "\n";
			}
			return $selectlist;
		}

		function delete()
		{
			if (!$GLOBALS['phpgw']->acl->check('run',1,'admin'))
			{
				$GLOBALS['phpgw']->common->phpgw_header();
				echo parse_navbar();
				$this->deny();
			}

			$site_id = get_var('site_id',array('POST','GET'));
			if ($_POST['yes'] || $_POST['no'])
			{
				if ($_POST['yes'])
				{
					$this->bo->delete($site_id);
				}
				$GLOBALS['phpgw']->redirect_link('/index.php','menuaction=sitemgr.Sites_UI.list_sites');
			}
			else
			{
				$GLOBALS['phpgw']->common->phpgw_header();
				echo parse_navbar();

				$site = $this->bo->read($site_id);

				$GLOBALS['phpgw']->template->set_file(array('site_delete' => 'delete_common.tpl'));

				$GLOBALS['phpgw']->template->set_var(array(
					'form_action' => $GLOBALS['phpgw']->link('/index.php','menuaction=sitemgr.Sites_UI.delete'),
					'hidden_vars' => '<input type="hidden" name="site_id" value="' . $site_id . '"><script>document.yesbutton.yesbutton.focus()</script>',
					'messages' => lang('Are you sure you want to delete site %1 and all its content? You cannot retrieve it if you continue.',$site['site_name']),
					'no' => lang('No'),
					'yes' => lang('Yes'),
				));
				$GLOBALS['phpgw']->template->pparse('phpgw_body','site_delete');
			}
		}

		function deny()
		{
			echo '<p><center><b>'.lang('Access not permitted').'</b></center>';
			$GLOBALS['phpgw']->common->phpgw_exit(True);
		}
	}
?>
