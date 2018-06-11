<?php

class getRelativeFileURLResponse
{

    /**
     * @var string $getRelativeFileURLResult
     */
    protected $getRelativeFileURLResult = null;

    /**
     * @param string $getRelativeFileURLResult
     */
    public function __construct($getRelativeFileURLResult)
    {
      $this->getRelativeFileURLResult = $getRelativeFileURLResult;
    }

    /**
     * @return string
     */
    public function getGetRelativeFileURLResult()
    {
      return $this->getRelativeFileURLResult;
    }

    /**
     * @param string $getRelativeFileURLResult
     * @return getRelativeFileURLResponse
     */
    public function setGetRelativeFileURLResult($getRelativeFileURLResult)
    {
      $this->getRelativeFileURLResult = $getRelativeFileURLResult;
      return $this;
    }

}
