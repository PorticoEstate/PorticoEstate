<?php
/**
 * File for class Bra5StructGetDocumentSplitTypes
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructGetDocumentSplitTypes originally named getDocumentSplitTypes
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructGetDocumentSplitTypes extends Bra5WsdlClass
{
    /**
     * The docClassID
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 1
     * @var int
     */
    public $docClassID;
    /**
     * The seckey
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $seckey;
    /**
     * Constructor method for getDocumentSplitTypes
     * @see parent::__construct()
     * @param int $_docClassID
     * @param string $_seckey
     * @return Bra5StructGetDocumentSplitTypes
     */
    public function __construct($_docClassID,$_seckey = NULL)
    {
        parent::__construct(array('docClassID'=>$_docClassID,'seckey'=>$_seckey),false);
    }
    /**
     * Get docClassID value
     * @return int
     */
    public function getDocClassID()
    {
        return $this->docClassID;
    }
    /**
     * Set docClassID value
     * @param int $_docClassID the docClassID
     * @return int
     */
    public function setDocClassID($_docClassID)
    {
        return ($this->docClassID = $_docClassID);
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
     * @return Bra5StructGetDocumentSplitTypes
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
