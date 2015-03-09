<?php
/**
 * File for class Bra5StructFileTransferRequestChunkResponse
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructFileTransferRequestChunkResponse originally named fileTransferRequestChunkResponse
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructFileTransferRequestChunkResponse extends Bra5WsdlClass
{
    /**
     * The fileTransferRequestChunkResult
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $fileTransferRequestChunkResult;
    /**
     * Constructor method for fileTransferRequestChunkResponse
     * @see parent::__construct()
     * @param string $_fileTransferRequestChunkResult
     * @return Bra5StructFileTransferRequestChunkResponse
     */
    public function __construct($_fileTransferRequestChunkResult = NULL)
    {
        parent::__construct(array('fileTransferRequestChunkResult'=>$_fileTransferRequestChunkResult),false);
    }
    /**
     * Get fileTransferRequestChunkResult value
     * @return string|null
     */
    public function getFileTransferRequestChunkResult()
    {
        return $this->fileTransferRequestChunkResult;
    }
    /**
     * Set fileTransferRequestChunkResult value
     * @param string $_fileTransferRequestChunkResult the fileTransferRequestChunkResult
     * @return string
     */
    public function setFileTransferRequestChunkResult($_fileTransferRequestChunkResult)
    {
        return ($this->fileTransferRequestChunkResult = $_fileTransferRequestChunkResult);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructFileTransferRequestChunkResponse
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
