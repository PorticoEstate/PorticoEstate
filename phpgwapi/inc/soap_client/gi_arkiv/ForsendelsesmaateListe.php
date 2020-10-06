<?php

class ForsendelsesmaateListe
{

    /**
     * @var Forsendelsesmaate[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Forsendelsesmaate[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param Forsendelsesmaate[] $liste
     * @return ForsendelsesmaateListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
