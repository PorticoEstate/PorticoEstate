<?php
/**
 * File for class Bra5StructGetArchiveNameResponse
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructGetArchiveNameResponse originally named GetArchiveNameResponse
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructGetArchiveNameResponse extends Bra5WsdlClass
{
    /**
     * The GetArchiveNameResult
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $GetArchiveNameResult;
    /**
     * Constructor method for GetArchiveNameResponse
     * @see parent::__construct()
     * @param string $_getArchiveNameResult
     * @return Bra5StructGetArchiveNameResponse
     */
    public function __construct($_getArchiveNameResult = NULL)
    {
        parent::__construct(array('GetArchiveNameResult'=>$_getArchiveNameResult),false);
    }
    /**
     * Get GetArchiveNameResult value
     * @return string|null
     */
    public function getGetArchiveNameResult()
    {
        return $this->GetArchiveNameResult;
    }
    /**
     * Set GetArchiveNameResult value
     * @param string $_getArchiveNameResult the GetArchiveNameResult
     * @return string
     */
    public function setGetArchiveNameResult($_getArchiveNameResult)
    {
        return ($this->GetArchiveNameResult = $_getArchiveNameResult);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructGetArchiveNameResponse
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
