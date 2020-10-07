<?php

class SakspartListe
{

    /**
     * @var Sakspart[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Sakspart[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param Sakspart[] $liste
     * @return SakspartListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
