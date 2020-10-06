<?php

class SlettKorrespondansepart
{

    /**
     * @var StringListe $systemID
     */
    protected $systemID = null;

    /**
     * @var Journpostnoekkel $journalpostnokkel
     */
    protected $journalpostnokkel = null;

    /**
     * @var ArkivKontekst $kontekst
     */
    protected $kontekst = null;

    /**
     * @param StringListe $systemID
     * @param Journpostnoekkel $journalpostnokkel
     * @param ArkivKontekst $kontekst
     */
    public function __construct($systemID, $journalpostnokkel, $kontekst)
    {
      $this->systemID = $systemID;
      $this->journalpostnokkel = $journalpostnokkel;
      $this->kontekst = $kontekst;
    }

    /**
     * @return StringListe
     */
    public function getSystemID()
    {
      return $this->systemID;
    }

    /**
     * @param StringListe $systemID
     * @return SlettKorrespondansepart
     */
    public function setSystemID($systemID)
    {
      $this->systemID = $systemID;
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
     * @return SlettKorrespondansepart
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
     * @return SlettKorrespondansepart
     */
    public function setKontekst($kontekst)
    {
      $this->kontekst = $kontekst;
      return $this;
    }

}
