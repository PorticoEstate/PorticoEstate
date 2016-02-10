<?php
/**
 * File for class Bra5ServiceGet
 * @package Bra5
 * @subpackage Services
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5ServiceGet originally named Get
 * @package Bra5
 * @subpackage Services
 * @date 2015-03-06
 */
class Bra5ServiceGet extends Bra5WsdlClass
{
    /**
     * Method to call the operation originally named getProductionLines
     * @uses Bra5WsdlClass::getSoapClient()
     * @uses Bra5WsdlClass::setResult()
     * @uses Bra5WsdlClass::saveLastError()
     * @param Bra5StructGetProductionLines $_bra5StructGetProductionLines
     * @return Bra5StructGetProductionLinesResponse
     */
    public function getProductionLines(Bra5StructGetProductionLines $_bra5StructGetProductionLines)
    {
        try
        {
            return $this->setResult(new Bra5StructGetProductionLinesResponse(self::getSoapClient()->getProductionLines($_bra5StructGetProductionLines)));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named getDocumentSplitTypes
     * @uses Bra5WsdlClass::getSoapClient()
     * @uses Bra5WsdlClass::setResult()
     * @uses Bra5WsdlClass::saveLastError()
     * @param Bra5StructGetDocumentSplitTypes $_bra5StructGetDocumentSplitTypes
     * @return Bra5StructGetDocumentSplitTypesResponse
     */
    public function getDocumentSplitTypes(Bra5StructGetDocumentSplitTypes $_bra5StructGetDocumentSplitTypes)
    {
        try
        {
            return $this->setResult(new Bra5StructGetDocumentSplitTypesResponse(self::getSoapClient()->getDocumentSplitTypes($_bra5StructGetDocumentSplitTypes)));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named GetAvailableClasses
     * @uses Bra5WsdlClass::getSoapClient()
     * @uses Bra5WsdlClass::setResult()
     * @uses Bra5WsdlClass::saveLastError()
     * @param Bra5StructGetAvailableClasses $_bra5StructGetAvailableClasses
     * @return Bra5StructGetAvailableClassesResponse
     */
    public function GetAvailableClasses(Bra5StructGetAvailableClasses $_bra5StructGetAvailableClasses)
    {
        try
        {
            return $this->setResult(new Bra5StructGetAvailableClassesResponse(self::getSoapClient()->GetAvailableClasses($_bra5StructGetAvailableClasses)));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named getRelativeFileURL
     * @uses Bra5WsdlClass::getSoapClient()
     * @uses Bra5WsdlClass::setResult()
     * @uses Bra5WsdlClass::saveLastError()
     * @param Bra5StructGetRelativeFileURL $_bra5StructGetRelativeFileURL
     * @return Bra5StructGetRelativeFileURLResponse
     */
    public function getRelativeFileURL(Bra5StructGetRelativeFileURL $_bra5StructGetRelativeFileURL)
    {
        try
        {
            return $this->setResult(new Bra5StructGetRelativeFileURLResponse(self::getSoapClient()->getRelativeFileURL($_bra5StructGetRelativeFileURL)));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named getAvailableAttributes
     * @uses Bra5WsdlClass::getSoapClient()
     * @uses Bra5WsdlClass::setResult()
     * @uses Bra5WsdlClass::saveLastError()
     * @param Bra5StructGetAvailableAttributes $_bra5StructGetAvailableAttributes
     * @return Bra5StructGetAvailableAttributesResponse
     */
    public function getAvailableAttributes(Bra5StructGetAvailableAttributes $_bra5StructGetAvailableAttributes)
    {
        try
        {
            return $this->setResult(new Bra5StructGetAvailableAttributesResponse(self::getSoapClient()->getAvailableAttributes($_bra5StructGetAvailableAttributes)));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named getLookupValues
     * @uses Bra5WsdlClass::getSoapClient()
     * @uses Bra5WsdlClass::setResult()
     * @uses Bra5WsdlClass::saveLastError()
     * @param Bra5StructGetLookupValues $_bra5StructGetLookupValues
     * @return Bra5StructGetLookupValuesResponse
     */
    public function getLookupValues(Bra5StructGetLookupValues $_bra5StructGetLookupValues)
    {
        try
        {
            return $this->setResult(new Bra5StructGetLookupValuesResponse(self::getSoapClient()->getLookupValues($_bra5StructGetLookupValues)));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named getDocument
     * @uses Bra5WsdlClass::getSoapClient()
     * @uses Bra5WsdlClass::setResult()
     * @uses Bra5WsdlClass::saveLastError()
     * @param Bra5StructGetDocument $_bra5StructGetDocument
     * @return Bra5StructGetDocumentResponse
     */
    public function getDocument(Bra5StructGetDocument $_bra5StructGetDocument)
    {
        try
        {
            return $this->setResult(new Bra5StructGetDocumentResponse(self::getSoapClient()->getDocument($_bra5StructGetDocument)));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named getAttribute
     * @uses Bra5WsdlClass::getSoapClient()
     * @uses Bra5WsdlClass::setResult()
     * @uses Bra5WsdlClass::saveLastError()
     * @param Bra5StructGetAttribute $_bra5StructGetAttribute
     * @return Bra5StructGetAttributeResponse
     */
    public function getAttribute(Bra5StructGetAttribute $_bra5StructGetAttribute)
    {
        try
        {
            return $this->setResult(new Bra5StructGetAttributeResponse(self::getSoapClient()->getAttribute($_bra5StructGetAttribute)));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named getFileName
     * @uses Bra5WsdlClass::getSoapClient()
     * @uses Bra5WsdlClass::setResult()
     * @uses Bra5WsdlClass::saveLastError()
     * @param Bra5StructGetFileName $_bra5StructGetFileName
     * @return Bra5StructGetFileNameResponse
     */
    public function getFileName(Bra5StructGetFileName $_bra5StructGetFileName)
    {
        try
        {
            return $this->setResult(new Bra5StructGetFileNameResponse(self::getSoapClient()->getFileName($_bra5StructGetFileName)));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named getFileAsByteArray
     * @uses Bra5WsdlClass::getSoapClient()
     * @uses Bra5WsdlClass::setResult()
     * @uses Bra5WsdlClass::saveLastError()
     * @param Bra5StructGetFileAsByteArray $_bra5StructGetFileAsByteArray
     * @return Bra5StructGetFileAsByteArrayResponse
     */
    public function getFileAsByteArray(Bra5StructGetFileAsByteArray $_bra5StructGetFileAsByteArray)
    {
        try
        {
            return $this->setResult(new Bra5StructGetFileAsByteArrayResponse(self::getSoapClient()->getFileAsByteArray($_bra5StructGetFileAsByteArray)));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named GetArchiveName
     * @uses Bra5WsdlClass::getSoapClient()
     * @uses Bra5WsdlClass::setResult()
     * @uses Bra5WsdlClass::saveLastError()
     * @param Bra5StructGetArchiveName $_bra5StructGetArchiveName
     * @return Bra5StructGetArchiveNameResponse
     */
    public function GetArchiveName(Bra5StructGetArchiveName $_bra5StructGetArchiveName)
    {
        try
        {
            return $this->setResult(new Bra5StructGetArchiveNameResponse(self::getSoapClient()->GetArchiveName($_bra5StructGetArchiveName)));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Returns the result
     * @see Bra5WsdlClass::getResult()
     * @return Bra5StructGetArchiveNameResponse|Bra5StructGetAttributeResponse|Bra5StructGetAvailableAttributesResponse|Bra5StructGetAvailableClassesResponse|Bra5StructGetDocumentResponse|Bra5StructGetDocumentSplitTypesResponse|Bra5StructGetFileAsByteArrayResponse|Bra5StructGetFileNameResponse|Bra5StructGetLookupValuesResponse|Bra5StructGetProductionLinesResponse|Bra5StructGetRelativeFileURLResponse
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
