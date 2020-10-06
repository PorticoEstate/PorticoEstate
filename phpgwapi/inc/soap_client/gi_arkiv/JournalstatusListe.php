<?php

class JournalstatusListe
{

    /**
     * @var Journalstatus[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Journalstatus[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param Journalstatus[] $liste
     * @return JournalstatusListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
