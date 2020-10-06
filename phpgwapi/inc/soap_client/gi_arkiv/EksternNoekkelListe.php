<?php

class EksternNoekkelListe
{

    /**
     * @var EksternNoekkel[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return EksternNoekkel[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param EksternNoekkel[] $liste
     * @return EksternNoekkelListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
