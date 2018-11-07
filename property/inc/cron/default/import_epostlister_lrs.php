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

	class import_epostlister_lrs extends property_cron_parent
	{

		var $function_name = 'import_epostlister_lrs';
		var $debug = true;
		protected $updated_tickects = array();
		protected $updated_tickects_per_file = array();
		protected $receipt = array();

		function __construct()
		{
			parent::__construct();

			$this->function_name = get_class($this);
			$this->sub_location = lang('ticket');
			$this->function_msg = 'Importer predefinerte epostlister fra Outlook';

			$this->sotts = CreateObject('property.sotts');
			$this->config = CreateObject('admin.soconfig', $GLOBALS['phpgw']->locations->get_id('property', '.invoice'));
			$this->send = CreateObject('phpgwapi.send');
			$this->historylog = CreateObject('property.historylog', 'tts');
		}

		public function execute()
		{
	//		$this->get_files();

			$dirname = $this->config->config_data['import']['local_path_email'];
			$dirname = "/var/lib/phpgw/lrs/files/home";
			// prevent path traversal
			if (preg_match('/\./', $dirname) || !is_dir($dirname))
			{
				return array();
			}

			$file_list = array();
			$dir = new DirectoryIterator($dirname);
			if (is_object($dir))
			{
				foreach ($dir as $file)
				{
					if ($file->isDot() || !$file->isFile() || !$file->isReadable() || strcasecmp(end(explode(".", $file->getPathname())), 'txt') != 0)
					{
						continue;
					}

					$file_list[] = (string)"{$dirname}/{$file}";
				}
			}


			foreach ($file_list as $file)
			{
				$this->db->transaction_begin();

				if ($this->debug)
				{
					_debug_array("Start import file: $file");
				}

				$this->updated_tickects_per_file = array();
				$ok = $this->import($file);

				if ($ok)
				{
//					unlink($file);
					$this->db->transaction_commit();
				}
				else
				{
					$this->receipt['error'][] = array('msg' => "fil som feiler: $file");
					$this->db->transaction_abort();
				}
			}

			$this->send_error_messages_as_email();
		}

		protected function get_files()
		{
			$server = $this->config->config_data['common']['host'];
			$user = $this->config->config_data['common']['user'];
			$password = $this->config->config_data['common']['password'];
			$directory_remote = rtrim($this->config->config_data['import']['remote_basedir_email'], '/');
			$directory_local = rtrim($this->config->config_data['import']['local_path_email'], '/');

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
					if ($file_name == 'Hele_Listen.txt')
					{
						$file_remote = $file_name;
						$file_local = "{$directory_local}/{$file_name}";

						if (ftp_get($connection, $file_local, $file_remote, FTP_ASCII))
						{
//							if (ftp_rename($connection, $file_remote, "arkiv/{$file_remote}"))
//							{
//								echo "File remote: {$file_remote} was moved to archive: arkiv/{$file_remote}<br/>";
//								echo "File remote: {$file_remote} was copied to local: $file_local<br/>";
//							}
//							else
//							{
//								echo "ERROR! File remote: {$file_remote} failed to move from remote: {$directory_remote}/arkiv/{$file_name}<br/>";
//								if (unlink($file_local))
//								{
//									echo "Lokal file was deleted: {$file_local}<br/>";
//								}
//							}
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

			$fp = fopen($file, 'rb');

			$values = array();


//1	Ledere Gruppe 1
//2	Saksbehandlere Gruppe 1
//3	Saksbehandlere Gruppe 2
//4	LRS Lønn_Refusjon - medarbeidere
//5	Medarbeider i forvaltningen
//6	Ragnar Buset
//

			$email_list = array
			(
				'AgrHRLeder'	=> 1,	//AgressoHR-LedereGruppe1
				'AgrHRSak'		=> 2,	//AgressoHR-SaksbehandlereGruppe1, AgressoHR-SaksbehandlereGruppe2
				'AgrHRSysr'		=> 5,	//HR-seksjonen-Systemforvaltning
				'AgrHRLonn'		=> 4,	//LRSAnsatteLønn
				'AgrHRRef'		=> 4,	//LRSAnsatteRefusjon
			);

			while (($data = fgetcsv($fp, 1000, ";")) !== false && $ok == true)
			{
				$name =  trim(mb_convert_encoding($data[0], "UTF-8", "utf-16"));
				$email = trim(mb_convert_encoding($data[1], "UTF-8", "utf-16"));
				$liste = trim(mb_convert_encoding($data[2], "UTF-8", "utf-16"));
				$liste_id = $email_list[$liste];

				if($liste)
				{
					$values[$liste_id][$email] = $name;
				}
			}

			$ok = $this->update_email( $values );

			fclose($fp);

			return $ok;
		}


		function update_email($values = array())
		{
			if ($this->debug)
			{
				_debug_array(array_keys($values));
					_debug_array($values);
			}
die();

			$metadata = $GLOBALS['phpgw']->db->metadata('phpgw_helpdesk_email_out_recipient_list_temp');
//_debug_array($metadata);
			if (!$metadata)
			{
				$sql_table = <<<SQL
				CREATE TABLE public.phpgw_helpdesk_email_out_recipient_list_temp
				(
				  set_id integer NOT NULL,
				  name character varying(255) NOT NULL,
				  email character varying(255) NOT NULL,
				  active smallint DEFAULT 0,
				  public smallint,
				  user_id integer,
				  created bigint DEFAULT date_part('epoch'::text, now()),
				  modified bigint DEFAULT date_part('epoch'::text, now()),
				  CONSTRAINT phpgw_helpdesk_email_out_recipient_list_temp_pkey PRIMARY KEY (set_id, email),
				  CONSTRAINT phpgw_helpdesk_email_out_recipient_list_set_id_fkey FOREIGN KEY (set_id)
					  REFERENCES public.phpgw_helpdesk_email_out_recipient_set (id) MATCH SIMPLE
					  ON UPDATE NO ACTION ON DELETE NO ACTION,
				);
SQL;
				$GLOBALS['phpgw']->db->query($sql_table, __LINE__, __FILE__);
			}

			$GLOBALS['phpgw']->db->query('DELETE FROM phpgw_helpdesk_email_out_recipient_list_temp', __LINE__, __FILE__);


			$error = false;


			$GLOBALS['phpgw']->db->transaction_begin();

			$sql = 'INSERT INTO phpgw_helpdesk_email_out_recipient_list_temp (id, status, navn, adresse, postnummer, sted, organisasjonsnr, bankkontonr, aktiv)'
				. ' VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)';

			//remove duplicates

			if(empty($values[0]['leverandornummer']))
			{
				_debug_array($values);
				$error = true;
			}
			$vendors = array();
			foreach ($values as $entry)
			{
				$vendors[$entry['leverandornummer']] = $entry;
			}

			unset($entry);
//			_debug_array($vendors);die();

			$valueset = array();

			foreach ($vendors as $key => $entry)
			{
				$valueset[] = array
					(
					1 => array
						(
						'value' => (int)$entry['leverandornummer'],
						'type' => PDO::PARAM_INT
					),
					2 => array
						(
						'value' => $entry['status'],
						'type' => PDO::PARAM_STR
					),
					3 => array
						(
						'value' => $entry['navn'],
						'type' => PDO::PARAM_STR
					),
					4 => array
						(
						'value' => $entry['adresse'],
						'type' => PDO::PARAM_STR
					),
					5 => array
						(
						'value' => $entry['postnummer'],
						'type' => PDO::PARAM_STR
					),
					6 => array
						(
						'value' => $entry['sted'],
						'type' => PDO::PARAM_STR
					),
					7 => array
						(
						'value' => $entry['organisasjonsNr'],
						'type' => PDO::PARAM_STR
					),
					8 => array
						(
						'value' => $entry['bankkontoNr'],
						'type' => PDO::PARAM_STR
					),
					9 => array
						(
						'value' => (int)$entry['aktiv'],
						'type' => PDO::PARAM_INT
					)
				);
			}

			if($valueset && !$error)
			{
				$GLOBALS['phpgw']->db->insert($sql, $valueset, __LINE__, __FILE__);
			}

/*
            [leverandornummer] => 9906
            [status] => N
            [navn] => Bergen Vann KF (BV)
            [adresse] => Postboks 7700
            [postnummer] => 5020
            [sted] => BERGEN
            [organisasjonsNr] => 987328096
            [bankkontoNr] => 52020801786
            [aktiv] => 1
*/
//			_debug_array($valueset);die();


			$sql = "SELECT phpgw_helpdesk_email_out_recipient_list.*"
				. " FROM phpgw_helpdesk_email_out_recipient_list RIGHT OUTER JOIN phpgw_helpdesk_email_out_recipient_list_temp ON (phpgw_helpdesk_email_out_recipient_list.id = phpgw_helpdesk_email_out_recipient_list_temp.id)"
				. " WHERE phpgw_helpdesk_email_out_recipient_list.id IS NULL";

			$GLOBALS['phpgw']->db->query($sql, __LINE__, __FILE__);
			$vendors = array();
			while ($GLOBALS['phpgw']->db->next_record())
			{
				$vendors[] = array(
					1 => array(
						'value' => (int)$GLOBALS['phpgw']->db->f('id'),
						'type' => PDO::PARAM_INT
					),
					2 => array(
						'value' => $GLOBALS['phpgw']->db->f('navn'),
						'type' => PDO::PARAM_STR
					),
					3 => array(
						'value' => 1,
						'type' => PDO::PARAM_INT
					),
					4 => array(
						'value' => 6,
						'type' => PDO::PARAM_INT
					),
					5 => array(
						'value' => (int)$GLOBALS['phpgw']->db->f('aktiv'),
						'type' => PDO::PARAM_INT
					),
					6 => array(
						'value' => $GLOBALS['phpgw']->db->f('adresse'),
						'type' => PDO::PARAM_STR
					),
					7 => array(
						'value' => $GLOBALS['phpgw']->db->f('postnummer'),
						'type' => PDO::PARAM_STR
					),
					8 => array(
						'value' => $GLOBALS['phpgw']->db->f('sted'),
						'type' => PDO::PARAM_STR
					),
					9 => array(
						'value' => $GLOBALS['phpgw']->db->f('organisasjonsnr'),
						'type' => PDO::PARAM_STR
					),
					10 => array(
						'value' => $GLOBALS['phpgw']->db->f('bankkontonr'),
						'type' => PDO::PARAM_STR
					)
				);
			}
			$sql = 'INSERT INTO phpgw_helpdesk_email_out_recipient_list (id, org_name,category, owner_id, active, adresse, postnr, poststed, org_nr, konto_nr)'
				. ' VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

			if($vendors && !$error)
			{
				$GLOBALS['phpgw']->db->insert($sql, $vendors, __LINE__, __FILE__);

				$GLOBALS['phpgw']->db->query("UPDATE phpgw_helpdesk_email_out_recipient_list SET active = 0", __LINE__, __FILE__);

				$GLOBALS['phpgw']->db->query("UPDATE phpgw_helpdesk_email_out_recipient_list SET"
					. " active = 1,"
					. " org_name = phpgw_helpdesk_email_out_recipient_list_temp.navn,"
					. " adresse = phpgw_helpdesk_email_out_recipient_list_temp.adresse,"
					. " postnr = phpgw_helpdesk_email_out_recipient_list_temp.postnummer,"
					. " poststed = phpgw_helpdesk_email_out_recipient_list_temp.sted,"
					. " org_nr = phpgw_helpdesk_email_out_recipient_list_temp.organisasjonsnr"
					. " FROM phpgw_helpdesk_email_out_recipient_list_temp WHERE phpgw_helpdesk_email_out_recipient_list.id = phpgw_helpdesk_email_out_recipient_list_temp.id", __LINE__, __FILE__);
			}

			$GLOBALS['phpgw']->db->transaction_commit();
		}


		private function send_error_messages_as_email()
		{
			if (!isset($GLOBALS['phpgw_info']['server']['smtp_server']) || !$GLOBALS['phpgw_info']['server']['smtp_server'])
			{
				return;
			}

			if(empty($this->receipt['error']))
			{
				return;
			}

			$subject = 'Feil ved oppdatering av epostlister fra Outlook';
			$from = "Ikke svar<IkkeSvarLRS@Bergen.kommune.no>";
			$to = "Sigurd.Nes@bergen.kommune.no";
			$cc = "";
			$bcc = "";

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