<?php

class PersonidentifikatorListe
{

    /**
     * @var Personidentifikator[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Personidentifikator[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param Personidentifikator[] $liste
     * @return PersonidentifikatorListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
