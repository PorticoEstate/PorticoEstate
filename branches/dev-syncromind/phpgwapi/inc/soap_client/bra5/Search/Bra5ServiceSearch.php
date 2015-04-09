<?php
/**
 * File for class Bra5ServiceSearch
 * @package Bra5
 * @subpackage Services
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5ServiceSearch originally named Search
 * @package Bra5
 * @subpackage Services
 * @date 2015-03-06
 */
class Bra5ServiceSearch extends Bra5WsdlClass
{
    /**
     * Method to call the operation originally named searchDocument
     * @uses Bra5WsdlClass::getSoapClient()
     * @uses Bra5WsdlClass::setResult()
     * @uses Bra5WsdlClass::saveLastError()
     * @param Bra5StructSearchDocument $_bra5StructSearchDocument
     * @return Bra5StructSearchDocumentResponse
     */
    public function searchDocument(Bra5StructSearchDocument $_bra5StructSearchDocument)
    {
        try
        {
            return $this->setResult(new Bra5StructSearchDocumentResponse(self::getSoapClient()->searchDocument($_bra5StructSearchDocument)));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named searchAndGetDocuments
     * @uses Bra5WsdlClass::getSoapClient()
     * @uses Bra5WsdlClass::setResult()
     * @uses Bra5WsdlClass::saveLastError()
     * @param Bra5StructSearchAndGetDocuments $_bra5StructSearchAndGetDocuments
     * @return Bra5StructSearchAndGetDocumentsResponse
     */
    public function searchAndGetDocuments(Bra5StructSearchAndGetDocuments $_bra5StructSearchAndGetDocuments)
    {
        try
        {
            return $this->setResult(new Bra5StructSearchAndGetDocumentsResponse(self::getSoapClient()->searchAndGetDocuments($_bra5StructSearchAndGetDocuments)));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Returns the result
     * @see Bra5WsdlClass::getResult()
     * @return Bra5StructSearchAndGetDocumentsResponse|Bra5StructSearchDocumentResponse
     */
    public function getResult()
    {
        return parent::getResult();
    }
    /**
     * Method returning the class name
     * @return string __CLASS__
     */
    public function __toString()
    {
        return __CLASS__;
    }
}
