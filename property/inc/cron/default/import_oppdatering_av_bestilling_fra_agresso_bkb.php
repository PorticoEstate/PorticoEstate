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
	* @subpackage import
 	* @version $Id: import_oppdatering_av_bestilling_fra_agresso_bkb.php 11456 2013-11-13 17:34:50Z sigurdne $
	*/

	/**
	 * Filteret importerer rapporter fra Agresso som grunnlag for oppdatering av øknomi og status på meldings_bestilling.
	 * @package property
	 */


	class  import_oppdatering_av_bestilling_fra_agresso_bkb
	{
		var	$function_name = 'import_oppdatering_av_bestilling_fra_agresso_bkb';
		var $debug = false;
		protected $updated_tickects = array();
		protected $receipt = array();


		function __construct()
		{
			$this->sotts			= CreateObject('property.sotts');
			$this->db				= & $GLOBALS['phpgw']->db;
			$this->join				= & $this->db->join;
			$this->left_join		= & $this->db->left_join;
			$this->like				= & $this->db->like;

			$this->config			= CreateObject('admin.soconfig',$GLOBALS['phpgw']->locations->get_id('property', '.invoice'));
			$this->send				= CreateObject('phpgwapi.send');
		}

		function pre_run($data = array())
		{
			if(isset($data['enabled']) && $data['enabled']==1)
			{
				$confirm	= true;
				$cron		= true;
			}
			else
			{
				$confirm	= phpgw::get_var('confirm', 'bool', 'POST');
				$execute	= phpgw::get_var('execute', 'bool', 'GET');
			}

			if( isset($data['debug']) && $data['debug'] )
			{
				$this->debug = true;
			}
			else
			{
				$this->debug	= phpgw::get_var('debug', 'bool');
			}

			if ($confirm)
			{
				$this->execute($cron);
			}
			else
			{
				$this->confirm($execute=false);
			}
		}

		function confirm($execute='')
		{
			$link_data = array
			(
				'menuaction'	=> 'property.custom_functions.index',
				'function'		=> $this->function_name,
				'execute'		=> $execute,
				'debug'			=> $this->debug
			);


			if(!$execute)
			{
				$lang_confirm_msg 	= lang('do you want to perform this action');
			}

			$lang_yes			= lang('yes');

			$GLOBALS['phpgw']->xslttpl->add_file(array('confirm_custom'));

			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($this->receipt);

			$data = array
			(
				'msgbox_data'			=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'done_action'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=>'property.uiasync.index')),
				'run_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'message'				=> $this->receipt['message'],
				'lang_confirm_msg'		=> $lang_confirm_msg,
				'lang_yes'				=> $lang_yes,
				'lang_yes_statustext'	=> 'Importer rapport fra Agresso for oppdatering av meldinger',
				'lang_no_statustext'	=> 'tilbake',
				'lang_no'				=> lang('no'),
				'lang_done'				=> 'Avbryt',
				'lang_done_statustext'	=> 'tilbake'
			);

			$appname		= lang('location');
			$function_msg	= 'Importer rapport fra Agresso for oppdatering av meldinger';
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('confirm' => $data));
			$GLOBALS['phpgw']->xslttpl->pp();
		}

		private function alert_assigned()
		{

			$updated_tickects = array_keys($this->updated_tickects);
			
			foreach ($updated_tickects as $id)
			{
				$this->db->query("SELECT assignedto FROM fm_tts_tickets WHERE id= '{$id}'",__LINE__,__FILE__);
				$this->db->next_record();
				$assignedto	= $this->db->f('assignedto');
				$this->send_notification($assignedto, $id);
			}
		}

		private function send_notification($assignedto = 0, $id = 0)
		{
			if (!isset($GLOBALS['phpgw_info']['server']['smtp_server']) || !$GLOBALS['phpgw_info']['server']['smtp_server'])
			{
				return;
			}

			$subject = 'Melding er oppdatert fra Agresso';
			$from = "Ikke svar<IkkeSvar@Bergen.kommune.no>";
			$prefs = $this->bocommon->create_preferences('property', $assignedto);
			if(isset($prefs['email']) && $prefs['email'])
			{
				$body = '<a href ="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uitts.view', 'id' => $id),false,true).'">' . lang('Ticket').' #' .$id .'</a>'."\n";
				try
				{
					$rc = $this->send->msg('email',$prefs['email'], $subject, stripslashes($body), '', '', '',$from,'','html');
				}
				catch (phpmailerException $e)
				{
					$this->receipt['error'][] = array('msg' => $e->getMessage());
				}
			}
		}

		public function execute($cron='')
		{
			$this->get_files();

			$dirname = $this->config->config_data['import']['local_path'];
			// prevent path traversal
			if ( preg_match('/\./', $dirname) 
			 || !is_dir($dirname) )
			{
				return array();
			}

			$file_list = array();
			$dir = new DirectoryIterator($dirname); 
			if ( is_object($dir) )
			{
				foreach ( $dir as $file )
				{
					if ( $file->isDot()
						|| !$file->isFile()
						|| !$file->isReadable()
						|| strcasecmp( end( explode( ".", $file->getPathname() ) ), 'csv' ) != 0 )
 					{
						continue;
					}

					$file_list[] = (string) "{$dirname}/{$file}";
				}
			}

			if(is_writable("{$dirname}/archive"))
			{
				foreach($file_list as $file)
				{
					$this->db->transaction_begin();

					$ok = $this->import($file);

					if ($ok)
					{
						// move file
						$_file = basename($file);
						$movefrom = "{$dirname}/{$_file}";
						$moveto = "{$dirname}/archive/{$_file}";

						if( is_file($moveto) )
						{
							@unlink($moveto);//in case of duplicates
						}

						$ok = @rename($movefrom, $moveto);
						if(!$ok) // Should never happen.
						{
						//	$this->invoice->delete($bilagsnr);
							$this->db->transaction_abort();
							$this->receipt['error'][] = array('msg' => "Kunne ikke flytte importfil til arkiv, oppdatering avbrutt");
						}
						else
						{
							$this->db->transaction_commit();
						}
					}
					else
					{
						$this->db->transaction_abort();
					}
				}
				
				$this->alert_assigned();
			}
			else
			{
				$this->receipt['error'][] = array('msg' => "Arkiv katalog '{$dirname}/archive/' ikke er ikke skrivbar - kontakt systemadminstrator for å korrigere");
			}

			if(!$cron)
			{
				$this->confirm($execute=false);
			}

			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($this->receipt);

			$insert_values= array
			(
				$cron,
				date($this->db->datetime_format()),
				$this->function_name,
				$this->db->db_addslashes(implode(',',(array_keys($msgbox_data))))
			);

			$insert_values	= $this->db->validate_insert($insert_values);

			$sql = "INSERT INTO fm_cron_log (cron,cron_date,process,message) "
					. "VALUES ($insert_values)";
			$this->db->query($sql,__LINE__,__FILE__);

		}

		protected function get_files()
		{
			$server				= $this->config->config_data['common']['host'];
			$user				= $this->config->config_data['common']['user'];
			$password			= $this->config->config_data['common']['password'];
			$directory_remote	= rtrim($this->config->config_data['import']['remote_basedir'],'/');
			$directory_local	= rtrim($this->config->config_data['import']['local_path'],'/');

			try
			{
				$connection = ftp_connect($server);
			}
			catch (Exception $e)
			{
				$this->receipt['error'][] = array('msg' => $e->getMessage());
			}

			// try to authenticate with username root, password secretpassword
			if(!ftp_login($connection,$user,$password))
			{
				echo "fail: unable to authenticate\n";
			}
			else
			{
				// allright, we're in!
				echo "okay: logged in...<br/>";

				if (!ftp_chdir($connection, $directory_remote))
				{
					echo ("Change Dir Failed: $dir<BR>\r\n");
					return false;
				}

				// Scan directory
				$files = array();
				echo "Scanning {$directory_remote}<br/>";

				$files = ftp_nlist($connection,'.');

				if ($this->debug)
				{
					_debug_array($files);
				}

				foreach($files as $file_name)
				{
					if( stripos( $file_name, '.csv' ))
					{
						$file_remote = $file_name;	   
						$file_local = "{$directory_local}/{$file_name}";

						$fp = fopen($file_local, "wb");

						if(ftp_fget($connection,$fp,$file_remote,FTP_ASCII))
						{
							if( ftp_delete($connection, $file_remote))
							{
								echo "File remote: {$file_remote} was copied to local: $file_local<br/>";
							}
							else
							{
								echo "ERROR! File remote: {$file_remote} failed to move to remote: {$directory_remote}/archive/{$file_name}<br/>";
								if(unlink($file_local))
								{
									echo "Lokal file was deleted: {$file_local}<br/>";
								}
							}
							
						}
						fclose($fp);
					}
				}
			}
		}

		private function import($file)
		{
			$ok = true;

			$file_name = basename($file);

			$fp = fopen($file,'rb');
			$row = 1;

			while (($data = fgetcsv($handle, 1000, ";")) !== false && $ok == true )
			{
				if($row > 1 && stripos( $file_name, 'penger' ) === 0)
				{
					$ok = $this->update_amount($data);
				}
				else if($row > 1 && stripos( $file_name, 'status' ) === 0)
				{
					$ok = $this->update_status($data);
				}
				$row ++;
			}

			fclose($fp);

			return $ok;

		}

		private function update_amount($data)
		{
    		$agresso_prosjekt	= (int)$data[0];
			$prosjektstatus		= trim($data[1]);
			$order_id			= trim($data[2]);
			$actual_cost		= (float)trim($data[3]);

			$this->db->query("SELECT id, status, actual_cost FROM fm_tts_tickets WHERE order_id= '{$order_id}'",__LINE__,__FILE__);
			$this->db->next_record();
			$id					= $this->db->f('id');
			$old_status			= $this->db->f('status');
			$old_actual_cost	= $this->db->f('actual_cost');

			if(!$id)
			{
				$this->receipt['error'][] = array('msg' =>"Oppdatere beløp: fant ikke bestillingen, hopper over: {$order_id}");
				return false;
			}

			$this->receipt['message'][] = array('msg' =>"Oppdaterer melding #{$id}, gammelt beløp: {$old_actual_cost}, nytt beløp: {$actual_cost}");

			$value_set = array
			(
	    		'agresso_prosjekt' => $agresso_prosjekt,
				'actual_cost' => $actual_cost
			);

			$value_set	= $this->db->validate_update($value_set);
			$ok = $this->db->query("UPDATE fm_tts_tickets SET $value_set WHERE id={$id}",__LINE__,__FILE__);
			
			if($ok)
			{
				$this->updated_tickects[$id] = true;
				$this->update_status($data);
			}

			return $ok;
		}

		private function update_status($data)
		{
    		$agresso_prosjekt	= (int)$data[0];
			$prosjektstatus		= trim($data[1]);

			$this->db->query("SELECT id, status FROM fm_tts_tickets WHERE agresso_prosjekt= '{$agresso_prosjekt}'",__LINE__,__FILE__);
			$this->db->next_record();
			$id			= $this->db->f('id');
			$old_status = $this->db->f('status');

			if(!$id)
			{
				$this->receipt['error'][] = array('msg' =>"Oppdatere status: fant ikke bestillingen, hopper over: {$order_id}");
				return false;
			}

			$ok = true;
			if(preg_match('/(^C|^P)/i', $prosjektstatus))
			{
				$ticket = array
				(
					'status' => 'C8'; //Avsluttet og fakturert (C)
				);

				if( $this->sotts->update_status($ticket,$id))
				{
					$this->updated_tickects[$id] = true;
				}
			}

			return $ok;
		}

	}
