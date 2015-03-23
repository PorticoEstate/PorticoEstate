<?php
/**
 * File for class Bra5ServiceDelete
 * @package Bra5
 * @subpackage Services
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5ServiceDelete originally named Delete
 * @package Bra5
 * @subpackage Services
 * @date 2015-03-06
 */
class Bra5ServiceDelete extends Bra5WsdlClass
{
    /**
     * Method to call the operation originally named deleteDocument
     * @uses Bra5WsdlClass::getSoapClient()
     * @uses Bra5WsdlClass::setResult()
     * @uses Bra5WsdlClass::saveLastError()
     * @param Bra5StructDeleteDocument $_bra5StructDeleteDocument
     * @return Bra5StructDeleteDocumentResponse
     */
    public function deleteDocument(Bra5StructDeleteDocument $_bra5StructDeleteDocument)
    {
        try
        {
            return $this->setResult(new Bra5StructDeleteDocumentResponse(self::getSoapClient()->deleteDocument($_bra5StructDeleteDocument)));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Returns the result
     * @see Bra5WsdlClass::getResult()
     * @return Bra5StructDeleteDocumentResponse
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
