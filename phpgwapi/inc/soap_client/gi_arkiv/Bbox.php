<?php

class Bbox extends Geometri
{

    /**
     * @var Koordinat $nedreVenstre
     */
    protected $nedreVenstre = null;

    /**
     * @var Koordinat $oevreHoeyre
     */
    protected $oevreHoeyre = null;

    /**
     * @param KoordinatsystemKode $koordinatsystem
     * @param Koordinat $nedreVenstre
     * @param Koordinat $oevreHoeyre
     */
    public function __construct($koordinatsystem, $nedreVenstre, $oevreHoeyre)
    {
      parent::__construct($koordinatsystem);
      $this->nedreVenstre = $nedreVenstre;
      $this->oevreHoeyre = $oevreHoeyre;
    }

    /**
     * @return Koordinat
     */
    public function getNedreVenstre()
    {
      return $this->nedreVenstre;
    }

    /**
     * @param Koordinat $nedreVenstre
     * @return Bbox
     */
    public function setNedreVenstre($nedreVenstre)
    {
      $this->nedreVenstre = $nedreVenstre;
      return $this;
    }

    /**
     * @return Koordinat
     */
    public function getOevreHoeyre()
    {
      return $this->oevreHoeyre;
    }

    /**
     * @param Koordinat $oevreHoeyre
     * @return Bbox
     */
    public function setOevreHoeyre($oevreHoeyre)
    {
      $this->oevreHoeyre = $oevreHoeyre;
      return $this;
    }

}
