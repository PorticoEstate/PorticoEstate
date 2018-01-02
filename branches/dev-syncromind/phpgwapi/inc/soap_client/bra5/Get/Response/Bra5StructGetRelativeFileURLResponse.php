<?php
/**
 * File for class Bra5StructGetRelativeFileURLResponse
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructGetRelativeFileURLResponse originally named getRelativeFileURLResponse
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructGetRelativeFileURLResponse extends Bra5WsdlClass
{
    /**
     * The getRelativeFileURLResult
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $getRelativeFileURLResult;
    /**
     * Constructor method for getRelativeFileURLResponse
     * @see parent::__construct()
     * @param string $_getRelativeFileURLResult
     * @return Bra5StructGetRelativeFileURLResponse
     */
    public function __construct($_getRelativeFileURLResult = NULL)
    {
        parent::__construct(array('getRelativeFileURLResult'=>$_getRelativeFileURLResult),false);
    }
    /**
     * Get getRelativeFileURLResult value
     * @return string|null
     */
    public function getGetRelativeFileURLResult()
    {
        return $this->getRelativeFileURLResult;
    }
    /**
     * Set getRelativeFileURLResult value
     * @param string $_getRelativeFileURLResult the getRelativeFileURLResult
     * @return string
     */
    public function setGetRelativeFileURLResult($_getRelativeFileURLResult)
    {
        return ($this->getRelativeFileURLResult = $_getRelativeFileURLResult);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructGetRelativeFileURLResponse
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
