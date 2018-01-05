<?php
/**
 * File for class Bra5ServiceFile
 * @package Bra5
 * @subpackage Services
 * @date 2015-03-06
 */
/**
 * This class stands for Bra5ServiceFile originally named File
 * @package Bra5
 * @subpackage Services
 * @date 2015-03-06
 */
class Bra5ServiceFile extends Bra5WsdlClass
{
    /**
     * Method to call the operation originally named fileTransferSendChunkedInit
     * @uses Bra5WsdlClass::getSoapClient()
     * @uses Bra5WsdlClass::setResult()
     * @uses Bra5WsdlClass::saveLastError()
     * @param Bra5StructFileTransferSendChunkedInit $_bra5StructFileTransferSendChunkedInit
     * @return Bra5StructFileTransferSendChunkedInitResponse
     */
    public function fileTransferSendChunkedInit(Bra5StructFileTransferSendChunkedInit $_bra5StructFileTransferSendChunkedInit)
    {
        try
        {
            return $this->setResult(new Bra5StructFileTransferSendChunkedInitResponse(self::getSoapClient()->fileTransferSendChunkedInit($_bra5StructFileTransferSendChunkedInit)));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named fileTransferSendChunk
     * @uses Bra5WsdlClass::getSoapClient()
     * @uses Bra5WsdlClass::setResult()
     * @uses Bra5WsdlClass::saveLastError()
     * @param Bra5StructFileTransferSendChunk $_bra5StructFileTransferSendChunk
     * @return Bra5StructFileTransferSendChunkResponse
     */
    public function fileTransferSendChunk(Bra5StructFileTransferSendChunk $_bra5StructFileTransferSendChunk)
    {
        try
        {
            return $this->setResult(new Bra5StructFileTransferSendChunkResponse(self::getSoapClient()->fileTransferSendChunk($_bra5StructFileTransferSendChunk)));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named fileTransferSendChunkedEnd
     * @uses Bra5WsdlClass::getSoapClient()
     * @uses Bra5WsdlClass::setResult()
     * @uses Bra5WsdlClass::saveLastError()
     * @param Bra5StructFileTransferSendChunkedEnd $_bra5StructFileTransferSendChunkedEnd
     * @return Bra5StructFileTransferSendChunkedEndResponse
     */
    public function fileTransferSendChunkedEnd(Bra5StructFileTransferSendChunkedEnd $_bra5StructFileTransferSendChunkedEnd)
    {
        try
        {
            return $this->setResult(new Bra5StructFileTransferSendChunkedEndResponse(self::getSoapClient()->fileTransferSendChunkedEnd($_bra5StructFileTransferSendChunkedEnd)));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named fileTransferRequestChunkedInit
     * @uses Bra5WsdlClass::getSoapClient()
     * @uses Bra5WsdlClass::setResult()
     * @uses Bra5WsdlClass::saveLastError()
     * @param Bra5StructFileTransferRequestChunkedInit $_bra5StructFileTransferRequestChunkedInit
     * @return Bra5StructFileTransferRequestChunkedInitResponse
     */
    public function fileTransferRequestChunkedInit(Bra5StructFileTransferRequestChunkedInit $_bra5StructFileTransferRequestChunkedInit)
    {
        try
        {
            return $this->setResult(new Bra5StructFileTransferRequestChunkedInitResponse(self::getSoapClient()->fileTransferRequestChunkedInit($_bra5StructFileTransferRequestChunkedInit)));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named fileTransferRequestChunk
     * @uses Bra5WsdlClass::getSoapClient()
     * @uses Bra5WsdlClass::setResult()
     * @uses Bra5WsdlClass::saveLastError()
     * @param Bra5StructFileTransferRequestChunk $_bra5StructFileTransferRequestChunk
     * @return Bra5StructFileTransferRequestChunkResponse
     */
    public function fileTransferRequestChunk(Bra5StructFileTransferRequestChunk $_bra5StructFileTransferRequestChunk)
    {
        try
        {
            return $this->setResult(new Bra5StructFileTransferRequestChunkResponse(self::getSoapClient()->fileTransferRequestChunk($_bra5StructFileTransferRequestChunk)));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named fileTransferRequestChunkedEnd
     * @uses Bra5WsdlClass::getSoapClient()
     * @uses Bra5WsdlClass::setResult()
     * @uses Bra5WsdlClass::saveLastError()
     * @param Bra5StructFileTransferRequestChunkedEnd $_bra5StructFileTransferRequestChunkedEnd
     * @return Bra5StructFileTransferRequestChunkedEndResponse
     */
    public function fileTransferRequestChunkedEnd(Bra5StructFileTransferRequestChunkedEnd $_bra5StructFileTransferRequestChunkedEnd)
    {
        try
        {
            return $this->setResult(new Bra5StructFileTransferRequestChunkedEndResponse(self::getSoapClient()->fileTransferRequestChunkedEnd($_bra5StructFileTransferRequestChunkedEnd)));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Returns the result
     * @see Bra5WsdlClass::getResult()
     * @return Bra5StructFileTransferRequestChunkedEndResponse|Bra5StructFileTransferRequestChunkedInitResponse|Bra5StructFileTransferRequestChunkResponse|Bra5StructFileTransferSendChunkedEndResponse|Bra5StructFileTransferSendChunkedInitResponse|Bra5StructFileTransferSendChunkResponse
     */
    public function getResult()
    {
        return parent::getResult();
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
