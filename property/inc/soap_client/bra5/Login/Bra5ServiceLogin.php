<?php
/**
 * File for class Bra5ServiceLogin
 * @package Bra5
 * @subpackage Services
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5ServiceLogin originally named Login
 * @package Bra5
 * @subpackage Services
 * @date 2015-03-06
 */
class Bra5ServiceLogin extends Bra5WsdlClass
{
    /**
     * Method to call the operation originally named Login
     * @uses Bra5WsdlClass::getSoapClient()
     * @uses Bra5WsdlClass::setResult()
     * @uses Bra5WsdlClass::saveLastError()
     * @param Bra5StructLogin $_bra5StructLogin
     * @return Bra5StructLoginResponse
     */
    public function Login(Bra5StructLogin $_bra5StructLogin)
    {
        try
        {
            return $this->setResult(new Bra5StructLoginResponse(self::getSoapClient()->Login($_bra5StructLogin)));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Returns the result
     * @see Bra5WsdlClass::getResult()
     * @return Bra5StructLoginResponse
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
