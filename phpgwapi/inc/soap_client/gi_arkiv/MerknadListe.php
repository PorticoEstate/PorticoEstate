<?php

class MerknadListe
{

    /**
     * @var Merknad[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Merknad[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param Merknad[] $liste
     * @return MerknadListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
