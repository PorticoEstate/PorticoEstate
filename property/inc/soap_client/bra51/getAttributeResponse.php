<?php

class getAttributeResponse
{

    /**
     * @var Attribute $getAttributeResult
     */
    protected $getAttributeResult = null;

    /**
     * @param Attribute $getAttributeResult
     */
    public function __construct($getAttributeResult)
    {
      $this->getAttributeResult = $getAttributeResult;
    }

    /**
     * @return Attribute
     */
    public function getGetAttributeResult()
    {
      return $this->getAttributeResult;
    }

    /**
     * @param Attribute $getAttributeResult
     * @return getAttributeResponse
     */
    public function setGetAttributeResult($getAttributeResult)
    {
      $this->getAttributeResult = $getAttributeResult;
      return $this;
    }

}
