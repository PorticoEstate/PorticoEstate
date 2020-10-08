<?php

class Kommune extends Administrativenhetsnummer
{

    /**
     * @var string $kommunenummer
     */
    protected $kommunenummer = null;

    /**
     * @param string $kommunenummer
     */
    public function __construct($kommunenummer)
    {
      $this->kommunenummer = $kommunenummer;
    }

    /**
     * @return string
     */
    public function getKommunenummer()
    {
      return $this->kommunenummer;
    }

    /**
     * @param string $kommunenummer
     * @return Kommune
     */
    public function setKommunenummer($kommunenummer)
    {
      $this->kommunenummer = $kommunenummer;
      return $this;
    }

}
