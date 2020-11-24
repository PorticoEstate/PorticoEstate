<?php

class Omraade
{

    /**
     * @var Punkt $punkt
     */
    protected $punkt = null;

    /**
     * @var Flate $flate
     */
    protected $flate = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Punkt
     */
    public function getPunkt()
    {
      return $this->punkt;
    }

    /**
     * @param Punkt $punkt
     * @return Omraade
     */
    public function setPunkt($punkt)
    {
      $this->punkt = $punkt;
      return $this;
    }

    /**
     * @return Flate
     */
    public function getFlate()
    {
      return $this->flate;
    }

    /**
     * @param Flate $flate
     * @return Omraade
     */
    public function setFlate($flate)
    {
      $this->flate = $flate;
      return $this;
    }

}
