<?php

class ByggIdent
{

    /**
     * @var int $bygningsNummer
     */
    protected $bygningsNummer = null;

    /**
     * @var int $endringsloepenummer
     */
    protected $endringsloepenummer = null;

    /**
     * @param int $bygningsNummer
     */
    public function __construct($bygningsNummer)
    {
      $this->bygningsNummer = $bygningsNummer;
    }

    /**
     * @return int
     */
    public function getBygningsNummer()
    {
      return $this->bygningsNummer;
    }

    /**
     * @param int $bygningsNummer
     * @return ByggIdent
     */
    public function setBygningsNummer($bygningsNummer)
    {
      $this->bygningsNummer = $bygningsNummer;
      return $this;
    }

    /**
     * @return int
     */
    public function getEndringsloepenummer()
    {
      return $this->endringsloepenummer;
    }

    /**
     * @param int $endringsloepenummer
     * @return ByggIdent
     */
    public function setEndringsloepenummer($endringsloepenummer)
    {
      $this->endringsloepenummer = $endringsloepenummer;
      return $this;
    }

}
