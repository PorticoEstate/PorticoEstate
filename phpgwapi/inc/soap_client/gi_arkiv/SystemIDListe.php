<?php

class SystemIDListe
{

    /**
     * @var SystemID[] $liste
     */
    protected $liste = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return SystemID[]
     */
    public function getListe()
    {
      return $this->liste;
    }

    /**
     * @param SystemID[] $liste
     * @return SystemIDListe
     */
    public function setListe(array $liste = null)
    {
      $this->liste = $liste;
      return $this;
    }

}
