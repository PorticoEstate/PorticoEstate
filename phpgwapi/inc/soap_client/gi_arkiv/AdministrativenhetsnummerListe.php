<?php

class AdministrativenhetsnummerListe
{

    /**
     * @var Administrativenhetsnummer[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Administrativenhetsnummer[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param Administrativenhetsnummer[] $liste
     * @return AdministrativenhetsnummerListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
