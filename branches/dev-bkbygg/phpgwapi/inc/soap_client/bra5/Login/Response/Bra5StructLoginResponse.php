<?php
/**
 * File for class Bra5StructLoginResponse
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructLoginResponse originally named LoginResponse
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructLoginResponse extends Bra5WsdlClass
{
    /**
     * The LoginResult
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $LoginResult;
    /**
     * Constructor method for LoginResponse
     * @see parent::__construct()
     * @param string $_loginResult
     * @return Bra5StructLoginResponse
     */
    public function __construct($_loginResult = NULL)
    {
        parent::__construct(array('LoginResult'=>$_loginResult),false);
    }
    /**
     * Get LoginResult value
     * @return string|null
     */
    public function getLoginResult()
    {
        return $this->LoginResult;
    }
    /**
     * Set LoginResult value
     * @param string $_loginResult the LoginResult
     * @return string
     */
    public function setLoginResult($_loginResult)
    {
        return ($this->LoginResult = $_loginResult);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructLoginResponse
     */
    public static function __set_state(array $_array,$_className = __CLASS__)
    {
        return parent::__set_state($_array,$_className);
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
