<?php

class DokumentmediumListe
{

    /**
     * @var Dokumentmedium[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Dokumentmedium[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param Dokumentmedium[] $liste
     * @return DokumentmediumListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
