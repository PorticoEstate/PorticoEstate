<?php

class KassasjonsvedtakListe
{

    /**
     * @var Kassasjonsvedtak[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Kassasjonsvedtak[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param Kassasjonsvedtak[] $liste
     * @return KassasjonsvedtakListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
