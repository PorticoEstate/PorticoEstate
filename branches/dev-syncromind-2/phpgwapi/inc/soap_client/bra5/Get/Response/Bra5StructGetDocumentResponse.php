<?php
/**
 * File for class Bra5StructGetDocumentResponse
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructGetDocumentResponse originally named getDocumentResponse
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructGetDocumentResponse extends Bra5WsdlClass
{
    /**
     * The getDocumentResult
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var Bra5StructExtendedDocument
     */
    public $getDocumentResult;
    /**
     * Constructor method for getDocumentResponse
     * @see parent::__construct()
     * @param Bra5StructExtendedDocument $_getDocumentResult
     * @return Bra5StructGetDocumentResponse
     */
    public function __construct($_getDocumentResult = NULL)
    {
        parent::__construct(array('getDocumentResult'=>$_getDocumentResult),false);
    }
    /**
     * Get getDocumentResult value
     * @return Bra5StructExtendedDocument|null
     */
    public function getGetDocumentResult()
    {
        return $this->getDocumentResult;
    }
    /**
     * Set getDocumentResult value
     * @param Bra5StructExtendedDocument $_getDocumentResult the getDocumentResult
     * @return Bra5StructExtendedDocument
     */
    public function setGetDocumentResult($_getDocumentResult)
    {
        return ($this->getDocumentResult = $_getDocumentResult);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructGetDocumentResponse
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
