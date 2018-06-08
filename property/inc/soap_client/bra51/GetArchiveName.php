<?php

class GetArchiveName
{

    /**
     * @var string $secKey
     */
    protected $secKey = null;

    /**
     * @param string $secKey
     */
    public function __construct($secKey)
    {
      $this->secKey = $secKey;
    }

    /**
     * @return string
     */
    public function getSecKey()
    {
      return $this->secKey;
    }

    /**
     * @param string $secKey
     * @return GetArchiveName
     */
    public function setSecKey($secKey)
    {
      $this->secKey = $secKey;
      return $this;
    }

}
