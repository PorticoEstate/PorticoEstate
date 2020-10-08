<?php

class ElektroniskAdresseListe
{

    /**
     * @var ElektroniskAdresse[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return ElektroniskAdresse[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param ElektroniskAdresse[] $liste
     * @return ElektroniskAdresseListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
