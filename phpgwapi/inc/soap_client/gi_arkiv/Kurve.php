<?php

class Kurve extends Geometri
{

    /**
     * @var KoordinatListe $linje
     */
    protected $linje = null;

    /**
     * @param KoordinatsystemKode $koordinatsystem
     */
    public function __construct($koordinatsystem)
    {
      parent::__construct($koordinatsystem);
    }

    /**
     * @return KoordinatListe
     */
    public function getLinje()
    {
      return $this->linje;
    }

    /**
     * @param KoordinatListe $linje
     * @return Kurve
     */
    public function setLinje($linje)
    {
      $this->linje = $linje;
      return $this;
    }

}
