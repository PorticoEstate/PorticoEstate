<?php

class MatrikkelnummerListe
{

    /**
     * @var Matrikkelnummer[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Matrikkelnummer[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param Matrikkelnummer[] $liste
     * @return MatrikkelnummerListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
