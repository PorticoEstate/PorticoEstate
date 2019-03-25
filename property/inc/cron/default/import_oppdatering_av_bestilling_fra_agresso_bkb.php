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
	 * @version $Id$
	 */
	/**
	 * Filteret importerer rapporter fra Agresso som grunnlag for oppdatering av øknomi og status på meldings_bestilling.
	 * @package property
	 */
	include_class('property', 'cron_parent', 'inc/cron/');

	class import_oppdatering_av_bestilling_fra_agresso_bkb extends property_cron_parent
	{

		var $function_name				 = 'import_oppdatering_av_bestilling_fra_agresso_bkb';
		var $debug						 = true;
		protected $updated_tickects			 = array();
		protected $updated_tickects_per_file = array();
		protected $receipt					 = array();

		function __construct()
		{
			parent::__construct();

			$this->function_name = get_class($this);
			$this->sub_location	 = lang('ticket');
			$this->function_msg	 = 'Importer rapport fra Agresso for oppdatering av meldinger';

			$this->sotts		 = CreateObject('property.sotts');
			$this->config		 = CreateObject('admin.soconfig', $GLOBALS['phpgw']->locations->get_id('property', '.invoice'));
			$this->send			 = CreateObject('phpgwapi.send');
			$this->historylog	 = CreateObject('property.historylog', 'tts');
		}

		public function execute()
		{
			$this->get_files();

			$dirname = $this->config->config_data['import']['local_path_project'];
			// prevent path traversal
			if (preg_match('/\./', $dirname) || !is_dir($dirname))
			{
				return array();
			}

			$file_list	 = array();
			$dir		 = new DirectoryIterator($dirname);
			if (is_object($dir))
			{
				foreach ($dir as $file)
				{
					if ($file->isDot() || !$file->isFile() || !$file->isReadable() || strcasecmp(end(explode(".", $file->getPathname())), 'csv') != 0)
					{
						continue;
					}

					$file_list[] = (string)"{$dirname}/{$file}";
				}
			}

			if (is_writable("{$dirname}/archive"))
			{
				foreach ($file_list as $file)
				{
					$this->db->transaction_begin();

					if ($this->debug)
					{
						_debug_array("Start import file: $file");
					}

					$this->updated_tickects_per_file = array();
					$ok								 = $this->import($file);

					if ($ok)
					{
						// move file
						$_file		 = basename($file);
						$movefrom	 = "{$dirname}/{$_file}";
						$moveto		 = "{$dirname}/archive/{$_file}";

						if (is_file($moveto))
						{
							@unlink($moveto);//in case of duplicates
						}

						$ok = @rename($movefrom, $moveto);
						if (!$ok) // Should never happen.
						{
							$this->db->transaction_abort();
							$this->receipt['error'][] = array('msg' => "Kunne ikke flytte importfil til arkiv, oppdatering avbrutt");
						}
						else
						{
							$this->db->transaction_commit();
							$this->updated_tickects = array_merge($this->updated_tickects, $this->updated_tickects_per_file);
						}
					}
					else
					{
						$this->receipt['error'][] = array('msg' => "fil som feiler: $file");
						$this->db->transaction_abort();
					}
				}

				if (!$this->debug)
				{
					$this->alert_assigned();
				}
			}
			else
			{
				$this->receipt['error'][] = array('msg' => "Arkiv katalog '{$dirname}/archive/' ikke er ikke skrivbar - kontakt systemadminstrator for å korrigere");
			}

			$this->send_error_messages_as_email();
		}

		protected function get_files()
		{
			$server				 = $this->config->config_data['common']['host'];
			$user				 = $this->config->config_data['common']['user'];
			$password			 = $this->config->config_data['common']['password'];
			$directory_remote	 = rtrim($this->config->config_data['import']['remote_basedir_project'], '/');
			$directory_local	 = rtrim($this->config->config_data['import']['local_path_project'], '/');

			try
			{
				$connection = ftp_connect($server);
			}
			catch (Exception $e)
			{
				$this->receipt['error'][] = array('msg' => $e->getMessage());
			}

			// try to authenticate with username root, password secretpassword
			if (!ftp_login($connection, $user, $password))
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

				$files = ftp_nlist($connection, '.');

				if ($this->debug)
				{
					_debug_array($files);
				}

				foreach ($files as $file_name)
				{
					if ($this->debug)
					{
						_debug_array('preg_match("/csv$/i",' . $file_name . ': ' . preg_match('/csv$/i', $file_name));
					}

					if (preg_match('/csv$/i', $file_name))
					{
						$file_remote = $file_name;
						$file_local	 = "{$directory_local}/{$file_name}";

						if (ftp_get($connection, $file_local, $file_remote, FTP_ASCII))
						{
							if (ftp_rename($connection, $file_remote, "arkiv/{$file_remote}"))
							{
								echo "File remote: {$file_remote} was moved to archive: arkiv/{$file_remote}<br/>";
								echo "File remote: {$file_remote} was copied to local: $file_local<br/>";
							}
							else
							{
								echo "ERROR! File remote: {$file_remote} failed to move from remote: {$directory_remote}/arkiv/{$file_name}<br/>";
								if (unlink($file_local))
								{
									echo "Lokal file was deleted: {$file_local}<br/>";
								}
							}
						}
						else
						{
							echo "Feiler på ftp_fget()<br/>";
						}
					}
				}
			}
		}

		private function import( $file )
		{
			$ok = true;

			$file_name = basename($file);

			$fp = fopen($file, 'rb');

			while (($data = fgetcsv($fp, 1000, ";")) !== false && $ok == true)
			{
				if (preg_match('/^PENGER/i', $file_name))
				{
					$ok = $this->update_amount($data);
				}
				else if (preg_match('/^STATUS/i', $file_name))
				{
					$ok = true;
					if (trim($data[2]))//check for order_id
					{
						$ok = $this->update_status($data);
					}
				}
			}

			fclose($fp);

			return $ok;
		}

		private function update_amount( $data )
		{
			if ($this->debug)
			{
				_debug_array($data);
			}

			//prosjektnummer;prosjektstatus;bestillingsnummer;beløp
			$external_project_id = trim($data[0]);
			$prosjektstatus		 = trim($data[1]);
			$order_id			 = trim($data[2]);

			if (!ctype_digit($order_id))
			{
				$this->receipt['error'][] = array('msg' => "Feil format på bestillingsnummeret: {$order_id}");
				return false;
			}
			$diff_actual_cost = (float)trim($data[3]);

			$this->db->query("SELECT id FROM fm_tts_tickets WHERE order_id= '{$order_id}'", __LINE__, __FILE__);
			$this->db->next_record();
			$id = $this->db->f('id');

			if (!$id)
			{
				$this->receipt['error'][] = array('msg' => "Oppdatere beløp for agresso prosjekt {$external_project_id}: fant ikke bestillingen, hopper over: {$order_id}");
				return false;
			}

			$this->db->query("SELECT sum(amount) AS actual_cost FROM fm_tts_payments WHERE ticket_id = {$id}", __LINE__, __FILE__);
			$this->db->next_record();
			$old_actual_cost = (float)$this->db->f('actual_cost');
			$new_actual_cost = $old_actual_cost + $diff_actual_cost;

			$value_set_cost = array
				(
				'ticket_id'	 => $id,
				'amount'	 => $diff_actual_cost,
				'period'	 => date('Ym'),
				'remark'	 => 'Oppdatert fra Agresso',
				'created_on' => time(),
				'created_by' => -1
			);

			$cols_cost	 = implode(',', array_keys($value_set_cost));
			$values_cost = $this->db->validate_insert(array_values($value_set_cost));
			$this->db->query("INSERT INTO fm_tts_payments ({$cols_cost}) VALUES ({$values_cost})");

			$this->receipt['message'][] = array('msg' => "Oppdaterer melding #{$id} for agresso prosjekt {$external_project_id}: gammelt beløp: {$old_actual_cost}, nytt beløp: {$new_actual_cost}");
			$this->historylog->add('AC', $id, $new_actual_cost, $old_actual_cost);

			$value_set = array(
				'external_project_id'	 => $external_project_id,
				'actual_cost'			 => $new_actual_cost,
				'actual_cost_year'		 => date('Y'),
				'modified_date'			 => time()
			);

			$value_set	 = $this->db->validate_update($value_set);
			$ok			 = $this->db->query("UPDATE fm_tts_tickets SET $value_set WHERE id={$id}", __LINE__, __FILE__);

			if ($ok)
			{
				$this->updated_tickects_per_file[$id] = true;
				$this->update_status($data);
			}

			return $ok;
		}

		private function update_status( $data )
		{
			$external_project_id = trim($data[0]);
			$prosjektstatus		 = trim($data[1]);
			$order_id			 = trim($data[2]);

			$id = false;

			if ($order_id)
			{
				$this->db->query("SELECT id, status, external_project_id FROM fm_tts_tickets WHERE order_id= '{$order_id}'", __LINE__, __FILE__);
				$this->db->next_record();
				$id						 = $this->db->f('id');
				$old_status				 = $this->db->f('status');
				$old_external_project_id = $this->db->f('external_project_id');
			}

			if (!$id)
			{
				$this->receipt['error'][] = array('msg' => "Oppdatere status: fant ikke bestillingen for agresso prosjekt {$external_project_id}");
				return false;
			}

			if ($external_project_id != $old_external_project_id)
			{
				$this->db->query("UPDATE fm_tts_tickets SET external_project_id = '{$external_project_id}' WHERE id={$id}", __LINE__, __FILE__);
			}

			$ok = true;

			$update_ticket = array();
			switch ($prosjektstatus)
			{
				case 'C':
				case 'P':
					if ($old_status != 'C8')
					{
						$update_ticket = array
							(
							'status' => 'C8' //Avsluttet og fakturert (C)
						);
					}
					break;
				case 'N':
					if ($old_status == 'C8')
					{
						$update_ticket = array
							(
							'status' => 'C7' //I bestilling/ under utføring (B)
						);
					}
					break;
				default:
					break;
			}

			if ($update_ticket && $this->sotts->update_status($update_ticket, $id))
			{
				$this->updated_tickects_per_file[$id] = true;
			}

			return $ok;
		}

		private function alert_assigned()
		{

			$updated_tickects = array_keys($this->updated_tickects);

			foreach ($updated_tickects as $id)
			{
				$this->db->query("SELECT assignedto FROM fm_tts_tickets WHERE id= '{$id}'", __LINE__, __FILE__);
				$this->db->next_record();
				$assignedto = $this->db->f('assignedto');
				$this->send_notification($assignedto, $id);
			}
		}

		private function send_notification( $assignedto = 0, $id = 0 )
		{
			if (!isset($GLOBALS['phpgw_info']['server']['smtp_server']) || !$GLOBALS['phpgw_info']['server']['smtp_server'])
			{
				return;
			}

			$subject	 = 'Melding er oppdatert fra Agresso';
			$from		 = "Ikke svar<IkkeSvar@Bergen.kommune.no>";
			$bocommon	 = CreateObject('property.bocommon');
			$prefs		 = $bocommon->create_preferences('property', $assignedto);
			if (isset($prefs['email']) && $prefs['email'])
			{
				$body = '<a href ="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uitts.view',
						'id'		 => $id), false, true) . '">' . lang('Ticket') . ' #' . $id . '</a>' . "\n";
				try
				{
					$rc = $this->send->msg('email', $prefs['email'], $subject, stripslashes($body), '', '', '', $from, '', 'html');
				}
				catch (Exception $e)
				{
					$this->receipt['error'][] = array('msg' => $e->getMessage());
				}
			}
		}

		private function send_error_messages_as_email()
		{
			if (!isset($GLOBALS['phpgw_info']['server']['smtp_server']) || !$GLOBALS['phpgw_info']['server']['smtp_server'])
			{
				return;
			}

			$subject = 'Feil ved oppdatering av meldinger(bestillinger) fra Agresso';
			$from	 = "Ikke svar<IkkeSvar@Bergen.kommune.no>";
			$to		 = "Lene.Christensen@bergen.kommune.no";
			$cc		 = "Erik.Holm-Larsen@bergen.kommune.no";
			$bcc	 = "Sigurd.Nes@bergen.kommune.no";
			if ($this->receipt['error'])
			{
				$errors = array();
				foreach ($this->receipt['error'] as $error)
				{
					$errors[] = $error['msg'];
				}
				$body = implode("<br/>", $errors);
				try
				{
					$rc = $this->send->msg('email', $to, $subject, $body, '', $cc, $bcc, $from, '', 'html');
				}
				catch (Exception $e)
				{
					$this->receipt['error'][] = array('msg' => $e->getMessage());
				}
			}
		}
	}	