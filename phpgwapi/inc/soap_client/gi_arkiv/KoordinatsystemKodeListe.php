<?php

class KoordinatsystemKodeListe
{

    /**
     * @var KoordinatsystemKode[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return KoordinatsystemKode[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param KoordinatsystemKode[] $liste
     * @return KoordinatsystemKodeListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
