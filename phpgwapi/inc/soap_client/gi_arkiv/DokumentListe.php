<?php

class DokumentListe
{

    /**
     * @var Dokument[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Dokument[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param Dokument[] $liste
     * @return DokumentListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
