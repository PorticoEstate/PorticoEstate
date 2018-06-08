<?php

class Pair
{

    /**
     * @var string $Kode
     */
    protected $Kode = null;

    /**
     * @var string $Beskrivelse
     */
    protected $Beskrivelse = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return string
     */
    public function getKode()
    {
      return $this->Kode;
    }

    /**
     * @param string $Kode
     * @return Pair
     */
    public function setKode($Kode)
    {
      $this->Kode = $Kode;
      return $this;
    }

    /**
     * @return string
     */
    public function getBeskrivelse()
    {
      return $this->Beskrivelse;
    }

    /**
     * @param string $Beskrivelse
     * @return Pair
     */
    public function setBeskrivelse($Beskrivelse)
    {
      $this->Beskrivelse = $Beskrivelse;
      return $this;
    }

}
