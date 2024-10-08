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
	require_once PHPGW_API_INC . '/flysystem3/vendor/autoload.php';

	use League\Flysystem\Filesystem;
	use League\Flysystem\Ftp\FtpAdapter;
	use League\Flysystem\Ftp\FtpConnectionOptions;

	class Import_fra_agresso_X205_BK_vedlegg extends property_cron_parent
	{

		protected $debug = false;
		var $config;

		function __construct()
		{
			parent::__construct();

			$this->function_name = get_class($this);
			$this->sub_location	 = lang('invoice');
			$this->function_msg	 = 'Importer tillegsvedlegg til faktura fra Agresso';

			$this->config = CreateObject('admin.soconfig', $GLOBALS['phpgw']->locations->get_id('property', '.invoice'));
		}

		public function execute()
		{
			$this->get_files();
			$dirname	 = $this->config->config_data['import']['local_path'];
			$re_check	 = $this->config->config_data['import']['re_check'];
			// prevent path traversal
			if (preg_match('/\./', $dirname) || !is_dir($dirname))
			{
				return array();
			}

			$file_list = array();
			if ($re_check)
			{
				$dir = new DirectoryIterator("{$dirname}/arkiv");
			}
			else
			{
				$dir = new DirectoryIterator($dirname);
			}
			if (is_object($dir))
			{
				foreach ($dir as $file)
				{
					if ($file->isDot() || !$file->isFile() || !$file->isReadable() || strcasecmp(end(explode(".", $file->getPathname())), 'xml') != 0)
					{
						continue;
					}

					if (preg_match('/^Portico/i', (string)$file))
					{
						if ($re_check)
						{
							$file_list[] = (string)"{$dirname}/arkiv/{$file}";
						}
						else
						{
							$file_list[] = (string)"{$dirname}/{$file}";
						}
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

					if ($re_check)
					{
						$this->receipt['message'][] = array('msg' => "Pakket ut vedlegg på nytt for {$bilagsnr}");
					}
					else
					{
						if ($bilagsnr)
						{
							// move file
							$_file		 = basename($file);
							$movefrom	 = "{$dirname}/{$_file}";
							$moveto		 = "{$dirname}/arkiv/{$_file}";

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
			}
			else
			{
				$this->receipt['error'][] = array('msg' => "Arkiv katalog '{$dirname}/arkiv/' ikke er ikke skrivbar - kontakt systemadminstrator for å korrigere");
			}
		}

		protected function get_files()
		{
			$method = $this->config->config_data['common']['method'];
			if ($method == 'local')
			{
				return;
			}

			$server				 = $this->config->config_data['common']['host'];
			$user				 = $this->config->config_data['common']['user'];
			$password			 = $this->config->config_data['common']['password'];
			$directory_remote	 = rtrim($this->config->config_data['import']['remote_basedir'], '/');
			$directory_local	 = rtrim($this->config->config_data['import']['local_path'], '/');

			$adapter = new FtpAdapter(
				// Connection options
				FtpConnectionOptions::fromArray([
					'host' => $server, // required
					'root' => $directory_remote, // required
					'username' => $user, // required
					'password' => $password, // required
					'port' => 21,
					'ssl' => false,
					'timeout' => 90,
					'utf8' => true,
					'passive' => true,
					'transferMode' => FTP_BINARY,
					'systemType' => null, // 'windows' or 'unix'
					'ignorePassiveAddress' => null, // true or false
					'timestampsOnUnixListingsEnabled' => false, // true or false
					'recurseManually' => true // true
				])
			);

			// The FilesystemOperator
			$filesystem = new Filesystem($adapter);
			try
			{
				$listing = $filesystem->listContents('');
			}
			catch (Exception $exc)
			{
				$this->receipt['error'][] = array('msg' => $exc->getMessage());
				return;
			}


			// allright, we're in!
			echo "okay: logged in...<br/>\n";

			// Scan directory
			$empty_files = array();
			$files = array();
			echo "Scanning {$directory_remote}<br/>\n";

			foreach ($listing as $object)
			{
				$file = $object->path();

				if ($object->type() == 'dir')
				{
					echo "Directory: $file<br/>\n";
					continue;
				}

				$size = $filesystem->fileSize($file);
				echo "File $file Size: $size<br/>\n";

				if ($this->debug)
				{
					$contents = $filesystem->read($file);
					echo "CONTENTS: $contents<br/><br/>\n\n";
				}

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
					if (preg_match('/^Portico/i', (string)$file_name))
					{
						$file_remote = $file_name;
						$file_local	 = "{$directory_local}/{$file_name}";

						$contents = $filesystem->read($file_name);

						if(strlen($contents) == 0)
						{
							$empty_files[] = $file_name;
						}

						$fp = fopen($file_local, "wb");
						fwrite($fp, $contents);

						if (fclose($fp))
						{
							echo "File remote: {$file_remote} was copied to local: $file_local<br/>";
							if ($filesystem->fileExists("arkiv/{$file_name}")	)
							{
								try
								{
									$filesystem->delete("arkiv/{$file_name}");
									echo "Deleted duplicate File remote: {$directory_remote}/arkiv/{$file_name}<br/>";
								}
								catch (FilesystemException | UnableToDeleteFile $exception)
								{
									// handle the error
								}
							}

							try
							{
								$filesystem->move($file_remote, "arkiv/{$file_name}");
								echo "File remote: {$file_remote} was moved to remote: {$directory_remote}/arkiv/{$file_name}<br/>";
							}
							catch (FilesystemException | UnableToDeleteFile $exception)
							{
								// handle the error
								echo "ERROR! File remote: {$file_remote} failed to move to remote: {$directory_remote}/arkiv/{$file_name}<br/>";
								if (unlink($file_local))
								{
									echo "Lokal file was deleted: {$file_local}<br/>";
								}
							}
						}
					}
					else
					{
						echo "skip {$file_name}<br/>";
					}
				}
			}

		}


		protected function check_storage_dir( $files_path )
		{
			if (is_dir($files_path) && is_writable($files_path) && is_readable($files_path))
			{
				return true;
			}
		}

		protected function create_storage_dir( $files_path )
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

			$xml = new SimpleXMLElement(file_get_contents($file));

			$_data = array(
				'KEY'					 => $xml->xpath('//INVOICES/INVOICE/INVOICEHEADER/KEY'),
				'ATTACHMENT'			 => $xml->xpath('//INVOICES/INVOICE/INVOICEHEADER/ATTACHMENT'),
				'AMOUNT'				 => $xml->xpath('//INVOICES/INVOICE/INVOICEHEADER/AMOUNT'),
				'CLIENT.CODE'			 => $xml->xpath('//INVOICES/INVOICE/INVOICEHEADER/CLIENT.CODE'),
				'CURRENCY.CURRENCYID'	 => $xml->xpath('//INVOICES/INVOICE/INVOICEHEADER/CURRENCY.CURRENCYID'),
				'EXCHANGERATE'			 => $xml->xpath('//INVOICES/INVOICE/INVOICEHEADER/EXCHANGERATE'),
				'INVOICEDATE'			 => $xml->xpath('//INVOICES/INVOICE/INVOICEHEADER/INVOICEDATE'),
				'LOCALAMOUNT'			 => $xml->xpath('//INVOICES/INVOICE/INVOICEHEADER/LOCALAMOUNT'),
				'LOCALVATAMOUNT'		 => $xml->xpath('//INVOICES/INVOICE/INVOICEHEADER/LOCALVATAMOUNT'),
				'MATURITY'				 => $xml->xpath('//INVOICES/INVOICE/INVOICEHEADER/MATURITY'),
				'PAYAMOUNT'				 => $xml->xpath('//INVOICES/INVOICE/INVOICEHEADER/PAYAMOUNT'),
				'POSTATUSUPDATED'		 => $xml->xpath('//INVOICES/INVOICE/INVOICEHEADER/POSTATUSUPDATED'),
				'PURCHASEORDERNO'		 => $xml->xpath('//INVOICES/INVOICE/INVOICEHEADER/PURCHASEORDERNO'),
				'SUPPLIERBANKGIRO'		 => $xml->xpath('//INVOICES/INVOICE/INVOICEHEADER/SUPPLIERBANKGIRO'),
				'SUPPLIER.CODE'			 => $xml->xpath('//INVOICES/INVOICE/INVOICEHEADER/SUPPLIER.CODE'),
				'SUPPLIERREF'			 => $xml->xpath('//INVOICES/INVOICE/INVOICEHEADER/SUPPLIERREF'),
				'VATAMOUNT'				 => $xml->xpath('//INVOICES/INVOICE/INVOICEHEADER/VATAMOUNT')
			);

			foreach ($_data as $key => & $__data)
			{
				$__data = (string)$__data[0];
			}

			set_time_limit(300);

			if (!empty($_data['KEY']))
			{

				if (!empty($_data['ATTACHMENT']))
				{
					$attachment				 = base64_decode($_data['ATTACHMENT']);
//	_debug_array($_data);
					$directory_local		 = rtrim($this->config->config_data['import']['local_path'], '/');
					$directory_attachment	 = "{$directory_local}/attachment/{$_data['KEY']}";
					if (!$this->check_storage_dir($directory_attachment))
					{
						$this->create_storage_dir($directory_attachment);
					}

					$tmpfname = tempnam('', 'attachment');
//	_debug_array($tmpfname);

					$handle	 = fopen($tmpfname, "w");
					fwrite($handle, $attachment);
					fclose($handle);
					$zip	 = new ZipArchive;
					if ($zip->open($tmpfname) === true)
					{
						for ($j = 0; $j < $zip->numFiles; $j++)
						{
							$filename		 = $zip->getNameIndex($j);
							$path_parts		 = explode('.', $filename);
							$new_filename	 = $filename;
							if (count($path_parts) == 2)
							{
								$suffix_parts	 = explode(' ', $path_parts[1]);
								$suffix			 = $suffix_parts[0];
								$new_filename	 = "{$path_parts[0]}.{$suffix}";
							}
							copy("zip://" . $tmpfname . "#" . $filename, "{$directory_attachment}/{$new_filename}");
						}
						$zip->close();
					}
					unlink($tmpfname);
				}

				$sql			 = "SELECT bilagsnr, bilagsnr_ut FROM fm_ecobilag WHERE external_voucher_id = '{$_data['KEY']}'";
				$this->db->query($sql, __LINE__, __FILE__);
				if ($this->db->next_record())
				{
					$bilagsnr					 = $this->db->f('bilagsnr');
					$this->receipt['message'][]	 = array('msg' => "Oppdatert med nye filer, key: {$_data['KEY']}, bestilling: {$_data['PURCHASEORDERNO']}");
				}

				$sql = "SELECT bilagsnr FROM fm_ecobilagoverf WHERE external_voucher_id = '{$_data['KEY']}'";
				$this->db->query($sql, __LINE__, __FILE__);
				if ($this->db->next_record())
				{
					$bilagsnr					 = $this->db->f('bilagsnr');
					$this->receipt['message'][] = array('msg' => "Oppdatert med nye filer, key: {$_data['KEY']}, bestilling: {$_data['PURCHASEORDERNO']}");
				}
			}
			return $bilagsnr;
		}
	}