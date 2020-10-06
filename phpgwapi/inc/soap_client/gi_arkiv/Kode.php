<?php

class Kode
{

    /**
     * @var string $kodeverdi
     */
    protected $kodeverdi = null;

    /**
     * @var string $kodebeskrivelse
     */
    protected $kodebeskrivelse = null;

    /**
     * @var boolean $erGyldig
     */
    protected $erGyldig = null;

    /**
     * @param string $kodeverdi
     */
    public function __construct($kodeverdi)
    {
      $this->kodeverdi = $kodeverdi;
    }

    /**
     * @return string
     */
    public function getKodeverdi()
    {
      return $this->kodeverdi;
    }

    /**
     * @param string $kodeverdi
     * @return Kode
     */
    public function setKodeverdi($kodeverdi)
    {
      $this->kodeverdi = $kodeverdi;
      return $this;
    }

    /**
     * @return string
     */
    public function getKodebeskrivelse()
    {
      return $this->kodebeskrivelse;
    }

    /**
     * @param string $kodebeskrivelse
     * @return Kode
     */
    public function setKodebeskrivelse($kodebeskrivelse)
    {
      $this->kodebeskrivelse = $kodebeskrivelse;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getErGyldig()
    {
      return $this->erGyldig;
    }

    /**
     * @param boolean $erGyldig
     * @return Kode
     */
    public function setErGyldig($erGyldig)
    {
      $this->erGyldig = $erGyldig;
      return $this;
    }

}
