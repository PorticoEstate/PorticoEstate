<?php

class Services extends \SoapClient
{

    /**
     * @var array $classmap The defined classes
     */
    private static $classmap = array (
      'Login' => '\\Login',
      'LoginResponse' => '\\LoginResponse',
      'Logout' => '\\Logout',
      'LogoutResponse' => '\\LogoutResponse',
      'getProductionLines' => '\\getProductionLines',
      'getProductionLinesResponse' => '\\getProductionLinesResponse',
      'ArrayOfProductionLine' => '\\ArrayOfProductionLine',
      'ProductionLine' => '\\ProductionLine',
      'getDocumentSplitTypes' => '\\getDocumentSplitTypes',
      'getDocumentSplitTypesResponse' => '\\getDocumentSplitTypesResponse',
      'ArrayOfDocumentSplitType' => '\\ArrayOfDocumentSplitType',
      'DocumentSplitType' => '\\DocumentSplitType',
      'GetAvailableClasses' => '\\GetAvailableClasses',
      'GetAvailableClassesResponse' => '\\GetAvailableClassesResponse',
      'ArrayOfDocumentClassPrivilege' => '\\ArrayOfDocumentClassPrivilege',
      'DocumentClassPrivilege' => '\\DocumentClassPrivilege',
      'getRelativeFileURL' => '\\getRelativeFileURL',
      'getRelativeFileURLResponse' => '\\getRelativeFileURLResponse',
      'getAvailableAttributes' => '\\getAvailableAttributes',
      'getAvailableAttributesResponse' => '\\getAvailableAttributesResponse',
      'ArrayOfAttribute' => '\\ArrayOfAttribute',
      'Attribute' => '\\Attribute',
      'ArrayOfAnyType' => '\\ArrayOfAnyType',
      'LookupValue' => '\\LookupValue',
      'Matrikkel' => '\\Matrikkel',
      'Pair' => '\\Pair',
      'Address' => '\\Address',
      'getLookupValues' => '\\getLookupValues',
      'ArrayOfLookupValue' => '\\ArrayOfLookupValue',
      'getLookupValuesResponse' => '\\getLookupValuesResponse',
      'searchDocument' => '\\searchDocument',
      'ArrayOfString' => '\\ArrayOfString',
      'searchDocumentResponse' => '\\searchDocumentResponse',
      'createDocument' => '\\createDocument',
      'Document' => '\\Document',
      'createDocumentResponse' => '\\createDocumentResponse',
      'getDocument' => '\\getDocument',
      'Variant' => '\\Variant',
      'ArrayOfVariant' => '\\ArrayOfVariant',
      'getDocumentResponse' => '\\getDocumentResponse',
      'ExtendedDocument' => '\\ExtendedDocument',
      'getAttribute' => '\\getAttribute',
      'getAttributeResponse' => '\\getAttributeResponse',
      'updateDocument' => '\\updateDocument',
      'updateDocumentResponse' => '\\updateDocumentResponse',
      'updateAttribute' => '\\updateAttribute',
      'updateAttributeResponse' => '\\updateAttributeResponse',
      'deleteDocument' => '\\deleteDocument',
      'deleteDocumentResponse' => '\\deleteDocumentResponse',
      'getFileName' => '\\getFileName',
      'getFileNameResponse' => '\\getFileNameResponse',
      'searchAndGetDocuments' => '\\searchAndGetDocuments',
      'ArrayOfExtendedDocument' => '\\ArrayOfExtendedDocument',
      'searchAndGetDocumentsResponse' => '\\searchAndGetDocumentsResponse',
      'putFileAsByteArray' => '\\putFileAsByteArray',
      'putFileAsByteArrayResponse' => '\\putFileAsByteArrayResponse',
      'getFileAsByteArray' => '\\getFileAsByteArray',
      'getFileAsByteArrayResponse' => '\\getFileAsByteArrayResponse',
      'fileTransferSendChunkedInit' => '\\fileTransferSendChunkedInit',
      'fileTransferSendChunkedInitResponse' => '\\fileTransferSendChunkedInitResponse',
      'fileTransferSendChunk' => '\\fileTransferSendChunk',
      'fileTransferSendChunkResponse' => '\\fileTransferSendChunkResponse',
      'fileTransferSendChunkedEnd' => '\\fileTransferSendChunkedEnd',
      'fileTransferSendChunkedEndResponse' => '\\fileTransferSendChunkedEndResponse',
      'fileTransferRequestChunkedInit' => '\\fileTransferRequestChunkedInit',
      'fileTransferRequestChunkedInitResponse' => '\\fileTransferRequestChunkedInitResponse',
      'fileTransferRequestChunk' => '\\fileTransferRequestChunk',
      'fileTransferRequestChunkResponse' => '\\fileTransferRequestChunkResponse',
      'fileTransferRequestChunkedEnd' => '\\fileTransferRequestChunkedEnd',
      'fileTransferRequestChunkedEndResponse' => '\\fileTransferRequestChunkedEndResponse',
      'GetArchiveName' => '\\GetArchiveName',
      'GetArchiveNameResponse' => '\\GetArchiveNameResponse',
    );

    /**
     * @param array $options A array of config values
     * @param string $wsdl The wsdl file to use
     */
    public function __construct(array $options = array(), $wsdl = null)
    {
      foreach (self::$classmap as $key => $value) {
        if (!isset($options['classmap'][$key])) {
          $options['classmap'][$key] = $value;
        }
      }
      $options = array_merge(array (
      'features' => 1,
    ), $options);
      if (!$wsdl) {
        $wsdl = 'http://braarkiv-test.uadm.bgo/braArkiv51/service/services.asmx?wsdl';
      }
      parent::__construct($wsdl, $options);
    }

    /**
     * @param Login $parameters
     * @return LoginResponse
     */
    public function Login(Login $parameters)
    {
      return $this->__soapCall('Login', array($parameters));
    }

    /**
     * @param Logout $parameters
     * @return LogoutResponse
     */
    public function Logout(Logout $parameters)
    {
      return $this->__soapCall('Logout', array($parameters));
    }

    /**
     * @param getProductionLines $parameters
     * @return getProductionLinesResponse
     */
    public function getProductionLines(getProductionLines $parameters)
    {
      return $this->__soapCall('getProductionLines', array($parameters));
    }

    /**
     * @param getDocumentSplitTypes $parameters
     * @return getDocumentSplitTypesResponse
     */
    public function getDocumentSplitTypes(getDocumentSplitTypes $parameters)
    {
      return $this->__soapCall('getDocumentSplitTypes', array($parameters));
    }

    /**
     * @param GetAvailableClasses $parameters
     * @return GetAvailableClassesResponse
     */
    public function GetAvailableClasses(GetAvailableClasses $parameters)
    {
      return $this->__soapCall('GetAvailableClasses', array($parameters));
    }

    /**
     * @param getRelativeFileURL $parameters
     * @return getRelativeFileURLResponse
     */
    public function getRelativeFileURL(getRelativeFileURL $parameters)
    {
      return $this->__soapCall('getRelativeFileURL', array($parameters));
    }

    /**
     * @param getAvailableAttributes $parameters
     * @return getAvailableAttributesResponse
     */
    public function getAvailableAttributes(getAvailableAttributes $parameters)
    {
      return $this->__soapCall('getAvailableAttributes', array($parameters));
    }

    /**
     * @param getLookupValues $parameters
     * @return getLookupValuesResponse
     */
    public function getLookupValues(getLookupValues $parameters)
    {
      return $this->__soapCall('getLookupValues', array($parameters));
    }

    /**
     * @param searchDocument $parameters
     * @return searchDocumentResponse
     */
    public function searchDocument(searchDocument $parameters)
    {
      return $this->__soapCall('searchDocument', array($parameters));
    }

    /**
     * @param createDocument $parameters
     * @return createDocumentResponse
     */
    public function createDocument(createDocument $parameters)
    {
      return $this->__soapCall('createDocument', array($parameters));
    }

    /**
     * @param getDocument $parameters
     * @return getDocumentResponse
     */
    public function getDocument(getDocument $parameters)
    {
      return $this->__soapCall('getDocument', array($parameters));
    }

    /**
     * @param getAttribute $parameters
     * @return getAttributeResponse
     */
    public function getAttribute(getAttribute $parameters)
    {
      return $this->__soapCall('getAttribute', array($parameters));
    }

    /**
     * @param updateDocument $parameters
     * @return updateDocumentResponse
     */
    public function updateDocument(updateDocument $parameters)
    {
      return $this->__soapCall('updateDocument', array($parameters));
    }

    /**
     * @param updateAttribute $parameters
     * @return updateAttributeResponse
     */
    public function updateAttribute(updateAttribute $parameters)
    {
      return $this->__soapCall('updateAttribute', array($parameters));
    }

    /**
     * @param deleteDocument $parameters
     * @return deleteDocumentResponse
     */
    public function deleteDocument(deleteDocument $parameters)
    {
      return $this->__soapCall('deleteDocument', array($parameters));
    }

    /**
     * @param getFileName $parameters
     * @return getFileNameResponse
     */
    public function getFileName(getFileName $parameters)
    {
      return $this->__soapCall('getFileName', array($parameters));
    }

    /**
     * @param searchAndGetDocuments $parameters
     * @return searchAndGetDocumentsResponse
     */
    public function searchAndGetDocuments(searchAndGetDocuments $parameters)
    {
      return $this->__soapCall('searchAndGetDocuments', array($parameters));
    }

    /**
     * @param putFileAsByteArray $parameters
     * @return putFileAsByteArrayResponse
     */
    public function putFileAsByteArray(putFileAsByteArray $parameters)
    {
      return $this->__soapCall('putFileAsByteArray', array($parameters));
    }

    /**
     * @param getFileAsByteArray $parameters
     * @return getFileAsByteArrayResponse
     */
    public function getFileAsByteArray(getFileAsByteArray $parameters)
    {
      return $this->__soapCall('getFileAsByteArray', array($parameters));
    }

    /**
     * @param fileTransferSendChunkedInit $parameters
     * @return fileTransferSendChunkedInitResponse
     */
    public function fileTransferSendChunkedInit(fileTransferSendChunkedInit $parameters)
    {
      return $this->__soapCall('fileTransferSendChunkedInit', array($parameters));
    }

    /**
     * @param fileTransferSendChunk $parameters
     * @return fileTransferSendChunkResponse
     */
    public function fileTransferSendChunk(fileTransferSendChunk $parameters)
    {
      return $this->__soapCall('fileTransferSendChunk', array($parameters));
    }

    /**
     * @param fileTransferSendChunkedEnd $parameters
     * @return fileTransferSendChunkedEndResponse
     */
    public function fileTransferSendChunkedEnd(fileTransferSendChunkedEnd $parameters)
    {
      return $this->__soapCall('fileTransferSendChunkedEnd', array($parameters));
    }

    /**
     * @param fileTransferRequestChunkedInit $parameters
     * @return fileTransferRequestChunkedInitResponse
     */
    public function fileTransferRequestChunkedInit(fileTransferRequestChunkedInit $parameters)
    {
      return $this->__soapCall('fileTransferRequestChunkedInit', array($parameters));
    }

    /**
     * @param fileTransferRequestChunk $parameters
     * @return fileTransferRequestChunkResponse
     */
    public function fileTransferRequestChunk(fileTransferRequestChunk $parameters)
    {
      return $this->__soapCall('fileTransferRequestChunk', array($parameters));
    }

    /**
     * @param fileTransferRequestChunkedEnd $parameters
     * @return fileTransferRequestChunkedEndResponse
     */
    public function fileTransferRequestChunkedEnd(fileTransferRequestChunkedEnd $parameters)
    {
      return $this->__soapCall('fileTransferRequestChunkedEnd', array($parameters));
    }

    /**
     * @param GetArchiveName $parameters
     * @return GetArchiveNameResponse
     */
    public function GetArchiveName(GetArchiveName $parameters)
    {
      return $this->__soapCall('GetArchiveName', array($parameters));
    }

}
