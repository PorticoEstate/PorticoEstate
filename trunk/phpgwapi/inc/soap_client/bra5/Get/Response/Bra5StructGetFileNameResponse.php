<?php
/**
 * File for class Bra5StructGetFileNameResponse
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructGetFileNameResponse originally named getFileNameResponse
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructGetFileNameResponse extends Bra5WsdlClass
{
    /**
     * The getFileNameResult
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $getFileNameResult;
    /**
     * Constructor method for getFileNameResponse
     * @see parent::__construct()
     * @param string $_getFileNameResult
     * @return Bra5StructGetFileNameResponse
     */
    public function __construct($_getFileNameResult = NULL)
    {
        parent::__construct(array('getFileNameResult'=>$_getFileNameResult),false);
    }
    /**
     * Get getFileNameResult value
     * @return string|null
     */
    public function getGetFileNameResult()
    {
        return $this->getFileNameResult;
    }
    /**
     * Set getFileNameResult value
     * @param string $_getFileNameResult the getFileNameResult
     * @return string
     */
    public function setGetFileNameResult($_getFileNameResult)
    {
        return ($this->getFileNameResult = $_getFileNameResult);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructGetFileNameResponse
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
