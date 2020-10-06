<?php

class OppdaterJournalpostAnsvarlig
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
     * @var Journpostnoekkel $journalpostnokkel
     */
    protected $journalpostnokkel = null;

    /**
     * @var ArkivKontekst $kontekst
     */
    protected $kontekst = null;

    /**
     * @param string $nyAdministrativEnhetKode
     * @param string $nySaksbehandlerInit
     * @param Journpostnoekkel $journalpostnokkel
     * @param ArkivKontekst $kontekst
     */
    public function __construct($nyAdministrativEnhetKode, $nySaksbehandlerInit, $journalpostnokkel, $kontekst)
    {
      $this->nyAdministrativEnhetKode = $nyAdministrativEnhetKode;
      $this->nySaksbehandlerInit = $nySaksbehandlerInit;
      $this->journalpostnokkel = $journalpostnokkel;
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
     * @return OppdaterJournalpostAnsvarlig
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
     * @return OppdaterJournalpostAnsvarlig
     */
    public function setNySaksbehandlerInit($nySaksbehandlerInit)
    {
      $this->nySaksbehandlerInit = $nySaksbehandlerInit;
      return $this;
    }

    /**
     * @return Journpostnoekkel
     */
    public function getJournalpostnokkel()
    {
      return $this->journalpostnokkel;
    }

    /**
     * @param Journpostnoekkel $journalpostnokkel
     * @return OppdaterJournalpostAnsvarlig
     */
    public function setJournalpostnokkel($journalpostnokkel)
    {
      $this->journalpostnokkel = $journalpostnokkel;
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
     * @return OppdaterJournalpostAnsvarlig
     */
    public function setKontekst($kontekst)
    {
      $this->kontekst = $kontekst;
      return $this;
    }

}
