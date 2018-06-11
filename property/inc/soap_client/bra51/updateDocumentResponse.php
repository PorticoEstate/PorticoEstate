<?php

class updateDocumentResponse
{

    /**
     * @var Document $updateDocumentResult
     */
    protected $updateDocumentResult = null;

    /**
     * @param Document $updateDocumentResult
     */
    public function __construct($updateDocumentResult)
    {
      $this->updateDocumentResult = $updateDocumentResult;
    }

    /**
     * @return Document
     */
    public function getUpdateDocumentResult()
    {
      return $this->updateDocumentResult;
    }

    /**
     * @param Document $updateDocumentResult
     * @return updateDocumentResponse
     */
    public function setUpdateDocumentResult($updateDocumentResult)
    {
      $this->updateDocumentResult = $updateDocumentResult;
      return $this;
    }

}
