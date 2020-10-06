<?php

class PunktListe
{

    /**
     * @var Punkt[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Punkt[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param Punkt[] $liste
     * @return PunktListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
