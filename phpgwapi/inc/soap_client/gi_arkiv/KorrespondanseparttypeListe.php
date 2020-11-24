<?php

class KorrespondanseparttypeListe
{

    /**
     * @var Korrespondanseparttype[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Korrespondanseparttype[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param Korrespondanseparttype[] $liste
     * @return KorrespondanseparttypeListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
