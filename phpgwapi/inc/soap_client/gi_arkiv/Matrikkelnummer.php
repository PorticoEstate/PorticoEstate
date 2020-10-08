<?php

class Matrikkelnummer
{

    /**
     * @var string $kommunenummer
     */
    protected $kommunenummer = null;

    /**
     * @var int $gaardsnummer
     */
    protected $gaardsnummer = null;

    /**
     * @var int $bruksnummer
     */
    protected $bruksnummer = null;

    /**
     * @var int $festenummer
     */
    protected $festenummer = null;

    /**
     * @var int $seksjonsnummer
     */
    protected $seksjonsnummer = null;

    /**
     * @param string $kommunenummer
     * @param int $gaardsnummer
     * @param int $bruksnummer
     */
    public function __construct($kommunenummer, $gaardsnummer, $bruksnummer)
    {
      $this->kommunenummer = $kommunenummer;
      $this->gaardsnummer = $gaardsnummer;
      $this->bruksnummer = $bruksnummer;
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
     * @return Matrikkelnummer
     */
    public function setKommunenummer($kommunenummer)
    {
      $this->kommunenummer = $kommunenummer;
      return $this;
    }

    /**
     * @return int
     */
    public function getGaardsnummer()
    {
      return $this->gaardsnummer;
    }

    /**
     * @param int $gaardsnummer
     * @return Matrikkelnummer
     */
    public function setGaardsnummer($gaardsnummer)
    {
      $this->gaardsnummer = $gaardsnummer;
      return $this;
    }

    /**
     * @return int
     */
    public function getBruksnummer()
    {
      return $this->bruksnummer;
    }

    /**
     * @param int $bruksnummer
     * @return Matrikkelnummer
     */
    public function setBruksnummer($bruksnummer)
    {
      $this->bruksnummer = $bruksnummer;
      return $this;
    }

    /**
     * @return int
     */
    public function getFestenummer()
    {
      return $this->festenummer;
    }

    /**
     * @param int $festenummer
     * @return Matrikkelnummer
     */
    public function setFestenummer($festenummer)
    {
      $this->festenummer = $festenummer;
      return $this;
    }

    /**
     * @return int
     */
    public function getSeksjonsnummer()
    {
      return $this->seksjonsnummer;
    }

    /**
     * @param int $seksjonsnummer
     * @return Matrikkelnummer
     */
    public function setSeksjonsnummer($seksjonsnummer)
    {
      $this->seksjonsnummer = $seksjonsnummer;
      return $this;
    }

}
