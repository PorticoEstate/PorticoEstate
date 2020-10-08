<?php

class Punkt extends Geometri
{

    /**
     * @var Koordinat $posisjon
     */
    protected $posisjon = null;

    /**
     * @param KoordinatsystemKode $koordinatsystem
     * @param Koordinat $posisjon
     */
    public function __construct($koordinatsystem, $posisjon)
    {
      parent::__construct($koordinatsystem);
      $this->posisjon = $posisjon;
    }

    /**
     * @return Koordinat
     */
    public function getPosisjon()
    {
      return $this->posisjon;
    }

    /**
     * @param Koordinat $posisjon
     * @return Punkt
     */
    public function setPosisjon($posisjon)
    {
      $this->posisjon = $posisjon;
      return $this;
    }

}
