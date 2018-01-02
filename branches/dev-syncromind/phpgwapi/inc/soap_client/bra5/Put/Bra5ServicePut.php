<?php
/**
 * File for class Bra5ServicePut
 * @package Bra5
 * @subpackage Services
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5ServicePut originally named Put
 * @package Bra5
 * @subpackage Services
 * @date 2015-03-06
 */
class Bra5ServicePut extends Bra5WsdlClass
{
    /**
     * Method to call the operation originally named putFileAsByteArray
     * @uses Bra5WsdlClass::getSoapClient()
     * @uses Bra5WsdlClass::setResult()
     * @uses Bra5WsdlClass::saveLastError()
     * @param Bra5StructPutFileAsByteArray $_bra5StructPutFileAsByteArray
     * @return Bra5StructPutFileAsByteArrayResponse
     */
    public function putFileAsByteArray(Bra5StructPutFileAsByteArray $_bra5StructPutFileAsByteArray)
    {
        try
        {
            return $this->setResult(new Bra5StructPutFileAsByteArrayResponse(self::getSoapClient()->putFileAsByteArray($_bra5StructPutFileAsByteArray)));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Returns the result
     * @see Bra5WsdlClass::getResult()
     * @return Bra5StructPutFileAsByteArrayResponse
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
