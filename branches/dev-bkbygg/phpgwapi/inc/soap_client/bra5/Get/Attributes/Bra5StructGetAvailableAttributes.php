<?php
/**
 * File for class Bra5StructGetAvailableAttributes
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructGetAvailableAttributes originally named getAvailableAttributes
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructGetAvailableAttributes extends Bra5WsdlClass
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
     * The classname
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $classname;
    /**
     * Constructor method for getAvailableAttributes
     * @see parent::__construct()
     * @param string $_secKey
     * @param string $_baseclassname
     * @param string $_classname
     * @return Bra5StructGetAvailableAttributes
     */
    public function __construct($_secKey = NULL,$_baseclassname = NULL,$_classname = NULL)
    {
        parent::__construct(array('secKey'=>$_secKey,'baseclassname'=>$_baseclassname,'classname'=>$_classname),false);
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
     * Get classname value
     * @return string|null
     */
    public function getClassname()
    {
        return $this->classname;
    }
    /**
     * Set classname value
     * @param string $_classname the classname
     * @return string
     */
    public function setClassname($_classname)
    {
        return ($this->classname = $_classname);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructGetAvailableAttributes
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
