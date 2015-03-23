<?php
/**
 * File for class Bra5StructFileTransferRequestChunk
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructFileTransferRequestChunk originally named fileTransferRequestChunk
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructFileTransferRequestChunk extends Bra5WsdlClass
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
     * The fileid
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $fileid;
    /**
     * The offset
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $offset;
    /**
     * Constructor method for fileTransferRequestChunk
     * @see parent::__construct()
     * @param string $_secKey
     * @param string $_fileid
     * @param string $_offset
     * @return Bra5StructFileTransferRequestChunk
     */
    public function __construct($_secKey = NULL,$_fileid = NULL,$_offset = NULL)
    {
        parent::__construct(array('secKey'=>$_secKey,'fileid'=>$_fileid,'offset'=>$_offset),false);
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
     * Get fileid value
     * @return string|null
     */
    public function getFileid()
    {
        return $this->fileid;
    }
    /**
     * Set fileid value
     * @param string $_fileid the fileid
     * @return string
     */
    public function setFileid($_fileid)
    {
        return ($this->fileid = $_fileid);
    }
    /**
     * Get offset value
     * @return string|null
     */
    public function getOffset()
    {
        return $this->offset;
    }
    /**
     * Set offset value
     * @param string $_offset the offset
     * @return string
     */
    public function setOffset($_offset)
    {
        return ($this->offset = $_offset);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructFileTransferRequestChunk
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
