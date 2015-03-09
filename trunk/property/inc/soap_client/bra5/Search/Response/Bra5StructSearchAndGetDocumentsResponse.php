<?php
/**
 * File for class Bra5StructSearchAndGetDocumentsResponse
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructSearchAndGetDocumentsResponse originally named searchAndGetDocumentsResponse
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructSearchAndGetDocumentsResponse extends Bra5WsdlClass
{
    /**
     * The searchAndGetDocumentsResult
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var Bra5StructArrayOfExtendedDocument
     */
    public $searchAndGetDocumentsResult;
    /**
     * Constructor method for searchAndGetDocumentsResponse
     * @see parent::__construct()
     * @param Bra5StructArrayOfExtendedDocument $_searchAndGetDocumentsResult
     * @return Bra5StructSearchAndGetDocumentsResponse
     */
    public function __construct($_searchAndGetDocumentsResult = NULL)
    {
        parent::__construct(array('searchAndGetDocumentsResult'=>($_searchAndGetDocumentsResult instanceof Bra5StructArrayOfExtendedDocument)?$_searchAndGetDocumentsResult:new Bra5StructArrayOfExtendedDocument($_searchAndGetDocumentsResult)),false);
    }
    /**
     * Get searchAndGetDocumentsResult value
     * @return Bra5StructArrayOfExtendedDocument|null
     */
    public function getSearchAndGetDocumentsResult()
    {
        return $this->searchAndGetDocumentsResult;
    }
    /**
     * Set searchAndGetDocumentsResult value
     * @param Bra5StructArrayOfExtendedDocument $_searchAndGetDocumentsResult the searchAndGetDocumentsResult
     * @return Bra5StructArrayOfExtendedDocument
     */
    public function setSearchAndGetDocumentsResult($_searchAndGetDocumentsResult)
    {
        return ($this->searchAndGetDocumentsResult = $_searchAndGetDocumentsResult);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructSearchAndGetDocumentsResponse
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
