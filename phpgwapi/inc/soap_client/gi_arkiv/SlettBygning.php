<?php

class SlettBygning
{

    /**
     * @var ByggIdentListe $bygninger
     */
    protected $bygninger = null;

    /**
     * @var Saksnoekkel $saksnokkel
     */
    protected $saksnokkel = null;

    /**
     * @var ArkivKontekst $kontekst
     */
    protected $kontekst = null;

    /**
     * @param ByggIdentListe $bygninger
     * @param Saksnoekkel $saksnokkel
     * @param ArkivKontekst $kontekst
     */
    public function __construct($bygninger, $saksnokkel, $kontekst)
    {
      $this->bygninger = $bygninger;
      $this->saksnokkel = $saksnokkel;
      $this->kontekst = $kontekst;
    }

    /**
     * @return ByggIdentListe
     */
    public function getBygninger()
    {
      return $this->bygninger;
    }

    /**
     * @param ByggIdentListe $bygninger
     * @return SlettBygning
     */
    public function setBygninger($bygninger)
    {
      $this->bygninger = $bygninger;
      return $this;
    }

    /**
     * @return Saksnoekkel
     */
    public function getSaksnokkel()
    {
      return $this->saksnokkel;
    }

    /**
     * @param Saksnoekkel $saksnokkel
     * @return SlettBygning
     */
    public function setSaksnokkel($saksnokkel)
    {
      $this->saksnokkel = $saksnokkel;
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
     * @return SlettBygning
     */
    public function setKontekst($kontekst)
    {
      $this->kontekst = $kontekst;
      return $this;
    }

}
