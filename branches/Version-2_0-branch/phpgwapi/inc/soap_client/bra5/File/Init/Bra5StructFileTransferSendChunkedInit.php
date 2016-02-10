<?php
/**
 * File for class Bra5StructFileTransferSendChunkedInit
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructFileTransferSendChunkedInit originally named fileTransferSendChunkedInit
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructFileTransferSendChunkedInit extends Bra5WsdlClass
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
     * The docid
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $docid;
    /**
     * The filename
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $filename;
    /**
     * Constructor method for fileTransferSendChunkedInit
     * @see parent::__construct()
     * @param string $_secKey
     * @param string $_docid
     * @param string $_filename
     * @return Bra5StructFileTransferSendChunkedInit
     */
    public function __construct($_secKey = NULL,$_docid = NULL,$_filename = NULL)
    {
        parent::__construct(array('secKey'=>$_secKey,'docid'=>$_docid,'filename'=>$_filename),false);
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
     * Get docid value
     * @return string|null
     */
    public function getDocid()
    {
        return $this->docid;
    }
    /**
     * Set docid value
     * @param string $_docid the docid
     * @return string
     */
    public function setDocid($_docid)
    {
        return ($this->docid = $_docid);
    }
    /**
     * Get filename value
     * @return string|null
     */
    public function getFilename()
    {
        return $this->filename;
    }
    /**
     * Set filename value
     * @param string $_filename the filename
     * @return string
     */
    public function setFilename($_filename)
    {
        return ($this->filename = $_filename);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructFileTransferSendChunkedInit
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
