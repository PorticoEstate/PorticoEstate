<?php

class InformasjonstypeListe
{

    /**
     * @var Informasjonstype[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Informasjonstype[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param Informasjonstype[] $liste
     * @return InformasjonstypeListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
