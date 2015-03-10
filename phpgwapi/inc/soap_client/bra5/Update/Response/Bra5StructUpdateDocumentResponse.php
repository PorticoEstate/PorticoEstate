<?php
/**
 * File for class Bra5StructUpdateDocumentResponse
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructUpdateDocumentResponse originally named updateDocumentResponse
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructUpdateDocumentResponse extends Bra5WsdlClass
{
    /**
     * The updateDocumentResult
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var Bra5StructDocument
     */
    public $updateDocumentResult;
    /**
     * Constructor method for updateDocumentResponse
     * @see parent::__construct()
     * @param Bra5StructDocument $_updateDocumentResult
     * @return Bra5StructUpdateDocumentResponse
     */
    public function __construct($_updateDocumentResult = NULL)
    {
        parent::__construct(array('updateDocumentResult'=>$_updateDocumentResult),false);
    }
    /**
     * Get updateDocumentResult value
     * @return Bra5StructDocument|null
     */
    public function getUpdateDocumentResult()
    {
        return $this->updateDocumentResult;
    }
    /**
     * Set updateDocumentResult value
     * @param Bra5StructDocument $_updateDocumentResult the updateDocumentResult
     * @return Bra5StructDocument
     */
    public function setUpdateDocumentResult($_updateDocumentResult)
    {
        return ($this->updateDocumentResult = $_updateDocumentResult);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructUpdateDocumentResponse
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
