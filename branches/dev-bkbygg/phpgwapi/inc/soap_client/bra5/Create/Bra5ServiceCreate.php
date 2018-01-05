<?php
/**
 * File for class Bra5ServiceCreate
 * @package Bra5
 * @subpackage Services
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5ServiceCreate originally named Create
 * @package Bra5
 * @subpackage Services
 * @date 2015-03-06
 */
class Bra5ServiceCreate extends Bra5WsdlClass
{
    /**
     * Method to call the operation originally named createDocument
     * @uses Bra5WsdlClass::getSoapClient()
     * @uses Bra5WsdlClass::setResult()
     * @uses Bra5WsdlClass::saveLastError()
     * @param Bra5StructCreateDocument $_bra5StructCreateDocument
     * @return Bra5StructCreateDocumentResponse
     */
    public function createDocument(Bra5StructCreateDocument $_bra5StructCreateDocument)
    {
        try
        {
            return $this->setResult(new Bra5StructCreateDocumentResponse(self::getSoapClient()->createDocument($_bra5StructCreateDocument)));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Returns the result
     * @see Bra5WsdlClass::getResult()
     * @return Bra5StructCreateDocumentResponse
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
