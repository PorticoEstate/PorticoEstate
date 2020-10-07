<?php

class OppdaterMappeAnsvarlig
{

    /**
     * @var string $nyAdministrativEnhetKode
     */
    protected $nyAdministrativEnhetKode = null;

    /**
     * @var string $nySaksbehandlerInit
     */
    protected $nySaksbehandlerInit = null;

    /**
     * @var Saksnoekkel $saksnokkel
     */
    protected $saksnokkel = null;

    /**
     * @var ArkivKontekst $kontekst
     */
    protected $kontekst = null;

    /**
     * @param string $nyAdministrativEnhetKode
     * @param string $nySaksbehandlerInit
     * @param Saksnoekkel $saksnokkel
     * @param ArkivKontekst $kontekst
     */
    public function __construct($nyAdministrativEnhetKode, $nySaksbehandlerInit, $saksnokkel, $kontekst)
    {
      $this->nyAdministrativEnhetKode = $nyAdministrativEnhetKode;
      $this->nySaksbehandlerInit = $nySaksbehandlerInit;
      $this->saksnokkel = $saksnokkel;
      $this->kontekst = $kontekst;
    }

    /**
     * @return string
     */
    public function getNyAdministrativEnhetKode()
    {
      return $this->nyAdministrativEnhetKode;
    }

    /**
     * @param string $nyAdministrativEnhetKode
     * @return OppdaterMappeAnsvarlig
     */
    public function setNyAdministrativEnhetKode($nyAdministrativEnhetKode)
    {
      $this->nyAdministrativEnhetKode = $nyAdministrativEnhetKode;
      return $this;
    }

    /**
     * @return string
     */
    public function getNySaksbehandlerInit()
    {
      return $this->nySaksbehandlerInit;
    }

    /**
     * @param string $nySaksbehandlerInit
     * @return OppdaterMappeAnsvarlig
     */
    public function setNySaksbehandlerInit($nySaksbehandlerInit)
    {
      $this->nySaksbehandlerInit = $nySaksbehandlerInit;
      return $this;
    }

    /**
     * @return Saksnoekkel
     */
    public function getSaksnokkel()
    {
      return $this->saksnokkel;
    }

    /**
     * @param Saksnoekkel $saksnokkel
     * @return OppdaterMappeAnsvarlig
     */
    public function setSaksnokkel($saksnokkel)
    {
      $this->saksnokkel = $saksnokkel;
      return $this;
    }

    /**
     * @return ArkivKontekst
     */
    public function getKontekst()
    {
      return $this->kontekst;
    }

    /**
     * @param ArkivKontekst $kontekst
     * @return OppdaterMappeAnsvarlig
     */
    public function setKontekst($kontekst)
    {
      $this->kontekst = $kontekst;
      return $this;
    }

}
