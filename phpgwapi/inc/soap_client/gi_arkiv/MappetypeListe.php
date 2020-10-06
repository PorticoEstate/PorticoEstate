<?php

class MappetypeListe
{

    /**
     * @var Mappetype[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Mappetype[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param Mappetype[] $liste
     * @return MappetypeListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
