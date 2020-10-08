<?php

class Dokumentnummer extends Journpostnoekkel
{

    /**
     * @var int $saksaar
     */
    protected $saksaar = null;

    /**
     * @var int $sakssekvensnummer
     */
    protected $sakssekvensnummer = null;

    /**
     * @var int $journalpostnummer
     */
    protected $journalpostnummer = null;

    /**
     * @param int $saksaar
     * @param int $sakssekvensnummer
     */
    public function __construct($saksaar, $sakssekvensnummer)
    {
      $this->saksaar = $saksaar;
      $this->sakssekvensnummer = $sakssekvensnummer;
    }

    /**
     * @return int
     */
    public function getSaksaar()
    {
      return $this->saksaar;
    }

    /**
     * @param int $saksaar
     * @return Dokumentnummer
     */
    public function setSaksaar($saksaar)
    {
      $this->saksaar = $saksaar;
      return $this;
    }

    /**
     * @return int
     */
    public function getSakssekvensnummer()
    {
      return $this->sakssekvensnummer;
    }

    /**
     * @param int $sakssekvensnummer
     * @return Dokumentnummer
     */
    public function setSakssekvensnummer($sakssekvensnummer)
    {
      $this->sakssekvensnummer = $sakssekvensnummer;
      return $this;
    }

    /**
     * @return int
     */
    public function getJournalpostnummer()
    {
      return $this->journalpostnummer;
    }

    /**
     * @param int $journalpostnummer
     * @return Dokumentnummer
     */
    public function setJournalpostnummer($journalpostnummer)
    {
      $this->journalpostnummer = $journalpostnummer;
      return $this;
    }

}
