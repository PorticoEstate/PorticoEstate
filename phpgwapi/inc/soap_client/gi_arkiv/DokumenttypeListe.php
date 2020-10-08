<?php

class DokumenttypeListe
{

    /**
     * @var Dokumenttype[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Dokumenttype[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param Dokumenttype[] $liste
     * @return DokumenttypeListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
