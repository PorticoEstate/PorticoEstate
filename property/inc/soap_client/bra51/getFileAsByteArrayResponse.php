<?php

class getFileAsByteArrayResponse
{

    /**
     * @var string $getFileAsByteArrayResult
     */
    protected $getFileAsByteArrayResult = null;

    /**
     * @param string $getFileAsByteArrayResult
     */
    public function __construct($getFileAsByteArrayResult)
    {
      $this->getFileAsByteArrayResult = $getFileAsByteArrayResult;
    }

    /**
     * @return string
     */
    public function getGetFileAsByteArrayResult()
    {
      return $this->getFileAsByteArrayResult;
    }

    /**
     * @param string $getFileAsByteArrayResult
     * @return getFileAsByteArrayResponse
     */
    public function setGetFileAsByteArrayResult($getFileAsByteArrayResult)
    {
      $this->getFileAsByteArrayResult = $getFileAsByteArrayResult;
      return $this;
    }

}
