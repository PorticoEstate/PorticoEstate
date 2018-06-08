<?php

class getAvailableAttributesResponse
{

    /**
     * @var ArrayOfAttribute $getAvailableAttributesResult
     */
    protected $getAvailableAttributesResult = null;

    /**
     * @param ArrayOfAttribute $getAvailableAttributesResult
     */
    public function __construct($getAvailableAttributesResult)
    {
      $this->getAvailableAttributesResult = $getAvailableAttributesResult;
    }

    /**
     * @return ArrayOfAttribute
     */
    public function getGetAvailableAttributesResult()
    {
      return $this->getAvailableAttributesResult;
    }

    /**
     * @param ArrayOfAttribute $getAvailableAttributesResult
     * @return getAvailableAttributesResponse
     */
    public function setGetAvailableAttributesResult($getAvailableAttributesResult)
    {
      $this->getAvailableAttributesResult = $getAvailableAttributesResult;
      return $this;
    }

}
