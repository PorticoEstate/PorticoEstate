<?php

class TilgangsrestriksjonListe
{

    /**
     * @var Tilgangsrestriksjon[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Tilgangsrestriksjon[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param Tilgangsrestriksjon[] $liste
     * @return TilgangsrestriksjonListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
