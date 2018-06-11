<?php

class getProductionLinesResponse
{

    /**
     * @var ArrayOfProductionLine $getProductionLinesResult
     */
    protected $getProductionLinesResult = null;

    /**
     * @param ArrayOfProductionLine $getProductionLinesResult
     */
    public function __construct($getProductionLinesResult)
    {
      $this->getProductionLinesResult = $getProductionLinesResult;
    }

    /**
     * @return ArrayOfProductionLine
     */
    public function getGetProductionLinesResult()
    {
      return $this->getProductionLinesResult;
    }

    /**
     * @param ArrayOfProductionLine $getProductionLinesResult
     * @return getProductionLinesResponse
     */
    public function setGetProductionLinesResult($getProductionLinesResult)
    {
      $this->getProductionLinesResult = $getProductionLinesResult;
      return $this;
    }

}
