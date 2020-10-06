<?php

class KontaktListe
{

    /**
     * @var Kontakt[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Kontakt[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param Kontakt[] $liste
     * @return KontaktListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
