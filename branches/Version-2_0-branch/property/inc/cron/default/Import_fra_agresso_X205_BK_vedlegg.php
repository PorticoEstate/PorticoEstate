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
	 * 
	 * @package property
	 */
	include_class('property', 'cron_parent', 'inc/cron/');

	class Import_fra_agresso_X205_BK_vedlegg extends property_cron_parent
	{
		protected $debug = false;

		function __construct()
		{
			parent::__construct();

			$this->function_name = get_class($this);
			$this->sub_location = lang('invoice');
			$this->function_msg = 'Importer tillegsvedlegg til faktura fra Agresso';

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

					if(preg_match('/^Portico/i', (string)$file ))
					{
						$file_list[] = (string)"{$dirname}/{$file}";
					}
				}
			}

			if (is_writable("{$dirname}/arkiv"))
			{
				foreach ($file_list as $file)
				{
					$bilagsnr = $this->import($file);
					if ($this->debug)
					{
						_debug_array("Behandler fil: {$file}");
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
							$this->receipt['error'][] = array('msg' => "Kunne ikke flytte importfil {$file} til arkiv");
						}
					}
					else
					{
						$this->receipt['error'][] = array('msg' => "Refererer ikke til et gyldig bilag: {$file}");
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

					if(preg_match('/^Portico/i', (string)$file_name ))
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

		protected function get_files_old()
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
			$port = 22;

			if (!function_exists("ssh2_connect"))
			{
				die("function ssh2_connect doesn't exist");
			}
			if (!($connection = ssh2_connect($server, $port)))
			{
				echo "fail: unable to establish connection\n";
			}
			else
			{
				// try to authenticate with username root, password secretpassword
				if (!ssh2_auth_password($connection, $user, $password))
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

						/* 						if ($this->debug)
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
						foreach ($files as $file_name)
						{
							if(preg_match('/^Portico/i', (string)$file_name ))
							{
						//		_debug_array($file_name);
								$file_remote = "{$directory_remote}/{$file_name}";
								$file_local = "{$directory_local}/{$file_name}";

								$stream = fopen("ssh2.sftp://$sftp$file_remote", 'r');
								$contents = fread($stream, filesize("ssh2.sftp://$sftp$file_remote"));
								fclose($stream);

								$fp = fopen($file_local, "wb");
								fwrite($fp, $contents);

								if (fclose($fp))
								{
									echo "File remote: {$file_remote} was copied to local: $file_local<br/>";
									if (ssh2_sftp_unlink($sftp, "{$directory_remote}/arkiv/{$file_name}"))
									{
										echo "Deleted duplicate File remote: {$directory_remote}/arkiv/{$file_name}<br/>";
									}
									if (ssh2_sftp_rename($sftp, $file_remote, "{$directory_remote}/arkiv/{$file_name}"))
									{
										echo "File remote: {$file_remote} was moved to remote: {$directory_remote}/arkiv/{$file_name}<br/>";
									}
									else
									{
										echo "ERROR! File remote: {$file_remote} failed to move to remote: {$directory_remote}/arkiv/{$file_name}<br/>";
										if (unlink($file_local))
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

				$update_voucher = false;
				$sql = "SELECT bilagsnr, bilagsnr_ut FROM fm_ecobilag WHERE external_voucher_id = '{$_data['KEY']}'";
				$this->db->query($sql, __LINE__, __FILE__);
				if ($this->db->next_record())
				{
					$update_voucher = true;
					$bilagsnr = $this->db->f('bilagsnr');
					$this->receipt['message'][] = array('msg' => "Oppdatert med nye filer, key: {$_data['KEY']}, bestilling: {$_data['PURCHASEORDERNO']}");
				}

				$sql = "SELECT bilagsnr FROM fm_ecobilagoverf WHERE external_voucher_id = '{$_data['KEY']}'";
				$this->db->query($sql, __LINE__, __FILE__);
				if ($this->db->next_record())
				{
					$this->receipt['message'][] = array('msg' => "Oppdatert med nye filer, key: {$_data['KEY']}, bestilling: {$_data['PURCHASEORDERNO']}");
				}
			}
			return $bilagsnr;
		}
	}