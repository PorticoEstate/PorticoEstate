<?php

class JournalpostListe
{

    /**
     * @var Journalpost[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Journalpost[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param Journalpost[] $liste
     * @return JournalpostListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
