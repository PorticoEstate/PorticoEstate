<?php

class SakspartRolleListe
{

    /**
     * @var SakspartRolle[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return SakspartRolle[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param SakspartRolle[] $liste
     * @return SakspartRolleListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
