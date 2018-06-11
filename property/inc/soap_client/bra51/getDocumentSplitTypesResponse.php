<?php

class getDocumentSplitTypesResponse
{

    /**
     * @var ArrayOfDocumentSplitType $getDocumentSplitTypesResult
     */
    protected $getDocumentSplitTypesResult = null;

    /**
     * @param ArrayOfDocumentSplitType $getDocumentSplitTypesResult
     */
    public function __construct($getDocumentSplitTypesResult)
    {
      $this->getDocumentSplitTypesResult = $getDocumentSplitTypesResult;
    }

    /**
     * @return ArrayOfDocumentSplitType
     */
    public function getGetDocumentSplitTypesResult()
    {
      return $this->getDocumentSplitTypesResult;
    }

    /**
     * @param ArrayOfDocumentSplitType $getDocumentSplitTypesResult
     * @return getDocumentSplitTypesResponse
     */
    public function setGetDocumentSplitTypesResult($getDocumentSplitTypesResult)
    {
      $this->getDocumentSplitTypesResult = $getDocumentSplitTypesResult;
      return $this;
    }

}
