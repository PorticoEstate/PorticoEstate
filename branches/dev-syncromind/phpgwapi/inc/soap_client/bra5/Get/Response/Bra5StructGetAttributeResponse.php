<?php
/**
 * File for class Bra5StructGetAttributeResponse
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructGetAttributeResponse originally named getAttributeResponse
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructGetAttributeResponse extends Bra5WsdlClass
{
    /**
     * The getAttributeResult
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var Bra5StructAttribute
     */
    public $getAttributeResult;
    /**
     * Constructor method for getAttributeResponse
     * @see parent::__construct()
     * @param Bra5StructAttribute $_getAttributeResult
     * @return Bra5StructGetAttributeResponse
     */
    public function __construct($_getAttributeResult = NULL)
    {
        parent::__construct(array('getAttributeResult'=>$_getAttributeResult),false);
    }
    /**
     * Get getAttributeResult value
     * @return Bra5StructAttribute|null
     */
    public function getGetAttributeResult()
    {
        return $this->getAttributeResult;
    }
    /**
     * Set getAttributeResult value
     * @param Bra5StructAttribute $_getAttributeResult the getAttributeResult
     * @return Bra5StructAttribute
     */
    public function setGetAttributeResult($_getAttributeResult)
    {
        return ($this->getAttributeResult = $_getAttributeResult);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructGetAttributeResponse
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
