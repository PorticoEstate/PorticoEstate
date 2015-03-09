<?php
/**
 * File for class Bra5StructGetAttribute
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructGetAttribute originally named getAttribute
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructGetAttribute extends Bra5WsdlClass
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
     * The documentId
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $documentId;
    /**
     * The attributeName
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $attributeName;
    /**
     * Constructor method for getAttribute
     * @see parent::__construct()
     * @param string $_secKey
     * @param string $_documentId
     * @param string $_attributeName
     * @return Bra5StructGetAttribute
     */
    public function __construct($_secKey = NULL,$_documentId = NULL,$_attributeName = NULL)
    {
        parent::__construct(array('secKey'=>$_secKey,'documentId'=>$_documentId,'attributeName'=>$_attributeName),false);
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
     * Get documentId value
     * @return string|null
     */
    public function getDocumentId()
    {
        return $this->documentId;
    }
    /**
     * Set documentId value
     * @param string $_documentId the documentId
     * @return string
     */
    public function setDocumentId($_documentId)
    {
        return ($this->documentId = $_documentId);
    }
    /**
     * Get attributeName value
     * @return string|null
     */
    public function getAttributeName()
    {
        return $this->attributeName;
    }
    /**
     * Set attributeName value
     * @param string $_attributeName the attributeName
     * @return string
     */
    public function setAttributeName($_attributeName)
    {
        return ($this->attributeName = $_attributeName);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructGetAttribute
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
