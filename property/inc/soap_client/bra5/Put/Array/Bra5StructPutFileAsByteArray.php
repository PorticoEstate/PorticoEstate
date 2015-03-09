<?php
/**
 * File for class Bra5StructPutFileAsByteArray
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructPutFileAsByteArray originally named putFileAsByteArray
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructPutFileAsByteArray extends Bra5WsdlClass
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
     * The documentId
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $documentId;
    /**
     * The filename
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $filename;
    /**
     * The file
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $file;
    /**
     * Constructor method for putFileAsByteArray
     * @see parent::__construct()
     * @param string $_secKey
     * @param string $_documentId
     * @param string $_filename
     * @param string $_file
     * @return Bra5StructPutFileAsByteArray
     */
    public function __construct($_secKey = NULL,$_documentId = NULL,$_filename = NULL,$_file = NULL)
    {
        parent::__construct(array('secKey'=>$_secKey,'documentId'=>$_documentId,'filename'=>$_filename,'file'=>$_file),false);
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
     * Get documentId value
     * @return string|null
     */
    public function getDocumentId()
    {
        return $this->documentId;
    }
    /**
     * Set documentId value
     * @param string $_documentId the documentId
     * @return string
     */
    public function setDocumentId($_documentId)
    {
        return ($this->documentId = $_documentId);
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
     * Get file value
     * @return string|null
     */
    public function getFile()
    {
        return $this->file;
    }
    /**
     * Set file value
     * @param string $_file the file
     * @return string
     */
    public function setFile($_file)
    {
        return ($this->file = $_file);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructPutFileAsByteArray
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
