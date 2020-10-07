<?php

class OmraadeListe
{

    /**
     * @var Omraade[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Omraade[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param Omraade[] $liste
     * @return OmraadeListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
