<?php

class NyKorrespondansepart
{

    /**
     * @var KorrespondansepartListe $part
     */
    protected $part = null;

    /**
     * @var Journpostnoekkel $journalpostnokkel
     */
    protected $journalpostnokkel = null;

    /**
     * @var ArkivKontekst $kontekst
     */
    protected $kontekst = null;

    /**
     * @param KorrespondansepartListe $part
     * @param Journpostnoekkel $journalpostnokkel
     * @param ArkivKontekst $kontekst
     */
    public function __construct($part, $journalpostnokkel, $kontekst)
    {
      $this->part = $part;
      $this->journalpostnokkel = $journalpostnokkel;
      $this->kontekst = $kontekst;
    }

    /**
     * @return KorrespondansepartListe
     */
    public function getPart()
    {
      return $this->part;
    }

    /**
     * @param KorrespondansepartListe $part
     * @return NyKorrespondansepart
     */
    public function setPart($part)
    {
      $this->part = $part;
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
     * @return NyKorrespondansepart
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
     * @return NyKorrespondansepart
     */
    public function setKontekst($kontekst)
    {
      $this->kontekst = $kontekst;
      return $this;
    }

}
