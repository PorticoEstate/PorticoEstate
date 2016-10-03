<?php
	/**
	 * phpGroupWare - property: a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2016 Free Software Foundation, Inc. http://www.fsf.org/
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
	 * @subpackage helpdesk
	 * @version $Id$
	 */
	/**
	 * Description
	 * @package property
	 */


	/**
	 * Description of BkBygg_exporter_data_til_Agresso
	 *
	 * @author Sigurd Nes
	 */
	class BkBygg_exporter_data_til_Agresso
	{

		var $dim0; // Art
		var $dim1; // Ansvar
		var $dim2; // Tjeneste
		var $dim3; // Objekt
		var $dim4; // Kontrakt - frivillig
		var $dim5; // Prosjekt
		var $dim6; // Aktivitet - frivillig
		var $transfer_xml;
		var $connection;
		var $order_id;

		public function __construct( $param )
		{
			$this->db = & $GLOBALS['phpgw']->db;
			$this->soXport = CreateObject('property.soXport');
			$this->config = CreateObject('admin.soconfig', $GLOBALS['phpgw']->locations->get_id('property', '.invoice'));
			$this->order_id = $param['order_id'];
		}

		public function create_transfer_xml( $param )
		{
			$Orders = array();
			/*
			  UN-kodene i Agresso ligger med prefiks UN- foran koden.
			  Eks: UN-70111601: Plantetjenester
			 */

			$Seller = array(
				'Name' => $param['vendor_name'],
				'AddressInfo' => array(
					array(
						'Address' => $param['vendor_address']
					)
				),
				'SellerNo' => $param['vendor_id'],
//				'SellerReferences' => array(
//					array(
//						'SalesMan' => 12573,
//					)
//				)
			);

			$Header = array(
				'AcceptFlag' => 1,
				'OrderType]' => 'WB',
				'Status' => 'N',
				'OrderDate' => date('Y-m-d'),
				'Currency' => 'NOK',
				'Seller' => array($Seller),
				'Buyer' => array($param['buyer']),
			);

			$DetailInfo = array();
			$DetailInfo[] = array(
				'TaxCode' => $param['tax_code'] // Moms kode
			);
			$DetailInfo[] = array(
				'ReferenceCode' => array(
					'Code' => 'A0',
					'Value' => $param['dim0'] // Art
				)
			);
			$DetailInfo[] = array(
				'ReferenceCode' => array(
					'Code' => 'C1',
					'Value' => $param['dim1'] // Ansvar
				)
			);
			$DetailInfo[] = array(
				'ReferenceCode' => array(
					'Code' => 'Q0',
					'Value' => $param['dim2'] // Tjeneste
				)
			);
			$DetailInfo[] = array(
				'ReferenceCode' => array(
					'Code' => 'F0',
					'Value' => $param['dim3'] // Objekt
				)
			);
			$DetailInfo[] = array(
				'ReferenceCode' => array(
					'Code' => 'A7',
					'Value' => $param['dim4'] // Kontrakt
				)
			);
			$DetailInfo[] = array(
				'ReferenceCode' => array(
					'Code' => 'B0',
					'Value' => $param['dim5'] // Prosjekt
				)
			);
			$DetailInfo[] = array(
				'ReferenceCode' => array(
					'Code' => 'B1',
					'Value' => $param['dim6'] // Aktivitet
				)
			);
/*
			$DetailInfo[] = array(
				'ReferenceCode' => array(
					'Code' => 'A1',
					'Value' => $param['tax_code'] // Moms kode
				)
			);
*/
			$Detail = array();
			$i = 1;
			foreach ($param['lines'] as $line)
			{

				$Detail[] = array(
					'LineNo' => $i,
					'Status' => 'N',
					'BuyerProductCode' => $line['unspsc_code'], //74000176, //UN-kode
					'BuyerProductDescr' => $line['descr'], //'Kopipapir',
					'UnitCode' => 'STK',
					'Quantity' => 100,
					'Price' =>'',
					'Linetotal'=> '',
					'DetailInfo' => $DetailInfo
				);

				$i++;
			}


			$Orders['Order'][] = array(
				'OrderNo' => $param['order_id'],
				'VoucherType' => 'P3',
				'TransType' => 41,
				'Header' => array($Header),
				'Details' => array('Detail' => $Detail)
			);

//			_debug_array($Orders);
//			die();

			$root_attributes = array(
				'Version' => "542",
				'xmlns:xsi' => "http://www.w3.org/2001/XMLSchema-instance",
				'xsi:noNamespaceSchemaLocation' => "http://services.agresso.com/schema/ABWOrder/2004/07/02/ABWOrder.xsd"
			);
			$xml_creator = new xml_creator('ABWOrder', $root_attributes);
			$xml_creator->fromArray($Orders);
			$this->transfer_xml = $xml_creator->getDocument();
			return $this->transfer_xml;
	//		$xml_creator->output();
	//		die();
		}

		/**
		 * Output the content of a current xml document.
		 * @access public
		 * @param null
		 */
		public function output()
		{
			header('Content-type: text/xml');
			echo $this->transfer_xml;
		}


		protected function create_file_name( $ref = '' )
		{
			if (!$ref)
			{
				throw new Exception('BkBygg_exporter_data_til_Agresso::create_file_name() Mangler referanse');
			}
			$fil_katalog = $this->config->config_data['export']['path'];

			$filename = "{$fil_katalog}/FDV_ordre_{$ref}.xml";

			//Sjekk om filen eksisterer
			if (file_exists($filename))
			{
				unlink($filename);
			}

			return $filename;
		}

		public function transfer( $debug )
		{
			$this->db->transaction_begin();

			$filename = $this->create_file_name($this->order_id);
			$batchid = $this->soXport->increment_batchid();
			$content = $this->transfer_xml;

			if(false) // keep a copy?
			{
				$file_written = false;
				$fp = fopen($filename, "wb");
				fwrite($fp, $buffer);

				if (fclose($fp))
				{
					$file_written = true;
				}
			}

			$transfer_ok = false;
//			if ($this->config->config_data['common']['method'] == 'ftp' || $this->config->config_data['common']['method'] == 'ssh')
			if (!$debug)//Not yet...
			{
				if (!$connection = $this->connection)
				{
					$connection = $this->phpftp_connect();
				}

				$basedir = $this->config->config_data['export']['remote_basedir'];
				if ($basedir)
				{
					$remote_file = $basedir . '/' . basename($filename);
				}
				else
				{
					$remote_file = basename($filename);
				}

				switch ($this->config->config_data['common']['method'])
				{
					case 'ftp';
						$tmp = tmpfile();
						fwrite($tmp, $content);
						rewind($tmp);
						$transfer_ok = ftp_fput($connection, $remote_file, $tmp, FTP_BINARY);
						fclose($tmp);
					//	$transfer_ok = ftp_put($connection, $remote_file, $filename, FTP_BINARY);
						break;
					case 'ssh';
						$sftp = ssh2_sftp($connection);
						$stream = @fopen("ssh2.sftp://$sftp$remote_file", 'w');
						fwrite($stream, $content);
						$transfer_ok = @fclose($stream);
						break;
					default:
						$transfer_ok = false;
				}
				if ($transfer_ok)
				{
					$this->soXport->log_transaction($batchid, $this->order_id, lang('transferred Order %1 to Agresso', basename($filename)));
					$this->db->transaction_commit(); // Reverse the batch_id - increment
				}
				else
				{
					$this->db->transaction_abort();
					$this->soXport->log_transaction($batchid, $this->order_id, lang('Failed to transfere Order %1 to Agresso', basename($filename)));
			//		@unlink($filename);
				}
			}
			else
			{
				$transfer_ok = true;

				$GLOBALS['phpgw_info']['flags']['noheader'] = true;
				$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
				$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;
				$size = strlen($content);
				$browser = CreateObject('phpgwapi.browser');
				$browser->content_header(basename($filename), '', $size);
				echo $content;
			}
			return $transfer_ok;
		}

		function phpftp_connect()
		{
			$server = $this->config->config_data['common']['host'];
			$user = $this->config->config_data['common']['user'];
			$password = $this->config->config_data['common']['password'];
			$port = 22;

			switch ($this->config->config_data['common']['method'])
			{
				case 'ftp';
					if ($connection = ftp_connect($server))
					{
						ftp_login($connection, $user, $password);
					}
					break;
				case 'ssh';
					if (!function_exists("ssh2_connect"))
					{
						die("function ssh2_connect doesn't exist");
					}
					if (!($connection = ssh2_connect("$server", $port)))
					{
						$message = "fail: unable to establish connection";
						_debug_array($message);
						//$receipt['error'][]= array('msg' => $message);
					}
					else
					{
						// try to authenticate with username root, password secretpassword
						if (!ssh2_auth_password($connection, $user, $password))
						{
							$message = "fail: unable to authenticate";
							_debug_array($message);
							//$receipt['error'][]= array('msg' => $message);
						}
					}
					break;
			}
			$this->connection = $connection;
			return $connection;
		}
	}

	class xml_creator extends XMLWriter
	{

		/**
		 * Constructor.
		 * @param string $prm_rootElementName A root element's name of a current xml document
		 * @param ARRAY $root_attributtes array of root attributes.
		 * @param string $prm_xsltFilePath Path of a XSLT file.
		 * @access public
		 * @param null
		 */
		public function __construct( $prm_rootElementName, $root_attributes = array(), $prm_xsltFilePath = '' )
		{
			$this->openMemory();
			$this->setIndent(true);
			$this->setIndentString(' ');
			$this->startDocument('1.0', 'UTF-8');

			if ($prm_xsltFilePath)
			{
				$this->writePi('xml-stylesheet', 'type="text/xsl" href="' . $prm_xsltFilePath . '"');
			}

			$this->startElement($prm_rootElementName);

			foreach ($root_attributes as $key => $value)
			{
				$this->writeAttribute($key, $value);
			}
		}

		/**
		 * Set an element with a text to a current xml document.
		 * @access public
		 * @param string $prm_elementName An element's name
		 * @param string $prm_ElementText An element's text
		 * @return null
		 */
		public function setElement( $prm_elementName, $prm_ElementText )
		{
			$this->startElement($prm_elementName);
			$this->text($prm_ElementText);
			$this->endElement();
		}

		/**
		 * Construct elements and texts from an array.
		 * The array should contain an attribute's name in index part
		 * and a attribute's text in value part.
		 * @access public
		 * @param array $prm_array Contains attributes and texts
		 * @return null
		 */
		public function fromArray( array $array )
		{
			foreach ($array as $key => $val)
			{
				if (is_array($val))
				{
					if (is_numeric($key))
					{
						// numeric keys aren't allowed so we'll skip the key
						$this->fromArray($val);
					}
					else
					{
						$this->startElement($key);
						$this->fromArray($val);
						$this->endElement();
					}
				}
				else
				{
					$this->writeElement($key, $val);
				}
			}
		}

		/**
		 * Return the content of a current xml document.
		 * @access public
		 * @param null
		 * @return string Xml document
		 */
		public function getDocument()
		{
			$this->endElement();
			$this->endDocument();
			return $this->outputMemory();
		}

		/**
		 * Output the content of a current xml document.
		 * @access public
		 * @param null
		 */
		public function output()
		{
			header('Content-type: text/xml');
			echo $this->getDocument();
		}
	}