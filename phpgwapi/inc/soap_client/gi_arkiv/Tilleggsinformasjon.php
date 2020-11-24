<?php

class Tilleggsinformasjon
{

    /**
     * @var string $systemID
     */
    protected $systemID = null;

    /**
     * @var string $rekkefoelge
     */
    protected $rekkefoelge = null;

    /**
     * @var Informasjonstype $informasjonstype
     */
    protected $informasjonstype = null;

    /**
     * @var Tilgangsrestriksjon $tilgangsrestriksjon
     */
    protected $tilgangsrestriksjon = null;

    /**
     * @var \DateTime $oppbevaresTilDato
     */
    protected $oppbevaresTilDato = null;

    /**
     * @var string $informasjon
     */
    protected $informasjon = null;

    /**
     * @var string $tilgangsgruppeNavn
     */
    protected $tilgangsgruppeNavn = null;

    /**
     * @var \DateTime $registrertDato
     */
    protected $registrertDato = null;

    /**
     * @var string $registrertAv
     */
    protected $registrertAv = null;

    /**
     * @var string $registrertAvInit
     */
    protected $registrertAvInit = null;

    /**
     * @param Informasjonstype $informasjonstype
     * @param string $informasjon
     */
    public function __construct($informasjonstype, $informasjon)
    {
      $this->informasjonstype = $informasjonstype;
      $this->informasjon = $informasjon;
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
     * @return Tilleggsinformasjon
     */
    public function setSystemID($systemID)
    {
      $this->systemID = $systemID;
      return $this;
    }

    /**
     * @return string
     */
    public function getRekkefoelge()
    {
      return $this->rekkefoelge;
    }

    /**
     * @param string $rekkefoelge
     * @return Tilleggsinformasjon
     */
    public function setRekkefoelge($rekkefoelge)
    {
      $this->rekkefoelge = $rekkefoelge;
      return $this;
    }

    /**
     * @return Informasjonstype
     */
    public function getInformasjonstype()
    {
      return $this->informasjonstype;
    }

    /**
     * @param Informasjonstype $informasjonstype
     * @return Tilleggsinformasjon
     */
    public function setInformasjonstype($informasjonstype)
    {
      $this->informasjonstype = $informasjonstype;
      return $this;
    }

    /**
     * @return Tilgangsrestriksjon
     */
    public function getTilgangsrestriksjon()
    {
      return $this->tilgangsrestriksjon;
    }

    /**
     * @param Tilgangsrestriksjon $tilgangsrestriksjon
     * @return Tilleggsinformasjon
     */
    public function setTilgangsrestriksjon($tilgangsrestriksjon)
    {
      $this->tilgangsrestriksjon = $tilgangsrestriksjon;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getOppbevaresTilDato()
    {
      if ($this->oppbevaresTilDato == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->oppbevaresTilDato);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $oppbevaresTilDato
     * @return Tilleggsinformasjon
     */
    public function setOppbevaresTilDato(\DateTime $oppbevaresTilDato = null)
    {
      if ($oppbevaresTilDato == null) {
       $this->oppbevaresTilDato = null;
      } else {
        $this->oppbevaresTilDato = $oppbevaresTilDato->format(\DateTime::ATOM);
      }
      return $this;
    }

    /**
     * @return string
     */
    public function getInformasjon()
    {
      return $this->informasjon;
    }

    /**
     * @param string $informasjon
     * @return Tilleggsinformasjon
     */
    public function setInformasjon($informasjon)
    {
      $this->informasjon = $informasjon;
      return $this;
    }

    /**
     * @return string
     */
    public function getTilgangsgruppeNavn()
    {
      return $this->tilgangsgruppeNavn;
    }

    /**
     * @param string $tilgangsgruppeNavn
     * @return Tilleggsinformasjon
     */
    public function setTilgangsgruppeNavn($tilgangsgruppeNavn)
    {
      $this->tilgangsgruppeNavn = $tilgangsgruppeNavn;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getRegistrertDato()
    {
      if ($this->registrertDato == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->registrertDato);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $registrertDato
     * @return Tilleggsinformasjon
     */
    public function setRegistrertDato(\DateTime $registrertDato = null)
    {
      if ($registrertDato == null) {
       $this->registrertDato = null;
      } else {
        $this->registrertDato = $registrertDato->format(\DateTime::ATOM);
      }
      return $this;
    }

    /**
     * @return string
     */
    public function getRegistrertAv()
    {
      return $this->registrertAv;
    }

    /**
     * @param string $registrertAv
     * @return Tilleggsinformasjon
     */
    public function setRegistrertAv($registrertAv)
    {
      $this->registrertAv = $registrertAv;
      return $this;
    }

    /**
     * @return string
     */
    public function getRegistrertAvInit()
    {
      return $this->registrertAvInit;
    }

    /**
     * @param string $registrertAvInit
     * @return Tilleggsinformasjon
     */
    public function setRegistrertAvInit($registrertAvInit)
    {
      $this->registrertAvInit = $registrertAvInit;
      return $this;
    }

}
