<?php

class getLookupValuesResponse
{

    /**
     * @var ArrayOfLookupValue $getLookupValuesResult
     */
    protected $getLookupValuesResult = null;

    /**
     * @param ArrayOfLookupValue $getLookupValuesResult
     */
    public function __construct($getLookupValuesResult)
    {
      $this->getLookupValuesResult = $getLookupValuesResult;
    }

    /**
     * @return ArrayOfLookupValue
     */
    public function getGetLookupValuesResult()
    {
      return $this->getLookupValuesResult;
    }

    /**
     * @param ArrayOfLookupValue $getLookupValuesResult
     * @return getLookupValuesResponse
     */
    public function setGetLookupValuesResult($getLookupValuesResult)
    {
      $this->getLookupValuesResult = $getLookupValuesResult;
      return $this;
    }

}
