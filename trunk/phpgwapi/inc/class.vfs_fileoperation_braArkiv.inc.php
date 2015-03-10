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
		private $Services;
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

			$options=array();
			$options['soap_version']	= SOAP_1_2;
			$options['location']		= $location_url;
			$options['uri']				= $location_url;
			$options['trace']			= false;
			$options['encoding']		= 'UTF-8';

			$wdsl = "{$location_url}?WSDL";
			$Services = new Services($wdsl, $options);
			$Login = new Login();
			$Login->userName = $braarkiv_user;
			$Login->password = $braarkiv_pass;
			$LoginResponse = $Services->Login($Login);
			$secKey = $LoginResponse->LoginResult;
			$this->Services = $Services;
			$this->secKey = $secKey;
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
				$getAvailableFileVariants = new getAvailableFileVariants();
				$getAvailableFileVariants->secKey = $this->secKey;
				$getAvailableFileVariants->documentId = $fileid;

				$getAvailableFileVariantsResponse = $this->Services->getAvailableFileVariants($getAvailableFileVariants);

				$getFileAsByteArray = new getFileAsByteArray();
				$getFileAsByteArray->secKey = $this->secKey;
				$getFileAsByteArray->documentId = $fileid;
				$getFileAsByteArray->variant = $getAvailableFileVariantsResponse->getAvailableFileVariantsResult->string[0];
				$getFileAsByteArray->versjon = 1;

				$getFileAsByteArrayResponse = $this->Services->getFileAsByteArray($getFileAsByteArray);

				$getFileAsByteArrayResult = $getFileAsByteArrayResponse->getFileAsByteArrayResult;

				if($getFileAsByteArrayResult)
				{
					$file = base64_decode($getFileAsByteArrayResult);
				}
			}

			return $file;
		}

		/**
		* Copy file from local dist to braArkiv
		* @param object $p path_parts
		* @return boolean.  True if copy is ok, False otherwise.
		*/
		public function copy($from, $to)
		{
			$document_id = $this->touch($to);//creates the document

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
/*			
			$putFileAsByteArray = new putFileAsByteArray();
			$putFileAsByteArray->secKey = $this->secKey;
			$putFileAsByteArray->documentId = $fileid;
			$putFileAsByteArray->filename = $to->fake_name_clean;
			$putFileAsByteArray->file = base64_encode($content);
			$putFileAsByteArrayResponse = $this->Services->putFileAsByteArray($putFileAsByteArray);
*/
			$fileTransferSendChunkedInit = new fileTransferSendChunkedInit();
			$fileTransferSendChunkedInit->secKey = $this->secKey;
			$fileTransferSendChunkedInit->docid = $fileid;
			$fileTransferSendChunkedInit->filename = $to->fake_name_clean;

			$fileTransferSendChunkedInitResponse = $this->Services->fileTransferSendChunkedInit($fileTransferSendChunkedInit);
			$transaction_id = $fileTransferSendChunkedInitResponse->fileTransferSendChunkedInitResult;
//			_debug_array($transaction_id);die();
			$new_string = chunk_split(base64_encode($content),1048576);// Definerer en bufferstørrelse/pakkestørrelse på ca 1mb.

			$content_arr = explode('\r\n', $new_string);

			foreach($content_arr as $content_part)
			{
				$fileTransferSendChunk = new fileTransferSendChunk();
				$fileTransferSendChunk->secKey = $this->secKey;
				$fileTransferSendChunk->fileid = $transaction_id; //internal transcation id - not the file/document id
				$fileTransferSendChunk->chunk = $content_part;
				
				$this->Services->fileTransferSendChunk($fileTransferSendChunk);
			}
			
			$fileTransferSendChunkedEnd = new fileTransferSendChunkedEnd();
			$fileTransferSendChunkedEnd->secKey = $this->secKey;
			$fileTransferSendChunkedEnd->fileid = $transaction_id;

			try
			{
				$fileTransferSendChunkedEndResponse = $this->Services->fileTransferSendChunkedEnd($fileTransferSendChunkedEnd);
			}

			catch(Exception $e)
			{
				if ( $e )
				{
					throw $e;
				}
			}

			return true;
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
				return true;
			}

			$check_document[$p>real_full_path] = true;
/*
			$bt = debug_backtrace();
			echo "<b>db::{$bt[0]['function']} Called from file: {$bt[0]['file']} line: {$bt[0]['line']}</b><br/>";
			unset($bt);
*/
			$document = new Document();
			$document->BBRegTime = date('Y-m-d');
			$document->BaseClassName = "Eiendomsarkiver";
			$document->ClassName = "Byggesak";

			$attributter = array();

			$att1 = new Attribute();
			$att1->AttribType = 'braArkivDate';
			$att1->Name = "Saksdato";
//			$att1->Value = array(date('Y-m-d'));
			$att1->Value = date('Y-m-d');
			$attributter[] = $att1;

			$att2 = new Attribute();
			$att2->AttribType = 'braArkivString';
			$att2->Name = "Tiltakstype";
			$att2->Value = array("Testtittel");
			$att2->Value = "Tiltakstype";
			$attributter[] = $att2;

			$att3 = new Attribute();
			$att3->AttribType = 'braArkivString';
			$att3->Name = "Tiltaksart";
//			$att3->Value = array("Testtittel");
			$att3->Value = 'Tiltaksart';
			$attributter[] = $att3;

			$att4 = new Attribute();
			$att4->AttribType = 'braArkivString';
			$att4->Name = "ASTA_Signatur";
			$att4->Value = array("1");
			$att4->Value = "ASTA_Signatur";
			$attributter[] = $att4;

			$att5 = new Attribute();
			$att5->AttribType = 'braArkivDate';
			$att5->Name = "Dokumentdato";
//			$att5->Value = array(date('Y-m-d'));
			$att5->Value = date('Y-m-d');
			$attributter[] = $att5;

			$att6 = new Attribute();
			$att6->AttribType = 'braArkivString';
			$att6->Name = "BrukerID";
//			$att6->Value = array("1");
			$att6->Value = "BrukerID";
			$attributter[] = $att6;

			$att7 = new Attribute();
			$att7->AttribType = 'braArkivString';
			$att7->Name = "Team";
//			$att7->Value = array("Testtittel");
			$att7->Value = "Team";
			$attributter[] = $att7;

			$att8 = new Attribute();
			$att8->AttribType = 'braArkivString';
			$att8->Name = "Sakstype";
//			$att8->Value = array("Testtittel");
			$att8->Value = "Sakstype";
			$attributter[] = $att8;

			$att9 = new Attribute();
			$att9->AttribType = 'braArkivString';
			$att9->Name = "Dokumentkategori";
//			$att9->Value = array("Testtittel");
			$att9->Value = "Dokumentkategori";
			$attributter[] = $att9;

			$att10 = new Attribute();
			$att10->AttribType = 'braArkivString';
			$att10->Name = "Dokumentstatus";
			$att10->Value = array("Testtittel");
			$att10->Value = "Dokumentstatus";
			$attributter[] = $att10;
	
			$document->Attributes = $attributter;

			$createDocument = new createDocument();
			$createDocument->secKey = $this->secKey;
			$createDocument->assignDocKey = 0;
			$createDocument->doc = $document;

//_debug_array($createDocument);//die();

			$createDocumentResponse = $this->Services->createDocument($createDocument);
			$document_id =  $createDocumentResponse->createDocumentResult->ID;
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
			
			$getDocument = new getDocument();
			$getDocument->secKey = $this->secKey;
			$getDocument->documentId = $fileid;
			$getDocumentResponse = $this->Services->getDocument($getDocument);
			
			$document = $getDocumentResponse->getDocumentResult;
			
			foreach($document->Attributes as & $Attribute)
			{
				if($Attribute->Name == 'Tittel')
				{
					$Attribute->Value = $to->fake_name_clean;
				}
			}
			$updateDocument = new $updateDocument();
			$updateDocument->secKey = $this->secKey;
			$updateDocument->document = $document;

			try
			{
				$updateDocumentResponse = $this->Services->updateDocument($getDocument);
			}

			catch(Exception $e)
			{
				if ( $e )
				{
					throw $e;
				}
			}

			return true;
		}

		/**
		* Deletes a file
		* @param object $p path_parts
		* @return boolean.  True if copy is ok, False otherwise.
		*/
		public function unlink($p)
		{
			$fileid = $this->get_file_id($p);
			$deleteDocument = new deleteDocument();
			$deleteDocument->secKey = $this->secKey;
			$deleteDocument->documentId = $fileid;

			try
			{
				$deleteDocumentResponse = $this->Services->deleteDocument($deleteDocument);
			}

			catch(Exception $e)
			{
				if ( $e )
				{
					throw $e;
				}
			}

			return true;
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
				$getAvailableFileVariants = new getAvailableFileVariants();
				$getAvailableFileVariants->secKey = $this->secKey;
				$getAvailableFileVariants->documentId = $fileid;

				$getAvailableFileVariantsResponse = $this->Services->getAvailableFileVariants($getAvailableFileVariants);
			}

			return !!$getAvailableFileVariantsResponse->getAvailableFileVariantsResult->string[0];
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