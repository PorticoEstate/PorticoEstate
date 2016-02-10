<?php
/**
 * File for class Bra5StructExtendedDocument
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructExtendedDocument originally named ExtendedDocument
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructExtendedDocument extends Bra5StructDocument
{
    /**
     * The Variants
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var Bra5StructArrayOfVariant
     */
    public $Variants;
    /**
     * Constructor method for ExtendedDocument
     * @see parent::__construct()
     * @param Bra5StructArrayOfVariant $_variants
     * @return Bra5StructExtendedDocument
     */
    public function __construct($_variants = NULL)
    {
        Bra5WsdlClass::__construct(array('Variants'=>($_variants instanceof Bra5StructArrayOfVariant)?$_variants:new Bra5StructArrayOfVariant($_variants)),false);
    }
    /**
     * Get Variants value
     * @return Bra5StructArrayOfVariant|null
     */
    public function getVariants()
    {
        return $this->Variants;
    }
    /**
     * Set Variants value
     * @param Bra5StructArrayOfVariant $_variants the Variants
     * @return Bra5StructArrayOfVariant
     */
    public function setVariants($_variants)
    {
        return ($this->Variants = $_variants);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructExtendedDocument
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
