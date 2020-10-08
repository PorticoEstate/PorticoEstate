<?php

class KoordinatListe
{

    /**
     * @var Koordinat[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Koordinat[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param Koordinat[] $liste
     * @return KoordinatListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
