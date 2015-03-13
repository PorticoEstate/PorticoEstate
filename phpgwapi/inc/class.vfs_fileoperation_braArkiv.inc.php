<?php
	/**
	* Fileoperation
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2014 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License v2 or later
	* @package phpgwapi
	* @subpackage vfs
	* @version $Id$
	*/

	/*
		This program is free software: you can redistribute it and/or modify
		it under the terms of the GNU Lesser General Public License as published by
		the Free Software Foundation, either version 2 of the License, or
		(at your option) any later version.

		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU Lesser General Public License for more details.

		You should have received a copy of the GNU Lesser General Public License
		along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/**
	 * Load autoload
	 */
	require_once PHPGW_API_INC . '/soap_client/bra5/Bra5Autoload.php';

	class phpgwapi_vfs_fileoperation_braArkiv
	{
		public  $external_ref = true;
		private $secKey;
		private $db;
		private $meta_types = array ('journal', 'journal-deleted');

		public function __construct()
		{
			$this->db = & $GLOBALS['phpgw']->db;
			$location_id		= $GLOBALS['phpgw']->locations->get_id('admin', 'vfs_filedata');

			$c	= CreateObject('admin.soconfig',$location_id);

			$section = 'braArkiv';
			$location_url = $c->config_data[$section]['location_url'];//'http://braarkiv.adm.bgo/service/services.asmx';
			$braarkiv_user =  $c->config_data[$section]['braarkiv_user'];
			$braarkiv_pass =  $c->config_data[$section]['braarkiv_pass'];

			if(!isset($c->config_data) || !$c->config_data)
			{
				$this->config = $c;
				$this->init_config();
			}

			if(!$location_url || !$braarkiv_pass || !$braarkiv_pass)
			{
				throw new Exception('braArkiv is not configured');
			}

			$wdsl = "{$location_url}?WSDL";
			$options = array();

			$options[Bra5WsdlClass::WSDL_URL] = $wdsl;
			$options[Bra5WsdlClass::WSDL_ENCODING] = 'UTF-8';
			$options[Bra5WsdlClass::WSDL_TRACE] = false;
			$options[Bra5WsdlClass::WSDL_SOAP_VERSION] = SOAP_1_2;

			$wsdlObject = new Bra5WsdlClass($options);

			$bra5ServiceLogin = new Bra5ServiceLogin();
			if($bra5ServiceLogin->Login(new Bra5StructLogin($braarkiv_user,$braarkiv_pass)))
			{
				$this->secKey = $bra5ServiceLogin->getResult()->getLoginResult()->LoginResult;
			}
			else
			{
				throw new Exception('vfs_fileoperation_braArkiv::Login failed');
			}
		}


		private function init_config()
		{
			$receipt_section = $this->config->add_section(array
				(
					'name' => 'braArkiv',
					'descr' => 'braArkiv'
				)
			);

			$receipt = $this->config->add_attrib(array
				(
					'section_id'	=> $receipt_section['section_id'],
					'input_type'	=> 'text',
					'name'			=> 'location_url',
					'descr'			=> 'Location url'
				)
			);
			$receipt = $this->config->add_attrib(array
				(
					'section_id'	=> $receipt_section['section_id'],
					'input_type'	=> 'text',
					'name'			=> 'braarkiv_user',
					'descr'			=> 'braArkiv user'
				)
			);

			$receipt = $this->config->add_attrib(array
				(
					'section_id'	=> $receipt_section['section_id'],
					'input_type'	=> 'password',
					'name'			=> 'braarkiv_pass',
					'descr'			=> 'braArkiv password'
				)
			);

			$GLOBALS['phpgw']->redirect_link('/index.php', array(
					'menuaction'	=> 'admin.uiconfig2.list_attrib',
					'section_id'	=> $receipt_section['section_id'],
					'location_id'	=> $GLOBALS['phpgw']->locations->get_id('admin', 'vfs_filedata')
				)
			);
		}

		/**
		* Get file id corresponding to braArkiv
		* @param object $p path_parts
		* @return integer file id
		*/
		private function get_file_id($p)
		{
			$sql = "SELECT external_id FROM phpgw_vfs WHERE  directory='{$p->fake_leading_dirs_clean}' AND name='{$p->fake_name_clean}'"
				. " AND ((mime_type != 'journal' AND mime_type != 'journal-deleted') OR mime_type IS NULL)";
//	_debug_array($sql);
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			return $this->db->f('external_id');
		}

		/**
		* Get filesize
		* @param object $path_parts path_parts
		* @return integer filesize
		*/
		public function filesize($path_parts)
		{
			$path = $path_parts->real_full_path;
			return filesize($path);
			}

		/**
		* Retreive file contents
		* @param object $p path_parts
		* @return String.  Contents of 'string', or False on error.
		*/
		public function read($p)
		{
			$fileid = $this->get_file_id($p);
			$file = false;

			if($fileid)
			{
				$bra5ServiceGet = new Bra5ServiceGet();

				if($bra5ServiceGet->getFileAsByteArray(new Bra5StructGetFileAsByteArray($this->secKey, $fileid)))
				{
//					_debug_array($bra5ServiceGet->getResult());
					$file_result = $bra5ServiceGet->getResult()->getFileAsByteArrayResult;
					$file = base64_decode($file_result->getFileAsByteArrayResult);
				}
				else
				{
					_debug_array($bra5ServiceGet->getLastError());
					$GLOBALS['phpgw']->common->phpgw_exit();
				}
			}
//die();
			return $file;
		}

		/**
		* Copy file from local dist to braArkiv
		* @param object $p path_parts
		* @return boolean.  True if copy is ok, False otherwise.
		*/
		public function copy($from, $to, $document_id = 0)
		{
/*
			//FIXME
			//$filesize = filesize($from->real_full_path);
			$content = $this->read($from);
			$document_id = $this->get_file_id($from);

			return $this->write($to, $content,$document_id);
*/

			if(!$document_id)
			{
				$document_id = $this->get_file_id($from);
			}

			$filesize = filesize($from->real_full_path);
			$content = false;
			if( $filesize  > 0 && $fp = fopen($from->real_full_path, 'rb'))
			{
				$content = fread($fp, $filesize);
				fclose ($fp);
			}

			return $this->write($to, $content,$document_id);


		}

		/**
		* Write content to braArkiv
		* @param object $p path_parts
		* @param string $content filecontent
		* @return boolean.  True if copy is ok, False otherwise.
		*/
		public function write($to, $content, $fileid = 0)
		{
			if(!$fileid)
			{
				$fileid = $this->get_file_id($to); //this represent the document
			}

			$bra5ServiceFile = new Bra5ServiceFile();
			if($bra5ServiceFile->fileTransferSendChunkedInit(new Bra5StructFileTransferSendChunkedInit($this->secKey, $fileid, $to->fake_name_clean)))
			{
				$transaction_id = $bra5ServiceFile->getResult()->getfileTransferSendChunkedInitResult()->fileTransferSendChunkedInitResult;
			}
			else
			{
				_debug_array($bra5ServiceFile->getLastError());
				die();
			}

			$new_string = chunk_split(base64_encode($content),1048576);// Definerer en bufferstørrelse/pakkestørrelse på ca 1mb.

			$content_arr = explode('\r\n', $new_string);

			foreach($content_arr as $content_part)
			{
				$bra5ServiceFile->fileTransferSendChunk(new Bra5StructFileTransferSendChunk($this->secKey, $transaction_id, $content_part));
			}

			
			$ok = !!$bra5ServiceFile->fileTransferSendChunkedEnd(new Bra5StructFileTransferSendChunkedEnd($this->secKey, $transaction_id));
/*
			_debug_array($bra5ServiceFile->getResult());
*/
//			die();

			if ( !$ok )
			{
				_debug_array($bra5ServiceFile->getLastError());
			}

//	_debug_array($fileid);
			return $ok;
		}


		/**
		* Create a document
		* @param object $p path_parts
		* @return integer.  The document_id
		*/
		public function touch($p)
		{
			static $check_document = array(); // only touch it once.
			
			if($check_document[$p>real_full_path])
			{
				return $check_document[$p>real_full_path];
			}

/*
			$bt = debug_backtrace();
			echo "<b>db::{$bt[0]['function']} Called from file: {$bt[0]['file']} line: {$bt[0]['line']}</b><br/>";
			unset($bt);
*/

/*
     * @uses Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVSTRING
     * @uses Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVLONGTEXT
     * @uses Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVINT
     * @uses Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVFLOAT
     * @uses Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVDATE
     * @uses Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVMATRIKKEL
     * @uses Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVADDRESS
     * @uses Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVPAIR
     * @uses Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVBOOLEAN
     * @uses Bra5EnumBraArkivAttributeType::VALUE_UNKNOWN
*/
/*
-	 * @param boolean $_bFDoubleSided
-    * @param boolean $_bFSeparateKeySheet
-    * @param boolean $_classified
-    * @param int $_priority
     * @param int $_productionLineID
     * @param int $_docSplitTypeID
     * @param Bra5StructArrayOfAttribute $_attributes
     * @param string $_iD
     * @param string $_bFDocKey
     * @param string $_bFNoSheets
     * @param string $_bBRegTime
     * @param string $_name
     * @param string $_className
     * @param string $_baseClassName

*/
			$document = new Bra5StructDocument();
			$document->setBFDoubleSided(false);
			$document->setBFSeparateKeySheet(false);
			$document->setClassified(false);
			$document->setPriority(0);
			$document->setProductionLineID(1);
//			$document->setDocSplitTypeID(0);
//			$document->setID('');
//			$document->setBFDocKey('');
//			$document->setBFNoSheets('');
			$document->setBBRegTime(date('Y-m-d'));
//			$document->setName('');
			$document->setClassName("Byggesak");
			$document->setBaseClassName("Eiendomsarkiver");

			$attributter = array();

			$att0 = new Bra5StructAttribute();
			$att0->setUsesLookupValues(false);
			$att0->setAttribType(Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVADDRESS);
			$att0->setName("Adresse");
		//	$att0->setValue(new Bra5StructArrayOfAnyType());
			$_gate = 'Lønborglien'; $_nummer = 285; $_bokstav = NULL;
			$att0->setValue(new Bra5StructArrayOfAnyType(new Bra5StructAddress($_gate, $_nummer, $_bokstav)));
			$attributter[] = $att0;


			$att1 = new Bra5StructAttribute();
			$att1->setUsesLookupValues(true);
			$att1->setAttribType(Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVSTRING);
			$att1->setName("Sakstype");
//			$att1->setValue(new Bra5StructArrayOfAnyType(100));
			$att1->setValue(array('100'));
			$attributter[] = $att1;

			$att2 = new Bra5StructAttribute();
			$att2->setUsesLookupValues(false);
			$att2->setAttribType(Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVSTRING);
			$att2->setName("ASTA_Signatur");
			$att2->setValue(new Bra5StructArrayOfAnyType("BBA/A-0430/H/Ha/L1002"));
			$attributter[] = $att2;

			$att3 = new Bra5StructAttribute();
			$att3->setUsesLookupValues(false);
			$att3->setAttribType(Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVMATRIKKEL);
			$att3->setName("Eiendom");
			$_gNr = 164;
			$_bNr = 1401;
			$_fNr = 0;
			$_sNr = 0;
			$att3->setValue(new Bra5StructArrayOfAnyType(new Bra5StructMatrikkel($_gNr, $_bNr, $_fNr, $_sNr)));
			$attributter[] = $att3;

			$att4 = new Bra5StructAttribute();
			$att4->setUsesLookupValues(false);
			$att4->setAttribType(Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVSTRING);
			$att4->setName("Byggnr");
		//	$att4->setValue(new Bra5StructArrayOfAnyType(array('139276655')));
			$att4->setValue(array('139276655'));

			//$att4->setValue("139276655");
			$attributter[] = $att4;

			$att5 = new Bra5StructAttribute();
			$att5->setUsesLookupValues(false);
			$att5->setAttribType(Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVSTRING);
			$att5->setName("Saksnr");
			$att5->setValue(array(1));

			$attributter[] = $att5;
/*
			$att6 = new Bra5StructAttribute();
			$att6->setUsesLookupValues(false);
			$att6->setAttribType(Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVDATE);
			$att6->setName("Saksdato");
			$att6->setValue(array(date('Y-m-d')));
			$attributter[] = $att6;

			$att7 = new Bra5StructAttribute();
			$att7->setUsesLookupValues(false);
			$att7->setAttribType(Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVDATE);
			$att7->setName("Dokumentdato");
			$att7->setValue(array(date('Y-m-d')));
			$attributter[] = $att7;
*/
			$att8 = new Bra5StructAttribute();
			$att8->setUsesLookupValues(true);
			$att8->setAttribType(Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVSTRING);
			$att8->setName("Tiltakstype");
			$att8->setValue( array('BOL3'));
			$attributter[] = $att8;

			$att9 = new Bra5StructAttribute();
			$att9->setUsesLookupValues(true);
			$att9->setAttribType(Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVSTRING);
			$att9->setName("Tiltaksart");
			$att9->setValue(array('10'));
			$attributter[] = $att9;

			$att10 = new Bra5StructAttribute();
			$att10->setUsesLookupValues(true);
			$att10->setAttribType(Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVSTRING);
			$att10->setName("Dokumentstatus");
			$att10->setValue( array('Gjeldende'));
			$attributter[] = $att10;

			$att11 = new Bra5StructAttribute();
			$att11->setUsesLookupValues(true);
			$att11->setAttribType(Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVSTRING);
			$att11->setName("Dokumentkategori");
			$att11->setValue(array('Søknad'));
			$attributter[] = $att11;

			$att12 = new Bra5StructAttribute();
			$att12->setUsesLookupValues(true);
			$att12->setAttribType(Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVSTRING);
			$att12->setName("Merknad");
			$att12->setValue( array(''));
			$attributter[] = $att12;
/*
			$att13 = new Bra5StructAttribute();
			$att13->setUsesLookupValues(true);
			$att13->setAttribType(Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVINT);
			$att13->setName("BrukerID");
			$att13->setValue(array(13));
			$attributter[] = $att13;
*/
			$att14 = new Bra5StructAttribute();
			$att14->setUsesLookupValues(true);
			$att14->setAttribType(Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVSTRING);
			$att14->setName("Gradering");
			$att14->setValue(array(''));
			$attributter[] = $att14;

			$document->setAttributes($attributter);

			$bra5ServiceCreate = new Bra5ServiceCreate();
			$bra5ServiceCreateDocument = new Bra5StructCreateDocument($_assignDocKey = false, $this->secKey, $document);
//			_debug_array($bra5ServiceCreateDocument);
//			die();
			if($bra5ServiceCreate->createDocument($bra5ServiceCreateDocument))
			{
//				_debug_array($bra5ServiceCreate->getResult());
			}
			else
			{
				_debug_array($bra5ServiceCreate->getLastError());
			}

			$document_id =  $bra5ServiceCreate->getResult()->getCreateDocumentResult()->getcreateDocumentResult()->ID;
			$check_document[$p>real_full_path] = $document_id;

//	_debug_array($document_id);
//die();
			return $document_id;

		}

		/**
		* Rename a file
		* @param object $from path_parts
		* @param object $to path_parts
		* @return boolean.  True if copy is ok, False otherwise.
		*/

		public function rename($from, $to)
		{
			$fileid = $this->get_file_id($from);

			$bra5ServiceGet = new Bra5ServiceGet();
			$bra5ServiceGet->getDocument(new Bra5StructGetDocument($this->secKey, $fileid));
			$document = $bra5ServiceGet->getResult()->getGetDocumentResult();
			
			foreach($document->Attributes as & $Attribute)
			{
				if($Attribute->getName() == 'Tittel')
				{
					$Attribute->setValue(array($to->fake_name_clean));
				}
			}
			$bra5ServiceUpdate = new Bra5ServiceUpdate();
			$ok = false;
			if(!$ok = $bra5ServiceUpdate->updateDocument(new Bra5StructUpdateDocument($this->secKey, $document)))
			{
				_debug_array($bra5ServiceUpdate->getResult());
			}
			return $ok;
		}

		/**
		* Deletes a file
		* @param object $p path_parts
		* @return boolean.  True if copy is ok, False otherwise.
		*/
		public function unlink($p)
		{
			$fileid = $this->get_file_id($p);

			$bra5ServiceDelete = new bra5ServiceDelete();
			
			if($bra5ServiceDelete->deleteDocument(new Bra5StructDeleteDocument($this->secKey,$fileid)))
			{
				return true;
			}
		}


		/**
		* check for existing file
		* @param object $p path_parts
		* @return boolean.  True if copy is ok, False otherwise.
		*/
		public function file_exists($p)
		{
			$fileid = $this->get_file_id($p);

			if($fileid)
			{
				$bra5ServiceGet = new Bra5ServiceGet();
				return !!$bra5ServiceGet->getFileAsByteArray(new Bra5StructGetFileAsByteArray($this->secKey, $fileid));
			}
		}

		/**
		* Removes directory
		* Does not apply to braArkiv
		* @return boolean.  True.
		*/
		public function rmdir($p)
		{
			return true;
		}

		/**
		* Does not apply to braArkiv
		* @return boolean.  True.
		*/
		public function check_target_directory($p)
		{
			return true;
		}

		/**
		* Does not apply to braArkiv
		* @return boolean.  True.
		*/
		public function auto_create_home($basedir)
		{
			return true;
		}

		/**
		* Does not apply to braArkiv
		* @return boolean.  True.
		*/
		public function is_dir($p)
		{
			return true;
		}

		/**
		* Does not apply to braArkiv
		* @return boolean.  True.
		*/
		public function mkdir($p)
		{
			return true;
		}

	}