<?php

class Klasse
{

    /**
     * @var string $rekkefoelge
     */
    protected $rekkefoelge = null;

    /**
     * @var Klassifikasjonssystem $klassifikasjonssystem
     */
    protected $klassifikasjonssystem = null;

    /**
     * @var string $klasseID
     */
    protected $klasseID = null;

    /**
     * @var boolean $skjermetKlasse
     */
    protected $skjermetKlasse = null;

    /**
     * @var string $ledetekst
     */
    protected $ledetekst = null;

    /**
     * @var string $tittel
     */
    protected $tittel = null;

    /**
     * @param Klassifikasjonssystem $klassifikasjonssystem
     * @param string $klasseID
     */
    public function __construct($klassifikasjonssystem, $klasseID)
    {
      $this->klassifikasjonssystem = $klassifikasjonssystem;
      $this->klasseID = $klasseID;
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
     * @return Klasse
     */
    public function setRekkefoelge($rekkefoelge)
    {
      $this->rekkefoelge = $rekkefoelge;
      return $this;
    }

    /**
     * @return Klassifikasjonssystem
     */
    public function getKlassifikasjonssystem()
    {
      return $this->klassifikasjonssystem;
    }

    /**
     * @param Klassifikasjonssystem $klassifikasjonssystem
     * @return Klasse
     */
    public function setKlassifikasjonssystem($klassifikasjonssystem)
    {
      $this->klassifikasjonssystem = $klassifikasjonssystem;
      return $this;
    }

    /**
     * @return string
     */
    public function getKlasseID()
    {
      return $this->klasseID;
    }

    /**
     * @param string $klasseID
     * @return Klasse
     */
    public function setKlasseID($klasseID)
    {
      $this->klasseID = $klasseID;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getSkjermetKlasse()
    {
      return $this->skjermetKlasse;
    }

    /**
     * @param boolean $skjermetKlasse
     * @return Klasse
     */
    public function setSkjermetKlasse($skjermetKlasse)
    {
      $this->skjermetKlasse = $skjermetKlasse;
      return $this;
    }

    /**
     * @return string
     */
    public function getLedetekst()
    {
      return $this->ledetekst;
    }

    /**
     * @param string $ledetekst
     * @return Klasse
     */
    public function setLedetekst($ledetekst)
    {
      $this->ledetekst = $ledetekst;
      return $this;
    }

    /**
     * @return string
     */
    public function getTittel()
    {
      return $this->tittel;
    }

    /**
     * @param string $tittel
     * @return Klasse
     */
    public function setTittel($tittel)
    {
      $this->tittel = $tittel;
      return $this;
    }

}
