<?php
	/*******************************************************************\
	* phpGroupWare - Backup                                             *
	* http://www.phpgroupware.org                                       *
	*                                                                   *
	* Administration Tool for data backup                               *
	* Written by Bettina Gille [ceb@phpgroupware.org]                   *
	* -----------------------------------------------                   *
	* Copyright (C) 2001 Bettina Gille                                  *
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

	class bobackup
	{
		var $public_functions = array
		(
			'check_values'		=> True,
			'save_items'		=> True,
			'get_config'		=> True,
			'create_config'		=> True,
			'save_config'		=> True,
			'phpftp_connect'	=> True,
			'get_archives'		=> True,
			'drop_archive'		=> True
		);

		function bobackup()
		{
			$this->config = CreateObject('phpgwapi.config','backup');
			$this->config->read_repository();
		}

		function get_config()
		{
			if ($this->config->config_data)
			{
				$items = $this->config->config_data;
			}
			return $items;
		}

		function phpftp_connect($host,$user,$pass)
		{
			$ftp = ftp_connect($host);
			if ($ftp)
			{
				if (ftp_login($ftp,$user,$pass))
				{
					return $ftp;
					ftp_quit($ftp);
				}
			}
		}

		function check_values($values)
		{
			if ($values['b_create'])
			{
				$doc_root = get_var('DOCUMENT_ROOT',Array('GLOBAL','SERVER'));

				if ($values['script_path'])
				{
					if (substr($values['script_path'],0,strlen($doc_root)) == $doc_root)
					{
						$error[] = lang('The directory to store the backup script must be outside of the webservers *DocumentRoot* !');
					}
				}
				else
				{
					$error[] = lang('Please set the path to a local dir to store the backup script !');
				}

				if ($values['versions'])
				{
					if (intval($values['versions']) == 0)
					{
						$error[] = lang('Versions can only be a number !');
					}
				}

				if ($values['l_save'])
				{
					if (! $values['l_path'] && ! $values['l_websave'])
					{
						$error[] = lang('Please enter the path to the backup dir and/or enable showing archives in phpGroupWare !');					
					}
				}

				if ($values['r_save'])
				{
					if (! $values['r_app'])
					{
						$error[] = lang('Please select an application for transport to the remote host !');					
					}
					elseif ($values['r_app'] != 'nfs')
					{
						if (! $values['r_user'] || ! $values['r_pwd'])
						{
							$error[] = lang('Please enter username and password for remote connection !');					
						}
					}
					elseif (!$values['r_ip'])
					{
						$error[] = lang('Please specify the ip of the remote host !');
					}
					elseif (!$values['r_path'])
					{
						$error[] = lang('Please specify the path to the backup directory !');
					}
					elseif ($values['r_app'] == 'ftp')
					{
						$ftp = $this->phpftp_connect($values['r_ip'],$values['r_user'],$values['r_pwd']);
						if (! $ftp)
						{
							$error[] = lang('The ftp connection failed ! Please check your configuration !');
						}
					}
				}

				$site_co = $this->get_config();
				if (is_array($site_co))
				{
					if (!isset($site_co['php_cgi']) || !isset($site_co['tar']) || !isset($site_co['zip']) || !isset($site_co['bzip2']))
					{
						$error[] = lang('Please enter the path of the needed applications in *Site configuration* !');
					}

					if ($values['b_sql'])
					{
						if ($GLOBALS['phpgw_info']['server']['db_type'] == 'mysql')
						{
							if (!isset($site_co['mysql']))
							{
								$error[] = lang('Please set the path to the MySQL database dir in *Site configuration* !');
							}
						}
						elseif($GLOBALS['phpgw_info']['server']['db_type'] == 'pgsql')
						{
							if (!isset($site_co['pgsql']))
							{
								$error[] = lang('Please set the path to the PostgreSQL database dir in *Site configuration* !');
							}
						}
						else
						{
							$error[] = lang('Your SQL database isnt supported by this application !');
						}
					}

					if ($values['b_ldap'])
					{
						if (!isset($site_co['ldap']) || !isset($site_co['ldap_in']))
						{
							$error[] = lang('Please set the path to the LDAP database dir in *Site configuration* !');
						}
					}

					if ($values['b_email'])
					{
						if (!isset($site_co['maildir']))
						{
							$error[] = lang('Please specify the name of the Maildir in *Site configuration* !');
						}
					}

				}
				else
				{
					$error[] = lang('Please set the values in *Site configuration* !');
				}
			}

			if (is_array($error))
			{
				return $error;
			}
		}

		function save_items($values)
		{
			if ($values['versions'])
			{
				$values['versions'] = intval($values['versions']);
			}
			else
			{
				$values['versions'] = 1;
			}

			if ($values['b_create'])
			{
				$values['b_create'] = 'yes';
			}
			else
			{
				$values['b_create'] = 'no';
			}

			if ($values['b_sql'])
			{
				$values['b_sql'] = $GLOBALS['phpgw_info']['server']['db_type'];
			}

			if ($values['b_ldap'])
			{
				$values['b_ldap'] = 'yes';
			}
			else
			{
				$values['b_ldap'] = 'no';
			}

			if ($values['b_email'])
			{
				$values['b_email'] = 'yes';
			}
			else
			{
				$values['b_email'] = 'no';
			}

			if ($values['r_save'])
			{
				$values['r_save'] = 'yes';
			}
			else
			{
				$values['r_save'] = 'no';
			}

			if ($values['l_save'])
			{
				$values['l_save'] = 'yes';
			}
			else
			{
				$values['l_save'] = 'no';
			}

			if ($values['l_websave'])
			{
				$values['l_websave'] = 'yes';
			}
			else
			{
				$values['l_websave'] = 'no';
			}


			while (list($key,$config) = each($values))
			{
				if ($config)
				{
					$this->config->config_data[$key] = $config;
				}
				else
				{
					unset($config->config_data[$key]);
				}
			}
			$this->config->save_repository(True);
			$this->create_config();
		}


		function save_config($conf_file, $config)
		{
			$file = fopen($conf_file,'w');
 //			ftruncate($file,0);
			fwrite($file,$config);
			fclose($file);
		}

		function create_config()
		{
			$co = $this->get_config();

			$co['db_type'] = $GLOBALS['phpgw_info']['server']['db_type'];
			$co['db_name'] = $GLOBALS['phpgw_info']['server']['db_name'];
			$co['server_root'] = PHPGW_SERVER_ROOT;

			if (!is_dir($co['script_path'] . '/backup'))
			{
				mkdir($co['script_path'] . '/backup',0700);
			}

			$co['basedir'] = $co['server_root'] . '/backup/archives';

			if (!is_dir($co['basedir']))
			{
				mkdir($co['basedir'], 0700);
			}

			$co['script_path'] = $co['script_path'] . '/backup';

			if ($co['b_create'] == 'yes')
			{
				
// ------------------------------------ check -----------------------------------------------

				$check = $GLOBALS['phpgw']->template->set_file(array('check' => 'check_form.tpl'));
				$check .= $GLOBALS['phpgw']->template->set_var('server_root',$co['server_root']);
				$check .= $GLOBALS['phpgw']->template->set_var('script_path',$co['script_path']);
				$check .= $GLOBALS['phpgw']->template->fp('out','check',True);
				$conf_file = $co['server_root'] . '/backup/phpgw_check_for_backup';
				$this->save_config($conf_file,$check);

// -------------------------------- end check -----------------------------------------------

// --------------------------------- backup -------------------------------------------------

				$config = $GLOBALS['phpgw']->template->set_file(array('backup' => 'backup_form.tpl'));
				$config .= $GLOBALS['phpgw']->template->set_var('script_path',$co['script_path']);
				$config .= $GLOBALS['phpgw']->template->set_var('php_path',$co['php_cgi']);
				$config .= $GLOBALS['phpgw']->template->fp('out','backup',True);
				$conf_file = $co['server_root'] . '/backup/phpgw_start_backup.' . $co['b_intval'];
				$this->save_config($conf_file,$config);

// -------------------------------- end backup ----------------------------------------------

// --------------------------------- script --------------------------------------------------

				$config = $GLOBALS['phpgw']->template->set_file(array('script_ba_t' => 'script_form.tpl'));
				$config .= $GLOBALS['phpgw']->template->set_block('script_ba_t','script_ba','ba');

				$config .= $GLOBALS['phpgw']->template->set_var('basedir',$co['basedir']);
				$config .= $GLOBALS['phpgw']->template->set_var('server_root',$co['server_root']);
				$config .= $GLOBALS['phpgw']->template->set_var('versions',$co['versions']);
				$config .= $GLOBALS['phpgw']->template->set_var('bintval',$co['b_intval']);
				$config .= $GLOBALS['phpgw']->template->set_var('bcomp',$co['b_type']);

				$config .= $GLOBALS['phpgw']->template->set_var('php_path',$co['php_cgi']);
				$config .= $GLOBALS['phpgw']->template->set_var('tar_path',$co['tar']);
				$config .= $GLOBALS['phpgw']->template->set_var('zip_path',$co['zip']);
				$config .= $GLOBALS['phpgw']->template->set_var('bzip2_path',$co['bzip2']);

				if ($co['b_sql'])
				{
					$config .= $GLOBALS['phpgw']->template->set_var('bsql',$co['b_sql']);
					$config .= $GLOBALS['phpgw']->template->set_var('db_name',$co['db_name']);
					$config .= $GLOBALS['phpgw']->template->set_var('mysql_dir',$co['mysql']);
					$config .= $GLOBALS['phpgw']->template->set_var('pgsql_dir',$co['pgsql']);
				}

				if ($co['b_ldap'] == 'yes')
				{
					$config .= $GLOBALS['phpgw']->template->set_var('bldap','yes');
					$config .= $GLOBALS['phpgw']->template->set_var('ldap_dir',$co['ldap']);
					$config .= $GLOBALS['phpgw']->template->set_var('ldap_in',$co['ldap_in']);
				}

				if ($co['b_email'] == 'yes')
				{
					$config .= $GLOBALS['phpgw']->template->set_var('bemail','yes');
					$config .= $GLOBALS['phpgw']->template->set_var('maildir',$co['maildir']);

					$allaccounts = $GLOBALS['phpgw']->accounts->get_list('accounts');

					while (list($null,$account) = each($allaccounts))
					{
						$config .= $GLOBALS['phpgw']->template->set_var(array
						(
							'lid'			=> stripslashes($account['account_lid']),
							'server_root'	=> $co['server_root']
						));
						$GLOBALS['phpgw']->template->fp('ba','script_ba',True);
					}
				}

				if ($co['r_save'] == 'yes')
				{
					$config .= $GLOBALS['phpgw']->template->set_var('rsave','yes');
					$config .= $GLOBALS['phpgw']->template->set_var('rip',$co['r_ip']);
					$config .= $GLOBALS['phpgw']->template->set_var('rpath',$co['r_path']);
					$config .= $GLOBALS['phpgw']->template->set_var('ruser',$co['r_user']);
					$config .= $GLOBALS['phpgw']->template->set_var('rpwd',$co['r_pwd']);
					$config .= $GLOBALS['phpgw']->template->set_var('rapp',$co['r_app']);
				}

				if ($co['l_save'] == 'yes')
				{
					$config .= $GLOBALS['phpgw']->template->set_var('lsave','yes');
					$config .= $GLOBALS['phpgw']->template->set_var('lpath',$co['l_path']);		
				}

				if ($co['l_websave'] == 'yes')
				{
					$config .= $GLOBALS['phpgw']->template->set_var('lsave','yes');
					$config .= $GLOBALS['phpgw']->template->set_var('lwebsave','yes');
				}

				$config .= $GLOBALS['phpgw']->template->fp('out','script_ba_t',True);

				$conf_file = $co['script_path'] . '/phpgw_data_backup.php';
				$this->save_config($conf_file,$config);
			}
			else
			{
				$conf_file = $co['server_root'] . '/backup/phpgw_delete_backup.all';
				$this->save_config($conf_file,'delete');
			}
		}

		function get_archives()
		{
			$basedir = PHPGW_SERVER_ROOT . '/backup/archives';
			if (is_dir($basedir))
			{
				$basedir = opendir($basedir);

				while (false !== ($files = readdir($basedir)))
				{
					if (($files != '.') && ($files != '..'))
					{
						$archives[] = $files;
//						_debug_array($archives);
//						exit;
					}
				}
				return $archives;
			}
			else
			{
				return False;
			}
		}

		function drop_archive($archive)
		{
			$basedir = PHPGW_SERVER_ROOT . '/backup/archives';			

			if (is_file($basedir . '/' . $archive))
			{
				unlink($basedir . '/' . $archive);
			}
		}
	}

?>
