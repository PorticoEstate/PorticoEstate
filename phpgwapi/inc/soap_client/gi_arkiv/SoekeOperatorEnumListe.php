<?php

class SoekeOperatorEnumListe
{

    /**
     * @var SoekeOperatorEnum[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return SoekeOperatorEnum[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param SoekeOperatorEnum[] $liste
     * @return SoekeOperatorEnumListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
