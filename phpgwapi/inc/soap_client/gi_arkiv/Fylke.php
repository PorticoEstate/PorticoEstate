<?php

class Fylke extends Administrativenhetsnummer
{

    /**
     * @var string $fylkesnummer
     */
    protected $fylkesnummer = null;

    /**
     * @param string $fylkesnummer
     */
    public function __construct($fylkesnummer)
    {
      $this->fylkesnummer = $fylkesnummer;
    }

    /**
     * @return string
     */
    public function getFylkesnummer()
    {
      return $this->fylkesnummer;
    }

    /**
     * @param string $fylkesnummer
     * @return Fylke
     */
    public function setFylkesnummer($fylkesnummer)
    {
      $this->fylkesnummer = $fylkesnummer;
      return $this;
    }

}
