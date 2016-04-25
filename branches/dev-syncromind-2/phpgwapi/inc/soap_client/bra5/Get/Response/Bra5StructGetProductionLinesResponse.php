<?php
/**
 * File for class Bra5StructGetProductionLinesResponse
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructGetProductionLinesResponse originally named getProductionLinesResponse
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructGetProductionLinesResponse extends Bra5WsdlClass
{
    /**
     * The getProductionLinesResult
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var Bra5StructArrayOfProductionLine
     */
    public $getProductionLinesResult;
    /**
     * Constructor method for getProductionLinesResponse
     * @see parent::__construct()
     * @param Bra5StructArrayOfProductionLine $_getProductionLinesResult
     * @return Bra5StructGetProductionLinesResponse
     */
    public function __construct($_getProductionLinesResult = NULL)
    {
        parent::__construct(array('getProductionLinesResult'=>($_getProductionLinesResult instanceof Bra5StructArrayOfProductionLine)?$_getProductionLinesResult:new Bra5StructArrayOfProductionLine($_getProductionLinesResult)),false);
    }
    /**
     * Get getProductionLinesResult value
     * @return Bra5StructArrayOfProductionLine|null
     */
    public function getGetProductionLinesResult()
    {
        return $this->getProductionLinesResult;
    }
    /**
     * Set getProductionLinesResult value
     * @param Bra5StructArrayOfProductionLine $_getProductionLinesResult the getProductionLinesResult
     * @return Bra5StructArrayOfProductionLine
     */
    public function setGetProductionLinesResult($_getProductionLinesResult)
    {
        return ($this->getProductionLinesResult = $_getProductionLinesResult);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructGetProductionLinesResponse
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
