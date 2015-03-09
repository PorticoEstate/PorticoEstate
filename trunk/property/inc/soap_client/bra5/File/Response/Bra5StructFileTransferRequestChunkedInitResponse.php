<?php
/**
 * File for class Bra5StructFileTransferRequestChunkedInitResponse
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructFileTransferRequestChunkedInitResponse originally named fileTransferRequestChunkedInitResponse
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructFileTransferRequestChunkedInitResponse extends Bra5WsdlClass
{
    /**
     * The fileTransferRequestChunkedInitResult
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $fileTransferRequestChunkedInitResult;
    /**
     * Constructor method for fileTransferRequestChunkedInitResponse
     * @see parent::__construct()
     * @param string $_fileTransferRequestChunkedInitResult
     * @return Bra5StructFileTransferRequestChunkedInitResponse
     */
    public function __construct($_fileTransferRequestChunkedInitResult = NULL)
    {
        parent::__construct(array('fileTransferRequestChunkedInitResult'=>$_fileTransferRequestChunkedInitResult),false);
    }
    /**
     * Get fileTransferRequestChunkedInitResult value
     * @return string|null
     */
    public function getFileTransferRequestChunkedInitResult()
    {
        return $this->fileTransferRequestChunkedInitResult;
    }
    /**
     * Set fileTransferRequestChunkedInitResult value
     * @param string $_fileTransferRequestChunkedInitResult the fileTransferRequestChunkedInitResult
     * @return string
     */
    public function setFileTransferRequestChunkedInitResult($_fileTransferRequestChunkedInitResult)
    {
        return ($this->fileTransferRequestChunkedInitResult = $_fileTransferRequestChunkedInitResult);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructFileTransferRequestChunkedInitResponse
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
