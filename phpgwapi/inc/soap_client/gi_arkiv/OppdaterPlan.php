<?php

class OppdaterPlan
{

    /**
     * @var NasjonalArealplanId $planid
     */
    protected $planid = null;

    /**
     * @var Saksnoekkel $saksnokkel
     */
    protected $saksnokkel = null;

    /**
     * @var ArkivKontekst $kontekst
     */
    protected $kontekst = null;

    /**
     * @param NasjonalArealplanId $planid
     * @param Saksnoekkel $saksnokkel
     * @param ArkivKontekst $kontekst
     */
    public function __construct($planid, $saksnokkel, $kontekst)
    {
      $this->planid = $planid;
      $this->saksnokkel = $saksnokkel;
      $this->kontekst = $kontekst;
    }

    /**
     * @return NasjonalArealplanId
     */
    public function getPlanid()
    {
      return $this->planid;
    }

    /**
     * @param NasjonalArealplanId $planid
     * @return OppdaterPlan
     */
    public function setPlanid($planid)
    {
      $this->planid = $planid;
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
     * @return OppdaterPlan
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
     * @return OppdaterPlan
     */
    public function setKontekst($kontekst)
    {
      $this->kontekst = $kontekst;
      return $this;
    }

}
