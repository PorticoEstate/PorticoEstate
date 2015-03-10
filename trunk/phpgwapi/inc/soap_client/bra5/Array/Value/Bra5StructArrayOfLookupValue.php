<?php
/**
 * File for class Bra5StructArrayOfLookupValue
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructArrayOfLookupValue originally named ArrayOfLookupValue
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructArrayOfLookupValue extends Bra5WsdlClass
{
    /**
     * The LookupValue
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * - nillable : true
     * @var Bra5StructLookupValue
     */
    public $LookupValue;
    /**
     * Constructor method for ArrayOfLookupValue
     * @see parent::__construct()
     * @param Bra5StructLookupValue $_lookupValue
     * @return Bra5StructArrayOfLookupValue
     */
    public function __construct($_lookupValue = NULL)
    {
        parent::__construct(array('LookupValue'=>$_lookupValue),false);
    }
    /**
     * Get LookupValue value
     * @return Bra5StructLookupValue|null
     */
    public function getLookupValue()
    {
        return $this->LookupValue;
    }
    /**
     * Set LookupValue value
     * @param Bra5StructLookupValue $_lookupValue the LookupValue
     * @return Bra5StructLookupValue
     */
    public function setLookupValue($_lookupValue)
    {
        return ($this->LookupValue = $_lookupValue);
    }
    /**
     * Returns the current element
     * @see Bra5WsdlClass::current()
     * @return Bra5StructLookupValue
     */
    public function current()
    {
        return parent::current();
    }
    /**
     * Returns the indexed element
     * @see Bra5WsdlClass::item()
     * @param int $_index
     * @return Bra5StructLookupValue
     */
    public function item($_index)
    {
        return parent::item($_index);
    }
    /**
     * Returns the first element
     * @see Bra5WsdlClass::first()
     * @return Bra5StructLookupValue
     */
    public function first()
    {
        return parent::first();
    }
    /**
     * Returns the last element
     * @see Bra5WsdlClass::last()
     * @return Bra5StructLookupValue
     */
    public function last()
    {
        return parent::last();
    }
    /**
     * Returns the element at the offset
     * @see Bra5WsdlClass::last()
     * @param int $_offset
     * @return Bra5StructLookupValue
     */
    public function offsetGet($_offset)
    {
        return parent::offsetGet($_offset);
    }
    /**
     * Returns the attribute name
     * @see Bra5WsdlClass::getAttributeName()
     * @return string LookupValue
     */
    public function getAttributeName()
    {
        return 'LookupValue';
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructArrayOfLookupValue
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
