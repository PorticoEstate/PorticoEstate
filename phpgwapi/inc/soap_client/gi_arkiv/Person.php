<?php

class Person extends Kontakt
{

    /**
     * @var Personidentifikator $personid
     */
    protected $personid = null;

    /**
     * @var string $etternavn
     */
    protected $etternavn = null;

    /**
     * @var string $fornavn
     */
    protected $fornavn = null;

    /**
     * @param string $navn
     */
    public function __construct($navn)
    {
      parent::__construct($navn);
    }

    /**
     * @return Personidentifikator
     */
    public function getPersonid()
    {
      return $this->personid;
    }

    /**
     * @param Personidentifikator $personid
     * @return Person
     */
    public function setPersonid($personid)
    {
      $this->personid = $personid;
      return $this;
    }

    /**
     * @return string
     */
    public function getEtternavn()
    {
      return $this->etternavn;
    }

    /**
     * @param string $etternavn
     * @return Person
     */
    public function setEtternavn($etternavn)
    {
      $this->etternavn = $etternavn;
      return $this;
    }

    /**
     * @return string
     */
    public function getFornavn()
    {
      return $this->fornavn;
    }

    /**
     * @param string $fornavn
     * @return Person
     */
    public function setFornavn($fornavn)
    {
      $this->fornavn = $fornavn;
      return $this;
    }

}
