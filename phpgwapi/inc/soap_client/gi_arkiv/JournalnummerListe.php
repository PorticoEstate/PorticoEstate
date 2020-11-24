<?php

class JournalnummerListe
{

    /**
     * @var Journalnummer[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Journalnummer[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param Journalnummer[] $liste
     * @return JournalnummerListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
