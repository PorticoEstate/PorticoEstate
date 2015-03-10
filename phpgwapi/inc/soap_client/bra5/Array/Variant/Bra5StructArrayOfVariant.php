<?php
/**
 * File for class Bra5StructArrayOfVariant
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructArrayOfVariant originally named ArrayOfVariant
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructArrayOfVariant extends Bra5WsdlClass
{
    /**
     * The Variant
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * - nillable : true
     * @var Bra5StructVariant
     */
    public $Variant;
    /**
     * Constructor method for ArrayOfVariant
     * @see parent::__construct()
     * @param Bra5StructVariant $_variant
     * @return Bra5StructArrayOfVariant
     */
    public function __construct($_variant = NULL)
    {
        parent::__construct(array('Variant'=>$_variant),false);
    }
    /**
     * Get Variant value
     * @return Bra5StructVariant|null
     */
    public function getVariant()
    {
        return $this->Variant;
    }
    /**
     * Set Variant value
     * @param Bra5StructVariant $_variant the Variant
     * @return Bra5StructVariant
     */
    public function setVariant($_variant)
    {
        return ($this->Variant = $_variant);
    }
    /**
     * Returns the current element
     * @see Bra5WsdlClass::current()
     * @return Bra5StructVariant
     */
    public function current()
    {
        return parent::current();
    }
    /**
     * Returns the indexed element
     * @see Bra5WsdlClass::item()
     * @param int $_index
     * @return Bra5StructVariant
     */
    public function item($_index)
    {
        return parent::item($_index);
    }
    /**
     * Returns the first element
     * @see Bra5WsdlClass::first()
     * @return Bra5StructVariant
     */
    public function first()
    {
        return parent::first();
    }
    /**
     * Returns the last element
     * @see Bra5WsdlClass::last()
     * @return Bra5StructVariant
     */
    public function last()
    {
        return parent::last();
    }
    /**
     * Returns the element at the offset
     * @see Bra5WsdlClass::last()
     * @param int $_offset
     * @return Bra5StructVariant
     */
    public function offsetGet($_offset)
    {
        return parent::offsetGet($_offset);
    }
    /**
     * Returns the attribute name
     * @see Bra5WsdlClass::getAttributeName()
     * @return string Variant
     */
    public function getAttributeName()
    {
        return 'Variant';
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructArrayOfVariant
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
