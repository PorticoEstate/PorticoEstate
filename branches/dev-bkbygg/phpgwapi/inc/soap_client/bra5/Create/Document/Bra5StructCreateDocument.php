<?php
/**
 * File for class Bra5StructCreateDocument
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructCreateDocument originally named createDocument
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructCreateDocument extends Bra5WsdlClass
{
    /**
     * The assignDocKey
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 1
     * @var boolean
     */
    public $assignDocKey;
    /**
     * The secKey
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $secKey;
    /**
     * The doc
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var Bra5StructDocument
     */
    public $doc;
    /**
     * Constructor method for createDocument
     * @see parent::__construct()
     * @param boolean $_assignDocKey
     * @param string $_secKey
     * @param Bra5StructDocument $_doc
     * @return Bra5StructCreateDocument
     */
    public function __construct($_assignDocKey,$_secKey = NULL,$_doc = NULL)
    {
        parent::__construct(array('assignDocKey'=>$_assignDocKey,'secKey'=>$_secKey,'doc'=>$_doc),false);
    }
    /**
     * Get assignDocKey value
     * @return boolean
     */
    public function getAssignDocKey()
    {
        return $this->assignDocKey;
    }
    /**
     * Set assignDocKey value
     * @param boolean $_assignDocKey the assignDocKey
     * @return boolean
     */
    public function setAssignDocKey($_assignDocKey)
    {
        return ($this->assignDocKey = $_assignDocKey);
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
     * Get doc value
     * @return Bra5StructDocument|null
     */
    public function getDoc()
    {
        return $this->doc;
    }
    /**
     * Set doc value
     * @param Bra5StructDocument $_doc the doc
     * @return Bra5StructDocument
     */
    public function setDoc($_doc)
    {
        return ($this->doc = $_doc);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructCreateDocument
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
