<?php

class SaksstatusListe
{

    /**
     * @var Saksstatus[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Saksstatus[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param Saksstatus[] $liste
     * @return SaksstatusListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
