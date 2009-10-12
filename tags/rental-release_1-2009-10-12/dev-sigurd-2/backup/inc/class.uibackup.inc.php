<?php
	/*******************************************************************\
	* phpGroupWare - backup                                             *
	* http://www.phpgroupware.org                                       *
	*                                                                   *
	* Administration Tool for data backup                               *
	* Written by Bettina Gille [ceb@phpgroupware.org]                   *
	* -----------------------------------------------                   *
	* Copyright (C) 2001 - 2003 Bettina Gille                           *
	*                                                                   *
	* This program is free software; you can redistribute it and/or     *
	* modify it under the terms of the GNU General Public License as    *
	* published by the Free Software Foundation; either version 2 of    *
	* the License, or (at your option) any later version.               *
	*                                                                   *
	* This program is distributed in the hope that it will be useful,   *
	* but WITHOUT ANY WARRANTY; without even the implied warranty of    *
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU  *
	* General Public License for more details.                          *
	*                                                                   *
	* You should have received a copy of the GNU General Public License *
	* along with this program; if not, write to the Free Software       *
	* Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.         *
	\*******************************************************************/
	/* $Id$ */

	class uibackup
	{
		var $public_functions = array
		(
			'backup_admin'	=> True,
			'web_backup'	=> True
		);

		function uibackup()
		{
			$this->bobackup	= CreateObject('backup.bobackup');
		}

		function set_app_langs()
		{
			$GLOBALS['phpgw']->template->set_var('bg_color',$GLOBALS['phpgw_info']['theme']['th_bg']);
			$GLOBALS['phpgw']->template->set_var('row_on',$GLOBALS['phpgw_info']['theme']['row_on']);
			$GLOBALS['phpgw']->template->set_var('row_off',$GLOBALS['phpgw_info']['theme']['row_off']);
			$GLOBALS['phpgw']->template->set_var('lang_b_create',lang('Create backups of your data ?'));
			$GLOBALS['phpgw']->template->set_var('lang_b_intval',lang('Interval'));
			$GLOBALS['phpgw']->template->set_var('lang_select_b_intval',lang('Select interval'));
			$GLOBALS['phpgw']->template->set_var('lang_b_data',lang('Data'));
			$GLOBALS['phpgw']->template->set_var('lang_b_sql',lang('SQL'));
			$GLOBALS['phpgw']->template->set_var('lang_b_ldap',lang('LDAP'));
			$GLOBALS['phpgw']->template->set_var('lang_b_email',lang('E-MAIL'));
			$GLOBALS['phpgw']->template->set_var('lang_r_host',lang('Operating system'));
			$GLOBALS['phpgw']->template->set_var('lang_r_config',lang('Configuration remote host'));
			$GLOBALS['phpgw']->template->set_var('lang_r_save',lang('Save backup to a remote host ?'));
			$GLOBALS['phpgw']->template->set_var('lang_config_path',lang('Absolute path of the directory to store the backup script'));
			$GLOBALS['phpgw']->template->set_var('lang_path',lang('Absolute path of the backup directory'));
			$GLOBALS['phpgw']->template->set_var('lang_r_ip',lang('IP or hostname'));
			$GLOBALS['phpgw']->template->set_var('lang_user',lang('User'));
			$GLOBALS['phpgw']->template->set_var('lang_pwd',lang('Password'));
			$GLOBALS['phpgw']->template->set_var('lang_l_config',lang('Configuration localhost'));
			$GLOBALS['phpgw']->template->set_var('lang_l_save',lang('Save backup locally ?'));
			$GLOBALS['phpgw']->template->set_var('lang_l_websave',lang('Show backup archives in phpGroupWare ?'));
			$GLOBALS['phpgw']->template->set_var('lang_b_config',lang('Configuration backup'));
			$GLOBALS['phpgw']->template->set_var('lang_b_type',lang('Archive type'));
			$GLOBALS['phpgw']->template->set_var('lang_select_b_type',lang('Select archive type'));
			$GLOBALS['phpgw']->template->set_var('lang_app',lang('Transport application'));
			$GLOBALS['phpgw']->template->set_var('lang_select_app',lang('Select transport application'));
			$GLOBALS['phpgw']->template->set_var('lang_save',lang('Save'));
			$GLOBALS['phpgw']->template->set_var('lang_cancel',lang('cancel'));
			$GLOBALS['phpgw']->template->set_var('lang_versions',lang('Number of stored backup versions'));
		}

		function backup_admin()
		{
			$values = $_POST['values'];

			$link_data = array
			(
				'menuaction' => 'backup.uibackup.backup_admin'
			);

			if ($values['save'])
			{
				$error = $this->bobackup->check_values($values);
				if (is_array($error))
				{
					$GLOBALS['phpgw']->template->set_var('message',$GLOBALS['phpgw']->common->error_list($error));
				}
				else
				{
					$this->bobackup->save_items($values);
					$GLOBALS['phpgw']->redirect_link('/admin/index.php');
				}
			}

			$GLOBALS['phpgw']->common->phpgw_header();

			$this->set_app_langs();

			$GLOBALS['phpgw']->template->set_file(array('admin_form' => 'admin_form.tpl'));

			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->set_var('cancel_url',$GLOBALS['phpgw']->link('/admin/index.php'));

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('backup') . ': ' . lang('administration');

			$values = $this->bobackup->get_config();

			$GLOBALS['phpgw']->template->set_var('b_create','<input type="checkbox" name="values[b_create]" value="True"' . ($values['b_create'] == 'yes'?' checked':'') . '>');

			switch($values['b_intval'])
			{
				case 'daily': $b_intval_sel[0]=' selected';break;
				case 'weekly': $b_intval_sel[1]=' selected';break;
				case 'monthly': $b_intval_sel[2]=' selected';break;
			}

			$intval_list = '<option value="daily"' . $b_intval_sel[0] . '>' . lang('daily') . '</option>' . "\n"
						. '<option value="weekly"' . $b_intval_sel[1] . '>' . lang('weekly') . '</option>' . "\n"
						. '<option value="monthly"' . $b_intval_sel[2] . '>' . lang('monthly') . '</option>' . "\n";

			$GLOBALS['phpgw']->template->set_var('intval_list',$intval_list);

			switch($values['b_type'])
			{
				case 'tgz':		$b_type_sel[0]=' selected';break;
				case 'tar.bz2':	$b_type_sel[1]=' selected';break;
				case 'zip':		$b_type_sel[2]=' selected';break;
			}

			$type_list = '<option value="tgz"' . $b_type_sel[0] . '>' . lang('tar.gz') . '</option>' . "\n"
						. '<option value="tar.bz2"' . $b_type_sel[1] . '>' . lang('tar.bz2') . '</option>' . "\n"
						. '<option value="zip"' . $b_type_sel[2] . '>' . lang('zip') . '</option>' . "\n";

			$GLOBALS['phpgw']->template->set_var('type_list',$type_list);

			switch($values['r_app'])
			{
				case 'ftp':			$r_type_sel[0]=' selected';break;
				case 'nfs':			$r_type_sel[1]=' selected';break;
				case 'smbmount':	$r_type_sel[2]=' selected';break;
			}

			$r_app_list = '<option value="ftp"' . $r_type_sel[0] . '>' . lang('ftp') . '</option>' . "\n"
						. '<option value="nfs"' . $r_type_sel[1] . '>' . lang('nfs') . '</option>' . "\n"
						. '<option value="smbmount"' . $r_type_sel[2] . '>' . lang('smbmount') . '</option>' . "\n";

			$GLOBALS['phpgw']->template->set_var('r_app_list',$r_app_list);

			if ($values['b_sql'] == 'mysql' || $values['b_sql'] == 'pgsql')
			{
				$values['b_sql'] = 'yes';
			}

			$GLOBALS['phpgw']->template->set_var('b_sql','<input type="checkbox" name="values[b_sql]" value="True"' . ($values['b_sql'] == 'yes'?' checked':'') . '>');
			$GLOBALS['phpgw']->template->set_var('b_ldap','<input type="checkbox" name="values[b_ldap]" value="True"' . ($values['b_ldap'] == 'yes'?' checked':'') . '>');
			$GLOBALS['phpgw']->template->set_var('b_email','<input type="checkbox" name="values[b_email]" value="True"' . ($values['b_email'] == 'yes'?' checked':'') . '>');

			$GLOBALS['phpgw']->template->set_var('l_save','<input type="checkbox" name="values[l_save]" value="True"' . ($values['l_save'] == 'yes'?' checked':'') . '>');
			$GLOBALS['phpgw']->template->set_var('l_websave','<input type="checkbox" name="values[l_websave]" value="True"' . ($values['l_websave'] == 'yes'?' checked':'') . '>');
			$GLOBALS['phpgw']->template->set_var('r_save','<input type="checkbox" name="values[r_save]" value="True"' . ($values['r_save'] == 'yes'?' checked':'') . '>');

			$r_host = '<input type="radio" name="values[r_host]" value="unix"' . ($values['r_host'] == 'unix'?' checked':'') . '>UNIX' . "\n";
			$r_host .= '<input type="radio" name="values[r_host]" value="win"' . ($values['r_host'] == 'win'?' checked':'') . '>WIN';

			$GLOBALS['phpgw']->template->set_var('r_host',$r_host);
			$GLOBALS['phpgw']->template->set_var('r_path',$values['r_path']);
			$GLOBALS['phpgw']->template->set_var('r_ip',$values['r_ip']);
			$GLOBALS['phpgw']->template->set_var('r_user',$values['r_user']);
			$GLOBALS['phpgw']->template->set_var('r_pwd',$values['r_pwd']);

			$GLOBALS['phpgw']->template->set_var('script_path',$values['script_path']);
			$GLOBALS['phpgw']->template->set_var('l_path',$values['l_path']);
			$GLOBALS['phpgw']->template->set_var('versions',$values['versions']);

			$GLOBALS['phpgw']->template->pfp('out','admin_form');
		}

		function web_backup()
		{
			if ($_POST['delete'] && $_POST['archive'])
			{
				$this->bobackup->drop_archive($_POST['archive']);
			}

			$link_data = array
			(
				'menuaction' 	=> 'backup.uibackup.web_backup',
				'delete'		=> $delete,
				'archive'		=> $archive
			);

			$GLOBALS['phpgw']->common->phpgw_header();

			$this->set_app_langs();

			$GLOBALS['phpgw']->template->set_file(array('archive_list_t' => 'web_form.tpl'));
			$GLOBALS['phpgw']->template->set_block('archive_list_t','archive_list','list');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('backup') . ': ' . lang('list backup archives');

			$config = $this->bobackup->get_config();

			if ($config['l_websave'] == 'yes')
			{
				$archives = $this->bobackup->get_archives();
				$this->nextmatchs = CreateObject('phpgwapi.nextmatchs');

				if ($archives)
				{
					for ($i=0;$i<count($archives);$i++)
					{
						$this->nextmatchs->template_alternate_row_color($GLOBALS['phpgw']->template);

						$GLOBALS['phpgw']->template->set_var(array
						(
							'archive'	=> 'archives/' . $archives[$i],
							'aname'		=> $archives[$i]
						));

						$GLOBALS['phpgw']->template->set_var('delete',$GLOBALS['phpgw']->link('/index.php','menuaction=backup.uibackup.web_backup&delete=True&archive='
											. $archives[$i]));
						$GLOBALS['phpgw']->template->set_var('lang_delete',lang('Delete'));

						$GLOBALS['phpgw']->template->fp('list','archive_list',True);
					}
				}
				else
				{
					$GLOBALS['phpgw']->template->set_var('noweb',lang('No backup archives available !'));
				}
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('noweb',lang('The backup application is not configured for showing the archives in phpGroupWare yet !'));
			}

			$GLOBALS['phpgw']->template->pfp('out','archive_list_t',True);
		}
	}
?>
