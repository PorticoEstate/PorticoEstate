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
	 * Filteret benytter format X205 for integrasjon mellom Contempus Invoice og ClockWork Logistics.
	 * Formatet sender innskannede fakturaer fra Contempus til ClockWork - og Portico Estate
	 * @package property
	 */


	class  Import_fra_basware_X205
	{
		var	$function_name = 'Import_fra_basware_X205';
		var $auto_tax = true;
		var $mvakode=0;
		var $kildeid=1;
		var $splitt=0;
		var $soXport;
		var $invoice;
		var $bestiller = 85; //cat_id for rolle
		var $attestant = 83; //cat_id for rolle
		var $budsjettansvarlig = 146; //cat_id for rolle
		var $default_kostra_id = 9999; //dummy
		var $debug = false;

		function __construct()
		{
			$this->soXport			= CreateObject('property.soXport');	
			$this->invoice			= CreateObject('property.soinvoice');
			$this->responsible		= CreateObject('property.soresponsible');
			$this->bocommon			= CreateObject('property.bocommon');
			$this->db				= & $GLOBALS['phpgw']->db;
			$this->join				= & $this->db->join;
			$this->left_join		= & $this->db->left_join;
			$this->like				= & $this->db->like;
			$this->dateformat		= $this->db->date_format();
			$this->datetimeformat	= $this->db->datetime_format();
			$this->config	= CreateObject('phpgwapi.config','property');
			$this->config->read();
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


			$msgbox_data = $this->bocommon->msgbox_data($this->receipt);

			$data = array
			(
				'msgbox_data'			=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'done_action'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=>'property.uiasync.index')),
				'run_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'message'				=> $this->receipt['message'],
				'lang_confirm_msg'		=> $lang_confirm_msg,
				'lang_yes'				=> $lang_yes,
				'lang_yes_statustext'	=> 'Importer faktura fra Basware',
				'lang_no_statustext'	=> 'tilbake',
				'lang_no'				=> lang('no'),
				'lang_done'				=> 'Avbryt',
				'lang_done_statustext'	=> 'tilbake'
			);

			$appname		= lang('location');
			$function_msg	= 'Importer faktura fra Basware';
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('confirm' => $data));
			$GLOBALS['phpgw']->xslttpl->pp();
		}

		public function execute($cron='')
		{
			$this->get_files();
			$dirname = $this->config->config_data['import_path'];
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
					$bilagsnr = $this->import($file);
					if ($bilagsnr)
					{
						// move file
						$_file = basename($file);
						$movefrom = "{$dirname}/{$_file}";
						$moveto = "{$dirname}/archive/{$_file}";

						$ok = @rename($movefrom, $moveto);
						if(!$ok) // Should never happen.
						{
							$this->invoice->delete($bilagsnr);
							$this->receipt['error'][] = array('msg' => "Kunne ikke flytte importfil til arkiv, Bilag {$bilagsnr} er slettet");
						}
					}
				}
			}
			else
			{
				$this->receipt['error'][] = array('msg' => "Arkiv katalog '{$dirname}/archive/' ikke er ikke skrivbar - kontakt systemadminstrator for å korrigere");			
			}

			if(!$cron)
			{
				$this->confirm($execute=false);
			}

			$msgbox_data = $this->bocommon->msgbox_data($this->receipt);

			$insert_values= array
			(
				$cron,
				date($this->bocommon->datetimeformat),
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
			$server				= $this->config->config_data['invoice_ftp_host'];
			$user				= $this->config->config_data['invoice_ftp_user'];
			$password			= $this->config->config_data['invoice_ftp_password'];
			$directory_remote	= rtrim($this->config->config_data['invoice_ftp_import_basedir'],'/');
			$directory_local	= rtrim($this->config->config_data['import_path'],'/');
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
//							echo "Directory: $file<br/>";
							continue;
						}

/*						$size = filesize("ssh2.sftp://$sftp$directory_remote/$file");
						echo "File $file Size: $size<br/>";

						$stream = @fopen("ssh2.sftp://$sftp$directory_remote/$file", 'r');
						$contents = fread($stream, filesize("ssh2.sftp://$sftp$directory_remote/$file"));
						@fclose($stream);
						echo "CONTENTS: $contents<br/><br/>";
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
							if( stripos( $file_name, 'x205' ) === 0)
							{
_debug_array($file_name);
								$file_remote = "{$directory_remote}/{$file_name}";	   
								$file_local = "{$directory_local}/{$file_name}";

								$stream = fopen("ssh2.sftp://$sftp$file_remote", 'r');
								$contents = fread($stream, filesize("ssh2.sftp://$sftp$file_remote"));
								fclose($stream);

								$fp = fopen($file_local, "wb");
								fwrite($fp,$contents);
				
								if(fclose($fp))
//								if(ssh2_scp_recv($connection, $file_remote,$file_local))
								{
									echo "File remote: ".$file_remote." was copied to local: $file_local<br/>";
									if( ssh2_sftp_rename ($sftp, $file_remote, "{$directory_remote}/archive/{$file_name}" ))
									{
										echo "File remote: ".$file_remote." was moved to remote: {$directory_remote}/archive/{$file_name}<br/>";
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

			$xmlparse = CreateObject('property.XmlToArray');
			$xmlparse->setEncoding('UTF-8');
			$var_result = $xmlparse->parseFile($file);

			set_time_limit(300);

			if (isset($var_result['INVOICES']) AND is_array($var_result['INVOICES']))
			{
				$transferdate =  str_replace('.', '-', $var_result['TRANSACTIONINFORMATION'][0]['TRANSFERDATE']);// 2009.05.28
				$transfertime =  $var_result['TRANSACTIONINFORMATION'][0]['TRANSFERTIME'];// 13:10:28
				$regtid		= date($this->datetimeformat,strtotime("{$transferdate} {$transfertime}"));

				$i = 0;
				foreach ($var_result['INVOICES'] as $dummy => $entry)
				{
					$_data = $entry['INVOICE'][0]['INVOICEHEADER'][0];
				
//_debug_array($_data);
//die();

					$_data['KEY']; // => 1400050146
//					$_data['SCANNINGNO']; // => 11E28NJINL3VR6
//					$_data['AMOUNT']; // => 312500
					$_data['ARRIVAL']; // => 2009.05.28
					$_data['CLIENT.CODE']; // => 14
//					$_data['CURRENCY.CURRENCYID']; // => NOK
					$_data['EXCHANGERATE']; // => 1
//					$_data['INVOICEDATE']; // => 2009.05.28
					$_data['LOCALAMOUNT']; // => 312500
					$_data['LOCALVATAMOUNT']; // => 62500
//					$_data['MATURITY']; // => 2009.06.30
					$_data['PAYAMOUNT']; // => 0
					$_data['POSTATUSUPDATED']; // => 0
//					$_data['PURCHASEORDERNO']; // => 1409220008
					$_data['PURCHASEORDERSTATUS.CODE']; // => WaitForMatch
					$_data['SUPPLIER.BANKGIRO']; // => 70580621110
//					$_data['SUPPLIER.CODE']; // => 100644
//					$_data['SUPPLIERREF']; // => 7869
					$_data['VATAMOUNT']; // => 62500
					
					$bilagsnr = isset($_data['VOUCHERID']) ? $_data['VOUCHERID'] : ''; // FIXME: innkommende bilagsnummer?

					$order_id 		= $_data['PURCHASEORDERNO'];
					$fakturanr		= $_data['KEY'];
					$fakturadato	= date($this->dateformat,strtotime(str_replace('.', '-', $_data['INVOICEDATE'])));
					$forfallsdato	= date($this->dateformat,strtotime(str_replace('.', '-', $_data['MATURITY'])));
					$periode 		= date('Ym',strtotime(str_replace('.', '-', $_data['INVOICEDATE'])));
					$belop 			= $_data['AMOUNT']/100;

					if( $belop < 0 )
					{
						$buffer[$i]['artid'] = 2;
					}
					else
					{
						$buffer[$i]['artid'] = 1;					
					}

					$kidnr 	= $_data['KIDNO'];

					$buffer[$i]['external_ref']		= $_data['SCANNINGNO'];
					$buffer[$i]['pmwrkord_code']	= $order_id;
					$buffer[$i]['fakturanr']		= $fakturanr;
					$buffer[$i]['periode']			= $periode;
					$buffer[$i]['forfallsdato']		= $forfallsdato;
					$buffer[$i]['fakturadato']		= $fakturadato;
					$buffer[$i]['belop']			= $belop;
					$buffer[$i]['currency']			= $_data['CURRENCY.CURRENCYID'];
					$buffer[$i]['godkjentbelop']	= $belop;
					
					$buffer[$i]['kidnr']			= $kidnr;
					$buffer[$i]['bilagsnr']			= $bilagsnr;
					$buffer[$i]['referanse']		= "ordre: {$order_id}";

					$order_info = $this->get_order_info($order_id);

					$buffer[$i]['dimb'] = $order_info['dimb'];
					$buffer[$i]['dima'] = $order_info['dima'];
					$buffer[$i]['loc1'] = $order_info['loc1'];

					$buffer[$i]['mvakode'] = $this->mvakode;

					if($buffer[$i]['dima'] && $this->auto_tax)
					{
						$mvakode = $this->soXport->auto_tax($buffer[$i]['dima']);
					
						if($mvakode)
						{
							$buffer[$i]['mvakode'] = $mvakode;
						}
					}

					if ($order_info['vendor_id'] != $_data['SUPPLIER.CODE'])
					{
						$this->receipt['message'][] = array('msg' => 'Ikke samsvar med leverandør på bestilling og mottatt faktura');
					}

					$sql = 'SELECT id FROM fm_vendor WHERE id = ' . (int) $_data['SUPPLIER.CODE'];
					$this->db->query($sql,__LINE__,__FILE__);
					if(!$this->db->next_record())
					{
						$this->receipt['error'][] = array('msg' => "Ikke gyldig leverandør id: {$_data['SUPPLIER.CODE']}");
					}

					$vendor_id = $_data['SUPPLIER.CODE'];

					if($this->auto_tax)
					{
						$buffer[$i]['mvakode'] = $this->soXport->tax_b_account_override($buffer[$i]['mvakode'], $order_info['spbudact_code']);
						$buffer[$i]['mvakode'] = $this->soXport->tax_vendor_override($buffer[$i]['mvakode'], $vendor_id);
					}

					$buffer[$i]['kostra_id'] = $this->default_kostra_id;//$this->soXport->get_kostra_id($buffer[$i]['loc1']);
					
					$merknad = '';
					
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

					$i++;
				}
			}

//_debug_array($buffer);
//_debug_array($this->receipt);

			if(!isset($this->receipt['error']) || !$this->receipt['error'])
			{
				if(!$bilagsnr)
				{
					$bilagsnr = $this->invoice->next_bilagsnr();
					
					foreach($buffer as &$entry)
					{
						$entry['bilagsnr'] = $bilagsnr;
					}
				}

				return $this->import_end_file($buffer);
			}
			return false;
		}

		function get_order_info($order_id = '')
		{
			$order_info = array();
			$order_id = (int) $order_id;
			$sql = "SELECT fm_workorder.location_code,fm_workorder.vendor_id,fm_workorder.account_id,fm_workorder.ecodimb, fm_workorder.user_id"
			. " FROM fm_workorder {$this->join} fm_project ON fm_workorder.project_id = fm_project.id WHERE fm_workorder.id = $order_id";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			if ($this->db->f('location_code'))
			{ 
				$parts = explode('-',$this->db->f('location_code'));
				$order_info['dima'] = implode('', $parts);
				$order_info['loc1'] = $parts[0];
			}
			
			$order_info['vendor_id'] 			= $this->db->f('vendor_id');
			$order_info['spbudact_code']		= $this->db->f('account_id');
			$order_info['dimb']					= $this->db->f('ecodimb');
			
//			$criteria_janitor					= array('ecodimb' => $order_info['dimb'], 'cat_id' => $this->bestiller ); //bestiller
//			$janitor_contact_id					= $this->responsible->get_responsible($criteria_janitor);
//			$janitor_user_id					= $this->responsible->get_contact_user_id($janitor_contact_id);
			$janitor_user_id 					= $this->db->f('user_id');
			$order_info['janitor']				= $GLOBALS['phpgw']->accounts->get($janitor_user_id)->lid;

			$criteria_supervisor				= array('ecodimb' => $order_info['dimb'], 'cat_id' => $this->attestant); // attestere
			$supervisor_contact_id				= $this->responsible->get_responsible($criteria_supervisor);
			$supervisor_user_id					= $this->responsible->get_contact_user_id($supervisor_contact_id);

			$order_info['supervisor']			= $GLOBALS['phpgw']->accounts->get($supervisor_user_id)->lid;

			$criteria_budget_responsible		= array('ecodimb' => $order_info['dimb'], 'cat_id' => $this->budsjettansvarlig); //anviser
			$budget_responsible_contact_id		= $this->responsible->get_responsible($criteria_budget_responsible);
			$budget_responsible_user_id			= $this->responsible->get_contact_user_id($budget_responsible_contact_id);
			$order_info['budget_responsible']	= $GLOBALS['phpgw']->accounts->get($budget_responsible_user_id)->lid;

			return $order_info;
		}


		function import_end_file($buffer)
		{
			$num = $this->soXport->add($buffer);
			if($num > 0)
			{
				$this->receipt['message'][]= array('msg' => lang('Successfully imported %1 records into your invoice register.',$num).' '.lang('ID').': '. $buffer[0]['bilagsnr']);
				return $buffer[0]['bilagsnr'];
			}
			return false;
		}
	}
