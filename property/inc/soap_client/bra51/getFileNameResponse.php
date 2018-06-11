<?php

class getFileNameResponse
{

    /**
     * @var string $getFileNameResult
     */
    protected $getFileNameResult = null;

    /**
     * @param string $getFileNameResult
     */
    public function __construct($getFileNameResult)
    {
      $this->getFileNameResult = $getFileNameResult;
    }

    /**
     * @return string
     */
    public function getGetFileNameResult()
    {
      return $this->getFileNameResult;
    }

    /**
     * @param string $getFileNameResult
     * @return getFileNameResponse
     */
    public function setGetFileNameResult($getFileNameResult)
    {
      $this->getFileNameResult = $getFileNameResult;
      return $this;
    }

}
