<?php

class fileTransferRequestChunkResponse
{

    /**
     * @var string $fileTransferRequestChunkResult
     */
    protected $fileTransferRequestChunkResult = null;

    /**
     * @param string $fileTransferRequestChunkResult
     */
    public function __construct($fileTransferRequestChunkResult)
    {
      $this->fileTransferRequestChunkResult = $fileTransferRequestChunkResult;
    }

    /**
     * @return string
     */
    public function getFileTransferRequestChunkResult()
    {
      return $this->fileTransferRequestChunkResult;
    }

    /**
     * @param string $fileTransferRequestChunkResult
     * @return fileTransferRequestChunkResponse
     */
    public function setFileTransferRequestChunkResult($fileTransferRequestChunkResult)
    {
      $this->fileTransferRequestChunkResult = $fileTransferRequestChunkResult;
      return $this;
    }

}
