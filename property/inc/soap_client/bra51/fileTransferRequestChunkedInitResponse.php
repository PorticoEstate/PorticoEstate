<?php

class fileTransferRequestChunkedInitResponse
{

    /**
     * @var string $fileTransferRequestChunkedInitResult
     */
    protected $fileTransferRequestChunkedInitResult = null;

    /**
     * @param string $fileTransferRequestChunkedInitResult
     */
    public function __construct($fileTransferRequestChunkedInitResult)
    {
      $this->fileTransferRequestChunkedInitResult = $fileTransferRequestChunkedInitResult;
    }

    /**
     * @return string
     */
    public function getFileTransferRequestChunkedInitResult()
    {
      return $this->fileTransferRequestChunkedInitResult;
    }

    /**
     * @param string $fileTransferRequestChunkedInitResult
     * @return fileTransferRequestChunkedInitResponse
     */
    public function setFileTransferRequestChunkedInitResult($fileTransferRequestChunkedInitResult)
    {
      $this->fileTransferRequestChunkedInitResult = $fileTransferRequestChunkedInitResult;
      return $this;
    }

}
