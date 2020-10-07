<?php

class SlettSaksmappeTilleggsinformasjon
{

    /**
     * @var StringListe $systemID
     */
    protected $systemID = null;

    /**
     * @var Saksnoekkel $saksnokkel
     */
    protected $saksnokkel = null;

    /**
     * @var ArkivKontekst $kontekst
     */
    protected $kontekst = null;

    /**
     * @param StringListe $systemID
     * @param Saksnoekkel $saksnokkel
     * @param ArkivKontekst $kontekst
     */
    public function __construct($systemID, $saksnokkel, $kontekst)
    {
      $this->systemID = $systemID;
      $this->saksnokkel = $saksnokkel;
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
     * @return SlettSaksmappeTilleggsinformasjon
     */
    public function setSystemID($systemID)
    {
      $this->systemID = $systemID;
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
     * @return SlettSaksmappeTilleggsinformasjon
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
     * @return SlettSaksmappeTilleggsinformasjon
     */
    public function setKontekst($kontekst)
    {
      $this->kontekst = $kontekst;
      return $this;
    }

}
