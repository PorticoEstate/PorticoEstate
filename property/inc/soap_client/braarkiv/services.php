<?php

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
			public $UsesLookupValues;

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
			public $ID;

			/**
			 * @access public
			 * @var sstring
			 */
			public $BFDocKey;

			/**
			 * @access public
			 * @var sstring
			 */
			public $BFNoSheets;

			/**
			 * @access public
			 * @var sboolean
			 */
			public $BFDoubleSided;

			/**
			 * @access public
			 * @var sboolean
			 */
			public $BFSeparateKeySheet;

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
			public $Classified;

			/**
			 * @access public
			 * @var sint
			 */
			public $Priority;

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
?>
