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
 	* @version $Id: Import_fra_agresso_X205.php 11595 2014-01-14 19:16:59Z sigurdne $
	*/

	/**
	 * Filteret benytter format X205 for integrasjon mellom Contempus Invoice og ClockWork Logistics.
	 * Formatet sender innskannede fakturaer fra Contempus til ClockWork - og Portico Estate
	 * @package property
	 */

	include_class('property', 'cron_parent', 'inc/cron/');

	class  Import_fra_agresso_X205 extends property_cron_parent
	{
		protected $auto_tax = true;
		protected $mvakode=0;
		protected $kildeid=1;
		protected $splitt=0;
		protected $soXport;
		protected $invoice;
		protected $default_kostra_id = 9999; //dummy
		protected $debug = false;
		protected $skip_import = false;
		protected $skip_email = false;
		protected $export;
		protected $skip_update_voucher_id = false;

		function __construct()
		{
			parent::__construct();

			$this->function_name = get_class($this);
			$this->sub_location = lang('invoice');
			$this->function_msg	= 'Importer faktura fra Agresso';

			$this->soXport			= CreateObject('property.soXport');
			$this->invoice			= CreateObject('property.soinvoice');
			$this->responsible		= CreateObject('property.soresponsible');
			$this->bocommon			= CreateObject('property.bocommon');

			$this->dateformat		= $this->db->date_format();
			$this->datetimeformat	= $this->db->datetime_format();
			$this->config			= CreateObject('admin.soconfig',$GLOBALS['phpgw']->locations->get_id('property', '.invoice'));
			$this->send				= CreateObject('phpgwapi.send');

			include (PHPGW_SERVER_ROOT . "/property/inc/export/{$GLOBALS['phpgw_info']['user']['domain']}/Agresso_X114");
			$this->export		= new export_conv;

			$now = time() + (int) $GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'] * 3600;

			$now_hour = date('G',$now );
			$now_day = date('N',$now );		

			if(($now_hour < 6 || $now_hour > 17) || $now_day > 5)
			{

				$this->skip_email = true;
			}

		}

		public function execute()
		{
			if(isset($this->config->config_data['import']['check_archive']) && $this->config->config_data['import']['check_archive'])
			{
				$this->check_archive();
			}

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
						|| strcasecmp( end( explode( ".", $file->getPathname() ) ), 'xml' ) != 0 )
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
					$this->skip_update_voucher_id = false;
					$this->db->transaction_begin();
					$bilagsnr = $this->import($file);
					if($this->debug)
					{
						_debug_array("Behandler fil: {$file}");
						_debug_array("Bilagsnr: {$bilagsnr}");
					}

					if ($bilagsnr)
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
							$this->db->transaction_abort();
							$this->receipt['error'][] = array('msg' => "Kunne ikke flytte importfil til arkiv, Bilag {$bilagsnr} er slettet");
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
			}
			else
			{
				$this->receipt['error'][] = array('msg' => "Arkiv katalog '{$dirname}/archive/' ikke er ikke skrivbar - kontakt systemadminstrator for å korrigere");
			}

			$this->remind();
		}


		protected function check_archive()
		{
			$dirname = $this->config->config_data['import']['local_path'];

			if ( preg_match('/\./', $dirname) 
			 || !is_dir($dirname) )
			{
				return array();
			}

			$archive = "{$dirname}/archive";
			$file_list = array();
			$dir = new DirectoryIterator($archive); 
			if ( is_object($dir) )
			{
				foreach ( $dir as $file )
				{
					if ( $file->isDot()
						|| !$file->isFile()
						|| !$file->isReadable()
						|| strcasecmp( end( explode( ".", $file->getPathname() ) ), 'xml' ) != 0 )
 					{
						continue;
					}

					$file_list[] = (string) $file;
				}
			}

			foreach($file_list as $file)
			{
				$file_parts = explode('_', basename($file, '.xml'));
				$external_voucher_id = $file_parts[2];

				$duplicate = false;
				$sql = "SELECT bilagsnr FROM fm_ecobilag WHERE external_voucher_id = '{$external_voucher_id}'";
				$this->db->query($sql,__LINE__,__FILE__);
				if($this->db->next_record())
				{
					$duplicate = true;
				}

				$sql = "SELECT bilagsnr FROM fm_ecobilagoverf WHERE external_voucher_id = '{$external_voucher_id}'";
				$this->db->query($sql,__LINE__,__FILE__);
				if($this->db->next_record())
				{
					$duplicate = true;
				}

				if( !$duplicate )
				{
					rename("{$archive}/{$file}", "{$dirname}/{$file}");
					$this->receipt['message'][] = array('msg' => "fil tilbakeført fra arkiv til importkø: {$external_voucher_id}");
				}
			}
		}

		protected function remind()
		{
			if (!isset($GLOBALS['phpgw_info']['server']['smtp_server']) || !$GLOBALS['phpgw_info']['server']['smtp_server'])
			{
				return;
			}

			if($this->skip_email || $this->debug)
			{
				return;
			}

			// max. one mail each day
			if ( (int) $GLOBALS['phpgw_info']['server']['invoice_mail_reminder_time'] < (time() - (3600 * 24)) )
			{
				$toarray = array();
				$sql = 'SELECT DISTINCT oppsynsmannid as responsible FROM fm_ecobilag WHERE oppsynsigndato IS NULL AND oppsynsmannid IS NOT NULL AND saksigndato IS NULL';
				$this->db->query($sql,__LINE__,__FILE__);
				while($this->db->next_record())
				{
					$toarray[$this->db->f('responsible')] = true;
				}
				$sql = 'SELECT DISTINCT saksbehandlerid as responsible FROM fm_ecobilag WHERE saksigndato IS NULL AND saksbehandlerid IS NOT NULL AND oppsynsigndato IS NULL';
				$this->db->query($sql,__LINE__,__FILE__);
				while($this->db->next_record())
				{
					$toarray[$this->db->f('responsible')] = true;
				}
				$sql = 'SELECT DISTINCT budsjettansvarligid as responsible FROM fm_ecobilag WHERE saksigndato IS NOT NULL AND budsjettsigndato IS NULL AND budsjettansvarligid IS NOT NULL';
				$this->db->query($sql,__LINE__,__FILE__);

				while($this->db->next_record())
				{
					$toarray[$this->db->f('responsible')] = true;
				}

				$subject = 'Du har faktura til behandling';


				$from = "Ikke svar<IkkeSvar@nlsh.no>";

				foreach ($toarray as $lid => $dummy)
				{
					$prefs = $this->bocommon->create_preferences('property', $GLOBALS['phpgw']->accounts->name2id($lid));
					if(isset($prefs['email']) && $prefs['email'])
					{
						$body = '<a href ="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiinvoice2.index', 'voucher_id' => $bilagsnr, 'user_lid' => $lid ),false,true).'">Link til fakturabehandling</a>';
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
				// save time of mail, to not send to many mails
				$config = createObject('phpgwapi.config', 'phpgwapi');
				$config->read_repository();
				$config->value('invoice_mail_reminder_time', time());
				$config->save_repository();
			}
		}


		protected function get_files()
		{
			$server				= $this->config->config_data['common']['host'];
			$user				= $this->config->config_data['common']['user'];
			$password			= $this->config->config_data['common']['password'];
			$directory_remote	= rtrim($this->config->config_data['import']['remote_basedir'],'/');
			$directory_local	= rtrim($this->config->config_data['import']['local_path'],'/');
			$port				= 22;

			if (!function_exists("ssh2_connect"))
			{
				die("function ssh2_connect doesn't exist");
			}
			if(!($connection = ssh2_connect($server, $port)))
			{
				echo "fail: unable to establish connection\n";
			}
			else
			{
				// try to authenticate with username root, password secretpassword
				if(!ssh2_auth_password($connection, $user, $password))
				{
					echo "fail: unable to authenticate\n";
				}
				else
				{
					// allright, we're in!
					echo "okay: logged in...<br/>";

					// Enter "sftp" mode
					$sftp = @ssh2_sftp($connection);

					// Scan directory
					$files = array();
					echo "Scanning {$directory_remote}<br/>";
					$dir = "ssh2.sftp://$sftp$directory_remote";
					$handle = opendir($dir);
					while (false !== ($file = readdir($handle)))
					{
						if (is_dir($file))
						{
							echo "Directory: $file<br/>";
							continue;
						}

/*						if ($this->debug)
						{
							$size = filesize("ssh2.sftp://$sftp$directory_remote/$file");
							echo "File $file Size: $size<br/>";

							$stream = @fopen("ssh2.sftp://$sftp$directory_remote/$file", 'r');
							$contents = fread($stream, filesize("ssh2.sftp://$sftp$directory_remote/$file"));
							@fclose($stream);
							echo "CONTENTS: $contents<br/><br/>";
						}
*/
							$files[] = $file;
					}

					if ($this->debug)
					{
						_debug_array($files);
					}
					else
					{
						foreach($files as $file_name)
						{
							if( stripos( $file_name, 'Px205' ) === 0)
							{
						//		_debug_array($file_name);
								$file_remote = "{$directory_remote}/{$file_name}";	   
								$file_local = "{$directory_local}/{$file_name}";

								$stream = fopen("ssh2.sftp://$sftp$file_remote", 'r');
								$contents = fread($stream, filesize("ssh2.sftp://$sftp$file_remote"));
								fclose($stream);

								$fp = fopen($file_local, "wb");
								fwrite($fp,$contents);

								if(fclose($fp))
								{
									echo "File remote: {$file_remote} was copied to local: $file_local<br/>";
									if( ssh2_sftp_unlink ($sftp, "{$directory_remote}/archive/{$file_name}" ))
									{
										echo "Deleted duplicate File remote: {$directory_remote}/archive/{$file_name}<br/>";
									}
									if( ssh2_sftp_rename ($sftp, $file_remote, "{$directory_remote}/archive/{$file_name}" ))
									{
										echo "File remote: {$file_remote} was moved to remote: {$directory_remote}/archive/{$file_name}<br/>";
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
							}
						}
					}
				}
			}
		}


		protected function import($file)
		{
//			$valid_data= False;

			$buffer = array();
			$bilagsnr = false;

			$xmlparse = CreateObject('property.XmlToArray');
			$xmlparse->setEncoding('UTF-8');
			$var_result = $xmlparse->parseFile($file);

			set_time_limit(300);

			if (isset($var_result['INVOICES']) && is_array($var_result['INVOICES']))
			{
				$regtid		= date($this->datetimeformat);

				$i = 0;
				$_data = $var_result['INVOICES'][0]['INVOICE'][0]['INVOICEHEADER'][0];

//_debug_array($_data);
//die();

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

				$fakturanr		= $_data['SUPPLIERREF'];
				$fakturadato	= date($this->dateformat,strtotime(str_replace('.', '-', $_data['INVOICEDATE'])));
				$forfallsdato	= date($this->dateformat,strtotime(str_replace('.', '-', $_data['MATURITY'])));
				$periode 		= '';
				$belop 			= $_data['AMOUNT']/100;

				if(!abs($belop) > 0)
				{
					$this->receipt['message'][] = array('msg' => "Beløpet er 0 for Skanningreferanse: {$_data['SCANNINGNO']}, FakturaNr: {$fakturanr}, fil: {$file}");
					$belop = (float) 0.0001; // imported as 0.00
				}

				if( $belop < 0 )
				{
					$buffer[$i]['artid'] = 2;
				}
				else
				{
					$buffer[$i]['artid'] = 1;
				}

				$kidnr 						= $_data['KIDNO'];
				$_order_id					= $_data['PURCHASEORDERNO'];
				$merknad					= '';
				$line_text					= '';
				$order_id					= '';
				$buffer[$i]['project_id']	= '';

				$order_info = $this->get_order_info($_order_id); // henter default verdier selv om  $_order_id ikke er gyldig.

				if(!$_order_id)
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
				}

				$buffer[$i]['external_ref']			= $_data['SCANNINGNO'];
				$buffer[$i]['external_voucher_id']	= $_data['KEY']; // => 1400050146
				$buffer[$i]['pmwrkord_code']		= $order_id;
				$buffer[$i]['fakturanr']			= $fakturanr;
				$buffer[$i]['periode']				= $periode;
				$buffer[$i]['forfallsdato']			= $forfallsdato;
				$buffer[$i]['fakturadato']			= $fakturadato;
				$buffer[$i]['belop']				= $belop;
				$buffer[$i]['currency']				= $_data['CURRENCY.CURRENCYID'];
				$buffer[$i]['godkjentbelop']		= $belop;

				$buffer[$i]['kidnr']				= $kidnr;
				$buffer[$i]['bilagsnr_ut']			= $bilagsnr_ut;
				$buffer[$i]['referanse']			= "ordre: {$order_id}";

				$buffer[$i]['dima'] 				= $order_info['dima'];
				$buffer[$i]['dimb'] 				= $order_info['dimb'];
				$buffer[$i]['dime'] 				= $order_info['dime'];
				$buffer[$i]['loc1'] 				= $order_info['loc1'];
				$buffer[$i]['line_text']			= $order_info['title'];

				$buffer[$i]['mvakode'] = $this->mvakode;

				if($buffer[$i]['loc1'] && $this->auto_tax)
				{
					$mvakode = $this->soXport->auto_tax($buffer[$i]['loc1']);

					if($mvakode)
					{
						$buffer[$i]['mvakode'] = $mvakode;
					}
				}

				$update_voucher = false;
				$sql = "SELECT bilagsnr, bilagsnr_ut FROM fm_ecobilag WHERE external_ref = '{$_data['SCANNINGNO']}'";
				$this->db->query($sql,__LINE__,__FILE__);
				if($this->db->next_record())
				{
					$this->skip_update_voucher_id = true;
					$update_voucher = true;
					$_bilagsnr_ut = $this->db->f('bilagsnr_ut');
					$bilagsnr = $this->db->f('bilagsnr');

					$buffer[$i]['bilagsnr'] 		= $bilagsnr;
					$buffer[$i]['bilagsnr_ut']		= $_bilagsnr_ut;

					$this->receipt['message'][] = array('msg' => "Oppdatert med nye data i arbeidsregister: {$_data['SCANNINGNO']}");
				}

				$sql = "SELECT bilagsnr, bilagsnr_ut FROM fm_ecobilagoverf WHERE external_ref = '{$_data['SCANNINGNO']}'";
				$this->db->query($sql,__LINE__,__FILE__);
				if($this->db->next_record())
				{
					$this->skip_update_voucher_id = true;
					$update_voucher = true;
					$_bilagsnr_ut = $this->db->f('bilagsnr_ut');
					$bilagsnr = $this->db->f('bilagsnr');
					$__bilagsnr = $_bilagsnr_ut ? $_bilagsnr_ut : $bilagsnr;

					$buffer[$i]['bilagsnr'] 		= $bilagsnr;
					$buffer[$i]['bilagsnr_ut']		= $_bilagsnr_ut;

					if($_bilagsnr_ut)
					{
						$receipt = $this->export->RullTilbake(false,false,$_bilagsnr_ut);
					}
					else
					{
						$receipt = $this->export->RullTilbake(false,false,false,$bilagsnr);
					}

					if( isset($receipt['message']) )
					{
						$this->receipt['message'][] = array('msg' => "Bilag rullet tilbake fra historikk : {$__bilagsnr}");
					}
					else
					{
						$this->receipt['error'][] = array('msg' => "Bilag ikke rullet tilbake fra historikk : {$__bilagsnr}, Skanningreferanse: {$_data['SCANNINGNO']}, FakturaNr: {$fakturanr}");
					}
					unset($_bilagsnr_ut);
				}

				$vendor_id = $_data['SUPPLIER.CODE'];

				$sql = 'SELECT id FROM fm_vendor WHERE id = ' . (int) $vendor_id;
				$this->db->query($sql,__LINE__,__FILE__);

				if(!abs($belop) > 0)
				{
					$this->receipt['error'][] = array('msg' => "Importeres ikke: Beløpet er 0 for Skanningreferanse: {$_data['SCANNINGNO']}, FakturaNr: {$fakturanr}, fil: {$file}");
					$this->skip_import = true;				
				}
				else if(!$_data['SUPPLIER.CODE'])
				{
					$this->receipt['error'][] = array('msg' => "LeverandørId ikke angitt for faktura: {$_data['SCANNINGNO']}");
					$this->skip_import = true;
				}
				else if(!$this->db->next_record())
				{
					$this->receipt['error'][] = array('msg' => "Importeres ikke: Ikke gyldig LeverandørId: {$_data['SUPPLIER.CODE']}, Skanningreferanse: {$_data['SCANNINGNO']}, FakturaNr: {$fakturanr}, fil: {$file}");
					$this->skip_import = true;

					$to = isset($this->config->config_data['import']['email_on_error']) && $this->config->config_data['import']['email_on_error'] ? $this->config->config_data['import']['email_on_error'] : '';

					if($to && !$this->skip_email)
					{
						$from = "Ikke svar<IkkeSvar@nlsh.no>";
						$body = "Ikke gyldig leverandør, id: {$_data['SUPPLIER.CODE']}</br>";
						$body .= '<a href ="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uigeneric.edit', 'appname' => 'property', 'type' => 'vendor'),false,true).'">Link til å legge inn ny leverandør</a>';

						try
						{
							$rc = $this->send->msg('email', $to, 'Ikke gyldig leverandør ved import av faktura til Portico', $body, '', '', '',$from,'','html');
							if($rc)
							{
								$this->receipt['message'][] = array('msg'=> "epost sendt til {$to}");
							}
						}
						catch (phpmailerException $e)
						{
							$this->receipt['error'][] = array('msg' => $e->getMessage());
						}
					}
				}
				else if ($order_info['vendor_id'] != $vendor_id)
				{
					$this->receipt['message'][] = array('msg' => 'Ikke samsvar med leverandør på bestilling og mottatt faktura');
				}

				if($this->auto_tax)
				{
					$buffer[$i]['mvakode'] = $this->soXport->tax_b_account_override($buffer[$i]['mvakode'], $order_info['spbudact_code']);
					$buffer[$i]['mvakode'] = $this->soXport->tax_vendor_override($buffer[$i]['mvakode'], $vendor_id);
				}

				$buffer[$i]['kostra_id'] = $this->default_kostra_id;//$this->soXport->get_kostra_id($buffer[$i]['loc1']);

				$buffer[$i]['merknad'] = $merknad;
				$buffer[$i]['splitt'] = $this->splitt;
				$buffer[$i]['kildeid'] = $this->kildeid;
				$buffer[$i]['spbudact_code'] = $order_info['spbudact_code'];
				$buffer[$i]['typeid'] = isset($invoice_common['type']) && $invoice_common['type'] ? $invoice_common['type'] : 1;
				$buffer[$i]['regtid'] = $regtid;

				$buffer[$i]['spvend_code'] = $vendor_id;

				if(isset($order_info['janitor']) && $order_info['janitor'])
				{
					$buffer[$i]['oppsynsmannid'] = $order_info['janitor'];
				}

				if(isset($order_info['supervisor']) && $order_info['supervisor'])
				{
					$buffer[$i]['saksbehandlerid']		= $order_info['supervisor'];
				}

				if(isset($order_info['budget_responsible']) && $order_info['budget_responsible'])
				{
					$buffer[$i]['budsjettansvarligid']	= $order_info['budget_responsible'];
				}
			}

//_debug_array($buffer);
//_debug_array($this->receipt);
//_debug_array($order_info['toarray']);
			if($this->debug && $this->skip_import)
			{
				_debug_array("Skip import - file: {$file}");
			}
			
			if($this->skip_import)
			{
				$this->skip_import = false;
				return false;
			}
			else
			{
				if($update_voucher && $bilagsnr)
				{
					$this->db->query("DELETE FROM fm_ecobilag WHERE external_ref = '{$_data['SCANNINGNO']}'",__LINE__,__FILE__);
				}

				if(!$bilagsnr)
				{
					$bilagsnr = $this->invoice->next_bilagsnr();

					foreach($buffer as &$entry)
					{
						$entry['bilagsnr'] = $bilagsnr;
					}
				}

				if($order_info['toarray'] && (!$this->skip_email || !$this->debug))
				{

					$from = "Ikke svar<IkkeSvar@nlsh.no>";

					$to = implode(';',$order_info['toarray']);

					if (isset($GLOBALS['phpgw_info']['server']['smtp_server']) && $GLOBALS['phpgw_info']['server']['smtp_server'])
					{
						$subject = 'Ny faktura venter på behandling';
						$body = '<a href ="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiinvoice2.index', 'voucher_id' => $bilagsnr, 'query' => $bilagsnr, 'user_lid' =>'all'),false,true).'">Link til fakturabehandling</a>';

						try
						{
							$rc = $this->send->msg('email', $to, $subject, stripslashes($body), '', $cc, $bcc,$from,'','html');
						}
						catch (phpmailerException $e)
						{
							$this->receipt['error'][] = array('msg' => $e->getMessage());
						}
					}
					else
					{
						$this->receipt['error'][] = array('msg'=>lang('SMTP server is not set! (admin section)'));
					}
				}

				$GLOBALS['phpgw']->db->Exception_On_Error = true;

				try
				{
					$bilagsnr = $this->import_end_file($buffer);
				}
				catch (Exception $e)
				{
					if($e)
					{
						$GLOBALS['phpgw']->log->error(array(
							'text'	=> 'import_fra_agresso_X205::import() : error when trying to execute import_end_file(): %1',
							'p1'	=> $e->getMessage(),
							'p2'	=> '',
							'line'	=> __LINE__,
							'file'	=> __FILE__
						));
						$this->receipt['error'][] = array('msg'=> $e->getMessage());
					}
					return false;
				}

				$GLOBALS['phpgw']->db->Exception_On_Error = false;
				return $bilagsnr;
			}
		}

		function get_order_info($order_id = '')
		{
			$order_info = array();
			$toarray = array();
			$order_id = (int) $order_id;
			$sql = "SELECT fm_workorder.location_code,fm_workorder.vendor_id,fm_workorder.account_id,fm_workorder.ecodimb,fm_workorder.category, fm_workorder.user_id,fm_workorder.title"
			. " FROM fm_workorder {$this->join} fm_project ON fm_workorder.project_id = fm_project.id WHERE fm_workorder.id = {$order_id}";
			$this->db->query($sql,__LINE__,__FILE__);
			if(	$this->db->next_record())
			{
				$order_info['order_exist'] = true;
			}
			if ($this->db->f('location_code'))
			{ 
				$parts = explode('-',$this->db->f('location_code'));
				$order_info['dima'] = implode('', $parts);
				$order_info['loc1'] = $parts[0];
			}

			$order_info['vendor_id'] 			= $this->db->f('vendor_id');
			$order_info['spbudact_code']		= $this->db->f('account_id');
			$order_info['dimb']					= $this->db->f('ecodimb');
			$order_info['dime']					= $this->db->f('category');
			$order_info['title']				= $this->db->f('title',true);			

			$janitor_user_id 					= $this->db->f('user_id');
			$order_info['janitor']				= $GLOBALS['phpgw']->accounts->get($janitor_user_id)->lid;
			$supervisor_user_id				= $this->invoice->get_default_dimb_role_user(2, $order_info['dimb']);
			if($supervisor_user_id)
			{
				$order_info['supervisor']			= $GLOBALS['phpgw']->accounts->get($supervisor_user_id)->lid;
			}

			$budget_responsible_user_id			= $this->invoice->get_default_dimb_role_user(3, $order_info['dimb']);
			if($budget_responsible_user_id)
			{
				$order_info['budget_responsible']	= $GLOBALS['phpgw']->accounts->get($budget_responsible_user_id)->lid;
			}

			if(!$order_info['budget_responsible'])
			{
				$order_info['budget_responsible'] = isset($this->config->config_data['import']['budget_responsible']) && $this->config->config_data['import']['budget_responsible'] ? $this->config->config_data['import']['budget_responsible'] : 'karhal';
			}

			$order_info['toarray'] = $toarray;
			return $order_info;
		}

		function import_end_file($buffer)
		{
			$num = $this->soXport->add($buffer,$this->skip_update_voucher_id);
			if($this->debug)
			{
				_debug_array("import_end_file() ");
				echo 'buffer: ';
				_debug_array($buffer);
				_debug_array("num: {$num}");
			}

			if($num > 0)
			{
				$this->receipt['message'][]= array('msg' => "Importert {$num} poster til bilag {$buffer[0]['bilagsnr']}, SCANNINGNO: {$buffer[0]['external_ref']}, KEY: {$buffer[0]['external_voucher_id']}");
				return $buffer[0]['bilagsnr'];
			}
			return false;
		}
	}
