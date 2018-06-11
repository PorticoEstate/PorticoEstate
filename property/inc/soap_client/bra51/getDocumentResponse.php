<?php

class getDocumentResponse
{

    /**
     * @var ExtendedDocument $getDocumentResult
     */
    protected $getDocumentResult = null;

    /**
     * @param ExtendedDocument $getDocumentResult
     */
    public function __construct($getDocumentResult)
    {
      $this->getDocumentResult = $getDocumentResult;
    }

    /**
     * @return ExtendedDocument
     */
    public function getGetDocumentResult()
    {
      return $this->getDocumentResult;
    }

    /**
     * @param ExtendedDocument $getDocumentResult
     * @return getDocumentResponse
     */
    public function setGetDocumentResult($getDocumentResult)
    {
      $this->getDocumentResult = $getDocumentResult;
      return $this;
    }

}
