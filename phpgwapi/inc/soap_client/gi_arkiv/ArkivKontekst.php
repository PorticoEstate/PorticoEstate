<?php

class ArkivKontekst extends Kontekst
{

    /**
     * @var KoordinatsystemKode $koordinatsystem
     */
    protected $koordinatsystem = null;

    /**
     * @var string $referanseoppsett
     */
    protected $referanseoppsett = null;

    
    public function __construct()
    {
      parent::__construct();
    }

    /**
     * @return KoordinatsystemKode
     */
    public function getKoordinatsystem()
    {
      return $this->koordinatsystem;
    }

    /**
     * @param KoordinatsystemKode $koordinatsystem
     * @return ArkivKontekst
     */
    public function setKoordinatsystem($koordinatsystem)
    {
      $this->koordinatsystem = $koordinatsystem;
      return $this;
    }

    /**
     * @return string
     */
    public function getReferanseoppsett()
    {
      return $this->referanseoppsett;
    }

    /**
     * @param string $referanseoppsett
     * @return ArkivKontekst
     */
    public function setReferanseoppsett($referanseoppsett)
    {
      $this->referanseoppsett = $referanseoppsett;
      return $this;
    }

}
