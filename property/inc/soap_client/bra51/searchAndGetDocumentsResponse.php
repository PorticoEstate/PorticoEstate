<?php

class searchAndGetDocumentsResponse
{

    /**
     * @var ArrayOfExtendedDocument $searchAndGetDocumentsResult
     */
    protected $searchAndGetDocumentsResult = null;

    /**
     * @param ArrayOfExtendedDocument $searchAndGetDocumentsResult
     */
    public function __construct($searchAndGetDocumentsResult)
    {
      $this->searchAndGetDocumentsResult = $searchAndGetDocumentsResult;
    }

    /**
     * @return ArrayOfExtendedDocument
     */
    public function getSearchAndGetDocumentsResult()
    {
      return $this->searchAndGetDocumentsResult;
    }

    /**
     * @param ArrayOfExtendedDocument $searchAndGetDocumentsResult
     * @return searchAndGetDocumentsResponse
     */
    public function setSearchAndGetDocumentsResult($searchAndGetDocumentsResult)
    {
      $this->searchAndGetDocumentsResult = $searchAndGetDocumentsResult;
      return $this;
    }

}
