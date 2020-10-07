<?php

class LandkodeListe
{

    /**
     * @var Landkode[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Landkode[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param Landkode[] $liste
     * @return LandkodeListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
