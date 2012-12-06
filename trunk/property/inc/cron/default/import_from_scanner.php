<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
	* This file is part of phpGroupWare.
	*
	* phpGroupWare is free software; you can redistribute it and/or modify
	* it under the terms of the GNU General Public License as published by
	* the Free Software Foundation; either version 2 of the License, or
	* (at your option) any later version.
	*
	* phpGroupWare is distributed in the hope that it will be useful,
	* but WITHOUT ANY WARRANTY; without even the implied warranty of
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	* GNU General Public License for more details.
	*
	* You should have received a copy of the GNU General Public License
	* along with phpGroupWare; if not, write to the Free Software
	* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage custom
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	class import_from_scanner
	{
		/* In Admin->Property->Async servises:
		*  Name: property.custom_functions.index
		*  Data: function=import_from_scanner,dir=C:/path/to/scanned_images
		*/

		var	$dir = '/home/sn5607/test';
		var	$suffix = 'pdf';
		var	$meta_suffix = 'csv';
		var	$delimiter = ',';
		var $bypass = false; // bypass location check (only for debugging)
		var $default_user_id = 6;
		var $default_user_last_name = 'Aspevik';
		var $mail_receipt = true;
		var	$function_name = 'import_from_scanner';
		var	$header = array('type','descr','target','user');

		function import_from_scanner()
		{
		//	$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->bocommon		= CreateObject('property.bocommon');
			$this->bofiles		= CreateObject('property.bofiles');
			$this->db 			= & $GLOBALS['phpgw']->db;
		}

		function pre_run($data='')
		{
			$cron		= false;
			$dry_run	= false;

			if(isset($data['enabled']) && $data['enabled']==1)
			{
				$confirm		= true;
				$execute		= true;
				$cron			= true;
				if($data['suffix'])
				{
					$this->suffix = $data['suffix'];
				}
				if($data['dir'])
				{
					$this->dir = $data['dir'];
				}
			}
			else
			{
				$confirm	= phpgw::get_var('confirm', 'bool', 'POST');
				$execute	= true;//phpgw::get_var('execute', 'bool', 'GET');
				if(phpgw::get_var('dir', 'string' ,'GET'))
				{
					$this->dir = urldecode (phpgw::get_var('dir', 'string' ,'GET'));
				}
				if(phpgw::get_var('suffix', 'string', 'GET'))
				{
					$this->suffix = phpgw::get_var('suffix', 'string', 'GET');
				}
			}

			if(!$execute)
			{
				$dry_run=true;
			}

			if ($confirm)
			{
				$this->execute($dry_run,$cron);
			}
			else
			{
				$this->confirm($execute=false);
			}
		}

		function confirm($execute='',$done='')
		{
			$link_data = array
			(
				'menuaction' => 'property.custom_functions.index',
				'function'	=> $this->function_name,
				'execute'	=> $execute,
				'dir'		=> $this->dir,
				'suffix'	=> $this->suffix,
			);

			if(!$done)
			{
				if(!$execute)
				{
					$lang_confirm_msg 	= 'Ga videre for aa se hva som blir lagt til';
				}
				else
				{
					$lang_confirm_msg 	= lang('do you want to perform this action');
				}
			}
			$lang_yes			= lang('yes');

			$GLOBALS['phpgw']->xslttpl->add_file(array('confirm_custom'));

			$msgbox_data = $this->bocommon->msgbox_data($this->receipt);

			$data = array
			(
				'msgbox_data'			=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'done_action'			=> $GLOBALS['phpgw']->link('/admin/index.php'),
				'run_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'message'				=> $this->receipt['message'],
				'lang_confirm_msg'		=> $lang_confirm_msg,
				'lang_yes'				=> $lang_yes,
				'lang_yes_statustext'	=> 'Legger til dokumenter fra scanner',
				'lang_no_statustext'	=> 'tilbake',
				'lang_no'				=> lang('no'),
				'lang_done'				=> 'Avbryt',
				'lang_done_statustext'	=> 'tilbake'
			);

			$appname		= 'import from scanner';
			$function_msg	= 'import files from scanner-drop-catalog';
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('confirm' => $data));
			$GLOBALS['phpgw']->xslttpl->pp();
		}

		function execute($dry_run='',$cron='')
		{
			$file_list = $this->get_files();

			if($dry_run)
			{
				_debug_array($file_list);
				$this->confirm($execute=true);

			}
			else
			{
				if (isset($file_list) && is_array($file_list))
				{
					$this->botts		= CreateObject('property.botts');
					$this->bolocation	= CreateObject('property.bolocation');
					$send			= CreateObject('phpgwapi.send');
					foreach($file_list as $file_entry)
					{
						$file_entry['user_id'] = $this->get_user_id($file_entry['user']);

						if($file_entry['type'] == 'Dokumentasjon')
						{
							if($values['location_code'] = $this->get_location_code($file_entry['target']))
							{
								$this->bolocation->initiate_ui_location(array('type_id'	=> -1,'tenant'	=> true));

								$insert_record = $GLOBALS['phpgw']->session->appsession('insert_record','property');

								$values = $this->bolocation->read_single($values['location_code'],array('tenant_id'=>'lookup'));
								for ($i=0; $i<count($insert_record['location']); $i++)
								{
									if($values[$insert_record['location'][$i]])
									{
										$values['location'][$insert_record['location'][$i]]= $values[$insert_record['location'][$i]];
									}
								}

								$values['category_id']	= 2;
								$values['values_date']	= time();
								$values['version']		= '1';
								$values['coordinator']	= '';
								$values['status']		= '1';
								$values['branch_id']	= '';
								$values['vendor_id']	= '';
								$values['user_id']		= $file_entry['user_id'];
								$values['file_name']	= $file_entry['file_name'];
								$values['title']		= $file_entry['descr'];
								$this->bofiles->create_document_dir("document/{$values['loc1']}");
								$this->copy_files($values);
							}
						}

						if($file_entry['type'] == 'Reklamasjon')
						{
							if($file_entry['target'] && $this->find_ticket($file_entry['target']))
							{
								$this->add_file_to_ticket($file_entry['target'],$file_entry['file_name']);
							}
							else
							{
								if($values['location_code'] = $this->get_location_code($file_entry['target']))
								{
									$this->bolocation->initiate_ui_location(array('type_id'	=> -1,'tenant'	=> true));

									$insert_record = $GLOBALS['phpgw']->session->appsession('insert_record','property');

									$values = $this->bolocation->read_single($values['location_code'],array('tenant_id'=>'lookup'));
									for ($i=0; $i<count($insert_record['location']); $i++)
									{
										if($values[$insert_record['location'][$i]])
										{
											$values['location'][$insert_record['location'][$i]]= $values[$insert_record['location'][$i]];
										}
									}

									$values['details']		= $file_entry['descr'];
									$values['subject']		= $file_entry['descr'];
									$values['assignedto']	= (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['assigntodefault'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['assigntodefault']:'');
									$values['group_id']		= (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['groupdefault'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['groupdefault']:'');
									$values['cat_id']		= (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_category'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['tts_category']:'');
									$values['priority']		= (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['prioritydefault'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['prioritydefault']:'');

									$receipt = $this->botts->add($values);
									$this->add_file_to_ticket($receipt['id'],$file_entry['file_name']);
								}
								else
								{
									$this->receipt['error'][]=array('msg'=>lang('Location is missing !'));
								}
							}
						}

						if($this->mail_receipt)
						{
							$prefs = $this->bocommon->create_preferences('property',$file_entry['user_id']);
							if (strlen($prefs['email'])> (strlen($members[$i]['account_name'])+1))
							{
								$subject = 'Resultat fra scanner';
								$msgbox_data = $this->bocommon->msgbox_data($this->receipt);
								$body = implode('</br>',array_keys($msgbox_data));
								//, '', $cc, $bcc,$current_user_address,$current_user_name,

								$to = $prefs['email'];
								$rc = $send->msg('email', $to, $subject, stripslashes($body), '', $cc, $bcc,$current_user_address,$current_user_name,'html');
							}
							else
							{
								$this->receipt['error'][] = array('msg'=> lang('Your message could not be sent!'));
								$this->receipt['error'][] = array('msg'=>lang('This user has not defined an email address !') . ' : ' . $members[$i]['account_name']);
							}
						}

						unlink("{$this->dir}/{$file_entry['file_name']}{$this->suffix}");
						unlink("{$this->dir}/{$file_entry['file_name']}{$this->meta_suffix}");

						$msgbox_data = $this->bocommon->msgbox_data($this->receipt);

						$insert_values= array(
							$cron,
							date($this->db->datetime_format()),
							$this->function_name,
							implode(',',(array_keys($msgbox_data)))
							);

						$insert_values	= $this->db->validate_insert($insert_values);

						$sql = "INSERT INTO fm_cron_log (cron,cron_date,process,message) "
								. "VALUES ($insert_values)";
						$this->db->query($sql,__LINE__,__FILE__);
						$receipt = array();
					}
				}

				if(!$cron)
				{
					$this->confirm($execute=false,$done=true);
				}
			}
		}

		function get_files()
		{
			$dir_handle = @opendir($this->dir);

			$myfilearray = array();
			while ($file = @readdir($dir_handle))
			{
				if ((strtolower(substr($file, -3, 3)) == $this->meta_suffix) && is_file("{$this->dir}/{$file}") )
				{
					$myfilearray[] = $file;
				}
			}

			@closedir($dir_handle);
			@sort($myfilearray);

			for ($i=0;$i<count($myfilearray);$i++)
			{
				$fname = $myfilearray[$i];
				$file_list[$i]['file_name'] = substr($fname,0, strlen($fname)-strlen($this->meta_suffix));

				$fp = fopen("{$this->dir}/{$fname}", 'rb');

				$row = 1;
				while ($data = fgetcsv($fp,8000,$this->delimiter))
				{
					if ($row ==2) // Ther first row is headerinfo
					{
						$num = count($this->header);

						$this->currentrecord = array();
						for ($c=0; $c<$num; $c++ )
						{
							$value=$data[$c];
							$name=$this->header[$c];

							$file_list[$i][$name] = $value;
						}
					}
					$row++;
				}
				fclose($fp);
			}
			return $file_list;
		}

		function add_file_to_ticket($id,$file_name)
		{
				$to_file = "{$this->bofiles->fakebase}/fmticket/{$id}/{$file_name}{$this->suffix}";

				if($this->bofiles->vfs->file_exists(array(
					'string' => $to_file,
					'relatives' => Array(RELATIVE_NONE)
					)))
				{
					$this->receipt['error'][]=array('msg'=> 'Denne filen finnes allerede for melding # ' . $id);
				}
				else
				{
					$this->bofiles->create_document_dir("fmticket/{$id}");
					$this->bofiles->vfs->override_acl = 1;

					if(!$this->bofiles->vfs->cp (array (
						'from'	=> $this->dir . '/' . $file_name . $this->suffix,
						'to'	=> $to_file,
						'relatives'	=> array (RELATIVE_NONE|VFS_REAL, RELATIVE_ALL))))
					{
						$this->receipt['error'][]=array('msg'=>lang('Failed to upload file !'));
					}
					else
					{
						$this->receipt['message'][]=array('msg'=>lang('File %1 added to ticket %2',$file_name . $this->suffix,$id));
					}
					$this->bofiles->vfs->override_acl = 0;
				}
		}

		function find_ticket($id='')
		{
			if(!ctype_digit($id))
			{
				return false;
			}
			else
			{
				$sql = "SELECT count(*) as cnt FROM fm_tts_tickets WHERE id='$id'";
				$this->db->query($sql,__LINE__,__FILE__);
				$this->db->next_record();
				if($this->db->f('cnt'))
				{
					return true;
				}
			}
		}

		function get_user_id($account_lastname = '')
		{
			$account_lastname = $account_lastname?$account_lastname:$this->default_user_last_name;
			$sql = "SELECT account_id FROM phpgw_accounts WHERE account_lastname='$account_lastname'";

			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			if($this->db->f('account_id'))
			{
				return $this->db->f('account_id');
			}
			else
			{
				return $this->default_user_id;
			}
		}

		function get_location_code($target = '')
		{
			if(strpos($target,'.'))
			{
				$location = explode('.', $target);
				$sql = "SELECT location_code FROM fm_location4 WHERE loc1= '{$location[0]}' AND loc4= '{$location[1]}'";
			}
			else
			{
				$location =  explode('-', $target);
				$type = count($location);
				$sql = "SELECT location_code FROM fm_location{$type} WHERE location_code = '{$target}'";
			}

			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			return $this->db->f('location_code');
		}


		function copy_files($values)
		{
			$to_file = "{$bofiles->fakebase}/document/{$values['loc1']}/{$values['file_name']}{$this->suffix}";
			$from_file = "{$this->dir}/{$values['file_name']}{$this->suffix}";
			$this->bofiles->vfs->override_acl = 1;

			if($this->bofiles->vfs->file_exists(array(
					'string' => $to_file,
					'relatives' => Array(RELATIVE_NONE)
				)))
			{
				$this->receipt['error'][]=array('msg'=>lang('File %1 already exists!',$values['file_name'] . $this->suffix));
			}
			else
			{

				if(!$this->bofiles->vfs->cp (array (
					'from'	=> $from_file,
					'to'	=> $to_file,
					'relatives'	=> array (RELATIVE_NONE|VFS_REAL, RELATIVE_ALL))))
				{
					$this->receipt['error'][]=array('msg'=>lang('Failed to copy file !') . $values['file_name'] . $this->suffix);
				}
				else
				{
					if($ticket['street_name'])
					{
						$address	= $this->db->db_addslashes($values['street_name'] . ' ' . $values['street_number']);
					}

					if(!$address)
					{
						$address = $this->db->db_addslashes($values['location_name']);
					}

					$insert_values= array(
						$values['file_name'] . $this->suffix,
						$values['title'],
						'public',
						$values['category_id'],
						time(),
						$values['values_date'],
						$values['version'],
						$values['coordinator'],
						$values['status'],
						$values['location_code'],
						$address,
						$values['branch_id'],
						$values['vendor_id'],
						$this->account,
						$values['loc1'],
						$values['loc2'],
						$values['loc3'],
						$values['loc4'],
						);

					$insert_values	= $this->db->validate_insert($insert_values);

					$sql = "INSERT INTO fm_document (document_name,title,access,category,entry_date,document_date,version,coordinator,status,"
						. "location_code,address,branch_id,vendor_id,user_id,loc1,loc2,loc3,loc4) "
						. "VALUES ($insert_values)";

					$this->db->query($sql,__LINE__,__FILE__);

					$this->receipt['message'][]=array('msg'=>lang('File %1 copied!',$values['file_name'] . $this->suffix));
				}
			}
			$this->bofiles->vfs->override_acl = 0;
		}
	}

