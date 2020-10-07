<?php

class Sakspart
{

    /**
     * @var string $systemID
     */
    protected $systemID = null;

    /**
     * @var boolean $skjermetSakspart
     */
    protected $skjermetSakspart = null;

    /**
     * @var string $kortnavn
     */
    protected $kortnavn = null;

    /**
     * @var string $kontaktperson
     */
    protected $kontaktperson = null;

    /**
     * @var SakspartRolle $sakspartRolle
     */
    protected $sakspartRolle = null;

    /**
     * @var string $merknad
     */
    protected $merknad = null;

    /**
     * @var Kontakt $Kontakt
     */
    protected $Kontakt = null;

    /**
     * @param Kontakt $Kontakt
     */
    public function __construct($Kontakt)
    {
      $this->Kontakt = $Kontakt;
    }

    /**
     * @return string
     */
    public function getSystemID()
    {
      return $this->systemID;
    }

    /**
     * @param string $systemID
     * @return Sakspart
     */
    public function setSystemID($systemID)
    {
      $this->systemID = $systemID;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getSkjermetSakspart()
    {
      return $this->skjermetSakspart;
    }

    /**
     * @param boolean $skjermetSakspart
     * @return Sakspart
     */
    public function setSkjermetSakspart($skjermetSakspart)
    {
      $this->skjermetSakspart = $skjermetSakspart;
      return $this;
    }

    /**
     * @return string
     */
    public function getKortnavn()
    {
      return $this->kortnavn;
    }

    /**
     * @param string $kortnavn
     * @return Sakspart
     */
    public function setKortnavn($kortnavn)
    {
      $this->kortnavn = $kortnavn;
      return $this;
    }

    /**
     * @return string
     */
    public function getKontaktperson()
    {
      return $this->kontaktperson;
    }

    /**
     * @param string $kontaktperson
     * @return Sakspart
     */
    public function setKontaktperson($kontaktperson)
    {
      $this->kontaktperson = $kontaktperson;
      return $this;
    }

    /**
     * @return SakspartRolle
     */
    public function getSakspartRolle()
    {
      return $this->sakspartRolle;
    }

    /**
     * @param SakspartRolle $sakspartRolle
     * @return Sakspart
     */
    public function setSakspartRolle($sakspartRolle)
    {
      $this->sakspartRolle = $sakspartRolle;
      return $this;
    }

    /**
     * @return string
     */
    public function getMerknad()
    {
      return $this->merknad;
    }

    /**
     * @param string $merknad
     * @return Sakspart
     */
    public function setMerknad($merknad)
    {
      $this->merknad = $merknad;
      return $this;
    }

    /**
     * @return Kontakt
     */
    public function getKontakt()
    {
      return $this->Kontakt;
    }

    /**
     * @param Kontakt $Kontakt
     * @return Sakspart
     */
    public function setKontakt($Kontakt)
    {
      $this->Kontakt = $Kontakt;
      return $this;
    }

}
