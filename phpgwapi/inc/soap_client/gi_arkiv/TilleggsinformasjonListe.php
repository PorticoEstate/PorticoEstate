<?php

class TilleggsinformasjonListe
{

    /**
     * @var Tilleggsinformasjon[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Tilleggsinformasjon[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param Tilleggsinformasjon[] $liste
     * @return TilleggsinformasjonListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
