<?php
/**
 * File for class Bra5StructGetFileAsByteArrayResponse
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructGetFileAsByteArrayResponse originally named getFileAsByteArrayResponse
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructGetFileAsByteArrayResponse extends Bra5WsdlClass
{
    /**
     * The getFileAsByteArrayResult
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $getFileAsByteArrayResult;
    /**
     * Constructor method for getFileAsByteArrayResponse
     * @see parent::__construct()
     * @param string $_getFileAsByteArrayResult
     * @return Bra5StructGetFileAsByteArrayResponse
     */
    public function __construct($_getFileAsByteArrayResult = NULL)
    {
        parent::__construct(array('getFileAsByteArrayResult'=>$_getFileAsByteArrayResult),false);
    }
    /**
     * Get getFileAsByteArrayResult value
     * @return string|null
     */
    public function getGetFileAsByteArrayResult()
    {
        return $this->getFileAsByteArrayResult;
    }
    /**
     * Set getFileAsByteArrayResult value
     * @param string $_getFileAsByteArrayResult the getFileAsByteArrayResult
     * @return string
     */
    public function setGetFileAsByteArrayResult($_getFileAsByteArrayResult)
    {
        return ($this->getFileAsByteArrayResult = $_getFileAsByteArrayResult);
    }
    /**
     * Returns the current element
     * @see Bra5WsdlClass::current()
     * @return string
     */
    public function current()
    {
        return parent::current();
    }
    /**
     * Returns the indexed element
     * @see Bra5WsdlClass::item()
     * @param int $_index
     * @return string
     */
    public function item($_index)
    {
        return parent::item($_index);
    }
    /**
     * Returns the first element
     * @see Bra5WsdlClass::first()
     * @return string
     */
    public function first()
    {
        return parent::first();
    }
    /**
     * Returns the last element
     * @see Bra5WsdlClass::last()
     * @return string
     */
    public function last()
    {
        return parent::last();
    }
    /**
     * Returns the element at the offset
     * @see Bra5WsdlClass::last()
     * @param int $_offset
     * @return string
     */
    public function offsetGet($_offset)
    {
        return parent::offsetGet($_offset);
    }
    /**
     * Returns the attribute name
     * @see Bra5WsdlClass::getAttributeName()
     * @return string getFileAsByteArrayResult
     */
    public function getAttributeName()
    {
        return 'getFileAsByteArrayResult';
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructGetFileAsByteArrayResponse
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
