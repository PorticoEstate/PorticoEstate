<?php

class GetArchiveNameResponse
{

    /**
     * @var string $GetArchiveNameResult
     */
    protected $GetArchiveNameResult = null;

    /**
     * @param string $GetArchiveNameResult
     */
    public function __construct($GetArchiveNameResult)
    {
      $this->GetArchiveNameResult = $GetArchiveNameResult;
    }

    /**
     * @return string
     */
    public function getGetArchiveNameResult()
    {
      return $this->GetArchiveNameResult;
    }

    /**
     * @param string $GetArchiveNameResult
     * @return GetArchiveNameResponse
     */
    public function setGetArchiveNameResult($GetArchiveNameResult)
    {
      $this->GetArchiveNameResult = $GetArchiveNameResult;
      return $this;
    }

}
