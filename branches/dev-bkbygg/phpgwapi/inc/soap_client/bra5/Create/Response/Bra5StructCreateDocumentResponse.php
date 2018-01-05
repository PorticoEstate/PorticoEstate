<?php
/**
 * File for class Bra5StructCreateDocumentResponse
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructCreateDocumentResponse originally named createDocumentResponse
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructCreateDocumentResponse extends Bra5WsdlClass
{
    /**
     * The createDocumentResult
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var Bra5StructDocument
     */
    public $createDocumentResult;
    /**
     * Constructor method for createDocumentResponse
     * @see parent::__construct()
     * @param Bra5StructDocument $_createDocumentResult
     * @return Bra5StructCreateDocumentResponse
     */
    public function __construct($_createDocumentResult = NULL)
    {
        parent::__construct(array('createDocumentResult'=>$_createDocumentResult),false);
    }
    /**
     * Get createDocumentResult value
     * @return Bra5StructDocument|null
     */
    public function getCreateDocumentResult()
    {
        return $this->createDocumentResult;
    }
    /**
     * Set createDocumentResult value
     * @param Bra5StructDocument $_createDocumentResult the createDocumentResult
     * @return Bra5StructDocument
     */
    public function setCreateDocumentResult($_createDocumentResult)
    {
        return ($this->createDocumentResult = $_createDocumentResult);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructCreateDocumentResponse
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
