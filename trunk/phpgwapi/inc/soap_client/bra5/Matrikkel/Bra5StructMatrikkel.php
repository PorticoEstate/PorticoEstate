<?php
/**
 * File for class Bra5StructMatrikkel
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructMatrikkel originally named Matrikkel
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructMatrikkel extends Bra5WsdlClass
{
    /**
     * The GNr
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $GNr;
    /**
     * The BNr
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $BNr;
    /**
     * The FNr
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $FNr;
    /**
     * The SNr
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $SNr;
    /**
     * Constructor method for Matrikkel
     * @see parent::__construct()
     * @param string $_gNr
     * @param string $_bNr
     * @param string $_fNr
     * @param string $_sNr
     * @return Bra5StructMatrikkel
     */
    public function __construct($_gNr = NULL,$_bNr = NULL,$_fNr = NULL,$_sNr = NULL)
    {
        parent::__construct(array('GNr'=>$_gNr,'BNr'=>$_bNr,'FNr'=>$_fNr,'SNr'=>$_sNr),false);
    }
    /**
     * Get GNr value
     * @return string|null
     */
    public function getGNr()
    {
        return $this->GNr;
    }
    /**
     * Set GNr value
     * @param string $_gNr the GNr
     * @return string
     */
    public function setGNr($_gNr)
    {
        return ($this->GNr = $_gNr);
    }
    /**
     * Get BNr value
     * @return string|null
     */
    public function getBNr()
    {
        return $this->BNr;
    }
    /**
     * Set BNr value
     * @param string $_bNr the BNr
     * @return string
     */
    public function setBNr($_bNr)
    {
        return ($this->BNr = $_bNr);
    }
    /**
     * Get FNr value
     * @return string|null
     */
    public function getFNr()
    {
        return $this->FNr;
    }
    /**
     * Set FNr value
     * @param string $_fNr the FNr
     * @return string
     */
    public function setFNr($_fNr)
    {
        return ($this->FNr = $_fNr);
    }
    /**
     * Get SNr value
     * @return string|null
     */
    public function getSNr()
    {
        return $this->SNr;
    }
    /**
     * Set SNr value
     * @param string $_sNr the SNr
     * @return string
     */
    public function setSNr($_sNr)
    {
        return ($this->SNr = $_sNr);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructMatrikkel
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
