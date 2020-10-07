<?php

class OppdaterJournalpostEksternNoekkel
{

    /**
     * @var EksternNoekkel $nokkel
     */
    protected $nokkel = null;

    /**
     * @var Journpostnoekkel $journalpostnokkel
     */
    protected $journalpostnokkel = null;

    /**
     * @var ArkivKontekst $kontekst
     */
    protected $kontekst = null;

    /**
     * @param EksternNoekkel $nokkel
     * @param Journpostnoekkel $journalpostnokkel
     * @param ArkivKontekst $kontekst
     */
    public function __construct($nokkel, $journalpostnokkel, $kontekst)
    {
      $this->nokkel = $nokkel;
      $this->journalpostnokkel = $journalpostnokkel;
      $this->kontekst = $kontekst;
    }

    /**
     * @return EksternNoekkel
     */
    public function getNokkel()
    {
      return $this->nokkel;
    }

    /**
     * @param EksternNoekkel $nokkel
     * @return OppdaterJournalpostEksternNoekkel
     */
    public function setNokkel($nokkel)
    {
      $this->nokkel = $nokkel;
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
     * @return OppdaterJournalpostEksternNoekkel
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
     * @return OppdaterJournalpostEksternNoekkel
     */
    public function setKontekst($kontekst)
    {
      $this->kontekst = $kontekst;
      return $this;
    }

}
