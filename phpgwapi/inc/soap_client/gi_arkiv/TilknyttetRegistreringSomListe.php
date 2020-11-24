<?php

class TilknyttetRegistreringSomListe
{

    /**
     * @var TilknyttetRegistreringSom[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return TilknyttetRegistreringSom[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param TilknyttetRegistreringSom[] $liste
     * @return TilknyttetRegistreringSomListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
