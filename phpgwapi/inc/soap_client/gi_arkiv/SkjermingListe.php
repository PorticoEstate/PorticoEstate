<?php

class SkjermingListe
{

    /**
     * @var Skjerming[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Skjerming[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param Skjerming[] $liste
     * @return SkjermingListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
