<?php

class AvskrivningListe
{

    /**
     * @var Avskrivning[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Avskrivning[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param Avskrivning[] $liste
     * @return AvskrivningListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
