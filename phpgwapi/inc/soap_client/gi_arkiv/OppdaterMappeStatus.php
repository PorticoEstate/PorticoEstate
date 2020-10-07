<?php

class OppdaterMappeStatus
{

    /**
     * @var Saksstatus $saksstatuskode
     */
    protected $saksstatuskode = null;

    /**
     * @var Saksnoekkel $saksnokkel
     */
    protected $saksnokkel = null;

    /**
     * @var ArkivKontekst $kontekst
     */
    protected $kontekst = null;

    /**
     * @param Saksstatus $saksstatuskode
     * @param Saksnoekkel $saksnokkel
     * @param ArkivKontekst $kontekst
     */
    public function __construct($saksstatuskode, $saksnokkel, $kontekst)
    {
      $this->saksstatuskode = $saksstatuskode;
      $this->saksnokkel = $saksnokkel;
      $this->kontekst = $kontekst;
    }

    /**
     * @return Saksstatus
     */
    public function getSaksstatuskode()
    {
      return $this->saksstatuskode;
    }

    /**
     * @param Saksstatus $saksstatuskode
     * @return OppdaterMappeStatus
     */
    public function setSaksstatuskode($saksstatuskode)
    {
      $this->saksstatuskode = $saksstatuskode;
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
     * @return OppdaterMappeStatus
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
     * @return OppdaterMappeStatus
     */
    public function setKontekst($kontekst)
    {
      $this->kontekst = $kontekst;
      return $this;
    }

}
