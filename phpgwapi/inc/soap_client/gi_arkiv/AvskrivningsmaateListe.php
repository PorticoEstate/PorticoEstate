<?php

class AvskrivningsmaateListe
{

    /**
     * @var Avskrivningsmaate[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Avskrivningsmaate[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param Avskrivningsmaate[] $liste
     * @return AvskrivningsmaateListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
