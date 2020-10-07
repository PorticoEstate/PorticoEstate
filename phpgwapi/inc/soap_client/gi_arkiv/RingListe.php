<?php

class RingListe
{

    /**
     * @var Ring[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Ring[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param Ring[] $liste
     * @return RingListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
