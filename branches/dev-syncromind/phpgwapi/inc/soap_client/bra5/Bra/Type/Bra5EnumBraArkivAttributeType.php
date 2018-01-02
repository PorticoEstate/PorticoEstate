<?php
/**
 * File for class Bra5EnumBraArkivAttributeType
 * @package Bra5
 * @subpackage Enumerations
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5EnumBraArkivAttributeType originally named braArkivAttributeType
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Enumerations
 * @date 2015-03-06
 */
class Bra5EnumBraArkivAttributeType extends Bra5WsdlClass
{
    /**
     * Constant for value 'braArkivString'
     * @return string 'braArkivString'
     */
    const VALUE_BRAARKIVSTRING = 'braArkivString';
    /**
     * Constant for value 'braArkivLongText'
     * @return string 'braArkivLongText'
     */
    const VALUE_BRAARKIVLONGTEXT = 'braArkivLongText';
    /**
     * Constant for value 'braArkivInt'
     * @return string 'braArkivInt'
     */
    const VALUE_BRAARKIVINT = 'braArkivInt';
    /**
     * Constant for value 'braArkivFloat'
     * @return string 'braArkivFloat'
     */
    const VALUE_BRAARKIVFLOAT = 'braArkivFloat';
    /**
     * Constant for value 'braArkivDate'
     * @return string 'braArkivDate'
     */
    const VALUE_BRAARKIVDATE = 'braArkivDate';
    /**
     * Constant for value 'braArkivMatrikkel'
     * @return string 'braArkivMatrikkel'
     */
    const VALUE_BRAARKIVMATRIKKEL = 'braArkivMatrikkel';
    /**
     * Constant for value 'braArkivAddress'
     * @return string 'braArkivAddress'
     */
    const VALUE_BRAARKIVADDRESS = 'braArkivAddress';
    /**
     * Constant for value 'braArkivPair'
     * @return string 'braArkivPair'
     */
    const VALUE_BRAARKIVPAIR = 'braArkivPair';
    /**
     * Constant for value 'braArkivBoolean'
     * @return string 'braArkivBoolean'
     */
    const VALUE_BRAARKIVBOOLEAN = 'braArkivBoolean';
    /**
     * Constant for value 'Unknown'
     * @return string 'Unknown'
     */
    const VALUE_UNKNOWN = 'Unknown';
    /**
     * Return true if value is allowed
     * @uses Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVSTRING
     * @uses Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVLONGTEXT
     * @uses Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVINT
     * @uses Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVFLOAT
     * @uses Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVDATE
     * @uses Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVMATRIKKEL
     * @uses Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVADDRESS
     * @uses Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVPAIR
     * @uses Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVBOOLEAN
     * @uses Bra5EnumBraArkivAttributeType::VALUE_UNKNOWN
     * @param mixed $_value value
     * @return bool true|false
     */
    public static function valueIsValid($_value)
    {
        return in_array($_value,array(Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVSTRING,Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVLONGTEXT,Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVINT,Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVFLOAT,Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVDATE,Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVMATRIKKEL,Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVADDRESS,Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVPAIR,Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVBOOLEAN,Bra5EnumBraArkivAttributeType::VALUE_UNKNOWN));
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
