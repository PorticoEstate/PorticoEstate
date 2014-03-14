<?php
	/**
	* Fileoperation
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2014 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License v2 or later
	* @package phpgwapi
	* @subpackage vfs
	* @version $Id: class.vfs_fileoperation_braArkiv.inc.php 11804 2014-03-11 11:39:45Z sigurdne $
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
		* @param object $p path_parts
		* @return integer filesize
		*/
		public function filesize($p)
		{
			$sql = "SELECT size FROM phpgw_vfs WHERE  directory='{$p->fake_leading_dirs_clean}' AND name='{$p->fake_name_clean}'"
				. " AND ((mime_type != 'journal' AND mime_type != 'journal-deleted') OR mime_type IS NULL)"
				. " ORDER BY file_id ASC";//Get the latest version.
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();

			return $this->db->f('size');
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
			$this->touch($to);//creates the document

			$filesize = filesize($from->real_full_path);
			$content = false;
			if( $filesize  > 0 && $fp = fopen($from->real_full_path, 'rb'))
			{
				$content = fread($fp, $filesize);
				fclose ($fp);
			}

			return $this->write($to, $content);

		}

		/**
		* Write content to braArkiv
		* @param object $p path_parts
		* @param string $content filecontent
		* @return boolean.  True if copy is ok, False otherwise.
		*/
		public function write($to, $content)
		{
			$fileid = $this->get_file_id($to); //this represent the document

			$fileTransferSendChunkedInit = new fileTransferSendChunkedInit();
			$fileTransferSendChunkedInit->secKey = $this->secKey;
			$fileTransferSendChunkedInit->docid = $fileid;
			$fileTransferSendChunkedInit->filename = $to->fake_name_clean;

			$fileTransferSendChunkedInitResponse = $this->Services->fileTransferSendChunkedInit($fileTransferSendChunkedInit);
			$transaction_id = $fileTransferSendChunkedInitResponse->fileTransferSendChunkedInitResult;

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
			$document = new Document();
			$document->BBRegTime = date('Y-m-d');
			$document->BaseClassName = "Eiendomsarkiver";
			$document->ClassName = "Byggesak";

			$attributter = array();

			$att1 = new Attribute();
			$att1->AttribType = 'braArkivDate';
			$att1->Name = "Saksdato";
			$att1->Value = array(date('Y-m-d'));
			$attributter[] = $att1;

			$att2 = new Attribute();
			$att2->AttribType = 'braArkivString';
			$att2->Name = "Tiltakstype";
			$att2->Value = array("Testtittel");
			$attributter[] = $att2;

			$att3 = new Attribute();
			$att3->AttribType = 'braArkivString';
			$att3->Name = "Tiltaksart";
			$att3->Value = array("Testtittel");
			$attributter[] = $att3;

			$att4 = new Attribute();
			$att4->AttribType = 'braArkivString';
			$att4->Name = "ASTA_Signatur";
			$att4->Value = array("1");
			$attributter[] = $att4;

			$att5 = new Attribute();
			$att5->AttribType = 'braArkivDate';
			$att5->Name = "Dokumentdato";
			$att5->Value = array(date('Y-m-d'));
			$attributter[] = $att5;

			$att6 = new Attribute();
			$att6->AttribType = 'braArkivString';
			$att6->Name = "BrukerID";
			$att6->Value = array("1");
			$attributter[] = $att6;

			$att7 = new Attribute();
			$att7->AttribType = 'braArkivString';
			$att7->Name = "Team";
			$att7->Value = array("Testtittel");
			$attributter[] = $att7;

			$att8 = new Attribute();
			$att8->AttribType = 'braArkivString';
			$att8->Name = "Sakstype";
			$att8->Value = array("Testtittel");
			$attributter[] = $att8;

			$att9 = new Attribute();
			$att9->AttribType = 'braArkivString';
			$att9->Name = "Dokumentkategori";
			$att9->Value = array("Testtittel");
			$attributter[] = $att9;

			$att10 = new Attribute();
			$att10->AttribType = 'braArkivString';
			$att10->Name = "Dokumentstatus";
			$att10->Value = array("Testtittel");
			$attributter[] = $att10;
	
			$document->Attributes = $attributter;

			$createDocument = new createDocument();
			$createDocument->secKey = $this->secKey;
			$createDocument->assignDocKey = 0;
			$createDocument->doc = $document;

	//		_debug_array($createDocument);die();

			$createDocumentResponse = $this->Services->createDocument($createDocument);
			$document_id =  $createDocumentResponse->createDocumentResult;
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

	/**
	 * soap client for http://geomatikk.no/ braArkiv
	 * this code is generated by the http://code.google.com/p/wsdl2php-interpreter/
	 *
	 * @package phpgwapi
	 * @subpackage vfs
	 */

	if ( !class_exists( "Login" ) )
	{

		/**
		 * Login
		 */
		class Login
		{

			/**
			 * @access public
			 * @var sstring
			 */
			public $userName;

			/**
			 * @access public
			 * @var sstring
			 */
			public $password;
		}

	}

	if ( !class_exists( "LoginResponse" ) )
	{

		/**
		 * LoginResponse
		 */
		class LoginResponse
		{

			/**
			 * @access public
			 * @var sstring
			 */
			public $LoginResult;

		}

	}

	if ( !class_exists( "Logout" ) )
	{

		/**
		 * Logout
		 */
		class Logout
		{

			/**
			 * @access public
			 * @var sstring
			 */
			public $secKey;

		}

	}

	if ( !class_exists( "LogoutResponse" ) )
	{

		/**
		 * LogoutResponse
		 */
		class LogoutResponse
		{

		}

	}

	if ( !class_exists( "getProductionLines" ) )
	{

		/**
		 * getProductionLines
		 */
		class getProductionLines
		{

			/**
			 * @access public
			 * @var sstring
			 */
			public $seckey;

		}

	}

	if ( !class_exists( "getProductionLinesResponse" ) )
	{

		/**
		 * getProductionLinesResponse
		 */
		class getProductionLinesResponse
		{

			/**
			 * @access public
			 * @var ArrayOfProductionLine
			 */
			public $getProductionLinesResult;

		}

	}

	if ( !class_exists( "ProductionLine" ) )
	{

		/**
		 * ProductionLine
		 */
		class ProductionLine
		{

			/**
			 * @access public
			 * @var sint
			 */
			public $ProductionLineID;

			/**
			 * @access public
			 * @var sstring
			 */
			public $Name;

			/**
			 * @access public
			 * @var sboolean
			 */
			public $Enabled;

			/**
			 * @access public
			 * @var sboolean
			 */
			public $Default;

		}

	}

	if ( !class_exists( "getDocumentSplitTypes" ) )
	{

		/**
		 * getDocumentSplitTypes
		 */
		class getDocumentSplitTypes
		{

			/**
			 * @access public
			 * @var sstring
			 */
			public $seckey;

			/**
			 * @access public
			 * @var sint
			 */
			public $docClassID;

		}

	}

	if ( !class_exists( "getDocumentSplitTypesResponse" ) )
	{

		/**
		 * getDocumentSplitTypesResponse
		 */
		class getDocumentSplitTypesResponse
		{

			/**
			 * @access public
			 * @var ArrayOfDocumentSplitType
			 */
			public $getDocumentSplitTypesResult;

		}

	}

	if ( !class_exists( "DocumentSplitType" ) )
	{

		/**
		 * DocumentSplitType
		 */
		class DocumentSplitType
		{

			/**
			 * @access public
			 * @var sint
			 */
			public $DocSplitTypeID;

			/**
			 * @access public
			 * @var sint
			 */
			public $DocClassID;

			/**
			 * @access public
			 * @var sboolean
			 */
			public $IsConcatDocument;

			/**
			 * @access public
			 * @var sstring
			 */
			public $Name;

			/**
			 * @access public
			 * @var sint
			 */
			public $SplitAttributeID;

			/**
			 * @access public
			 * @var sboolean
			 */
			public $Active;

			/**
			 * @access public
			 * @var sint
			 */
			public $NewDocSplitTypeID;

			/**
			 * @access public
			 * @var sboolean
			 */
			public $Default;

		}

	}

	if ( !class_exists( "GetClasses" ) )
	{

		/**
		 * GetClasses
		 */
		class GetClasses
		{

			/**
			 * @access public
			 * @var sstring
			 */
			public $secKey;

			/**
			 * @access public
			 * @var sstring
			 */
			public $userName;

		}

	}

	if ( !class_exists( "GetClassesResponse" ) )
	{

		/**
		 * GetClassesResponse
		 */
		class GetClassesResponse
		{

			/**
			 * @access public
			 * @var anyType
			 */
			public $GetClassesResult;

			/**
			 * @access public
			 * @var sschema
			 */
			public $schema;

		}

	}

	if ( !class_exists( "GetClassesResult" ) )
	{

		/**
		 * GetClassesResult
		 */
		class GetClassesResult
		{

			/**
			 * @access public
			 * @var sschema
			 */
			public $schema;

		}

	}

	if ( !class_exists( "getAvailableFileVariants" ) )
	{

		/**
		 * getAvailableFileVariants
		 */
		class getAvailableFileVariants
		{

			/**
			 * @access public
			 * @var sstring
			 */
			public $secKey;

			/**
			 * @access public
			 * @var sstring
			 */
			public $documentId;

		}

	}

	if ( !class_exists( "getAvailableFileVariantsResponse" ) )
	{

		/**
		 * getAvailableFileVariantsResponse
		 */
		class getAvailableFileVariantsResponse
		{

			/**
			 * @access public
			 * @var ArrayOfString
			 */
			public $getAvailableFileVariantsResult;

		}

	}

	if ( !class_exists( "getVariantVaultID" ) )
	{

		/**
		 * getVariantVaultID
		 */
		class getVariantVaultID
		{

			/**
			 * @access public
			 * @var sstring
			 */
			public $secKey;

			/**
			 * @access public
			 * @var sstring
			 */
			public $documentId;

			/**
			 * @access public
			 * @var sstring
			 */
			public $variantName;

		}

	}

	if ( !class_exists( "getVariantVaultIDResponse" ) )
	{

		/**
		 * getVariantVaultIDResponse
		 */
		class getVariantVaultIDResponse
		{

			/**
			 * @access public
			 * @var sstring
			 */
			public $getVariantVaultIDResult;

		}

	}

	if ( !class_exists( "getRelativeFileURL" ) )
	{

		/**
		 * getRelativeFileURL
		 */
		class getRelativeFileURL
		{

			/**
			 * @access public
			 * @var sstring
			 */
			public $secKey;

			/**
			 * @access public
			 * @var sstring
			 */
			public $documentId;

			/**
			 * @access public
			 * @var sstring
			 */
			public $variantName;

		}

	}

	if ( !class_exists( "getRelativeFileURLResponse" ) )
	{

		/**
		 * getRelativeFileURLResponse
		 */
		class getRelativeFileURLResponse
		{

			/**
			 * @access public
			 * @var sstring
			 */
			public $getRelativeFileURLResult;

		}

	}

	if ( !class_exists( "getAvailableAttributes" ) )
	{

		/**
		 * getAvailableAttributes
		 */
		class getAvailableAttributes
		{

			/**
			 * @access public
			 * @var sstring
			 */
			public $secKey;

			/**
			 * @access public
			 * @var sstring
			 */
			public $baseclassname;

			/**
			 * @access public
			 * @var sstring
			 */
			public $classname;

		}

	}

	if ( !class_exists( "getAvailableAttributesResponse" ) )
	{

		/**
		 * getAvailableAttributesResponse
		 */
		class getAvailableAttributesResponse
		{

			/**
			 * @access public
			 * @var ArrayOfAttribute
			 */
			public $getAvailableAttributesResult;

		}

	}

	if ( !class_exists( "Attribute" ) )
	{

		/**
		 * Attribute
		 */
		class Attribute
		{

			/**
			 * @access public
			 * @var sstring
			 */
			public $Name;

			/**
			 * @access public
			 * @var sboolean
			 */
			public $UsesLookupValues = false;

			/**
			 * @access public
			 * @var tnsbraArkivAttributeType
			 */
			public $AttribType;

			/**
			 * @access public
			 * @var ArrayOfAnyType
			 */
			public $Value;

		}

	}

	if ( !class_exists( "braArkivAttributeType" ) )
	{

		/**
		 * braArkivAttributeType
		 */
		class braArkivAttributeType
		{

		}

	}

	if ( !class_exists( "LookupValue" ) )
	{

		/**
		 * LookupValue
		 */
		class LookupValue
		{

			/**
			 * @access public
			 * @var sstring
			 */
			public $Id;

			/**
			 * @access public
			 * @var sstring
			 */
			public $Description;

		}

	}

	if ( !class_exists( "Pair" ) )
	{

		/**
		 * Pair
		 */
		class Pair
		{

			/**
			 * @access public
			 * @var sstring
			 */
			public $Kode;

			/**
			 * @access public
			 * @var sstring
			 */
			public $Beskrivelse;

		}

	}

	if ( !class_exists( "Address" ) )
	{

		/**
		 * Address
		 */
		class Address
		{

			/**
			 * @access public
			 * @var sstring
			 */
			public $Gate;

			/**
			 * @access public
			 * @var sstring
			 */
			public $Nummer;

			/**
			 * @access public
			 * @var sstring
			 */
			public $Bokstav;

		}

	}

	if ( !class_exists( "Matrikkel" ) )
	{

		/**
		 * Matrikkel
		 */
		class Matrikkel
		{

			/**
			 * @access public
			 * @var sstring
			 */
			public $GNr;

			/**
			 * @access public
			 * @var sstring
			 */
			public $BNr;

			/**
			 * @access public
			 * @var sstring
			 */
			public $FNr;

			/**
			 * @access public
			 * @var sstring
			 */
			public $SNr;

		}

	}

	if ( !class_exists( "getLookupValues" ) )
	{

		/**
		 * getLookupValues
		 */
		class getLookupValues
		{

			/**
			 * @access public
			 * @var sstring
			 */
			public $secKey;

			/**
			 * @access public
			 * @var sstring
			 */
			public $baseclassname;

			/**
			 * @access public
			 * @var sstring
			 */
			public $classname;

			/**
			 * @access public
			 * @var sstring
			 */
			public $attribname;

		}

	}

	if ( !class_exists( "getLookupValuesResponse" ) )
	{

		/**
		 * getLookupValuesResponse
		 */
		class getLookupValuesResponse
		{

			/**
			 * @access public
			 * @var ArrayOfLookupValue
			 */
			public $getLookupValuesResult;

		}

	}

	if ( !class_exists( "searchDocument" ) )
	{

		/**
		 * searchDocument
		 */
		class searchDocument
		{

			/**
			 * @access public
			 * @var sstring
			 */
			public $secKey;

			/**
			 * @access public
			 * @var sstring
			 */
			public $baseclassname;

			/**
			 * @access public
			 * @var sstring
			 */
			public $classname;

			/**
			 * @access public
			 * @var sstring
			 */
			public $where;

			/**
			 * @access public
			 * @var sstring
			 */
			public $maxhits;

		}

	}

	if ( !class_exists( "searchDocumentResponse" ) )
	{

		/**
		 * searchDocumentResponse
		 */
		class searchDocumentResponse
		{

			/**
			 * @access public
			 * @var ArrayOfString
			 */
			public $searchDocumentResult;

		}

	}

	if ( !class_exists( "createDocument" ) )
	{

		/**
		 * createDocument
		 */
		class createDocument
		{

			/**
			 * @access public
			 * @var sstring
			 */
			public $secKey;

			/**
			 * @access public
			 * @var sboolean
			 */
			public $assignDocKey;

			/**
			 * @access public
			 * @var Document
			 */
			public $doc;

		}

	}

	if ( !class_exists( "Document" ) )
	{

		/**
		 * Document
		 */
		class Document
		{

			/**
			 * @access public
			 * @var ArrayOfAttribute
			 */
			public $Attributes;

			/**
			 * @access public
			 * @var sstring
			 */
			public $ID = '';

			/**
			 * @access public
			 * @var sstring
			 */
			public $BFDocKey = '';

			/**
			 * @access public
			 * @var sstring
			 */
			public $BFNoSheets = '';

			/**
			 * @access public
			 * @var sboolean
			 */
			public $BFDoubleSided = false;

			/**
			 * @access public
			 * @var sboolean
			 */
			public $BFSeparateKeySheet = false;

			/**
			 * @access public
			 * @var sstring
			 */
			public $BBRegTime;

			/**
			 * @access public
			 * @var sstring
			 */
			public $Name;

			/**
			 * @access public
			 * @var sboolean
			 */
			public $Classified = false;

			/**
			 * @access public
			 * @var sint
			 */
			public $Priority = 0;

			/**
			 * @access public
			 * @var sint
			 */
			public $ProductionLineID;

			/**
			 * @access public
			 * @var sint
			 */
			public $DocSplitTypeID;

			/**
			 * @access public
			 * @var sstring
			 */
			public $ClassName;

			/**
			 * @access public
			 * @var sstring
			 */
			public $BaseClassName;

		}

	}

	if ( !class_exists( "createDocumentResponse" ) )
	{

		/**
		 * createDocumentResponse
		 */
		class createDocumentResponse
		{

			/**
			 * @access public
			 * @var Document
			 */
			public $createDocumentResult;

		}

	}

	if ( !class_exists( "getDocument" ) )
	{

		/**
		 * getDocument
		 */
		class getDocument
		{

			/**
			 * @access public
			 * @var sstring
			 */
			public $secKey;

			/**
			 * @access public
			 * @var sstring
			 */
			public $documentId;

		}

	}

	if ( !class_exists( "getDocumentResponse" ) )
	{

		/**
		 * getDocumentResponse
		 */
		class getDocumentResponse
		{

			/**
			 * @access public
			 * @var Document
			 */
			public $getDocumentResult;

		}

	}

	if ( !class_exists( "getAttribute" ) )
	{

		/**
		 * getAttribute
		 */
		class getAttribute
		{

			/**
			 * @access public
			 * @var sstring
			 */
			public $secKey;

			/**
			 * @access public
			 * @var sstring
			 */
			public $documentId;

			/**
			 * @access public
			 * @var sstring
			 */
			public $attributeName;

		}

	}

	if ( !class_exists( "getAttributeResponse" ) )
	{

		/**
		 * getAttributeResponse
		 */
		class getAttributeResponse
		{

			/**
			 * @access public
			 * @var Attribute
			 */
			public $getAttributeResult;

		}

	}

	if ( !class_exists( "updateDocument" ) )
	{

		/**
		 * updateDocument
		 */
		class updateDocument
		{

			/**
			 * @access public
			 * @var sstring
			 */
			public $secKey;

			/**
			 * @access public
			 * @var Document
			 */
			public $document;

		}

	}

	if ( !class_exists( "updateDocumentResponse" ) )
	{

		/**
		 * updateDocumentResponse
		 */
		class updateDocumentResponse
		{

			/**
			 * @access public
			 * @var Document
			 */
			public $updateDocumentResult;

		}

	}

	if ( !class_exists( "updateAttribute" ) )
	{

		/**
		 * updateAttribute
		 */
		class updateAttribute
		{

			/**
			 * @access public
			 * @var sstring
			 */
			public $secKey;

			/**
			 * @access public
			 * @var sstring
			 */
			public $baseclassname;

			/**
			 * @access public
			 * @var sstring
			 */
			public $documentId;

			/**
			 * @access public
			 * @var sstring
			 */
			public $attribute;

			/**
			 * @access public
			 * @var ArrayOfAnyType
			 */
			public $value;

		}

	}

	if ( !class_exists( "updateAttributeResponse" ) )
	{

		/**
		 * updateAttributeResponse
		 */
		class updateAttributeResponse
		{

		}

	}

	if ( !class_exists( "deleteDocument" ) )
	{

		/**
		 * deleteDocument
		 */
		class deleteDocument
		{

			/**
			 * @access public
			 * @var sstring
			 */
			public $secKey;

			/**
			 * @access public
			 * @var sstring
			 */
			public $documentId;

		}

	}

	if ( !class_exists( "deleteDocumentResponse" ) )
	{

		/**
		 * deleteDocumentResponse
		 */
		class deleteDocumentResponse
		{

		}

	}

	if ( !class_exists( "getFileName" ) )
	{

		/**
		 * getFileName
		 */
		class getFileName
		{

			/**
			 * @access public
			 * @var sstring
			 */
			public $secKey;

			/**
			 * @access public
			 * @var sstring
			 */
			public $documentId;

			/**
			 * @access public
			 * @var sstring
			 */
			public $variant;

			/**
			 * @access public
			 * @var sstring
			 */
			public $versjon;

		}

	}

	if ( !class_exists( "getFileNameResponse" ) )
	{

		/**
		 * getFileNameResponse
		 */
		class getFileNameResponse
		{

			/**
			 * @access public
			 * @var sstring
			 */
			public $getFileNameResult;

		}

	}

	if ( !class_exists( "searchAndGetDocuments" ) )
	{

		/**
		 * searchAndGetDocuments
		 */
		class searchAndGetDocuments
		{

			/**
			 * @access public
			 * @var sstring
			 */
			public $secKey;

			/**
			 * @access public
			 * @var sstring
			 */
			public $baseclassname;

			/**
			 * @access public
			 * @var sstring
			 */
			public $classname;

			/**
			 * @access public
			 * @var sstring
			 */
			public $where;

			/**
			 * @access public
			 * @var sstring
			 */
			public $maxhits;

		}

	}

	if ( !class_exists( "searchAndGetDocumentsResponse" ) )
	{

		/**
		 * searchAndGetDocumentsResponse
		 */
		class searchAndGetDocumentsResponse
		{

			/**
			 * @access public
			 * @var ArrayOfDocument
			 */
			public $searchAndGetDocumentsResult;

		}

	}

	if ( !class_exists( "searchAndGetDocumentsWithVariants" ) )
	{

		/**
		 * searchAndGetDocumentsWithVariants
		 */
		class searchAndGetDocumentsWithVariants
		{

			/**
			 * @access public
			 * @var sstring
			 */
			public $secKey;

			/**
			 * @access public
			 * @var sstring
			 */
			public $baseclassname;

			/**
			 * @access public
			 * @var sstring
			 */
			public $classname;

			/**
			 * @access public
			 * @var sstring
			 */
			public $where;

			/**
			 * @access public
			 * @var sstring
			 */
			public $maxhits;

		}

	}

	if ( !class_exists( "Variant" ) )
	{

		/**
		 * Variant
		 */
		class Variant
		{

			/**
			 * @access public
			 * @var sstring
			 */
			public $FileName;

			/**
			 * @access public
			 * @var sstring
			 */
			public $FileExtension;

			/**
			 * @access public
			 * @var sstring
			 */
			public $VaultName;

		}

	}

	if ( !class_exists( "ExtendedDocument" ) )
	{

		/**
		 * ExtendedDocument
		 */
		class ExtendedDocument extends Document
		{

			/**
			 * @access public
			 * @var ArrayOfVariant
			 */
			public $Variants;

		}

	}

	if ( !class_exists( "searchAndGetDocumentsWithVariantsResponse" ) )
	{

		/**
		 * searchAndGetDocumentsWithVariantsResponse
		 */
		class searchAndGetDocumentsWithVariantsResponse
		{

			/**
			 * @access public
			 * @var ArrayOfExtendedDocument
			 */
			public $searchAndGetDocumentsWithVariantsResult;

		}

	}

	if ( !class_exists( "putFileAsByteArray" ) )
	{

		/**
		 * putFileAsByteArray
		 */
		class putFileAsByteArray
		{

			/**
			 * @access public
			 * @var sstring
			 */
			public $secKey;

			/**
			 * @access public
			 * @var sstring
			 */
			public $documentId;

			/**
			 * @access public
			 * @var sstring
			 */
			public $filename;

			/**
			 * @access public
			 * @var sstring
			 */
			public $file;

		}

	}

	if ( !class_exists( "putFileAsByteArrayResponse" ) )
	{

		/**
		 * putFileAsByteArrayResponse
		 */
		class putFileAsByteArrayResponse
		{

		}

	}

	if ( !class_exists( "getFileAsByteArray" ) )
	{

		/**
		 * getFileAsByteArray
		 */
		class getFileAsByteArray
		{

			/**
			 * @access public
			 * @var sstring
			 */
			public $secKey;

			/**
			 * @access public
			 * @var sstring
			 */
			public $documentId;

			/**
			 * @access public
			 * @var sstring
			 */
			public $variant;

			/**
			 * @access public
			 * @var sstring
			 */
			public $versjon;

		}

	}

	if ( !class_exists( "getFileAsByteArrayResponse" ) )
	{

		/**
		 * getFileAsByteArrayResponse
		 */
		class getFileAsByteArrayResponse
		{

			/**
			 * @access public
			 * @var sstring
			 */
			public $getFileAsByteArrayResult;

		}

	}

	if ( !class_exists( "fileTransferSendChunk" ) )
	{

		/**
		 * fileTransferSendChunk
		 */
		class fileTransferSendChunk
		{

			/**
			 * @access public
			 * @var sstring
			 */
			public $secKey;

			/**
			 * @access public
			 * @var sstring
			 */
			public $fileid;

			/**
			 * @access public
			 * @var sstring
			 */
			public $chunk;

		}

	}

	if ( !class_exists( "fileTransferSendChunkResponse" ) )
	{

		/**
		 * fileTransferSendChunkResponse
		 */
		class fileTransferSendChunkResponse
		{

		}

	}

	if ( !class_exists( "fileTransferSendChunkedInit" ) )
	{

		/**
		 * fileTransferSendChunkedInit
		 */
		class fileTransferSendChunkedInit
		{

			/**
			 * @access public
			 * @var sstring
			 */
			public $secKey;

			/**
			 * @access public
			 * @var sstring
			 */
			public $docid;

			/**
			 * @access public
			 * @var sstring
			 */
			public $filename;

		}

	}

	if ( !class_exists( "fileTransferSendChunkedInitResponse" ) )
	{

		/**
		 * fileTransferSendChunkedInitResponse
		 */
		class fileTransferSendChunkedInitResponse
		{

			/**
			 * @access public
			 * @var sstring
			 */
			public $fileTransferSendChunkedInitResult;

		}

	}

	if ( !class_exists( "fileTransferSendChunkedEnd" ) )
	{

		/**
		 * fileTransferSendChunkedEnd
		 */
		class fileTransferSendChunkedEnd
		{

			/**
			 * @access public
			 * @var sstring
			 */
			public $secKey;

			/**
			 * @access public
			 * @var sstring
			 */
			public $fileid;

		}

	}

	if ( !class_exists( "fileTransferSendChunkedEndResponse" ) )
	{

		/**
		 * fileTransferSendChunkedEndResponse
		 */
		class fileTransferSendChunkedEndResponse
		{

		}

	}

	if ( !class_exists( "fileTransferRequestChunkedInit" ) )
	{

		/**
		 * fileTransferRequestChunkedInit
		 */
		class fileTransferRequestChunkedInit
		{

			/**
			 * @access public
			 * @var sstring
			 */
			public $secKey;

			/**
			 * @access public
			 * @var sstring
			 */
			public $documentId;

			/**
			 * @access public
			 * @var sstring
			 */
			public $variant;

			/**
			 * @access public
			 * @var sstring
			 */
			public $versjon;

		}

	}

	if ( !class_exists( "fileTransferRequestChunkedInitResponse" ) )
	{

		/**
		 * fileTransferRequestChunkedInitResponse
		 */
		class fileTransferRequestChunkedInitResponse
		{

			/**
			 * @access public
			 * @var sstring
			 */
			public $fileTransferRequestChunkedInitResult;

		}

	}

	if ( !class_exists( "fileTransferRequestChunk" ) )
	{

		/**
		 * fileTransferRequestChunk
		 */
		class fileTransferRequestChunk
		{

			/**
			 * @access public
			 * @var sstring
			 */
			public $secKey;

			/**
			 * @access public
			 * @var sstring
			 */
			public $fileid;

			/**
			 * @access public
			 * @var sstring
			 */
			public $offset;

		}

	}

	if ( !class_exists( "fileTransferRequestChunkResponse" ) )
	{

		/**
		 * fileTransferRequestChunkResponse
		 */
		class fileTransferRequestChunkResponse
		{

			/**
			 * @access public
			 * @var sstring
			 */
			public $fileTransferRequestChunkResult;

		}

	}

	if ( !class_exists( "fileTransferRequestChunkedEnd" ) )
	{

		/**
		 * fileTransferRequestChunkedEnd
		 */
		class fileTransferRequestChunkedEnd
		{

			/**
			 * @access public
			 * @var sstring
			 */
			public $secKey;

			/**
			 * @access public
			 * @var sstring
			 */
			public $fileid;

		}

	}

	if ( !class_exists( "fileTransferRequestChunkedEndResponse" ) )
	{

		/**
		 * fileTransferRequestChunkedEndResponse
		 */
		class fileTransferRequestChunkedEndResponse
		{

		}

	}

	if ( !class_exists( "Services" ) )
	{

		/**
		 * Services
		 * @author WSDLInterpreter
		 */
		class Services extends SoapClient
		{

			/**
			 * Default class map for wsdl=>php
			 * @access private
			 * @var array
			 */
			private static $classmap = array(
				"Login"										 => "Login",
				"LoginResponse"								 => "LoginResponse",
				"Logout"									 => "Logout",
				"LogoutResponse"							 => "LogoutResponse",
				"getProductionLines"						 => "getProductionLines",
				"getProductionLinesResponse"				 => "getProductionLinesResponse",
				"ProductionLine"							 => "ProductionLine",
				"getDocumentSplitTypes"						 => "getDocumentSplitTypes",
				"getDocumentSplitTypesResponse"				 => "getDocumentSplitTypesResponse",
				"DocumentSplitType"							 => "DocumentSplitType",
				"GetClasses"								 => "GetClasses",
				"GetClassesResponse"						 => "GetClassesResponse",
				"GetClassesResult"							 => "GetClassesResult",
				"getAvailableFileVariants"					 => "getAvailableFileVariants",
				"getAvailableFileVariantsResponse"			 => "getAvailableFileVariantsResponse",
				"getVariantVaultID"							 => "getVariantVaultID",
				"getVariantVaultIDResponse"					 => "getVariantVaultIDResponse",
				"getRelativeFileURL"						 => "getRelativeFileURL",
				"getRelativeFileURLResponse"				 => "getRelativeFileURLResponse",
				"getAvailableAttributes"					 => "getAvailableAttributes",
				"getAvailableAttributesResponse"			 => "getAvailableAttributesResponse",
				"Attribute"									 => "Attribute",
				"braArkivAttributeType"						 => "braArkivAttributeType",
				"LookupValue"								 => "LookupValue",
				"Pair"										 => "Pair",
				"Address"									 => "Address",
				"Matrikkel"									 => "Matrikkel",
				"getLookupValues"							 => "getLookupValues",
				"getLookupValuesResponse"					 => "getLookupValuesResponse",
				"searchDocument"							 => "searchDocument",
				"searchDocumentResponse"					 => "searchDocumentResponse",
				"createDocument"							 => "createDocument",
				"Document"									 => "Document",
				"createDocumentResponse"					 => "createDocumentResponse",
				"getDocument"								 => "getDocument",
				"getDocumentResponse"						 => "getDocumentResponse",
				"getAttribute"								 => "getAttribute",
				"getAttributeResponse"						 => "getAttributeResponse",
				"updateDocument"							 => "updateDocument",
				"updateDocumentResponse"					 => "updateDocumentResponse",
				"updateAttribute"							 => "updateAttribute",
				"updateAttributeResponse"					 => "updateAttributeResponse",
				"deleteDocument"							 => "deleteDocument",
				"deleteDocumentResponse"					 => "deleteDocumentResponse",
				"getFileName"								 => "getFileName",
				"getFileNameResponse"						 => "getFileNameResponse",
				"searchAndGetDocuments"						 => "searchAndGetDocuments",
				"searchAndGetDocumentsResponse"				 => "searchAndGetDocumentsResponse",
				"searchAndGetDocumentsWithVariants"			 => "searchAndGetDocumentsWithVariants",
				"Variant"									 => "Variant",
				"ExtendedDocument"							 => "ExtendedDocument",
				"searchAndGetDocumentsWithVariantsResponse"	 => "searchAndGetDocumentsWithVariantsResponse",
				"putFileAsByteArray"						 => "putFileAsByteArray",
				"putFileAsByteArrayResponse"				 => "putFileAsByteArrayResponse",
				"getFileAsByteArray"						 => "getFileAsByteArray",
				"getFileAsByteArrayResponse"				 => "getFileAsByteArrayResponse",
				"fileTransferSendChunk"						 => "fileTransferSendChunk",
				"fileTransferSendChunkResponse"				 => "fileTransferSendChunkResponse",
				"fileTransferSendChunkedInit"				 => "fileTransferSendChunkedInit",
				"fileTransferSendChunkedInitResponse"		 => "fileTransferSendChunkedInitResponse",
				"fileTransferSendChunkedEnd"				 => "fileTransferSendChunkedEnd",
				"fileTransferSendChunkedEndResponse"		 => "fileTransferSendChunkedEndResponse",
				"fileTransferRequestChunkedInit"			 => "fileTransferRequestChunkedInit",
				"fileTransferRequestChunkedInitResponse"	 => "fileTransferRequestChunkedInitResponse",
				"fileTransferRequestChunk"					 => "fileTransferRequestChunk",
				"fileTransferRequestChunkResponse"			 => "fileTransferRequestChunkResponse",
				"fileTransferRequestChunkedEnd"				 => "fileTransferRequestChunkedEnd",
				"fileTransferRequestChunkedEndResponse"		 => "fileTransferRequestChunkedEndResponse",
			);

			/**
			 * Constructor using wsdl location and options array
			 * @param string $wsdl WSDL location for this service
			 * @param array $options Options for the SoapClient
			 */
			public function __construct( $wsdl = "/home/sn5607/BRA_Arkiv/services.braarkiv.xml",
								$options = array() )
			{
				foreach ( self::$classmap as $wsdlClassName => $phpClassName )
				{
					if ( !isset( $options['classmap'][$wsdlClassName] ) )
					{
						$options['classmap'][$wsdlClassName] = $phpClassName;
					}
				}
				parent::__construct( $wsdl, $options );
			}

			/**
			 * Checks if an argument list matches against a valid argument type list
			 * @param array $arguments The argument list to check
			 * @param array $validParameters A list of valid argument types
			 * @return boolean true if arguments match against validParameters
			 * @throws Exception invalid function signature message
			 */
			public function _checkArguments( $arguments, $validParameters )
			{
				$variables = "";
				foreach ( $arguments as $arg )
				{
					$type = gettype( $arg );
					if ( $type == "object" )
					{
						$type = get_class( $arg );
					}
					$variables .= "(" . $type . ")";
				}
				if ( !in_array( $variables, $validParameters ) )
				{
					throw new Exception( "Invalid parameter types: " . str_replace( ")(", ", ",
																	 $variables ) );
				}
				return true;
			}

			/**
			 * Service Call: Login
			 * Parameter options:
			 * (Login) parameters
			 * (Login) parameters
			 * @param mixed,... See function description for parameter options
			 * @return LoginResponse
			 * @throws Exception invalid function signature message
			 */
			public function Login( $mixed = null )
			{
				$validParameters = array(
					"(Login)",
					"(Login)",
				);
				$args = func_get_args();
				$this->_checkArguments( $args, $validParameters );
				return $this->__soapCall( "Login", $args );
			}

			/**
			 * Service Call: Logout
			 * Parameter options:
			 * (Logout) parameters
			 * (Logout) parameters
			 * @param mixed,... See function description for parameter options
			 * @return LogoutResponse
			 * @throws Exception invalid function signature message
			 */
			public function Logout( $mixed = null )
			{
				$validParameters = array(
					"(Logout)",
					"(Logout)",
				);
				$args = func_get_args();
				$this->_checkArguments( $args, $validParameters );
				return $this->__soapCall( "Logout", $args );
			}

			/**
			 * Service Call: getProductionLines
			 * Parameter options:
			 * (getProductionLines) parameters
			 * (getProductionLines) parameters
			 * @param mixed,... See function description for parameter options
			 * @return getProductionLinesResponse
			 * @throws Exception invalid function signature message
			 */
			public function getProductionLines( $mixed = null )
			{
				$validParameters = array(
					"(getProductionLines)",
					"(getProductionLines)",
				);
				$args = func_get_args();
				$this->_checkArguments( $args, $validParameters );
				return $this->__soapCall( "getProductionLines", $args );
			}

			/**
			 * Service Call: getDocumentSplitTypes
			 * Parameter options:
			 * (getDocumentSplitTypes) parameters
			 * (getDocumentSplitTypes) parameters
			 * @param mixed,... See function description for parameter options
			 * @return getDocumentSplitTypesResponse
			 * @throws Exception invalid function signature message
			 */
			public function getDocumentSplitTypes( $mixed = null )
			{
				$validParameters = array(
					"(getDocumentSplitTypes)",
					"(getDocumentSplitTypes)",
				);
				$args = func_get_args();
				$this->_checkArguments( $args, $validParameters );
				return $this->__soapCall( "getDocumentSplitTypes", $args );
			}

			/**
			 * Service Call: GetClasses
			 * Parameter options:
			 * (GetClasses) parameters
			 * (GetClasses) parameters
			 * @param mixed,... See function description for parameter options
			 * @return GetClassesResponse
			 * @throws Exception invalid function signature message
			 */
			public function GetClasses( $mixed = null )
			{
				$validParameters = array(
					"(GetClasses)",
					"(GetClasses)",
				);
				$args = func_get_args();
				$this->_checkArguments( $args, $validParameters );
				return $this->__soapCall( "GetClasses", $args );
			}

			/**
			 * Service Call: getAvailableFileVariants
			 * Parameter options:
			 * (getAvailableFileVariants) parameters
			 * (getAvailableFileVariants) parameters
			 * @param mixed,... See function description for parameter options
			 * @return getAvailableFileVariantsResponse
			 * @throws Exception invalid function signature message
			 */
			public function getAvailableFileVariants( $mixed = null )
			{
				$validParameters = array(
					"(getAvailableFileVariants)",
					"(getAvailableFileVariants)",
				);
				$args = func_get_args();
				$this->_checkArguments( $args, $validParameters );
				return $this->__soapCall( "getAvailableFileVariants", $args );
			}

			/**
			 * Service Call: getVariantVaultID
			 * Parameter options:
			 * (getVariantVaultID) parameters
			 * (getVariantVaultID) parameters
			 * @param mixed,... See function description for parameter options
			 * @return getVariantVaultIDResponse
			 * @throws Exception invalid function signature message
			 */
			public function getVariantVaultID( $mixed = null )
			{
				$validParameters = array(
					"(getVariantVaultID)",
					"(getVariantVaultID)",
				);
				$args = func_get_args();
				$this->_checkArguments( $args, $validParameters );
				return $this->__soapCall( "getVariantVaultID", $args );
			}

			/**
			 * Service Call: getRelativeFileURL
			 * Parameter options:
			 * (getRelativeFileURL) parameters
			 * (getRelativeFileURL) parameters
			 * @param mixed,... See function description for parameter options
			 * @return getRelativeFileURLResponse
			 * @throws Exception invalid function signature message
			 */
			public function getRelativeFileURL( $mixed = null )
			{
				$validParameters = array(
					"(getRelativeFileURL)",
					"(getRelativeFileURL)",
				);
				$args = func_get_args();
				$this->_checkArguments( $args, $validParameters );
				return $this->__soapCall( "getRelativeFileURL", $args );
			}

			/**
			 * Service Call: getAvailableAttributes
			 * Parameter options:
			 * (getAvailableAttributes) parameters
			 * (getAvailableAttributes) parameters
			 * @param mixed,... See function description for parameter options
			 * @return getAvailableAttributesResponse
			 * @throws Exception invalid function signature message
			 */
			public function getAvailableAttributes( $mixed = null )
			{
				$validParameters = array(
					"(getAvailableAttributes)",
					"(getAvailableAttributes)",
				);
				$args = func_get_args();
				$this->_checkArguments( $args, $validParameters );
				return $this->__soapCall( "getAvailableAttributes", $args );
			}

			/**
			 * Service Call: getLookupValues
			 * Parameter options:
			 * (getLookupValues) parameters
			 * (getLookupValues) parameters
			 * @param mixed,... See function description for parameter options
			 * @return getLookupValuesResponse
			 * @throws Exception invalid function signature message
			 */
			public function getLookupValues( $mixed = null )
			{
				$validParameters = array(
					"(getLookupValues)",
					"(getLookupValues)",
				);
				$args = func_get_args();
				$this->_checkArguments( $args, $validParameters );
				return $this->__soapCall( "getLookupValues", $args );
			}

			/**
			 * Service Call: searchDocument
			 * Parameter options:
			 * (searchDocument) parameters
			 * (searchDocument) parameters
			 * @param mixed,... See function description for parameter options
			 * @return searchDocumentResponse
			 * @throws Exception invalid function signature message
			 */
			public function searchDocument( $mixed = null )
			{
				$validParameters = array(
					"(searchDocument)",
					"(searchDocument)",
				);
				$args = func_get_args();
				$this->_checkArguments( $args, $validParameters );
				return $this->__soapCall( "searchDocument", $args );
			}

			/**
			 * Service Call: createDocument
			 * Parameter options:
			 * (createDocument) parameters
			 * (createDocument) parameters
			 * @param mixed,... See function description for parameter options
			 * @return createDocumentResponse
			 * @throws Exception invalid function signature message
			 */
			public function createDocument( $mixed = null )
			{
				$validParameters = array(
					"(createDocument)",
					"(createDocument)",
				);
				$args = func_get_args();
				$this->_checkArguments( $args, $validParameters );
				return $this->__soapCall( "createDocument", $args );
			}

			/**
			 * Service Call: getDocument
			 * Parameter options:
			 * (getDocument) parameters
			 * (getDocument) parameters
			 * @param mixed,... See function description for parameter options
			 * @return getDocumentResponse
			 * @throws Exception invalid function signature message
			 */
			public function getDocument( $mixed = null )
			{
				$validParameters = array(
					"(getDocument)",
					"(getDocument)",
				);
				$args = func_get_args();
				$this->_checkArguments( $args, $validParameters );
				return $this->__soapCall( "getDocument", $args );
			}

			/**
			 * Service Call: getAttribute
			 * Parameter options:
			 * (getAttribute) parameters
			 * (getAttribute) parameters
			 * @param mixed,... See function description for parameter options
			 * @return getAttributeResponse
			 * @throws Exception invalid function signature message
			 */
			public function getAttribute( $mixed = null )
			{
				$validParameters = array(
					"(getAttribute)",
					"(getAttribute)",
				);
				$args = func_get_args();
				$this->_checkArguments( $args, $validParameters );
				return $this->__soapCall( "getAttribute", $args );
			}

			/**
			 * Service Call: updateDocument
			 * Parameter options:
			 * (updateDocument) parameters
			 * (updateDocument) parameters
			 * @param mixed,... See function description for parameter options
			 * @return updateDocumentResponse
			 * @throws Exception invalid function signature message
			 */
			public function updateDocument( $mixed = null )
			{
				$validParameters = array(
					"(updateDocument)",
					"(updateDocument)",
				);
				$args = func_get_args();
				$this->_checkArguments( $args, $validParameters );
				return $this->__soapCall( "updateDocument", $args );
			}

			/**
			 * Service Call: updateAttribute
			 * Parameter options:
			 * (updateAttribute) parameters
			 * (updateAttribute) parameters
			 * @param mixed,... See function description for parameter options
			 * @return updateAttributeResponse
			 * @throws Exception invalid function signature message
			 */
			public function updateAttribute( $mixed = null )
			{
				$validParameters = array(
					"(updateAttribute)",
					"(updateAttribute)",
				);
				$args = func_get_args();
				$this->_checkArguments( $args, $validParameters );
				return $this->__soapCall( "updateAttribute", $args );
			}

			/**
			 * Service Call: deleteDocument
			 * Parameter options:
			 * (deleteDocument) parameters
			 * (deleteDocument) parameters
			 * @param mixed,... See function description for parameter options
			 * @return deleteDocumentResponse
			 * @throws Exception invalid function signature message
			 */
			public function deleteDocument( $mixed = null )
			{
				$validParameters = array(
					"(deleteDocument)",
					"(deleteDocument)",
				);
				$args = func_get_args();
				$this->_checkArguments( $args, $validParameters );
				return $this->__soapCall( "deleteDocument", $args );
			}

			/**
			 * Service Call: getFileName
			 * Parameter options:
			 * (getFileName) parameters
			 * (getFileName) parameters
			 * @param mixed,... See function description for parameter options
			 * @return getFileNameResponse
			 * @throws Exception invalid function signature message
			 */
			public function getFileName( $mixed = null )
			{
				$validParameters = array(
					"(getFileName)",
					"(getFileName)",
				);
				$args = func_get_args();
				$this->_checkArguments( $args, $validParameters );
				return $this->__soapCall( "getFileName", $args );
			}

			/**
			 * Service Call: searchAndGetDocuments
			 * Parameter options:
			 * (searchAndGetDocuments) parameters
			 * (searchAndGetDocuments) parameters
			 * @param mixed,... See function description for parameter options
			 * @return searchAndGetDocumentsResponse
			 * @throws Exception invalid function signature message
			 */
			public function searchAndGetDocuments( $mixed = null )
			{
				$validParameters = array(
					"(searchAndGetDocuments)",
					"(searchAndGetDocuments)",
				);
				$args = func_get_args();
				$this->_checkArguments( $args, $validParameters );
				return $this->__soapCall( "searchAndGetDocuments", $args );
			}

			/**
			 * Service Call: searchAndGetDocumentsWithVariants
			 * Parameter options:
			 * (searchAndGetDocumentsWithVariants) parameters
			 * (searchAndGetDocumentsWithVariants) parameters
			 * @param mixed,... See function description for parameter options
			 * @return searchAndGetDocumentsWithVariantsResponse
			 * @throws Exception invalid function signature message
			 */
			public function searchAndGetDocumentsWithVariants( $mixed = null )
			{
				$validParameters = array(
					"(searchAndGetDocumentsWithVariants)",
					"(searchAndGetDocumentsWithVariants)",
				);
				$args = func_get_args();
				$this->_checkArguments( $args, $validParameters );
				return $this->__soapCall( "searchAndGetDocumentsWithVariants", $args );
			}

			/**
			 * Service Call: putFileAsByteArray
			 * Parameter options:
			 * (putFileAsByteArray) parameters
			 * (putFileAsByteArray) parameters
			 * @param mixed,... See function description for parameter options
			 * @return putFileAsByteArrayResponse
			 * @throws Exception invalid function signature message
			 */
			public function putFileAsByteArray( $mixed = null )
			{
				$validParameters = array(
					"(putFileAsByteArray)",
					"(putFileAsByteArray)",
				);
				$args = func_get_args();
				$this->_checkArguments( $args, $validParameters );
				return $this->__soapCall( "putFileAsByteArray", $args );
			}

			/**
			 * Service Call: getFileAsByteArray
			 * Parameter options:
			 * (getFileAsByteArray) parameters
			 * (getFileAsByteArray) parameters
			 * @param mixed,... See function description for parameter options
			 * @return getFileAsByteArrayResponse
			 * @throws Exception invalid function signature message
			 */
			public function getFileAsByteArray( $mixed = null )
			{
				$validParameters = array(
					"(getFileAsByteArray)",
					"(getFileAsByteArray)",
				);
				$args = func_get_args();
				$this->_checkArguments( $args, $validParameters );
				return $this->__soapCall( "getFileAsByteArray", $args );
			}

			/**
			 * Service Call: fileTransferSendChunk
			 * Parameter options:
			 * (fileTransferSendChunk) parameters
			 * (fileTransferSendChunk) parameters
			 * @param mixed,... See function description for parameter options
			 * @return fileTransferSendChunkResponse
			 * @throws Exception invalid function signature message
			 */
			public function fileTransferSendChunk( $mixed = null )
			{
				$validParameters = array(
					"(fileTransferSendChunk)",
					"(fileTransferSendChunk)",
				);
				$args = func_get_args();
				$this->_checkArguments( $args, $validParameters );
				return $this->__soapCall( "fileTransferSendChunk", $args );
			}

			/**
			 * Service Call: fileTransferSendChunkedInit
			 * Parameter options:
			 * (fileTransferSendChunkedInit) parameters
			 * (fileTransferSendChunkedInit) parameters
			 * @param mixed,... See function description for parameter options
			 * @return fileTransferSendChunkedInitResponse
			 * @throws Exception invalid function signature message
			 */
			public function fileTransferSendChunkedInit( $mixed = null )
			{
				$validParameters = array(
					"(fileTransferSendChunkedInit)",
					"(fileTransferSendChunkedInit)",
				);
				$args = func_get_args();
				$this->_checkArguments( $args, $validParameters );
				return $this->__soapCall( "fileTransferSendChunkedInit", $args );
			}

			/**
			 * Service Call: fileTransferSendChunkedEnd
			 * Parameter options:
			 * (fileTransferSendChunkedEnd) parameters
			 * (fileTransferSendChunkedEnd) parameters
			 * @param mixed,... See function description for parameter options
			 * @return fileTransferSendChunkedEndResponse
			 * @throws Exception invalid function signature message
			 */
			public function fileTransferSendChunkedEnd( $mixed = null )
			{
				$validParameters = array(
					"(fileTransferSendChunkedEnd)",
					"(fileTransferSendChunkedEnd)",
				);
				$args = func_get_args();
				$this->_checkArguments( $args, $validParameters );
				return $this->__soapCall( "fileTransferSendChunkedEnd", $args );
			}

			/**
			 * Service Call: fileTransferRequestChunkedInit
			 * Parameter options:
			 * (fileTransferRequestChunkedInit) parameters
			 * (fileTransferRequestChunkedInit) parameters
			 * @param mixed,... See function description for parameter options
			 * @return fileTransferRequestChunkedInitResponse
			 * @throws Exception invalid function signature message
			 */
			public function fileTransferRequestChunkedInit( $mixed = null )
			{
				$validParameters = array(
					"(fileTransferRequestChunkedInit)",
					"(fileTransferRequestChunkedInit)",
				);
				$args = func_get_args();
				$this->_checkArguments( $args, $validParameters );
				return $this->__soapCall( "fileTransferRequestChunkedInit", $args );
			}

			/**
			 * Service Call: fileTransferRequestChunk
			 * Parameter options:
			 * (fileTransferRequestChunk) parameters
			 * (fileTransferRequestChunk) parameters
			 * @param mixed,... See function description for parameter options
			 * @return fileTransferRequestChunkResponse
			 * @throws Exception invalid function signature message
			 */
			public function fileTransferRequestChunk( $mixed = null )
			{
				$validParameters = array(
					"(fileTransferRequestChunk)",
					"(fileTransferRequestChunk)",
				);
				$args = func_get_args();
				$this->_checkArguments( $args, $validParameters );
				return $this->__soapCall( "fileTransferRequestChunk", $args );
			}

			/**
			 * Service Call: fileTransferRequestChunkedEnd
			 * Parameter options:
			 * (fileTransferRequestChunkedEnd) parameters
			 * (fileTransferRequestChunkedEnd) parameters
			 * @param mixed,... See function description for parameter options
			 * @return fileTransferRequestChunkedEndResponse
			 * @throws Exception invalid function signature message
			 */
			public function fileTransferRequestChunkedEnd( $mixed = null )
			{
				$validParameters = array(
					"(fileTransferRequestChunkedEnd)",
					"(fileTransferRequestChunkedEnd)",
				);
				$args = func_get_args();
				$this->_checkArguments( $args, $validParameters );
				return $this->__soapCall( "fileTransferRequestChunkedEnd", $args );
			}

		}

	}
