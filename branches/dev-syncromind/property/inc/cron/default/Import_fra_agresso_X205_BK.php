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
	 * @version $Id: Import_fra_agresso_X205.php 14726 2016-02-11 20:07:07Z sigurdne $
	 */
	/**
	 * Filteret benytter format X205 for integrasjon mellom Contempus Invoice og ClockWork Logistics.
	 * Formatet sender innskannede fakturaer fra Contempus til ClockWork - og Portico Estate
	 * @package property
	 */
	include_class('property', 'cron_parent', 'inc/cron/');

	class Import_fra_agresso_X205_BK extends property_cron_parent
	{

		protected $auto_tax = false;
		protected $mvakode = 0;
		protected $kildeid = 1;
		protected $splitt = 0;
		protected $soXport;
		protected $invoice;
		protected $default_kostra_id = 9999; //dummy
		protected $debug = false;
		protected $skip_import = false;
		protected $skip_email = false;
		protected $export;
		protected $skip_update_voucher_id = false;
		protected $order_id;

		function __construct()
		{
			parent::__construct();

			$this->function_name = get_class($this);
			$this->sub_location = lang('invoice');
			$this->function_msg = 'Importer faktura fra Agresso';

			$this->soXport = CreateObject('property.soXport');
			$this->invoice = CreateObject('property.soinvoice');
			$this->responsible = CreateObject('property.soresponsible');
			$this->bocommon = CreateObject('property.bocommon');

			$this->dateformat = $this->db->date_format();
			$this->datetimeformat = $this->db->datetime_format();
			$this->config = CreateObject('admin.soconfig', $GLOBALS['phpgw']->locations->get_id('property', '.invoice'));
		}

		public function execute()
		{
			$this->get_files();
			$dirname = $this->config->config_data['import']['local_path'];
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
					if ($file->isDot() || !$file->isFile() || !$file->isReadable() || strcasecmp(end(explode(".", $file->getPathname())), 'xml') != 0)
					{
						continue;
					}

					if(preg_match('/^X205/i', (string)$file ))
					{
						$file_list[] = (string)"{$dirname}/{$file}";
					}
				}
			}

			if (is_writable("{$dirname}/arkiv"))
			{
				foreach ($file_list as $file)
				{
					$this->skip_update_voucher_id = false;
					$this->db->transaction_begin();
					$bilagsnr = $this->import($file);
					if ($this->debug)
					{
						_debug_array("Behandler fil: {$file}");
						_debug_array("Bilagsnr: {$bilagsnr}");
					}

					if ($bilagsnr)
					{
						// move file
						$_file = basename($file);
						$movefrom = "{$dirname}/{$_file}";
						$moveto = "{$dirname}/arkiv/{$_file}";

						if (is_file($moveto))
						{
							@unlink($moveto);//in case of duplicates
						}

						$ok = @rename($movefrom, $moveto);
						if (!$ok) // Should never happen.
						{
							$this->db->transaction_abort();
							$this->receipt['error'][] = array('msg' => "Kunne ikke flytte importfil til arkiv, Bilag {$bilagsnr} er slettet");
						}
						else
						{
							$this->db->transaction_commit();
							phpgwapi_cache::system_clear('property', "budget_order_{$this->order_id}");
						}
					}
					else
					{
						$this->db->transaction_abort();
					}
				}
			}
			else
			{
				$this->receipt['error'][] = array('msg' => "Arkiv katalog '{$dirname}/arkiv/' ikke er ikke skrivbar - kontakt systemadminstrator for å korrigere");
			}

		}


		protected function get_files()
		{
			$method = $this->config->config_data['common']['method'];
			if($method == 'local')
			{
				return;
			}

			$server = $this->config->config_data['common']['host'];
			$user = $this->config->config_data['common']['user'];
			$password = $this->config->config_data['common']['password'];
			$directory_remote = rtrim($this->config->config_data['import']['remote_basedir'], '/');
			$directory_local = rtrim($this->config->config_data['import']['local_path'], '/');


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
						_debug_array('preg_match("/xml$/i",' . $file_name . ': ' . preg_match('/xml$/i', $file_name));
					}

					if(preg_match('/^X205/i', (string)$file_name ))
					{
						$file_remote = $file_name;
						$file_local = "{$directory_local}/{$file_name}";

						if (ftp_get($connection, $file_local, $file_remote, FTP_ASCII))
						{
							ftp_delete( $connection , "arkiv/{$file_remote}");

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

		protected function check_storage_dir($files_path)
		{
			if (is_dir($files_path) && is_writable($files_path) && is_readable($files_path)	)
			{
				return true;
			}
		}

		protected function create_storage_dir($files_path)
		{
			$dirMode = 0777;
			if (!mkdir($files_path, $dirMode, true))
			{
				// failed to create the directory
				throw new Exception(sprintf('Failed to create file storage "%s".', $files_path));
			}

			chmod($files_path, $dirMode);
			return true;
		}

		protected function import( $file )
		{
			$buffer = array();
			$bilagsnr = false;

			$xml = new SimpleXMLElement(file_get_contents( $file ));

			$_data = array(
				'KEY' => $xml->xpath('//INVOICES/INVOICE/INVOICEHEADER/KEY'),
				'ATTACHMENT' => $xml->xpath('//INVOICES/INVOICE/INVOICEHEADER/ATTACHMENT'),
				'AMOUNT' => $xml->xpath('//INVOICES/INVOICE/INVOICEHEADER/AMOUNT'),
				'CLIENT.CODE' => $xml->xpath('//INVOICES/INVOICE/INVOICEHEADER/CLIENT.CODE'),
				'CURRENCY.CURRENCYID' => $xml->xpath('//INVOICES/INVOICE/INVOICEHEADER/CURRENCY.CURRENCYID'),
				'EXCHANGERATE' => $xml->xpath('//INVOICES/INVOICE/INVOICEHEADER/EXCHANGERATE'),
				'INVOICEDATE' => $xml->xpath('//INVOICES/INVOICE/INVOICEHEADER/INVOICEDATE'),
				'LOCALAMOUNT' => $xml->xpath('//INVOICES/INVOICE/INVOICEHEADER/LOCALAMOUNT'),
				'LOCALVATAMOUNT' => $xml->xpath('//INVOICES/INVOICE/INVOICEHEADER/LOCALVATAMOUNT'),
				'MATURITY' => $xml->xpath('//INVOICES/INVOICE/INVOICEHEADER/MATURITY'),
				'PAYAMOUNT' => $xml->xpath('//INVOICES/INVOICE/INVOICEHEADER/PAYAMOUNT'),
				'POSTATUSUPDATED' => $xml->xpath('//INVOICES/INVOICE/INVOICEHEADER/POSTATUSUPDATED'),
				'PURCHASEORDERNO' => $xml->xpath('//INVOICES/INVOICE/INVOICEHEADER/PURCHASEORDERNO'),
				'SUPPLIERBANKGIRO' => $xml->xpath('//INVOICES/INVOICE/INVOICEHEADER/SUPPLIERBANKGIRO'),
				'SUPPLIER.CODE' => $xml->xpath('//INVOICES/INVOICE/INVOICEHEADER/SUPPLIER.CODE'),
				'SUPPLIERREF' => $xml->xpath('//INVOICES/INVOICE/INVOICEHEADER/SUPPLIERREF'),
				'VATAMOUNT' => $xml->xpath('//INVOICES/INVOICE/INVOICEHEADER/VATAMOUNT')
			);

			foreach ($_data as $key => & $__data)
			{
				$__data = (string) $__data[0];
			}

			set_time_limit(300);

			if (!empty($_data['KEY']))
			{
				$regtid = date($this->datetimeformat);

				$i = 0;
				if(!empty($_data['ATTACHMENT']))
				{
					$attachment = base64_decode($_data['ATTACHMENT']);
//	_debug_array($_data);
					$directory_local = rtrim($this->config->config_data['import']['local_path'], '/');
					$directory_attachment = "{$directory_local}/attachment/{$_data['KEY']}";
					if(!$this->check_storage_dir($directory_attachment))
					{
						$this->create_storage_dir($directory_attachment);
					}

					$tmpfname = tempnam('', 'attachment');
//	_debug_array($tmpfname);

					$handle = fopen($tmpfname, "w");
					fwrite($handle, $attachment);
					fclose($handle);
					$zip = new ZipArchive;
					if ($zip->open($tmpfname) === true)
					{
						for($j = 0; $j < $zip->numFiles; $j++)
						{
							$filename = $zip->getNameIndex($j);
//	_debug_array("{$directory_attachment}/{$filename}");
							copy("zip://".$tmpfname."#".$filename, "{$directory_attachment}/{$filename}");
						}
						$zip->close();
					}
					unlink($tmpfname);
				}

				$_data['ARRIVAL']; // => 2009.05.28
				$_data['CLIENT.CODE']; // => 14
				$_data['EXCHANGERATE']; // => 1
				$_data['LOCALAMOUNT']; // => 312500
				$_data['LOCALVATAMOUNT']; // => 62500
				$_data['PAYAMOUNT']; // => 0
				$_data['POSTATUSUPDATED']; // => 0
				$_data['PURCHASEORDERSTATUS.CODE']; // => WaitForMatch
				$_data['SUPPLIER.BANKGIRO']; // => 70580621110
				$_data['VATAMOUNT']; // => 62500

				$bilagsnr_ut = isset($_data['VOUCHERID']) ? $_data['VOUCHERID'] : ''; // FIXME: innkommende bilagsnummer?

				$fakturanr = $_data['SUPPLIERREF'];
				$fakturadato = date($this->dateformat, strtotime(str_replace('.', '-', $_data['INVOICEDATE'])));
				$forfallsdato = date($this->dateformat, strtotime(str_replace('.', '-', $_data['MATURITY'])));
				$periode = '';
				$belop = $_data['AMOUNT'] / 100;

				if (!abs($belop) > 0)
				{
					$this->receipt['message'][] = array('msg' => "Beløpet er 0 for Skanningreferanse: {$_data['SCANNINGNO']}, FakturaNr: {$fakturanr}, fil: {$file}");
					$belop = (float)0.0001; // imported as 0.00
				}

				if ($belop < 0)
				{
					$buffer[$i]['artid'] = 2;
				}
				else
				{
					$buffer[$i]['artid'] = 1;
				}

				$kidnr = $_data['KIDNO'];
				$_order_id = $_data['PURCHASEORDERNO'];
				$merknad = '';
				$line_text = '';
				$order_id = '';
				$buffer[$i]['project_id'] = '';

				$order_info = $this->get_order_info($_order_id); // henter default verdier selv om  $_order_id ikke er gyldig.

				if (!$_order_id)
				{
					$merknad = 'Mangler bestillingsnummer';
					$this->receipt['error'][] = array('msg' => $merknad);
				}
				else if (!ctype_digit($_order_id))
				{
					$merknad = "bestillingsnummeret er på feil format: {$_order_id}";
					$this->receipt['error'][] = array('msg' => $merknad);
				}
				else if (!$order_info['order_exist'])
				{
					$merknad = 'bestillingsnummeret ikke gyldig: ' . $_order_id;
					$this->receipt['error'][] = array('msg' => "{$merknad}, fil: {$file}");
				}
				else
				{
					$buffer[$i]['project_id'] = $this->soXport->get_project($_order_id);
					$order_id = $_order_id;
					$this->order_id = $order_id;
				}

				$buffer[$i]['external_voucher_id'] = $_data['KEY']; // => 1400050146
				$buffer[$i]['pmwrkord_code'] = $order_id;
				$buffer[$i]['fakturanr'] = $fakturanr;
				$buffer[$i]['periode'] = $periode;
				$buffer[$i]['forfallsdato'] = $forfallsdato;
				$buffer[$i]['fakturadato'] = $fakturadato;
				$buffer[$i]['belop'] = $belop;
				$buffer[$i]['currency'] = $_data['CURRENCY.CURRENCYID'];
				$buffer[$i]['godkjentbelop'] = $belop;

				$buffer[$i]['kidnr'] = $kidnr;
				$buffer[$i]['bilagsnr_ut'] = $bilagsnr_ut;
				$buffer[$i]['referanse'] = "ordre: {$order_id}";

				$buffer[$i]['dima'] = $order_info['dima'];
				$buffer[$i]['dimb'] = $order_info['dimb'];
				$buffer[$i]['dime'] = $order_info['dime'];
				$buffer[$i]['loc1'] = $order_info['loc1'];
				$buffer[$i]['line_text'] = $order_info['title'];

				$buffer[$i]['mvakode'] = $order_info['tax_code'];

				if ($buffer[$i]['loc1'] && !$buffer[$i]['mvakode'])
				{
					$mvakode = $this->soXport->auto_tax($buffer[$i]['loc1']);

					if ($mvakode)
					{
						$buffer[$i]['mvakode'] = $mvakode;
					}
				}

				$update_voucher = false;
				$receive_order_performed = false;
				$sql = "SELECT bilagsnr, bilagsnr_ut FROM fm_ecobilag WHERE external_voucher_id = '{$_data['KEY']}'";
				$this->db->query($sql, __LINE__, __FILE__);
				if ($this->db->next_record())
				{
					$this->skip_update_voucher_id = true;
					$update_voucher = true;
					$receive_order_performed = true;
					$bilagsnr = $this->db->f('bilagsnr');
					$buffer[$i]['bilagsnr'] = $bilagsnr;
					$this->receipt['message'][] = array('msg' => "Oppdatert med nye data i arbeidsregister: ordre = {$_order_id}, bilag = {$_data['KEY']}");
				}

				$sql = "SELECT bilagsnr FROM fm_ecobilagoverf WHERE external_voucher_id = '{$_data['KEY']}'";
				$this->db->query($sql, __LINE__, __FILE__);
				if ($this->db->next_record())
				{
					$this->skip_update_voucher_id = true;
					$update_voucher = true;
					$receive_order_performed = true;
					$bilagsnr = $this->db->f('bilagsnr');

					$buffer[$i]['bilagsnr'] = $bilagsnr;

					$receipt = $this->rollback($bilagsnr);

					if (isset($receipt['message']))
					{
						$this->receipt['message'][] = array('msg' => "Bilag rullet tilbake fra historikk : {$bilagsnr}");
					}
					else
					{
						$this->receipt['error'][] = array('msg' => "Bilag ikke rullet tilbake fra historikk : {$bilagsnr}, Skanningreferanse: {$_data['KEY']}, FakturaNr: {$fakturanr}");
					}
				}

				$vendor_id = $_data['SUPPLIER.CODE'];

				$sql = 'SELECT id FROM fm_vendor WHERE id = ' . (int)$vendor_id;
				$this->db->query($sql, __LINE__, __FILE__);

				if (!$_data['SUPPLIER.CODE'])
				{
					$this->receipt['error'][] = array('msg' => "LeverandørId ikke angitt for faktura: {$_data['SCANNINGNO']}");
					$this->skip_import = true;
				}
				else if (!$this->db->next_record())
				{
					$this->receipt['error'][] = array('msg' => "Importeres ikke: Ikke gyldig LeverandørId: {$_data['SUPPLIER.CODE']}, Skanningreferanse: {$_data['SCANNINGNO']}, FakturaNr: {$fakturanr}, fil: {$file}");
					$this->skip_import = true;

				}
				else if ($order_info['vendor_id'] != $vendor_id)
				{
					$this->receipt['message'][] = array('msg' => 'Ikke samsvar med leverandør på bestilling og mottatt faktura');
				}

				$buffer[$i]['kostra_id'] = $order_info['service_id'];

				$buffer[$i]['merknad'] = $merknad;
				$buffer[$i]['splitt'] = $this->splitt;
				$buffer[$i]['kildeid'] = $this->kildeid;
				$buffer[$i]['spbudact_code'] = $order_info['spbudact_code'];
				$buffer[$i]['typeid'] = isset($invoice_common['type']) && $invoice_common['type'] ? $invoice_common['type'] : 1;
				$buffer[$i]['regtid'] = $regtid;

				$buffer[$i]['spvend_code'] = $vendor_id;

				if (isset($order_info['janitor']) && $order_info['janitor'])
				{
					$buffer[$i]['oppsynsmannid'] = $order_info['janitor'];
				}

				if (isset($order_info['supervisor']) && $order_info['supervisor'])
				{
					$buffer[$i]['saksbehandlerid'] = $order_info['supervisor'];
				}

				if (isset($order_info['budget_responsible']) && $order_info['budget_responsible'])
				{
					$buffer[$i]['budsjettansvarligid'] = $order_info['budget_responsible'];
				}
			}

//_debug_array($buffer);
//_debug_array($this->receipt);
//_debug_array($order_info['toarray']);
			if ($this->debug && $this->skip_import)
			{
				_debug_array("Skip import - file: {$file}");
			}

			if ($this->skip_import)
			{
				$this->skip_import = false;
				return false;
			}
			else
			{
				if ($update_voucher && $bilagsnr)
				{
					$this->db->query("DELETE FROM fm_ecobilag WHERE external_voucher_id = '{$_data['KEY']}'", __LINE__, __FILE__);
				}

				if (!$bilagsnr)
				{
					$bilagsnr = $this->invoice->next_bilagsnr();

					foreach ($buffer as &$entry)
					{
						$entry['bilagsnr'] = $bilagsnr;
					}
				}

				$GLOBALS['phpgw']->db->Exception_On_Error = true;

				try
				{
					$bilagsnr = $this->import_end_file($buffer);

					if(!$receive_order_performed && $this->config->config_data['export']['auto_receive_order'])
					{
						$received_amount = $this->get_total_received((int)$order_id);

						$received_amount = $received_amount * 0.8; //shave off 25 % tax from the top.

						$order_type = $this->bocommon->socommon->get_order_type($order_id);

						switch ($order_type)
						{
							case 'workorder':
								$received = createObject('property.boworkorder')->receive_order( (int)$order_id, $received_amount );
								break;
							case 'ticket':
								$received = createObject('property.botts')->receive_order( (int)$order_id, $received_amount );
								break;
							default:
								throw new Exception('Order type not supported');
						}
					}

				}
				catch (Exception $e)
				{
					if ($e)
					{
						$GLOBALS['phpgw']->log->error(array(
							'text' => 'import_fra_agresso_X205::import() : error when trying to execute import_end_file(): %1',
							'p1' => $e->getMessage(),
							'p2' => '',
							'line' => __LINE__,
							'file' => __FILE__
						));
						$this->receipt['error'][] = array('msg' => $e->getMessage());
					}
					return false;
				}

				$GLOBALS['phpgw']->db->Exception_On_Error = false;
				return $bilagsnr;
			}
		}

		function get_total_received( $order_id )
		{
			$amount = 0;
			$sql = "SELECT sum(godkjentbelop) AS amount FROM fm_ecobilag WHERE pmwrkord_code = {$order_id}";
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			$amount += (float)$this->db->f('amount');
			$sql = "SELECT sum(godkjentbelop) AS amount FROM fm_ecobilagoverf WHERE pmwrkord_code = {$order_id}";
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			$amount += (float)$this->db->f('amount');
			return $amount;
		}

		function get_order_info( $order_id = 0 )
		{
			$order_id = (int)$order_id;

			$this->db->query("SELECT type FROM fm_orders WHERE id = $order_id", __LINE__, __FILE__);
			$this->db->next_record();
			$order_type = $this->db->f('type');

			$order_info = array();
			$toarray = array();

			switch ($order_type)
			{
				case 'ticket':
					$sql = "SELECT fm_tts_tickets.location_code,"
						. " fm_tts_tickets.vendor_id,"
						. " fm_tts_tickets.b_account_id as account_id,"
						. " fm_tts_tickets.ecodimb,"
						. " fm_tts_tickets.service_id,"
						. " fm_tts_tickets.tax_code,"
						. " fm_tts_tickets.cat_id as category,"
						. " fm_tts_tickets.ordered_by as user_id,"
						. " fm_tts_tickets.subject as title"
						. " FROM fm_tts_tickets"
						. " WHERE fm_tts_tickets.order_id = {$order_id}";

					break;
				case 'workorder':
					$sql = "SELECT fm_workorder.location_code,"
						. " fm_workorder.vendor_id,"
						. " fm_workorder.account_id,"
						. " fm_project.ecodimb as project_ecodimb,"
						. " fm_workorder.ecodimb,"
						. " fm_workorder.category,"
						. " fm_workorder.user_id,"
						. " fm_workorder.service_id,"
						. " fm_workorder.tax_code,"
						. " fm_workorder.title"
						. " FROM fm_workorder {$this->join} fm_project ON fm_workorder.project_id = fm_project.id"
						. " WHERE fm_workorder.id = {$order_id}";
					break;

				default:
					throw new Exception("{$order_type} not supported");
					break;
			}

			$this->db->query($sql, __LINE__, __FILE__);
			if ($this->db->next_record())
			{
				$order_info['order_exist'] = true;
			}
			if ($this->db->f('location_code'))
			{
				$parts = explode('-', $this->db->f('location_code'));
				$order_info['dima'] = implode('', $parts);
				$order_info['loc1'] = $parts[0];
			}

			$order_info['vendor_id'] = $this->db->f('vendor_id');
			$order_info['spbudact_code'] = $this->db->f('account_id');
			$ecodimb = $this->db->f('ecodimb');
			$order_info['dimb'] = $ecodimb ? $ecodimb : $this->db->f('project_ecodimb');
			$order_info['dime'] = $this->db->f('category');
			$order_info['title'] = $this->db->f('title', true);
			$order_info['service_id'] = $this->db->f('service_id');
			$order_info['tax_code'] = $this->db->f('tax_code');

			$janitor_user_id = $this->db->f('user_id');
			$order_info['janitor'] = $GLOBALS['phpgw']->accounts->get($janitor_user_id)->lid;
			$supervisor_user_id = $this->invoice->get_default_dimb_role_user(2, $order_info['dimb']);
			if ($supervisor_user_id)
			{
				$order_info['supervisor'] = $GLOBALS['phpgw']->accounts->get($supervisor_user_id)->lid;
			}

			$budget_responsible_user_id = $this->invoice->get_default_dimb_role_user(3, $order_info['dimb']);
			if ($budget_responsible_user_id)
			{
				$order_info['budget_responsible'] = $GLOBALS['phpgw']->accounts->get($budget_responsible_user_id)->lid;
			}

			if (!$order_info['budget_responsible'])
			{
				$order_info['budget_responsible'] = isset($this->config->config_data['import']['budget_responsible']) && $this->config->config_data['import']['budget_responsible'] ? $this->config->config_data['import']['budget_responsible'] : 'karhal';
			}

			$order_info['toarray'] = $toarray;
			return $order_info;
		}

		function import_end_file( $buffer )
		{
			try
			{
				$num = $this->soXport->add($buffer, $this->skip_update_voucher_id);
			}
			catch (Exception $e)
			{
				throw $e;
			}

			if ($this->debug)
			{
				_debug_array("import_end_file() ");
				echo 'buffer: ';
				_debug_array($buffer);
				_debug_array("num: {$num}");
			}

			if ($num > 0)
			{
				$this->receipt['message'][] = array('msg' => "Importert {$num} poster til bilag {$buffer[0]['bilagsnr']}, KEY: {$buffer[0]['external_voucher_id']}");
				return $buffer[0]['bilagsnr'];
			}
			return false;
		}
		/**
		 * rollback er initiert fra import-filteret
		 * @param integer $rollback_internal_voucher
		 * @return array Receipt
		 */
		public function rollback( $rollback_internal_voucher )
		{
			$voucher = $this->select_invoice_rollback($rollback_internal_voucher);

			if ($this->db->get_transaction())
			{
				$this->global_lock = true;
			}
			else
			{
				$this->global_lock = false;
				$this->db->transaction_begin();
			}

			foreach ($voucher as $line)
			{
				//$this->bilag_update_overf($line);

				if ($line['pmwrkord_code'])
				{
					$orders_affected[$line['pmwrkord_code']] = true;

					$Belop = sprintf("%01.2f", $line['ordrebelop']) * 100;

					if ($line['dimd'] % 2 == 0)
					{
						$actual_cost_field = 'act_mtrl_cost';
					}
					else
					{
						$actual_cost_field = 'act_vendor_cost';
					}

					$operator = '-';

					$this->soXport->correct_actual_cost($line['pmwrkord_code'], $Belop, $actual_cost_field, $operator);
				}

				//Slett fra avviks tabell
				//	$this->soXport->delete_avvik($line['bilagsnr']);
				//Slett fra arkiv
				$this->soXport->delete_invoice($line['bilagsnr']);
			}

			$antall = count($voucher);
			if ($antall > 0)
			{
				if ( $rollback_internal_voucher)
				{
					if (!$this->global_lock)
					{
						$this->db->transaction_commit();
					}

					$receipt['message'][] = array('msg' => $antall . ' ' . lang('bilag/underbilag rullet tilbake'));
				}
				else
				{
					$this->db->transaction_abort();
					$receipt['error'][] = array('msg' => 'Noe gikk galt!');
				}
			}
			else
			{
				if (!$this->global_lock)
				{
					$this->db->transaction_commit();
				}

				$receipt['error'][] = array('msg' => lang('Sorry - No hits'));
			}
			return $receipt;
		}

		protected function select_invoice_rollback( $rollback_internal_voucher )
		{
			if ($rollback_internal_voucher)
			{
				$rollback_internal_voucher = (int)$rollback_internal_voucher;
				$sql = "SELECT * FROM fm_ecobilagoverf WHERE bilagsnr = {$rollback_internal_voucher} AND manual_record IS NULL";
			}
			else
			{
				return array();
			}
			$this->db->query($sql, __LINE__, __FILE__);

			$invoice_rollback = array();
			while ($this->db->next_record())
			{
				$invoice_rollback[] = array	(
					'id' => $this->db->f('id'),
					'bilagsnr' => $this->db->f('bilagsnr'),
					'bilagsnr_ut' => $this->db->f('bilagsnr_ut'),
					'kidnr' => $this->db->f('kidnr'),
					'typeid' => $this->db->f('typeid'),
					'kildeid' => $this->db->f('kildeid'),
					'pmwrkord_code' => $this->db->f('pmwrkord_code'),
					'belop' => $this->db->f('belop'),
					'fakturadato' => $this->db->f('fakturadato'),
					'periode' => $this->db->f('periode'),
					'periodization' => $this->db->f('periodization'),
					'periodization_start' => $this->db->f('periodization_start'),
					'forfallsdato' => $this->db->f('forfallsdato'),
					'fakturanr' => $this->db->f('fakturanr'),
					'spbudact_code' => $this->db->f('spbudact_code'),
					'regtid' => $this->db->f('regtid'),
					'artid' => $this->db->f('artid'),
					'godkjentbelop' => (int)$this->db->f('godkjentbelop') == 0 ? $this->db->f('belop') : $this->db->f('godkjentbelop'), //restore original amount
					'spvend_code' => $this->db->f('spvend_code'),
					'dima' => $this->db->f('dima'),
					'loc1' => $this->db->f('loc1'),
					'dimb' => $this->db->f('dimb'),
					'mvakode' => $this->db->f('mvakode'),
					'dimd' => $this->db->f('dimd'),
					'dime' => $this->db->f('dime'),
					'project_id' => $this->db->f('project_id'),
					'kostra_id' => $this->db->f('kostra_id'),
					'item_type' => $this->db->f('item_type'),
					'item_id' => $this->db->f('item_id'),
					'oppsynsmannid' => $this->db->f('oppsynsmannid'),
					'saksbehandlerid' => $this->db->f('saksbehandlerid'),
					'budsjettansvarligid' => $this->db->f('budsjettansvarligid'),
					'oppsynsigndato' => $this->db->f('oppsynsigndato'),
					'saksigndato' => $this->db->f('saksigndato'),
		//			'budsjettsigndato'		=> $this->db->f('budsjettsigndato'), // må anvises på nytt etter tilbakerulling
					'merknad' => $this->db->f('merknad', true),
					'line_text' => $this->db->f('line_text', true),
					'splitt' => $this->db->f('splitt'),
					'ordrebelop' => $this->db->f('ordrebelop'),
		//			'utbetalingid'			=> $this->db->f('utbetalingid'),
		//			'utbetalingsigndato'	=> $this->db->f('utbetalingsigndato'),
					'external_ref' => $this->db->f('external_ref'),
					'external_voucher_id' => $this->db->f('external_voucher_id'),
					'currency' => $this->db->f('currency'),
					'process_log' => $this->db->f('process_log', true),
					'process_code' => $this->db->f('process_code'),
				);
			}

			return $invoice_rollback;
		}

	}