<?php

class OppdaterJournalpostStatus
{

    /**
     * @var Journalstatus $journalstatus
     */
    protected $journalstatus = null;

    /**
     * @var Journpostnoekkel $journalpostnokkel
     */
    protected $journalpostnokkel = null;

    /**
     * @var ArkivKontekst $kontekst
     */
    protected $kontekst = null;

    /**
     * @param Journalstatus $journalstatus
     * @param Journpostnoekkel $journalpostnokkel
     * @param ArkivKontekst $kontekst
     */
    public function __construct($journalstatus, $journalpostnokkel, $kontekst)
    {
      $this->journalstatus = $journalstatus;
      $this->journalpostnokkel = $journalpostnokkel;
      $this->kontekst = $kontekst;
    }

    /**
     * @return Journalstatus
     */
    public function getJournalstatus()
    {
      return $this->journalstatus;
    }

    /**
     * @param Journalstatus $journalstatus
     * @return OppdaterJournalpostStatus
     */
    public function setJournalstatus($journalstatus)
    {
      $this->journalstatus = $journalstatus;
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
     * @return OppdaterJournalpostStatus
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
     * @return OppdaterJournalpostStatus
     */
    public function setKontekst($kontekst)
    {
      $this->kontekst = $kontekst;
      return $this;
    }

}
