<?php
/**
 * File for class Bra5StructAttribute
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructAttribute originally named Attribute
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructAttribute extends Bra5WsdlClass
{
    /**
     * The UsesLookupValues
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 1
     * @var boolean
     */
    public $UsesLookupValues;
    /**
     * The AttribType
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 1
     * @var Bra5EnumBraArkivAttributeType
     */
    public $AttribType;
    /**
     * The Name
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $Name;
    /**
     * The Value
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var Bra5StructArrayOfAnyType
     */
    public $Value;
    /**
     * Constructor method for Attribute
     * @see parent::__construct()
     * @param boolean $_usesLookupValues
     * @param Bra5EnumBraArkivAttributeType $_attribType
     * @param string $_name
     * @param Bra5StructArrayOfAnyType $_value
     * @return Bra5StructAttribute
     */
    public function __construct($_usesLookupValues,$_attribType,$_name = NULL,$_value = NULL)
    {
        parent::__construct(array('UsesLookupValues'=>$_usesLookupValues,'AttribType'=>$_attribType,'Name'=>$_name,'Value'=>($_value instanceof Bra5StructArrayOfAnyType)?$_value:new Bra5StructArrayOfAnyType($_value)),false);
    }
    /**
     * Get UsesLookupValues value
     * @return boolean
     */
    public function getUsesLookupValues()
    {
        return $this->UsesLookupValues;
    }
    /**
     * Set UsesLookupValues value
     * @param boolean $_usesLookupValues the UsesLookupValues
     * @return boolean
     */
    public function setUsesLookupValues($_usesLookupValues)
    {
        return ($this->UsesLookupValues = $_usesLookupValues);
    }
    /**
     * Get AttribType value
     * @return Bra5EnumBraArkivAttributeType
     */
    public function getAttribType()
    {
        return $this->AttribType;
    }
    /**
     * Set AttribType value
     * @uses Bra5EnumBraArkivAttributeType::valueIsValid()
     * @param Bra5EnumBraArkivAttributeType $_attribType the AttribType
     * @return Bra5EnumBraArkivAttributeType
     */
    public function setAttribType($_attribType)
    {
        if(!Bra5EnumBraArkivAttributeType::valueIsValid($_attribType))
        {
            return false;
        }
        return ($this->AttribType = $_attribType);
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
     * Get Value value
     * @return Bra5StructArrayOfAnyType|null
     */
    public function getValue()
    {
        return $this->Value;
    }
    /**
     * Set Value value
     * @param Bra5StructArrayOfAnyType $_value the Value
     * @return Bra5StructArrayOfAnyType
     */
    public function setValue($_value)
    {
        return ($this->Value = $_value);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructAttribute
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
