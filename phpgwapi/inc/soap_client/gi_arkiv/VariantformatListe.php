<?php

class VariantformatListe
{

    /**
     * @var Variantformat[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Variantformat[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param Variantformat[] $liste
     * @return VariantformatListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
