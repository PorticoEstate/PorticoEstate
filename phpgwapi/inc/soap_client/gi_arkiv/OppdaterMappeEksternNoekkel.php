<?php

class OppdaterMappeEksternNoekkel
{

    /**
     * @var EksternNoekkel $nokkel
     */
    protected $nokkel = null;

    /**
     * @var Saksnoekkel $saksnokkel
     */
    protected $saksnokkel = null;

    /**
     * @var ArkivKontekst $kontekst
     */
    protected $kontekst = null;

    /**
     * @param EksternNoekkel $nokkel
     * @param Saksnoekkel $saksnokkel
     * @param ArkivKontekst $kontekst
     */
    public function __construct($nokkel, $saksnokkel, $kontekst)
    {
      $this->nokkel = $nokkel;
      $this->saksnokkel = $saksnokkel;
      $this->kontekst = $kontekst;
    }

    /**
     * @return EksternNoekkel
     */
    public function getNokkel()
    {
      return $this->nokkel;
    }

    /**
     * @param EksternNoekkel $nokkel
     * @return OppdaterMappeEksternNoekkel
     */
    public function setNokkel($nokkel)
    {
      $this->nokkel = $nokkel;
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
     * @return OppdaterMappeEksternNoekkel
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
     * @return OppdaterMappeEksternNoekkel
     */
    public function setKontekst($kontekst)
    {
      $this->kontekst = $kontekst;
      return $this;
    }

}
