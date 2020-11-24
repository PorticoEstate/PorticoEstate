<?php

class KriterieListe
{

    /**
     * @var Kriterie[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Kriterie[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param Kriterie[] $liste
     * @return KriterieListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
