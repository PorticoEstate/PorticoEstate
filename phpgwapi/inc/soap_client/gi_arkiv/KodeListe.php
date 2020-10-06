<?php

class KodeListe
{

    /**
     * @var Kode[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Kode[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param Kode[] $liste
     * @return KodeListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
