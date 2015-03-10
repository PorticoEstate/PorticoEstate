<?php
/**
 * File for class Bra5ServiceLogout
 * @package Bra5
 * @subpackage Services
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5ServiceLogout originally named Logout
 * @package Bra5
 * @subpackage Services
 * @date 2015-03-06
 */
class Bra5ServiceLogout extends Bra5WsdlClass
{
    /**
     * Method to call the operation originally named Logout
     * @uses Bra5WsdlClass::getSoapClient()
     * @uses Bra5WsdlClass::setResult()
     * @uses Bra5WsdlClass::saveLastError()
     * @param Bra5StructLogout $_bra5StructLogout
     * @return Bra5StructLogoutResponse
     */
    public function Logout(Bra5StructLogout $_bra5StructLogout)
    {
        try
        {
            return $this->setResult(new Bra5StructLogoutResponse(self::getSoapClient()->Logout($_bra5StructLogout)));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Returns the result
     * @see Bra5WsdlClass::getResult()
     * @return Bra5StructLogoutResponse
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
