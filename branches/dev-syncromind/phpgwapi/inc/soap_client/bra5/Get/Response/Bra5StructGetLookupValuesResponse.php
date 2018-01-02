<?php
/**
 * File for class Bra5StructGetLookupValuesResponse
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructGetLookupValuesResponse originally named getLookupValuesResponse
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructGetLookupValuesResponse extends Bra5WsdlClass
{
    /**
     * The getLookupValuesResult
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var Bra5StructArrayOfLookupValue
     */
    public $getLookupValuesResult;
    /**
     * Constructor method for getLookupValuesResponse
     * @see parent::__construct()
     * @param Bra5StructArrayOfLookupValue $_getLookupValuesResult
     * @return Bra5StructGetLookupValuesResponse
     */
    public function __construct($_getLookupValuesResult = NULL)
    {
        parent::__construct(array('getLookupValuesResult'=>($_getLookupValuesResult instanceof Bra5StructArrayOfLookupValue)?$_getLookupValuesResult:new Bra5StructArrayOfLookupValue($_getLookupValuesResult)),false);
    }
    /**
     * Get getLookupValuesResult value
     * @return Bra5StructArrayOfLookupValue|null
     */
    public function getGetLookupValuesResult()
    {
        return $this->getLookupValuesResult;
    }
    /**
     * Set getLookupValuesResult value
     * @param Bra5StructArrayOfLookupValue $_getLookupValuesResult the getLookupValuesResult
     * @return Bra5StructArrayOfLookupValue
     */
    public function setGetLookupValuesResult($_getLookupValuesResult)
    {
        return ($this->getLookupValuesResult = $_getLookupValuesResult);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructGetLookupValuesResponse
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
