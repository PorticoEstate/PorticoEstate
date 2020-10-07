<?php

class KlasseListe
{

    /**
     * @var Klasse[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Klasse[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param Klasse[] $liste
     * @return KlasseListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
