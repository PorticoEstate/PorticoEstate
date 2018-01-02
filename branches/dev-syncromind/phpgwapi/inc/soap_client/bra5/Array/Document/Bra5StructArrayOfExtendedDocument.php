<?php
/**
 * File for class Bra5StructArrayOfExtendedDocument
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructArrayOfExtendedDocument originally named ArrayOfExtendedDocument
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructArrayOfExtendedDocument extends Bra5WsdlClass
{
    /**
     * The ExtendedDocument
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * - nillable : true
     * @var Bra5StructExtendedDocument
     */
    public $ExtendedDocument;
    /**
     * Constructor method for ArrayOfExtendedDocument
     * @see parent::__construct()
     * @param Bra5StructExtendedDocument $_extendedDocument
     * @return Bra5StructArrayOfExtendedDocument
     */
    public function __construct($_extendedDocument = NULL)
    {
        parent::__construct(array('ExtendedDocument'=>$_extendedDocument),false);
    }
    /**
     * Get ExtendedDocument value
     * @return Bra5StructExtendedDocument|null
     */
    public function getExtendedDocument()
    {
        return $this->ExtendedDocument;
    }
    /**
     * Set ExtendedDocument value
     * @param Bra5StructExtendedDocument $_extendedDocument the ExtendedDocument
     * @return Bra5StructExtendedDocument
     */
    public function setExtendedDocument($_extendedDocument)
    {
        return ($this->ExtendedDocument = $_extendedDocument);
    }
    /**
     * Returns the current element
     * @see Bra5WsdlClass::current()
     * @return Bra5StructExtendedDocument
     */
    public function current()
    {
        return parent::current();
    }
    /**
     * Returns the indexed element
     * @see Bra5WsdlClass::item()
     * @param int $_index
     * @return Bra5StructExtendedDocument
     */
    public function item($_index)
    {
        return parent::item($_index);
    }
    /**
     * Returns the first element
     * @see Bra5WsdlClass::first()
     * @return Bra5StructExtendedDocument
     */
    public function first()
    {
        return parent::first();
    }
    /**
     * Returns the last element
     * @see Bra5WsdlClass::last()
     * @return Bra5StructExtendedDocument
     */
    public function last()
    {
        return parent::last();
    }
    /**
     * Returns the element at the offset
     * @see Bra5WsdlClass::last()
     * @param int $_offset
     * @return Bra5StructExtendedDocument
     */
    public function offsetGet($_offset)
    {
        return parent::offsetGet($_offset);
    }
    /**
     * Returns the attribute name
     * @see Bra5WsdlClass::getAttributeName()
     * @return string ExtendedDocument
     */
    public function getAttributeName()
    {
        return 'ExtendedDocument';
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructArrayOfExtendedDocument
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
