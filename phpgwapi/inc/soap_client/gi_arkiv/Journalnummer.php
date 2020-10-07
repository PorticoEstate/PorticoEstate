<?php

class Journalnummer extends Journpostnoekkel
{

    /**
     * @var int $journalaar
     */
    protected $journalaar = null;

    /**
     * @var int $journalsekvensnummer
     */
    protected $journalsekvensnummer = null;

    /**
     * @param int $journalaar
     * @param int $journalsekvensnummer
     */
    public function __construct($journalaar, $journalsekvensnummer)
    {
      $this->journalaar = $journalaar;
      $this->journalsekvensnummer = $journalsekvensnummer;
    }

    /**
     * @return int
     */
    public function getJournalaar()
    {
      return $this->journalaar;
    }

    /**
     * @param int $journalaar
     * @return Journalnummer
     */
    public function setJournalaar($journalaar)
    {
      $this->journalaar = $journalaar;
      return $this;
    }

    /**
     * @return int
     */
    public function getJournalsekvensnummer()
    {
      return $this->journalsekvensnummer;
    }

    /**
     * @param int $journalsekvensnummer
     * @return Journalnummer
     */
    public function setJournalsekvensnummer($journalsekvensnummer)
    {
      $this->journalsekvensnummer = $journalsekvensnummer;
      return $this;
    }

}
