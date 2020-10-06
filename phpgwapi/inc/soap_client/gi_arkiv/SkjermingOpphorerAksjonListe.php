<?php

class SkjermingOpphorerAksjonListe
{

    /**
     * @var SkjermingOpphorerAksjon[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return SkjermingOpphorerAksjon[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param SkjermingOpphorerAksjon[] $liste
     * @return SkjermingOpphorerAksjonListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
