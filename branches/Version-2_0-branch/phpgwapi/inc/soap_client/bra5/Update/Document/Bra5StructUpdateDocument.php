<?php
/**
 * File for class Bra5StructUpdateDocument
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructUpdateDocument originally named updateDocument
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructUpdateDocument extends Bra5WsdlClass
{
    /**
     * The secKey
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $secKey;
    /**
     * The document
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var Bra5StructDocument
     */
    public $document;
    /**
     * Constructor method for updateDocument
     * @see parent::__construct()
     * @param string $_secKey
     * @param Bra5StructDocument $_document
     * @return Bra5StructUpdateDocument
     */
    public function __construct($_secKey = NULL,$_document = NULL)
    {
        parent::__construct(array('secKey'=>$_secKey,'document'=>$_document),false);
    }
    /**
     * Get secKey value
     * @return string|null
     */
    public function getSecKey()
    {
        return $this->secKey;
    }
    /**
     * Set secKey value
     * @param string $_secKey the secKey
     * @return string
     */
    public function setSecKey($_secKey)
    {
        return ($this->secKey = $_secKey);
    }
    /**
     * Get document value
     * @return Bra5StructDocument|null
     */
    public function getDocument()
    {
        return $this->document;
    }
    /**
     * Set document value
     * @param Bra5StructDocument $_document the document
     * @return Bra5StructDocument
     */
    public function setDocument($_document)
    {
        return ($this->document = $_document);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructUpdateDocument
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
