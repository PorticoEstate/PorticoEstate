<?php

class NyJournalpost
{

    /**
     * @var Journalpost $journalpost
     */
    protected $journalpost = null;

    /**
     * @var ArkivKontekst $kontekst
     */
    protected $kontekst = null;

    /**
     * @param Journalpost $journalpost
     * @param ArkivKontekst $kontekst
     */
    public function __construct($journalpost, $kontekst)
    {
      $this->journalpost = $journalpost;
      $this->kontekst = $kontekst;
    }

    /**
     * @return Journalpost
     */
    public function getJournalpost()
    {
      return $this->journalpost;
    }

    /**
     * @param Journalpost $journalpost
     * @return NyJournalpost
     */
    public function setJournalpost($journalpost)
    {
      $this->journalpost = $journalpost;
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
     * @return NyJournalpost
     */
    public function setKontekst($kontekst)
    {
      $this->kontekst = $kontekst;
      return $this;
    }

}
