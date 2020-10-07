<?php

class EnkelAdresse
{

    /**
     * @var EnkelAdressetype $adressetype
     */
    protected $adressetype = null;

    /**
     * @var string $adresselinje1
     */
    protected $adresselinje1 = null;

    /**
     * @var string $adresselinje2
     */
    protected $adresselinje2 = null;

    /**
     * @var PostadministrativeOmraader $postadresse
     */
    protected $postadresse = null;

    /**
     * @var Landkode $landkode
     */
    protected $landkode = null;

    /**
     * @param EnkelAdressetype $adressetype
     */
    public function __construct($adressetype)
    {
      $this->adressetype = $adressetype;
    }

    /**
     * @return EnkelAdressetype
     */
    public function getAdressetype()
    {
      return $this->adressetype;
    }

    /**
     * @param EnkelAdressetype $adressetype
     * @return EnkelAdresse
     */
    public function setAdressetype($adressetype)
    {
      $this->adressetype = $adressetype;
      return $this;
    }

    /**
     * @return string
     */
    public function getAdresselinje1()
    {
      return $this->adresselinje1;
    }

    /**
     * @param string $adresselinje1
     * @return EnkelAdresse
     */
    public function setAdresselinje1($adresselinje1)
    {
      $this->adresselinje1 = $adresselinje1;
      return $this;
    }

    /**
     * @return string
     */
    public function getAdresselinje2()
    {
      return $this->adresselinje2;
    }

    /**
     * @param string $adresselinje2
     * @return EnkelAdresse
     */
    public function setAdresselinje2($adresselinje2)
    {
      $this->adresselinje2 = $adresselinje2;
      return $this;
    }

    /**
     * @return PostadministrativeOmraader
     */
    public function getPostadresse()
    {
      return $this->postadresse;
    }

    /**
     * @param PostadministrativeOmraader $postadresse
     * @return EnkelAdresse
     */
    public function setPostadresse($postadresse)
    {
      $this->postadresse = $postadresse;
      return $this;
    }

    /**
     * @return Landkode
     */
    public function getLandkode()
    {
      return $this->landkode;
    }

    /**
     * @param Landkode $landkode
     * @return EnkelAdresse
     */
    public function setLandkode($landkode)
    {
      $this->landkode = $landkode;
      return $this;
    }

}
