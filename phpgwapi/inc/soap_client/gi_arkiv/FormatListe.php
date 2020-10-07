<?php

class FormatListe
{

    /**
     * @var Format[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Format[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param Format[] $liste
     * @return FormatListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
