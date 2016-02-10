<?php
/**
 * File for class Bra5StructGetDocumentSplitTypesResponse
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructGetDocumentSplitTypesResponse originally named getDocumentSplitTypesResponse
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructGetDocumentSplitTypesResponse extends Bra5WsdlClass
{
    /**
     * The getDocumentSplitTypesResult
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var Bra5StructArrayOfDocumentSplitType
     */
    public $getDocumentSplitTypesResult;
    /**
     * Constructor method for getDocumentSplitTypesResponse
     * @see parent::__construct()
     * @param Bra5StructArrayOfDocumentSplitType $_getDocumentSplitTypesResult
     * @return Bra5StructGetDocumentSplitTypesResponse
     */
    public function __construct($_getDocumentSplitTypesResult = NULL)
    {
        parent::__construct(array('getDocumentSplitTypesResult'=>($_getDocumentSplitTypesResult instanceof Bra5StructArrayOfDocumentSplitType)?$_getDocumentSplitTypesResult:new Bra5StructArrayOfDocumentSplitType($_getDocumentSplitTypesResult)),false);
    }
    /**
     * Get getDocumentSplitTypesResult value
     * @return Bra5StructArrayOfDocumentSplitType|null
     */
    public function getGetDocumentSplitTypesResult()
    {
        return $this->getDocumentSplitTypesResult;
    }
    /**
     * Set getDocumentSplitTypesResult value
     * @param Bra5StructArrayOfDocumentSplitType $_getDocumentSplitTypesResult the getDocumentSplitTypesResult
     * @return Bra5StructArrayOfDocumentSplitType
     */
    public function setGetDocumentSplitTypesResult($_getDocumentSplitTypesResult)
    {
        return ($this->getDocumentSplitTypesResult = $_getDocumentSplitTypesResult);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructGetDocumentSplitTypesResponse
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
