<?php
/**
 * File for class Bra5StructProductionLine
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructProductionLine originally named ProductionLine
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructProductionLine extends Bra5WsdlClass
{
    /**
     * The ProductionLineID
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 1
     * @var int
     */
    public $ProductionLineID;
    /**
     * The Enabled
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 1
     * @var boolean
     */
    public $Enabled;
    /**
     * The Default
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 1
     * @var boolean
     */
    public $Default;
    /**
     * The Name
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $Name;
    /**
     * Constructor method for ProductionLine
     * @see parent::__construct()
     * @param int $_productionLineID
     * @param boolean $_enabled
     * @param boolean $_default
     * @param string $_name
     * @return Bra5StructProductionLine
     */
    public function __construct($_productionLineID,$_enabled,$_default,$_name = NULL)
    {
        parent::__construct(array('ProductionLineID'=>$_productionLineID,'Enabled'=>$_enabled,'Default'=>$_default,'Name'=>$_name),false);
    }
    /**
     * Get ProductionLineID value
     * @return int
     */
    public function getProductionLineID()
    {
        return $this->ProductionLineID;
    }
    /**
     * Set ProductionLineID value
     * @param int $_productionLineID the ProductionLineID
     * @return int
     */
    public function setProductionLineID($_productionLineID)
    {
        return ($this->ProductionLineID = $_productionLineID);
    }
    /**
     * Get Enabled value
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->Enabled;
    }
    /**
     * Set Enabled value
     * @param boolean $_enabled the Enabled
     * @return boolean
     */
    public function setEnabled($_enabled)
    {
        return ($this->Enabled = $_enabled);
    }
    /**
     * Get Default value
     * @return boolean
     */
    public function getDefault()
    {
        return $this->Default;
    }
    /**
     * Set Default value
     * @param boolean $_default the Default
     * @return boolean
     */
    public function setDefault($_default)
    {
        return ($this->Default = $_default);
    }
    /**
     * Get Name value
     * @return string|null
     */
    public function getName()
    {
        return $this->Name;
    }
    /**
     * Set Name value
     * @param string $_name the Name
     * @return string
     */
    public function setName($_name)
    {
        return ($this->Name = $_name);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructProductionLine
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
