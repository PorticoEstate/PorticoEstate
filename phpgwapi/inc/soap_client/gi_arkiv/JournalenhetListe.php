<?php

class JournalenhetListe
{

    /**
     * @var Journalenhet[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Journalenhet[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param Journalenhet[] $liste
     * @return JournalenhetListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
