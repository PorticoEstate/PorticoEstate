<?php

class SakSystemIdListe
{

    /**
     * @var SakSystemId[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return SakSystemId[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param SakSystemId[] $liste
     * @return SakSystemIdListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
