<?php
/**
 * File for class Bra5StructFileTransferSendChunkedInitResponse
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5StructFileTransferSendChunkedInitResponse originally named fileTransferSendChunkedInitResponse
 * Meta informations extracted from the WSDL
 * - from schema : /home/hc483/wsdl/braarkiv_51/services.asmx.wsdl
 * @package Bra5
 * @subpackage Structs
 * @date 2015-03-06
 */
class Bra5StructFileTransferSendChunkedInitResponse extends Bra5WsdlClass
{
    /**
     * The fileTransferSendChunkedInitResult
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $fileTransferSendChunkedInitResult;
    /**
     * Constructor method for fileTransferSendChunkedInitResponse
     * @see parent::__construct()
     * @param string $_fileTransferSendChunkedInitResult
     * @return Bra5StructFileTransferSendChunkedInitResponse
     */
    public function __construct($_fileTransferSendChunkedInitResult = NULL)
    {
        parent::__construct(array('fileTransferSendChunkedInitResult'=>$_fileTransferSendChunkedInitResult),false);
    }
    /**
     * Get fileTransferSendChunkedInitResult value
     * @return string|null
     */
    public function getFileTransferSendChunkedInitResult()
    {
        return $this->fileTransferSendChunkedInitResult;
    }
    /**
     * Set fileTransferSendChunkedInitResult value
     * @param string $_fileTransferSendChunkedInitResult the fileTransferSendChunkedInitResult
     * @return string
     */
    public function setFileTransferSendChunkedInitResult($_fileTransferSendChunkedInitResult)
    {
        return ($this->fileTransferSendChunkedInitResult = $_fileTransferSendChunkedInitResult);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see Bra5WsdlClass::__set_state()
     * @uses Bra5WsdlClass::__set_state()
     * @param array $_array the exported values
     * @return Bra5StructFileTransferSendChunkedInitResponse
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
