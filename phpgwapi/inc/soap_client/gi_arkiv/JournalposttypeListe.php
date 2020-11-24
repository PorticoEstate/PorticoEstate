<?php

class JournalposttypeListe
{

    /**
     * @var Journalposttype[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Journalposttype[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param Journalposttype[] $liste
     * @return JournalposttypeListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
