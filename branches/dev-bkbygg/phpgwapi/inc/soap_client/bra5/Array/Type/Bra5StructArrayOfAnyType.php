<?php
/**
 * File for class Bra5StructArrayOfAnyType
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructArrayOfAnyType originally named ArrayOfAnyType
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructArrayOfAnyType extends Bra5WsdlClass
{
    /**
     * The anyType
     * @var anyType
     */
    public $anyType;
    /**
     * Constructor method for ArrayOfAnyType
     * @see parent::__construct()
     * @param anyType $_anyType
     * @return Bra5StructArrayOfAnyType
     */
    public function __construct($_anyType = NULL)
    {
        parent::__construct(array('anyType'=>$_anyType),false);
    }
    /**
     * Get anyType value
     * @return anyType|null
     */
    public function getAnyType()
    {
        return $this->anyType;
    }
    /**
     * Set anyType value
     * @param anyType $_anyType the anyType
     * @return anyType
     */
    public function setAnyType($_anyType)
    {
        return ($this->anyType = $_anyType);
    }
    /**
     * Returns the current element
     * @see Bra5WsdlClass::current()
     * @return anyType
     */
    public function current()
    {
        return parent::current();
    }
    /**
     * Returns the indexed element
     * @see Bra5WsdlClass::item()
     * @param int $_index
     * @return anyType
     */
    public function item($_index)
    {
        return parent::item($_index);
    }
    /**
     * Returns the first element
     * @see Bra5WsdlClass::first()
     * @return anyType
     */
    public function first()
    {
        return parent::first();
    }
    /**
     * Returns the last element
     * @see Bra5WsdlClass::last()
     * @return anyType
     */
    public function last()
    {
        return parent::last();
    }
    /**
     * Returns the element at the offset
     * @see Bra5WsdlClass::last()
     * @param int $_offset
     * @return anyType
     */
    public function offsetGet($_offset)
    {
        return parent::offsetGet($_offset);
    }
    /**
     * Returns the attribute name
     * @see Bra5WsdlClass::getAttributeName()
     * @return string anyType
     */
    public function getAttributeName()
    {
        return 'anyType';
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructArrayOfAnyType
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
