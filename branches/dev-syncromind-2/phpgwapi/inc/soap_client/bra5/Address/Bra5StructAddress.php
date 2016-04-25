<?php
/**
 * File for class Bra5StructAddress
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructAddress originally named Address
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructAddress extends Bra5WsdlClass
{
    /**
     * The Gate
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $Gate;
    /**
     * The Nummer
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $Nummer;
    /**
     * The Bokstav
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $Bokstav;
    /**
     * Constructor method for Address
     * @see parent::__construct()
     * @param string $_gate
     * @param string $_nummer
     * @param string $_bokstav
     * @return Bra5StructAddress
     */
    public function __construct($_gate = NULL,$_nummer = NULL,$_bokstav = NULL)
    {
        parent::__construct(array('Gate'=>$_gate,'Nummer'=>$_nummer,'Bokstav'=>$_bokstav),false);
    }
    /**
     * Get Gate value
     * @return string|null
     */
    public function getGate()
    {
        return $this->Gate;
    }
    /**
     * Set Gate value
     * @param string $_gate the Gate
     * @return string
     */
    public function setGate($_gate)
    {
        return ($this->Gate = $_gate);
    }
    /**
     * Get Nummer value
     * @return string|null
     */
    public function getNummer()
    {
        return $this->Nummer;
    }
    /**
     * Set Nummer value
     * @param string $_nummer the Nummer
     * @return string
     */
    public function setNummer($_nummer)
    {
        return ($this->Nummer = $_nummer);
    }
    /**
     * Get Bokstav value
     * @return string|null
     */
    public function getBokstav()
    {
        return $this->Bokstav;
    }
    /**
     * Set Bokstav value
     * @param string $_bokstav the Bokstav
     * @return string
     */
    public function setBokstav($_bokstav)
    {
        return ($this->Bokstav = $_bokstav);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructAddress
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
