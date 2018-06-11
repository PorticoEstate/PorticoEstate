<?php

class Address
{

    /**
     * @var string $Gate
     */
    protected $Gate = null;

    /**
     * @var string $Nummer
     */
    protected $Nummer = null;

    /**
     * @var string $Bokstav
     */
    protected $Bokstav = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return string
     */
    public function getGate()
    {
      return $this->Gate;
    }

    /**
     * @param string $Gate
     * @return Address
     */
    public function setGate($Gate)
    {
      $this->Gate = $Gate;
      return $this;
    }

    /**
     * @return string
     */
    public function getNummer()
    {
      return $this->Nummer;
    }

    /**
     * @param string $Nummer
     * @return Address
     */
    public function setNummer($Nummer)
    {
      $this->Nummer = $Nummer;
      return $this;
    }

    /**
     * @return string
     */
    public function getBokstav()
    {
      return $this->Bokstav;
    }

    /**
     * @param string $Bokstav
     * @return Address
     */
    public function setBokstav($Bokstav)
    {
      $this->Bokstav = $Bokstav;
      return $this;
    }

}
