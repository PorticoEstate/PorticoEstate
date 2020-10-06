<?php

class KlassifikasjonssystemListe
{

    /**
     * @var Klassifikasjonssystem[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Klassifikasjonssystem[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param Klassifikasjonssystem[] $liste
     * @return KlassifikasjonssystemListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
