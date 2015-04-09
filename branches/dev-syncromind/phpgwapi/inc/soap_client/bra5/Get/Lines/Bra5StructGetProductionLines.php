<?php
/**
 * File for class Bra5StructGetProductionLines
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructGetProductionLines originally named getProductionLines
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructGetProductionLines extends Bra5WsdlClass
{
    /**
     * The seckey
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $seckey;
    /**
     * Constructor method for getProductionLines
     * @see parent::__construct()
     * @param string $_seckey
     * @return Bra5StructGetProductionLines
     */
    public function __construct($_seckey = NULL)
    {
        parent::__construct(array('seckey'=>$_seckey),false);
    }
    /**
     * Get seckey value
     * @return string|null
     */
    public function getSeckey()
    {
        return $this->seckey;
    }
    /**
     * Set seckey value
     * @param string $_seckey the seckey
     * @return string
     */
    public function setSeckey($_seckey)
    {
        return ($this->seckey = $_seckey);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructGetProductionLines
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
