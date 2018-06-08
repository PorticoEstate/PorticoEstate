<?php

class fileTransferSendChunkedInitResponse
{

    /**
     * @var string $fileTransferSendChunkedInitResult
     */
    protected $fileTransferSendChunkedInitResult = null;

    /**
     * @param string $fileTransferSendChunkedInitResult
     */
    public function __construct($fileTransferSendChunkedInitResult)
    {
      $this->fileTransferSendChunkedInitResult = $fileTransferSendChunkedInitResult;
    }

    /**
     * @return string
     */
    public function getFileTransferSendChunkedInitResult()
    {
      return $this->fileTransferSendChunkedInitResult;
    }

    /**
     * @param string $fileTransferSendChunkedInitResult
     * @return fileTransferSendChunkedInitResponse
     */
    public function setFileTransferSendChunkedInitResult($fileTransferSendChunkedInitResult)
    {
      $this->fileTransferSendChunkedInitResult = $fileTransferSendChunkedInitResult;
      return $this;
    }

}
