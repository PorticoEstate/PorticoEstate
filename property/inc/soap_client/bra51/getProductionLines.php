<?php

class getProductionLines
{

    /**
     * @var string $seckey
     */
    protected $seckey = null;

    /**
     * @param string $seckey
     */
    public function __construct($seckey)
    {
      $this->seckey = $seckey;
    }

    /**
     * @return string
     */
    public function getSeckey()
    {
      return $this->seckey;
    }

    /**
     * @param string $seckey
     * @return getProductionLines
     */
    public function setSeckey($seckey)
    {
      $this->seckey = $seckey;
      return $this;
    }

}
