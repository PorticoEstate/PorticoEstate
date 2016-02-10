<?php
/**
 * File for class Bra5StructUpdateAttribute
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructUpdateAttribute originally named updateAttribute
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructUpdateAttribute extends Bra5WsdlClass
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
     * The baseclassname
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $baseclassname;
    /**
     * The documentId
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $documentId;
    /**
     * The attribute
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $attribute;
    /**
     * The value
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var Bra5StructArrayOfAnyType
     */
    public $value;
    /**
     * Constructor method for updateAttribute
     * @see parent::__construct()
     * @param string $_secKey
     * @param string $_baseclassname
     * @param string $_documentId
     * @param string $_attribute
     * @param Bra5StructArrayOfAnyType $_value
     * @return Bra5StructUpdateAttribute
     */
    public function __construct($_secKey = NULL,$_baseclassname = NULL,$_documentId = NULL,$_attribute = NULL,$_value = NULL)
    {
        parent::__construct(array('secKey'=>$_secKey,'baseclassname'=>$_baseclassname,'documentId'=>$_documentId,'attribute'=>$_attribute,'value'=>($_value instanceof Bra5StructArrayOfAnyType)?$_value:new Bra5StructArrayOfAnyType($_value)),false);
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
     * Get baseclassname value
     * @return string|null
     */
    public function getBaseclassname()
    {
        return $this->baseclassname;
    }
    /**
     * Set baseclassname value
     * @param string $_baseclassname the baseclassname
     * @return string
     */
    public function setBaseclassname($_baseclassname)
    {
        return ($this->baseclassname = $_baseclassname);
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
     * Get attribute value
     * @return string|null
     */
    public function getAttribute()
    {
        return $this->attribute;
    }
    /**
     * Set attribute value
     * @param string $_attribute the attribute
     * @return string
     */
    public function setAttribute($_attribute)
    {
        return ($this->attribute = $_attribute);
    }
    /**
     * Get value value
     * @return Bra5StructArrayOfAnyType|null
     */
    public function getValue()
    {
        return $this->value;
    }
    /**
     * Set value value
     * @param Bra5StructArrayOfAnyType $_value the value
     * @return Bra5StructArrayOfAnyType
     */
    public function setValue($_value)
    {
        return ($this->value = $_value);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructUpdateAttribute
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
