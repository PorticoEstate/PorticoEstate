<?php

class EnkelAdressetypeListe
{

    /**
     * @var EnkelAdressetype[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return EnkelAdressetype[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param EnkelAdressetype[] $liste
     * @return EnkelAdressetypeListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
