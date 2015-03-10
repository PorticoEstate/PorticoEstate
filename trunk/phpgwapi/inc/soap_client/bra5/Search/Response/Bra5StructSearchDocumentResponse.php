<?php
/**
 * File for class Bra5StructSearchDocumentResponse
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructSearchDocumentResponse originally named searchDocumentResponse
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructSearchDocumentResponse extends Bra5WsdlClass
{
    /**
     * The searchDocumentResult
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var Bra5StructArrayOfString
     */
    public $searchDocumentResult;
    /**
     * Constructor method for searchDocumentResponse
     * @see parent::__construct()
     * @param Bra5StructArrayOfString $_searchDocumentResult
     * @return Bra5StructSearchDocumentResponse
     */
    public function __construct($_searchDocumentResult = NULL)
    {
        parent::__construct(array('searchDocumentResult'=>($_searchDocumentResult instanceof Bra5StructArrayOfString)?$_searchDocumentResult:new Bra5StructArrayOfString($_searchDocumentResult)),false);
    }
    /**
     * Get searchDocumentResult value
     * @return Bra5StructArrayOfString|null
     */
    public function getSearchDocumentResult()
    {
        return $this->searchDocumentResult;
    }
    /**
     * Set searchDocumentResult value
     * @param Bra5StructArrayOfString $_searchDocumentResult the searchDocumentResult
     * @return Bra5StructArrayOfString
     */
    public function setSearchDocumentResult($_searchDocumentResult)
    {
        return ($this->searchDocumentResult = $_searchDocumentResult);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructSearchDocumentResponse
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
