<?php

class Faks extends ElektroniskAdresse
{

    /**
     * @var string $faksnummer
     */
    protected $faksnummer = null;

    /**
     * @param string $faksnummer
     */
    public function __construct($faksnummer)
    {
      $this->faksnummer = $faksnummer;
    }

    /**
     * @return string
     */
    public function getFaksnummer()
    {
      return $this->faksnummer;
    }

    /**
     * @param string $faksnummer
     * @return Faks
     */
    public function setFaksnummer($faksnummer)
    {
      $this->faksnummer = $faksnummer;
      return $this;
    }

}
