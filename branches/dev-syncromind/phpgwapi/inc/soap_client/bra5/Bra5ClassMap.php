<?php
/**
 * File for the class which returns the class map definition
 * @package Bra5
 * @date 2015-03-06
 */
/**
 * Class which returns the class map definition by the static method Bra5ClassMap::classMap()
 * @package Bra5
 * @date 2015-03-06
 */
class Bra5ClassMap
{
    /**
     * This method returns the array containing the mapping between WSDL structs and generated classes
     * This array is sent to the SoapClient when calling the WS
     * @return array
     */
    final public static function classMap()
    {
        return array (
  'Address' => 'Bra5StructAddress',
  'ArrayOfAnyType' => 'Bra5StructArrayOfAnyType',
  'ArrayOfAttribute' => 'Bra5StructArrayOfAttribute',
  'ArrayOfDocumentClassPrivilege' => 'Bra5StructArrayOfDocumentClassPrivilege',
  'ArrayOfDocumentSplitType' => 'Bra5StructArrayOfDocumentSplitType',
  'ArrayOfExtendedDocument' => 'Bra5StructArrayOfExtendedDocument',
  'ArrayOfLookupValue' => 'Bra5StructArrayOfLookupValue',
  'ArrayOfProductionLine' => 'Bra5StructArrayOfProductionLine',
  'ArrayOfString' => 'Bra5StructArrayOfString',
  'ArrayOfVariant' => 'Bra5StructArrayOfVariant',
  'Attribute' => 'Bra5StructAttribute',
  'Document' => 'Bra5StructDocument',
  'DocumentClassPrivilege' => 'Bra5StructDocumentClassPrivilege',
  'DocumentSplitType' => 'Bra5StructDocumentSplitType',
  'ExtendedDocument' => 'Bra5StructExtendedDocument',
  'GetArchiveName' => 'Bra5StructGetArchiveName',
  'GetArchiveNameResponse' => 'Bra5StructGetArchiveNameResponse',
  'GetAvailableClasses' => 'Bra5StructGetAvailableClasses',
  'GetAvailableClassesResponse' => 'Bra5StructGetAvailableClassesResponse',
  'Login' => 'Bra5StructLogin',
  'LoginResponse' => 'Bra5StructLoginResponse',
  'Logout' => 'Bra5StructLogout',
  'LogoutResponse' => 'Bra5StructLogoutResponse',
  'LookupValue' => 'Bra5StructLookupValue',
  'Matrikkel' => 'Bra5StructMatrikkel',
  'Pair' => 'Bra5StructPair',
  'ProductionLine' => 'Bra5StructProductionLine',
  'Variant' => 'Bra5StructVariant',
  'braArkivAttributeType' => 'Bra5EnumBraArkivAttributeType',
  'createDocument' => 'Bra5StructCreateDocument',
  'createDocumentResponse' => 'Bra5StructCreateDocumentResponse',
  'deleteDocument' => 'Bra5StructDeleteDocument',
  'deleteDocumentResponse' => 'Bra5StructDeleteDocumentResponse',
  'fileTransferRequestChunk' => 'Bra5StructFileTransferRequestChunk',
  'fileTransferRequestChunkResponse' => 'Bra5StructFileTransferRequestChunkResponse',
  'fileTransferRequestChunkedEnd' => 'Bra5StructFileTransferRequestChunkedEnd',
  'fileTransferRequestChunkedEndResponse' => 'Bra5StructFileTransferRequestChunkedEndResponse',
  'fileTransferRequestChunkedInit' => 'Bra5StructFileTransferRequestChunkedInit',
  'fileTransferRequestChunkedInitResponse' => 'Bra5StructFileTransferRequestChunkedInitResponse',
  'fileTransferSendChunk' => 'Bra5StructFileTransferSendChunk',
  'fileTransferSendChunkResponse' => 'Bra5StructFileTransferSendChunkResponse',
  'fileTransferSendChunkedEnd' => 'Bra5StructFileTransferSendChunkedEnd',
  'fileTransferSendChunkedEndResponse' => 'Bra5StructFileTransferSendChunkedEndResponse',
  'fileTransferSendChunkedInit' => 'Bra5StructFileTransferSendChunkedInit',
  'fileTransferSendChunkedInitResponse' => 'Bra5StructFileTransferSendChunkedInitResponse',
  'getAttribute' => 'Bra5StructGetAttribute',
  'getAttributeResponse' => 'Bra5StructGetAttributeResponse',
  'getAvailableAttributes' => 'Bra5StructGetAvailableAttributes',
  'getAvailableAttributesResponse' => 'Bra5StructGetAvailableAttributesResponse',
  'getDocument' => 'Bra5StructGetDocument',
  'getDocumentResponse' => 'Bra5StructGetDocumentResponse',
  'getDocumentSplitTypes' => 'Bra5StructGetDocumentSplitTypes',
  'getDocumentSplitTypesResponse' => 'Bra5StructGetDocumentSplitTypesResponse',
  'getFileAsByteArray' => 'Bra5StructGetFileAsByteArray',
  'getFileAsByteArrayResponse' => 'Bra5StructGetFileAsByteArrayResponse',
  'getFileName' => 'Bra5StructGetFileName',
  'getFileNameResponse' => 'Bra5StructGetFileNameResponse',
  'getLookupValues' => 'Bra5StructGetLookupValues',
  'getLookupValuesResponse' => 'Bra5StructGetLookupValuesResponse',
  'getProductionLines' => 'Bra5StructGetProductionLines',
  'getProductionLinesResponse' => 'Bra5StructGetProductionLinesResponse',
  'getRelativeFileURL' => 'Bra5StructGetRelativeFileURL',
  'getRelativeFileURLResponse' => 'Bra5StructGetRelativeFileURLResponse',
  'putFileAsByteArray' => 'Bra5StructPutFileAsByteArray',
  'putFileAsByteArrayResponse' => 'Bra5StructPutFileAsByteArrayResponse',
  'searchAndGetDocuments' => 'Bra5StructSearchAndGetDocuments',
  'searchAndGetDocumentsResponse' => 'Bra5StructSearchAndGetDocumentsResponse',
  'searchDocument' => 'Bra5StructSearchDocument',
  'searchDocumentResponse' => 'Bra5StructSearchDocumentResponse',
  'updateAttribute' => 'Bra5StructUpdateAttribute',
  'updateAttributeResponse' => 'Bra5StructUpdateAttributeResponse',
  'updateDocument' => 'Bra5StructUpdateDocument',
  'updateDocumentResponse' => 'Bra5StructUpdateDocumentResponse',
);
    }
}
