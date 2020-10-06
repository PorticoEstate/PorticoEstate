<?php

class Korrespondansepart
{

    /**
     * @var string $systemID
     */
    protected $systemID = null;

    /**
     * @var Korrespondanseparttype $korrespondanseparttype
     */
    protected $korrespondanseparttype = null;

    /**
     * @var string $behandlingsansvarlig
     */
    protected $behandlingsansvarlig = null;

    /**
     * @var boolean $skjermetKorrespondansepart
     */
    protected $skjermetKorrespondansepart = null;

    /**
     * @var string $kortnavn
     */
    protected $kortnavn = null;

    /**
     * @var string $deresReferanse
     */
    protected $deresReferanse = null;

    /**
     * @var Journalenhet $journalenhet
     */
    protected $journalenhet = null;

    /**
     * @var \DateTime $fristBesvarelse
     */
    protected $fristBesvarelse = null;

    /**
     * @var Forsendelsesmaate $forsendelsesmaate
     */
    protected $forsendelsesmaate = null;

    /**
     * @var string $administrativEnhetInit
     */
    protected $administrativEnhetInit = null;

    /**
     * @var string $administrativEnhet
     */
    protected $administrativEnhet = null;

    /**
     * @var string $saksbehandlerInit
     */
    protected $saksbehandlerInit = null;

    /**
     * @var string $saksbehandler
     */
    protected $saksbehandler = null;

    /**
     * @var Kontakt $Kontakt
     */
    protected $Kontakt = null;

    /**
     * @param Korrespondanseparttype $korrespondanseparttype
     * @param Kontakt $Kontakt
     */
    public function __construct($korrespondanseparttype, $Kontakt)
    {
      $this->korrespondanseparttype = $korrespondanseparttype;
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
     * @return Korrespondansepart
     */
    public function setSystemID($systemID)
    {
      $this->systemID = $systemID;
      return $this;
    }

    /**
     * @return Korrespondanseparttype
     */
    public function getKorrespondanseparttype()
    {
      return $this->korrespondanseparttype;
    }

    /**
     * @param Korrespondanseparttype $korrespondanseparttype
     * @return Korrespondansepart
     */
    public function setKorrespondanseparttype($korrespondanseparttype)
    {
      $this->korrespondanseparttype = $korrespondanseparttype;
      return $this;
    }

    /**
     * @return string
     */
    public function getBehandlingsansvarlig()
    {
      return $this->behandlingsansvarlig;
    }

    /**
     * @param string $behandlingsansvarlig
     * @return Korrespondansepart
     */
    public function setBehandlingsansvarlig($behandlingsansvarlig)
    {
      $this->behandlingsansvarlig = $behandlingsansvarlig;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getSkjermetKorrespondansepart()
    {
      return $this->skjermetKorrespondansepart;
    }

    /**
     * @param boolean $skjermetKorrespondansepart
     * @return Korrespondansepart
     */
    public function setSkjermetKorrespondansepart($skjermetKorrespondansepart)
    {
      $this->skjermetKorrespondansepart = $skjermetKorrespondansepart;
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
     * @return Korrespondansepart
     */
    public function setKortnavn($kortnavn)
    {
      $this->kortnavn = $kortnavn;
      return $this;
    }

    /**
     * @return string
     */
    public function getDeresReferanse()
    {
      return $this->deresReferanse;
    }

    /**
     * @param string $deresReferanse
     * @return Korrespondansepart
     */
    public function setDeresReferanse($deresReferanse)
    {
      $this->deresReferanse = $deresReferanse;
      return $this;
    }

    /**
     * @return Journalenhet
     */
    public function getJournalenhet()
    {
      return $this->journalenhet;
    }

    /**
     * @param Journalenhet $journalenhet
     * @return Korrespondansepart
     */
    public function setJournalenhet($journalenhet)
    {
      $this->journalenhet = $journalenhet;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getFristBesvarelse()
    {
      if ($this->fristBesvarelse == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->fristBesvarelse);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $fristBesvarelse
     * @return Korrespondansepart
     */
    public function setFristBesvarelse(\DateTime $fristBesvarelse = null)
    {
      if ($fristBesvarelse == null) {
       $this->fristBesvarelse = null;
      } else {
        $this->fristBesvarelse = $fristBesvarelse->format(\DateTime::ATOM);
      }
      return $this;
    }

    /**
     * @return Forsendelsesmaate
     */
    public function getForsendelsesmaate()
    {
      return $this->forsendelsesmaate;
    }

    /**
     * @param Forsendelsesmaate $forsendelsesmaate
     * @return Korrespondansepart
     */
    public function setForsendelsesmaate($forsendelsesmaate)
    {
      $this->forsendelsesmaate = $forsendelsesmaate;
      return $this;
    }

    /**
     * @return string
     */
    public function getAdministrativEnhetInit()
    {
      return $this->administrativEnhetInit;
    }

    /**
     * @param string $administrativEnhetInit
     * @return Korrespondansepart
     */
    public function setAdministrativEnhetInit($administrativEnhetInit)
    {
      $this->administrativEnhetInit = $administrativEnhetInit;
      return $this;
    }

    /**
     * @return string
     */
    public function getAdministrativEnhet()
    {
      return $this->administrativEnhet;
    }

    /**
     * @param string $administrativEnhet
     * @return Korrespondansepart
     */
    public function setAdministrativEnhet($administrativEnhet)
    {
      $this->administrativEnhet = $administrativEnhet;
      return $this;
    }

    /**
     * @return string
     */
    public function getSaksbehandlerInit()
    {
      return $this->saksbehandlerInit;
    }

    /**
     * @param string $saksbehandlerInit
     * @return Korrespondansepart
     */
    public function setSaksbehandlerInit($saksbehandlerInit)
    {
      $this->saksbehandlerInit = $saksbehandlerInit;
      return $this;
    }

    /**
     * @return string
     */
    public function getSaksbehandler()
    {
      return $this->saksbehandler;
    }

    /**
     * @param string $saksbehandler
     * @return Korrespondansepart
     */
    public function setSaksbehandler($saksbehandler)
    {
      $this->saksbehandler = $saksbehandler;
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
     * @return Korrespondansepart
     */
    public function setKontakt($Kontakt)
    {
      $this->Kontakt = $Kontakt;
      return $this;
    }

}
