<?php
/**
 * File for class Bra5StructPair
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructPair originally named Pair
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructPair extends Bra5WsdlClass
{
    /**
     * The Kode
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $Kode;
    /**
     * The Beskrivelse
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $Beskrivelse;
    /**
     * Constructor method for Pair
     * @see parent::__construct()
     * @param string $_kode
     * @param string $_beskrivelse
     * @return Bra5StructPair
     */
    public function __construct($_kode = NULL,$_beskrivelse = NULL)
    {
        parent::__construct(array('Kode'=>$_kode,'Beskrivelse'=>$_beskrivelse),false);
    }
    /**
     * Get Kode value
     * @return string|null
     */
    public function getKode()
    {
        return $this->Kode;
    }
    /**
     * Set Kode value
     * @param string $_kode the Kode
     * @return string
     */
    public function setKode($_kode)
    {
        return ($this->Kode = $_kode);
    }
    /**
     * Get Beskrivelse value
     * @return string|null
     */
    public function getBeskrivelse()
    {
        return $this->Beskrivelse;
    }
    /**
     * Set Beskrivelse value
     * @param string $_beskrivelse the Beskrivelse
     * @return string
     */
    public function setBeskrivelse($_beskrivelse)
    {
        return ($this->Beskrivelse = $_beskrivelse);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructPair
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
