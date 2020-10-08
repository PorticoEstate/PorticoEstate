<?php

class PersonidentifikatorTypeListe
{

    /**
     * @var PersonidentifikatorType[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return PersonidentifikatorType[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param PersonidentifikatorType[] $liste
     * @return PersonidentifikatorTypeListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
