<?php

class BboxListe
{

    /**
     * @var Bbox[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Bbox[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param Bbox[] $liste
     * @return BboxListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
