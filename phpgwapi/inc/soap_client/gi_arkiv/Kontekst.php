<?php

class Kontekst
{

    /**
     * @var string $spraak
     */
    protected $spraak = null;

    /**
     * @var string $klientnavn
     */
    protected $klientnavn = null;

    /**
     * @var string $klientversjon
     */
    protected $klientversjon = null;

    /**
     * @var string $systemversjon
     */
    protected $systemversjon = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return string
     */
    public function getSpraak()
    {
      return $this->spraak;
    }

    /**
     * @param string $spraak
     * @return Kontekst
     */
    public function setSpraak($spraak)
    {
      $this->spraak = $spraak;
      return $this;
    }

    /**
     * @return string
     */
    public function getKlientnavn()
    {
      return $this->klientnavn;
    }

    /**
     * @param string $klientnavn
     * @return Kontekst
     */
    public function setKlientnavn($klientnavn)
    {
      $this->klientnavn = $klientnavn;
      return $this;
    }

    /**
     * @return string
     */
    public function getKlientversjon()
    {
      return $this->klientversjon;
    }

    /**
     * @param string $klientversjon
     * @return Kontekst
     */
    public function setKlientversjon($klientversjon)
    {
      $this->klientversjon = $klientversjon;
      return $this;
    }

    /**
     * @return string
     */
    public function getSystemversjon()
    {
      return $this->systemversjon;
    }

    /**
     * @param string $systemversjon
     * @return Kontekst
     */
    public function setSystemversjon($systemversjon)
    {
      $this->systemversjon = $systemversjon;
      return $this;
    }

}
