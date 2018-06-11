<?php

class searchDocumentResponse
{

    /**
     * @var ArrayOfString $searchDocumentResult
     */
    protected $searchDocumentResult = null;

    /**
     * @param ArrayOfString $searchDocumentResult
     */
    public function __construct($searchDocumentResult)
    {
      $this->searchDocumentResult = $searchDocumentResult;
    }

    /**
     * @return ArrayOfString
     */
    public function getSearchDocumentResult()
    {
      return $this->searchDocumentResult;
    }

    /**
     * @param ArrayOfString $searchDocumentResult
     * @return searchDocumentResponse
     */
    public function setSearchDocumentResult($searchDocumentResult)
    {
      $this->searchDocumentResult = $searchDocumentResult;
      return $this;
    }

}
