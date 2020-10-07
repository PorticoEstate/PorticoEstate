<?php

class SaksmappeListe
{

    /**
     * @var Saksmappe[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Saksmappe[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param Saksmappe[] $liste
     * @return SaksmappeListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
