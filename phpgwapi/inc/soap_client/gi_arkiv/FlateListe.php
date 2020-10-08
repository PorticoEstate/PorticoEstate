<?php

class FlateListe
{

    /**
     * @var Flate[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Flate[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param Flate[] $liste
     * @return FlateListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
