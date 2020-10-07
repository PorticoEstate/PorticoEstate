<?php

class SlettJournalpostMerknad
{

    /**
     * @var StringListe $systemID
     */
    protected $systemID = null;

    /**
     * @var Journpostnoekkel $journalnokkel
     */
    protected $journalnokkel = null;

    /**
     * @var ArkivKontekst $kontekst
     */
    protected $kontekst = null;

    /**
     * @param StringListe $systemID
     * @param Journpostnoekkel $journalnokkel
     * @param ArkivKontekst $kontekst
     */
    public function __construct($systemID, $journalnokkel, $kontekst)
    {
      $this->systemID = $systemID;
      $this->journalnokkel = $journalnokkel;
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
     * @return SlettJournalpostMerknad
     */
    public function setSystemID($systemID)
    {
      $this->systemID = $systemID;
      return $this;
    }

    /**
     * @return Journpostnoekkel
     */
    public function getJournalnokkel()
    {
      return $this->journalnokkel;
    }

    /**
     * @param Journpostnoekkel $journalnokkel
     * @return SlettJournalpostMerknad
     */
    public function setJournalnokkel($journalnokkel)
    {
      $this->journalnokkel = $journalnokkel;
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
     * @return SlettJournalpostMerknad
     */
    public function setKontekst($kontekst)
    {
      $this->kontekst = $kontekst;
      return $this;
    }

}
