<?php

class NasjonalArealplanIdListe
{

    /**
     * @var NasjonalArealplanId[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return NasjonalArealplanId[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param NasjonalArealplanId[] $liste
     * @return NasjonalArealplanIdListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
