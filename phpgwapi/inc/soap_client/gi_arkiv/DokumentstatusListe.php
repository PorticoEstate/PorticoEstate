<?php

class DokumentstatusListe
{

    /**
     * @var Dokumentstatus[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Dokumentstatus[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param Dokumentstatus[] $liste
     * @return DokumentstatusListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
