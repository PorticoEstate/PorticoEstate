<?php

class Kontakt
{

    /**
     * @var string $navn
     */
    protected $navn = null;

    /**
     * @var EnkelAdresseListe $adresser
     */
    protected $adresser = null;

    /**
     * @var ElektroniskAdresseListe $elektroniskeAdresser
     */
    protected $elektroniskeAdresser = null;

    /**
     * @param string $navn
     */
    public function __construct($navn)
    {
      $this->navn = $navn;
    }

    /**
     * @return string
     */
    public function getNavn()
    {
      return $this->navn;
    }

    /**
     * @param string $navn
     * @return Kontakt
     */
    public function setNavn($navn)
    {
      $this->navn = $navn;
      return $this;
    }

    /**
     * @return EnkelAdresseListe
     */
    public function getAdresser()
    {
      return $this->adresser;
    }

    /**
     * @param EnkelAdresseListe $adresser
     * @return Kontakt
     */
    public function setAdresser($adresser)
    {
      $this->adresser = $adresser;
      return $this;
    }

    /**
     * @return ElektroniskAdresseListe
     */
    public function getElektroniskeAdresser()
    {
      return $this->elektroniskeAdresser;
    }

    /**
     * @param ElektroniskAdresseListe $elektroniskeAdresser
     * @return Kontakt
     */
    public function setElektroniskeAdresser($elektroniskeAdresser)
    {
      $this->elektroniskeAdresser = $elektroniskeAdresser;
      return $this;
    }

}
