<?php

class NySakspart
{

    /**
     * @var SakspartListe $sakspart
     */
    protected $sakspart = null;

    /**
     * @var Saksnoekkel $saksnokkel
     */
    protected $saksnokkel = null;

    /**
     * @var ArkivKontekst $kontekst
     */
    protected $kontekst = null;

    /**
     * @param SakspartListe $sakspart
     * @param Saksnoekkel $saksnokkel
     * @param ArkivKontekst $kontekst
     */
    public function __construct($sakspart, $saksnokkel, $kontekst)
    {
      $this->sakspart = $sakspart;
      $this->saksnokkel = $saksnokkel;
      $this->kontekst = $kontekst;
    }

    /**
     * @return SakspartListe
     */
    public function getSakspart()
    {
      return $this->sakspart;
    }

    /**
     * @param SakspartListe $sakspart
     * @return NySakspart
     */
    public function setSakspart($sakspart)
    {
      $this->sakspart = $sakspart;
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
     * @return NySakspart
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
     * @return NySakspart
     */
    public function setKontekst($kontekst)
    {
      $this->kontekst = $kontekst;
      return $this;
    }

}
