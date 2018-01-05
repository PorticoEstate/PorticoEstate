<?php
/**
 * File for class Bra5StructGetAvailableAttributesResponse
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructGetAvailableAttributesResponse originally named getAvailableAttributesResponse
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructGetAvailableAttributesResponse extends Bra5WsdlClass
{
    /**
     * The getAvailableAttributesResult
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var Bra5StructArrayOfAttribute
     */
    public $getAvailableAttributesResult;
    /**
     * Constructor method for getAvailableAttributesResponse
     * @see parent::__construct()
     * @param Bra5StructArrayOfAttribute $_getAvailableAttributesResult
     * @return Bra5StructGetAvailableAttributesResponse
     */
    public function __construct($_getAvailableAttributesResult = NULL)
    {
        parent::__construct(array('getAvailableAttributesResult'=>($_getAvailableAttributesResult instanceof Bra5StructArrayOfAttribute)?$_getAvailableAttributesResult:new Bra5StructArrayOfAttribute($_getAvailableAttributesResult)),false);
    }
    /**
     * Get getAvailableAttributesResult value
     * @return Bra5StructArrayOfAttribute|null
     */
    public function getGetAvailableAttributesResult()
    {
        return $this->getAvailableAttributesResult;
    }
    /**
     * Set getAvailableAttributesResult value
     * @param Bra5StructArrayOfAttribute $_getAvailableAttributesResult the getAvailableAttributesResult
     * @return Bra5StructArrayOfAttribute
     */
    public function setGetAvailableAttributesResult($_getAvailableAttributesResult)
    {
        return ($this->getAvailableAttributesResult = $_getAvailableAttributesResult);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructGetAvailableAttributesResponse
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
