<?php

class Ring
{

    /**
     * @var KoordinatListe $lukketKurve
     */
    protected $lukketKurve = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return KoordinatListe
     */
    public function getLukketKurve()
    {
      return $this->lukketKurve;
    }

    /**
     * @param KoordinatListe $lukketKurve
     * @return Ring
     */
    public function setLukketKurve($lukketKurve)
    {
      $this->lukketKurve = $lukketKurve;
      return $this;
    }

}
