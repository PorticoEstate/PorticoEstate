<?php
/**
 * File for class Bra5StructArrayOfProductionLine
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructArrayOfProductionLine originally named ArrayOfProductionLine
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructArrayOfProductionLine extends Bra5WsdlClass
{
    /**
     * The ProductionLine
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * - nillable : true
     * @var Bra5StructProductionLine
     */
    public $ProductionLine;
    /**
     * Constructor method for ArrayOfProductionLine
     * @see parent::__construct()
     * @param Bra5StructProductionLine $_productionLine
     * @return Bra5StructArrayOfProductionLine
     */
    public function __construct($_productionLine = NULL)
    {
        parent::__construct(array('ProductionLine'=>$_productionLine),false);
    }
    /**
     * Get ProductionLine value
     * @return Bra5StructProductionLine|null
     */
    public function getProductionLine()
    {
        return $this->ProductionLine;
    }
    /**
     * Set ProductionLine value
     * @param Bra5StructProductionLine $_productionLine the ProductionLine
     * @return Bra5StructProductionLine
     */
    public function setProductionLine($_productionLine)
    {
        return ($this->ProductionLine = $_productionLine);
    }
    /**
     * Returns the current element
     * @see Bra5WsdlClass::current()
     * @return Bra5StructProductionLine
     */
    public function current()
    {
        return parent::current();
    }
    /**
     * Returns the indexed element
     * @see Bra5WsdlClass::item()
     * @param int $_index
     * @return Bra5StructProductionLine
     */
    public function item($_index)
    {
        return parent::item($_index);
    }
    /**
     * Returns the first element
     * @see Bra5WsdlClass::first()
     * @return Bra5StructProductionLine
     */
    public function first()
    {
        return parent::first();
    }
    /**
     * Returns the last element
     * @see Bra5WsdlClass::last()
     * @return Bra5StructProductionLine
     */
    public function last()
    {
        return parent::last();
    }
    /**
     * Returns the element at the offset
     * @see Bra5WsdlClass::last()
     * @param int $_offset
     * @return Bra5StructProductionLine
     */
    public function offsetGet($_offset)
    {
        return parent::offsetGet($_offset);
    }
    /**
     * Returns the attribute name
     * @see Bra5WsdlClass::getAttributeName()
     * @return string ProductionLine
     */
    public function getAttributeName()
    {
        return 'ProductionLine';
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructArrayOfProductionLine
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
