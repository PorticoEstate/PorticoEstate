<?php

class Skjerming
{

    /**
     * @var Tilgangsrestriksjon $tilgangsrestriksjon
     */
    protected $tilgangsrestriksjon = null;

    /**
     * @var string $skjermingshjemmel
     */
    protected $skjermingshjemmel = null;

    /**
     * @var \DateTime $skjermingOpphoererDato
     */
    protected $skjermingOpphoererDato = null;

    /**
     * @var SkjermingOpphorerAksjon $skjermingOpphoererAksjon
     */
    protected $skjermingOpphoererAksjon = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Tilgangsrestriksjon
     */
    public function getTilgangsrestriksjon()
    {
      return $this->tilgangsrestriksjon;
    }

    /**
     * @param Tilgangsrestriksjon $tilgangsrestriksjon
     * @return Skjerming
     */
    public function setTilgangsrestriksjon($tilgangsrestriksjon)
    {
      $this->tilgangsrestriksjon = $tilgangsrestriksjon;
      return $this;
    }

    /**
     * @return string
     */
    public function getSkjermingshjemmel()
    {
      return $this->skjermingshjemmel;
    }

    /**
     * @param string $skjermingshjemmel
     * @return Skjerming
     */
    public function setSkjermingshjemmel($skjermingshjemmel)
    {
      $this->skjermingshjemmel = $skjermingshjemmel;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getSkjermingOpphoererDato()
    {
      if ($this->skjermingOpphoererDato == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->skjermingOpphoererDato);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $skjermingOpphoererDato
     * @return Skjerming
     */
    public function setSkjermingOpphoererDato(\DateTime $skjermingOpphoererDato = null)
    {
      if ($skjermingOpphoererDato == null) {
       $this->skjermingOpphoererDato = null;
      } else {
        $this->skjermingOpphoererDato = $skjermingOpphoererDato->format(\DateTime::ATOM);
      }
      return $this;
    }

    /**
     * @return SkjermingOpphorerAksjon
     */
    public function getSkjermingOpphoererAksjon()
    {
      return $this->skjermingOpphoererAksjon;
    }

    /**
     * @param SkjermingOpphorerAksjon $skjermingOpphoererAksjon
     * @return Skjerming
     */
    public function setSkjermingOpphoererAksjon($skjermingOpphoererAksjon)
    {
      $this->skjermingOpphoererAksjon = $skjermingOpphoererAksjon;
      return $this;
    }

}
