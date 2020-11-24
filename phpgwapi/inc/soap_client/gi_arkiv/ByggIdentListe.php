<?php

class ByggIdentListe
{

    /**
     * @var ByggIdent[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return ByggIdent[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param ByggIdent[] $liste
     * @return ByggIdentListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
