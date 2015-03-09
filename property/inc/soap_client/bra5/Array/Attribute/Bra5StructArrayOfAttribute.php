<?php
/**
 * File for class Bra5StructArrayOfAttribute
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructArrayOfAttribute originally named ArrayOfAttribute
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructArrayOfAttribute extends Bra5WsdlClass
{
    /**
     * The Attribute
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * - nillable : true
     * @var Bra5StructAttribute
     */
    public $Attribute;
    /**
     * Constructor method for ArrayOfAttribute
     * @see parent::__construct()
     * @param Bra5StructAttribute $_attribute
     * @return Bra5StructArrayOfAttribute
     */
    public function __construct($_attribute = NULL)
    {
        parent::__construct(array('Attribute'=>$_attribute),false);
    }
    /**
     * Get Attribute value
     * @return Bra5StructAttribute|null
     */
    public function getAttribute()
    {
        return $this->Attribute;
    }
    /**
     * Set Attribute value
     * @param Bra5StructAttribute $_attribute the Attribute
     * @return Bra5StructAttribute
     */
    public function setAttribute($_attribute)
    {
        return ($this->Attribute = $_attribute);
    }
    /**
     * Returns the current element
     * @see Bra5WsdlClass::current()
     * @return Bra5StructAttribute
     */
    public function current()
    {
        return parent::current();
    }
    /**
     * Returns the indexed element
     * @see Bra5WsdlClass::item()
     * @param int $_index
     * @return Bra5StructAttribute
     */
    public function item($_index)
    {
        return parent::item($_index);
    }
    /**
     * Returns the first element
     * @see Bra5WsdlClass::first()
     * @return Bra5StructAttribute
     */
    public function first()
    {
        return parent::first();
    }
    /**
     * Returns the last element
     * @see Bra5WsdlClass::last()
     * @return Bra5StructAttribute
     */
    public function last()
    {
        return parent::last();
    }
    /**
     * Returns the element at the offset
     * @see Bra5WsdlClass::last()
     * @param int $_offset
     * @return Bra5StructAttribute
     */
    public function offsetGet($_offset)
    {
        return parent::offsetGet($_offset);
    }
    /**
     * Returns the attribute name
     * @see Bra5WsdlClass::getAttributeName()
     * @return string Attribute
     */
    public function getAttributeName()
    {
        return 'Attribute';
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructArrayOfAttribute
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
