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

		var $function_name	 = 'import_epostlister_lrs';
		var $debug			 = true;
		protected $receipt		 = array();

		function __construct()
		{
			parent::__construct();

			$this->function_name = get_class($this);
			$this->sub_location	 = lang('ticket');
			$this->function_msg	 = 'Importer predefinerte epostlister fra Outlook';

			$this->config	 = CreateObject('admin.soconfig', $GLOBALS['phpgw']->locations->get_id('property', '.invoice'));
			$this->send		 = CreateObject('phpgwapi.send');
		}

		public function execute()
		{
			$this->get_files();

			$dirname = $this->config->config_data['import']['local_path_email'];
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
				$ok								 = $this->import($file);

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


			$this->register_new_users();
			$this->send_error_messages_as_email();
		}

		protected function get_files()
		{
			$server				 = $this->config->config_data['common']['host'];
			$user				 = $this->config->config_data['common']['user'];
			$password			 = $this->config->config_data['common']['password'];
			$directory_remote	 = rtrim($this->config->config_data['import']['remote_basedir_email'], '/');
			$directory_local	 = rtrim($this->config->config_data['import']['local_path_email'], '/');

			try
			{
				$connection = ftp_connect($server);
			}
			catch (Exception $e)
			{
				$this->receipt['error'][] = array('msg' => $e->getMessage());
			}
			error_reporting(E_ALL);

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
					echo ("Change Dir Failed: $directory_remote<BR>\r\n");
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

				$file_name = 'Hele_Listen.txt';

				$file_remote = $file_name;
				$file_local	 = "{$directory_local}/{$file_name}";

				ftp_pasv($connection, true);
				if (!ftp_get($connection, $file_local, $file_remote, FTP_ASCII))
				{
					echo "Feiler på ftp_fget()<br/>";
				}

				ftp_close($connection);
			}
		}

		private function import( $file )
		{
			$ok = true;

			$fp = fopen($file, 'rb');

			$values = array();

			$email_list = array
				(
				'AgrHRLeder' => 1, //AgressoHR-LedereGruppe1
				'AgrHRSak'	 => 2, //AgressoHR-SaksbehandlereGruppe1, AgressoHR-SaksbehandlereGruppe2
				'AgrHRLonn'	 => 3, //LRSAnsatteLønn
				'AgrHRRef'	 => 3, //LRSAnsatteRefusjon
				'AgrHRSysr'	 => 4, //HR-seksjonen-Systemforvaltning
			);

			while (($data = fgetcsv($fp, 1000, ";")) !== false && $ok == true)
			{
				$alias				 = trim(mb_convert_encoding($data[0], "UTF-8", "utf-16"));
				$name				 = trim(mb_convert_encoding($data[1], "UTF-8", "utf-16"));
				$email				 = trim(mb_convert_encoding($data[2], "UTF-8", "utf-16"));
				$liste				 = trim(mb_convert_encoding($data[3], "UTF-8", "utf-16"));
				$office				 = trim(mb_convert_encoding($data[4], "UTF-8", "utf-16"));
				$department			 = trim(mb_convert_encoding($data[5], "UTF-8", "utf-16"));
				$alias_supervisor	 = trim(mb_convert_encoding($data[6], "UTF-8", "utf-16"));
				$name_supervisor	 = trim(mb_convert_encoding($data[7], "UTF-8", "utf-16"));
				$email_supervisor	 = trim(mb_convert_encoding($data[8], "UTF-8", "utf-16"));

				$liste_id = $email_list[$liste];

				if ($liste)
				{
					if ($email)
					{
						$values[$liste_id][$email] = array(
							'alias'				 => $alias,
							'name'				 => $name,
							'email'				 => $email,
							'liste'				 => $liste,
							'office'			 => $office,
							'department'		 => $department,
							'alias_supervisor'	 => $alias_supervisor,
							'name_supervisor'	 => $name_supervisor,
							'email_supervisor'	 => $email_supervisor
						);
					}
					else
					{
						$this->receipt['error'][] = array('msg' => "{$name} i liste \"{$liste}\" mangler epost");
					}
				}
			}

			fclose($fp);

			$ok = $this->update_email($values);


			return $ok;
		}

		function update_email( $values = array() )
		{
			$ok = false;
			if ($this->debug)
			{
				_debug_array(array_keys($values));
				_debug_array($values);
			}

			if (!$values)
			{
				return;
			}

			$metadata			 = $GLOBALS['phpgw']->db->metadata('phpgw_helpdesk_email_out_recipient_list_temp');
			$create_temp_table	 = false;

			if ($metadata && empty($metadata['alias']))
			{
				$GLOBALS['phpgw']->db->query('DROP TABLE public.phpgw_helpdesk_email_out_recipient_list_temp', __LINE__, __FILE__);

				$create_temp_table = true;
			}

			if (!$metadata || $create_temp_table)
			{
				$sql_table = <<<SQL

				CREATE TABLE public.phpgw_helpdesk_email_out_recipient_list_temp
				(
				  set_id integer NOT NULL,
				  alias character varying(25),
				  name character varying(255) NOT NULL,
				  email character varying(255) NOT NULL,
				  office character varying(255),
				  department character varying(255),
				  alias_supervisor character varying(25),
				  name_supervisor character varying(255),
				  email_supervisor character varying(255),
				  active smallint DEFAULT 0,
				  public smallint,
				  user_id integer,
				  created bigint DEFAULT date_part('epoch'::text, now()),
				  modified bigint DEFAULT date_part('epoch'::text, now()),
				  CONSTRAINT phpgw_helpdesk_email_out_recipient_list_temp_pkey PRIMARY KEY (set_id, email),
				  CONSTRAINT phpgw_helpdesk_email_out_recipient_list_temp_set_id_fkey FOREIGN KEY (set_id)
					  REFERENCES public.phpgw_helpdesk_email_out_recipient_set (id) MATCH SIMPLE
					  ON UPDATE NO ACTION ON DELETE NO ACTION
				)
SQL;
				$GLOBALS['phpgw']->db->query($sql_table, __LINE__, __FILE__);
			}

			$GLOBALS['phpgw']->db->query('DELETE FROM phpgw_helpdesk_email_out_recipient_list_temp', __LINE__, __FILE__);


			$error = false;

			$sql = 'INSERT INTO phpgw_helpdesk_email_out_recipient_list_temp (set_id, alias, name, email, office, department, alias_supervisor,name_supervisor, email_supervisor )'
				. ' VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)';


			$valueset = array();

			foreach ($values as $set_id => $sub_list)
			{
				foreach ($sub_list as $email => $entry)
				{
					$alias				 = $entry['alias'];
					$name				 = $entry['name'];
					$office				 = $entry['office'];
					$department			 = $entry['department'];
					$alias_supervisor	 = $entry['alias_supervisor'];
					$name_supervisor	 = $entry['name_supervisor'];
					$email_supervisor	 = $entry['email_supervisor'];

					$valueset[] = array
						(
						1	 => array
							(
							'value'	 => (int)$set_id,
							'type'	 => PDO::PARAM_INT
						),
						2	 => array
							(
							'value'	 => $alias,
							'type'	 => PDO::PARAM_STR
						),
						3	 => array
							(
							'value'	 => $name,
							'type'	 => PDO::PARAM_STR
						),
						4	 => array
							(
							'value'	 => $email,
							'type'	 => PDO::PARAM_STR
						),
						5	 => array
							(
							'value'	 => $office,
							'type'	 => PDO::PARAM_STR
						),
						6	 => array
							(
							'value'	 => $department,
							'type'	 => PDO::PARAM_STR
						),
						7	 => array
							(
							'value'	 => $alias_supervisor,
							'type'	 => PDO::PARAM_STR
						),
						8	 => array
							(
							'value'	 => $name_supervisor,
							'type'	 => PDO::PARAM_STR
						),
						9	 => array
							(
							'value'	 => $email_supervisor,
							'type'	 => PDO::PARAM_STR
						)
					);
				}
			}

			if ($valueset && !$error)
			{
				$GLOBALS['phpgw']->db->insert($sql, $valueset, __LINE__, __FILE__);
			}

			$sql = "SELECT phpgw_helpdesk_email_out_recipient_list_temp.*"
				. " FROM phpgw_helpdesk_email_out_recipient_list RIGHT"
				. " OUTER JOIN phpgw_helpdesk_email_out_recipient_list_temp"
				. " ON (phpgw_helpdesk_email_out_recipient_list.email = phpgw_helpdesk_email_out_recipient_list_temp.email AND phpgw_helpdesk_email_out_recipient_list.set_id = phpgw_helpdesk_email_out_recipient_list_temp.set_id)"
				. " WHERE phpgw_helpdesk_email_out_recipient_list.id IS NULL";

			$GLOBALS['phpgw']->db->query($sql, __LINE__, __FILE__);
			$valueset_diff = array();
			while ($GLOBALS['phpgw']->db->next_record())
			{
				$set_id				 = (int)$GLOBALS['phpgw']->db->f('set_id');
				$alias				 = $GLOBALS['phpgw']->db->f('alias');
				$name				 = $GLOBALS['phpgw']->db->f('name');
				$email				 = $GLOBALS['phpgw']->db->f('email');
				$office				 = $GLOBALS['phpgw']->db->f('office');
				$department			 = $GLOBALS['phpgw']->db->f('department');
				$alias_supervisor	 = $GLOBALS['phpgw']->db->f('alias_supervisor');
				$name_supervisor	 = $GLOBALS['phpgw']->db->f('name_supervisor');
				$email_supervisor	 = $GLOBALS['phpgw']->db->f('email_supervisor');

				$this->receipt['message'][] = array('msg' => "Ny epost: {$name} [{$email}] i liste \"{$set_id}\"");

				$valueset_diff[] = array(
					1	 => array
						(
						'value'	 => $set_id,
						'type'	 => PDO::PARAM_INT
					),
					2	 => array
						(
						'value'	 => $alias,
						'type'	 => PDO::PARAM_STR
					),
					3	 => array
						(
						'value'	 => $name,
						'type'	 => PDO::PARAM_STR
					),
					4	 => array
						(
						'value'	 => $email,
						'type'	 => PDO::PARAM_STR
					),
					5	 => array
						(
						'value'	 => $office,
						'type'	 => PDO::PARAM_STR
					),
					6	 => array
						(
						'value'	 => $department,
						'type'	 => PDO::PARAM_STR
					),
					7	 => array
						(
						'value'	 => $alias_supervisor,
						'type'	 => PDO::PARAM_STR
					),
					8	 => array
						(
						'value'	 => $name_supervisor,
						'type'	 => PDO::PARAM_STR
					),
					9	 => array
						(
						'value'	 => $email_supervisor,
						'type'	 => PDO::PARAM_STR
					),
					10	 => array
						(
						'value'	 => 1,
						'type'	 => PDO::PARAM_INT
					),
					11	 => array
						(
						'value'	 => 1,
						'type'	 => PDO::PARAM_INT
					)
				);
			}
			$sql = 'INSERT INTO phpgw_helpdesk_email_out_recipient_list (set_id, alias, name, email, office, department, alias_supervisor, name_supervisor, email_supervisor , active, public)'
				. ' VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

			if ($valueset_diff && !$error)
			{
				$GLOBALS['phpgw']->db->insert($sql, $valueset_diff, __LINE__, __FILE__);
			}

			if ($valueset && !$error)
			{
				$GLOBALS['phpgw']->db->query("UPDATE phpgw_helpdesk_email_out_recipient_list SET active = 0", __LINE__, __FILE__);

				$ok = $GLOBALS['phpgw']->db->query("UPDATE phpgw_helpdesk_email_out_recipient_list SET"
					. " active = 1,"
					. " alias = phpgw_helpdesk_email_out_recipient_list_temp.alias,"
					. " office = phpgw_helpdesk_email_out_recipient_list_temp.office,"
					. " department = phpgw_helpdesk_email_out_recipient_list_temp.department,"
					. " alias_supervisor = phpgw_helpdesk_email_out_recipient_list_temp.alias_supervisor,"
					. " name_supervisor = phpgw_helpdesk_email_out_recipient_list_temp.name_supervisor,"
					. " email_supervisor = phpgw_helpdesk_email_out_recipient_list_temp.email_supervisor"
					. " FROM phpgw_helpdesk_email_out_recipient_list_temp"
					. " WHERE phpgw_helpdesk_email_out_recipient_list.set_id = phpgw_helpdesk_email_out_recipient_list_temp.set_id"
					. " AND phpgw_helpdesk_email_out_recipient_list.email = phpgw_helpdesk_email_out_recipient_list_temp.email", __LINE__, __FILE__);
			}


			if ($ok || !$error)
			{
				return true;
			}
		}

		private function send_error_messages_as_email()
		{
			if (!isset($GLOBALS['phpgw_info']['server']['smtp_server']) || !$GLOBALS['phpgw_info']['server']['smtp_server'])
			{
				return;
			}

			if (empty($this->receipt['error']))
			{
				return;
			}

			$subject = 'Feil ved oppdatering av epostlister fra Outlook';
			$from	 = "Ikke svar<IkkeSvarLRS@Bergen.kommune.no>";
			$to		 = "Sigurd.Nes@bergen.kommune.no";
			$cc		 = "";
			$bcc	 = "";

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

		private function register_new_users()
		{
			$sql = "SELECT DISTINCT phpgw_helpdesk_email_out_recipient_list.*"
				. " FROM phpgw_accounts RIGHT"
				. " OUTER JOIN phpgw_helpdesk_email_out_recipient_list"
				. " ON phpgw_accounts.account_lid = phpgw_helpdesk_email_out_recipient_list.alias"
				. " WHERE phpgw_accounts.account_id IS NULL"
				. " AND phpgw_helpdesk_email_out_recipient_list.active = 1";

			$GLOBALS['phpgw']->db->query($sql, __LINE__, __FILE__);
			$values = array();
			while ($GLOBALS['phpgw']->db->next_record())
			{
				$alias = $GLOBALS['phpgw']->db->f('alias');

				$values[$alias] = array(
					'alias'	 => $alias,
					'name'	 => $GLOBALS['phpgw']->db->f('name'),
					'email'	 => $GLOBALS['phpgw']->db->f('email')
				);
			}

			$sql = "SELECT DISTINCT phpgw_helpdesk_email_out_recipient_list.*"
				. " FROM phpgw_accounts RIGHT"
				. " OUTER JOIN phpgw_helpdesk_email_out_recipient_list"
				. " ON phpgw_accounts.account_lid = phpgw_helpdesk_email_out_recipient_list.alias_supervisor"
				. " WHERE phpgw_accounts.account_id IS NULL"
				. " AND phpgw_helpdesk_email_out_recipient_list.active = 1";

			$GLOBALS['phpgw']->db->query($sql, __LINE__, __FILE__);

			while ($GLOBALS['phpgw']->db->next_record())
			{
				$alias_supervisor			 = $GLOBALS['phpgw']->db->f('alias_supervisor');
				$values[$alias_supervisor]	 = array(
					'alias'		 => $alias_supervisor,
					'name'		 => $GLOBALS['phpgw']->db->f('name_supervisor'),
					'email'		 => $GLOBALS['phpgw']->db->f('email_supervisor'),
					'supervisor' => true
				);
			}

			$helpdesk_account = new helpdesk_account();
			$helpdesk_account->register_accounts($values);
		}
	}
	phpgw::import_class('helpdesk.hook_helper');

	class helpdesk_account extends helpdesk_hook_helper
	{

		public function __construct()
		{
			$this->config = CreateObject('phpgwapi.config', 'helpdesk')->read();
		}

		public function register_accounts( $values )
		{
			foreach ($values as $account_lid => $entry)
			{
				if (!$GLOBALS['phpgw']->accounts->exists($account_lid))
				{

					$autocreate_user = isset($this->config['autocreate_user']) && $this->config['autocreate_user'] ? $this->config['autocreate_user'] : 0;

					if ($autocreate_user)
					{
						$fellesdata_user = frontend_bofellesdata::get_instance()->get_user($account_lid);
						if ($fellesdata_user && $fellesdata_user['firstname'])
						{
							// Read default assign-to-group from config
							$default_group_id	 = isset($this->config['autocreate_default_group']) && $this->config['autocreate_default_group'] ? $this->config['autocreate_default_group'] : 0;
							$group_lid			 = $GLOBALS['phpgw']->accounts->id2lid($default_group_id);
							$group_lid			 = $group_lid ? $group_lid : 'frontend_delegates';

							$password	 = 'PEre' . mt_rand(100, mt_getrandmax()) . '&';
							$account_id	 = self::create_phpgw_account($account_lid, $fellesdata_user['firstname'], $fellesdata_user['lastname'], $password, $group_lid);
						}
					}
				}
			}
		}
	}