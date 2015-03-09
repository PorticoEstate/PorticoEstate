<?php
/**
 * File for class Bra5ServiceUpdate
 * @package Bra5
 * @subpackage Services
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5ServiceUpdate originally named Update
 * @package Bra5
 * @subpackage Services
 * @date 2015-03-06
 */
class Bra5ServiceUpdate extends Bra5WsdlClass
{
    /**
     * Method to call the operation originally named updateDocument
     * @uses Bra5WsdlClass::getSoapClient()
     * @uses Bra5WsdlClass::setResult()
     * @uses Bra5WsdlClass::saveLastError()
     * @param Bra5StructUpdateDocument $_bra5StructUpdateDocument
     * @return Bra5StructUpdateDocumentResponse
     */
    public function updateDocument(Bra5StructUpdateDocument $_bra5StructUpdateDocument)
    {
        try
        {
            return $this->setResult(new Bra5StructUpdateDocumentResponse(self::getSoapClient()->updateDocument($_bra5StructUpdateDocument)));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named updateAttribute
     * @uses Bra5WsdlClass::getSoapClient()
     * @uses Bra5WsdlClass::setResult()
     * @uses Bra5WsdlClass::saveLastError()
     * @param Bra5StructUpdateAttribute $_bra5StructUpdateAttribute
     * @return Bra5StructUpdateAttributeResponse
     */
    public function updateAttribute(Bra5StructUpdateAttribute $_bra5StructUpdateAttribute)
    {
        try
        {
            return $this->setResult(new Bra5StructUpdateAttributeResponse(self::getSoapClient()->updateAttribute($_bra5StructUpdateAttribute)));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Returns the result
     * @see Bra5WsdlClass::getResult()
     * @return Bra5StructUpdateAttributeResponse|Bra5StructUpdateDocumentResponse
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
