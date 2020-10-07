<?php

class Geometri
{

    /**
     * @var KoordinatsystemKode $koordinatsystem
     */
    protected $koordinatsystem = null;

    /**
     * @param KoordinatsystemKode $koordinatsystem
     */
    public function __construct($koordinatsystem)
    {
      $this->koordinatsystem = $koordinatsystem;
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
     * @return Geometri
     */
    public function setKoordinatsystem($koordinatsystem)
    {
      $this->koordinatsystem = $koordinatsystem;
      return $this;
    }

}
