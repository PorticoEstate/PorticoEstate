<?php

class KorrespondansepartListe
{

    /**
     * @var Korrespondansepart[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Korrespondansepart[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param Korrespondansepart[] $liste
     * @return KorrespondansepartListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
