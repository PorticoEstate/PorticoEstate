<?php

class createDocumentResponse
{

    /**
     * @var Document $createDocumentResult
     */
    protected $createDocumentResult = null;

    /**
     * @param Document $createDocumentResult
     */
    public function __construct($createDocumentResult)
    {
      $this->createDocumentResult = $createDocumentResult;
    }

    /**
     * @return Document
     */
    public function getCreateDocumentResult()
    {
      return $this->createDocumentResult;
    }

    /**
     * @param Document $createDocumentResult
     * @return createDocumentResponse
     */
    public function setCreateDocumentResult($createDocumentResult)
    {
      $this->createDocumentResult = $createDocumentResult;
      return $this;
    }

}
