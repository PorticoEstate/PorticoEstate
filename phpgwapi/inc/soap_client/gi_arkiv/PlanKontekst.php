<?php

class PlanKontekst extends Kontekst
{

    /**
     * @var KoordinatsystemKode $koordinatsystem
     */
    protected $koordinatsystem = null;

    
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
     * @return PlanKontekst
     */
    public function setKoordinatsystem($koordinatsystem)
    {
      $this->koordinatsystem = $koordinatsystem;
      return $this;
    }

}
