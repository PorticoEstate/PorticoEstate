<?php

class NasjonalArealplanId
{

    /**
     * @var Administrativenhetsnummer $nummer
     */
    protected $nummer = null;

    /**
     * @var string $planidentifikasjon
     */
    protected $planidentifikasjon = null;

    /**
     * @param Administrativenhetsnummer $nummer
     * @param string $planidentifikasjon
     */
    public function __construct($nummer, $planidentifikasjon)
    {
      $this->nummer = $nummer;
      $this->planidentifikasjon = $planidentifikasjon;
    }

    /**
     * @return Administrativenhetsnummer
     */
    public function getNummer()
    {
      return $this->nummer;
    }

    /**
     * @param Administrativenhetsnummer $nummer
     * @return NasjonalArealplanId
     */
    public function setNummer($nummer)
    {
      $this->nummer = $nummer;
      return $this;
    }

    /**
     * @return string
     */
    public function getPlanidentifikasjon()
    {
      return $this->planidentifikasjon;
    }

    /**
     * @param string $planidentifikasjon
     * @return NasjonalArealplanId
     */
    public function setPlanidentifikasjon($planidentifikasjon)
    {
      $this->planidentifikasjon = $planidentifikasjon;
      return $this;
    }

}
