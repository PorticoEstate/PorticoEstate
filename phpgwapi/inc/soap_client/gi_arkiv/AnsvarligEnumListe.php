<?php

class AnsvarligEnumListe
{

    /**
     * @var AnsvarligEnum[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return AnsvarligEnum[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param AnsvarligEnum[] $liste
     * @return AnsvarligEnumListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
