<?php

class FilListe
{

    /**
     * @var Fil[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Fil[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param Fil[] $liste
     * @return FilListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
