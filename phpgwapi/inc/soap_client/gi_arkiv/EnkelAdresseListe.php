<?php

class EnkelAdresseListe
{

    /**
     * @var EnkelAdresse[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return EnkelAdresse[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param EnkelAdresse[] $liste
     * @return EnkelAdresseListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
