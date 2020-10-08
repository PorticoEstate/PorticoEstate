<?php

class Saksnummer extends Saksnoekkel
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
     * @return Saksnummer
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
     * @return Saksnummer
     */
    public function setSakssekvensnummer($sakssekvensnummer)
    {
      $this->sakssekvensnummer = $sakssekvensnummer;
      return $this;
    }

}
