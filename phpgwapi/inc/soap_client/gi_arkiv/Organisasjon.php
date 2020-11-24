<?php

class Organisasjon extends Kontakt
{

    /**
     * @var string $organisasjonsnummer
     */
    protected $organisasjonsnummer = null;

    /**
     * @param string $navn
     */
    public function __construct($navn)
    {
      parent::__construct($navn);
    }

    /**
     * @return string
     */
    public function getOrganisasjonsnummer()
    {
      return $this->organisasjonsnummer;
    }

    /**
     * @param string $organisasjonsnummer
     * @return Organisasjon
     */
    public function setOrganisasjonsnummer($organisasjonsnummer)
    {
      $this->organisasjonsnummer = $organisasjonsnummer;
      return $this;
    }

}
