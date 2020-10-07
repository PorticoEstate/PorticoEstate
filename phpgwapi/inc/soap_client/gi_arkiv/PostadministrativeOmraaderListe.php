<?php

class PostadministrativeOmraaderListe
{

    /**
     * @var PostadministrativeOmraader[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return PostadministrativeOmraader[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param PostadministrativeOmraader[] $liste
     * @return PostadministrativeOmraaderListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
