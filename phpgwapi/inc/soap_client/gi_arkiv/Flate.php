<?php

class Flate extends Geometri
{

    /**
     * @var RingListe $indreAvgrensning
     */
    protected $indreAvgrensning = null;

    /**
     * @var Ring $ytreAvgrensning
     */
    protected $ytreAvgrensning = null;

    /**
     * @param KoordinatsystemKode $koordinatsystem
     * @param Ring $ytreAvgrensning
     */
    public function __construct($koordinatsystem, $ytreAvgrensning)
    {
      parent::__construct($koordinatsystem);
      $this->ytreAvgrensning = $ytreAvgrensning;
    }

    /**
     * @return RingListe
     */
    public function getIndreAvgrensning()
    {
      return $this->indreAvgrensning;
    }

    /**
     * @param RingListe $indreAvgrensning
     * @return Flate
     */
    public function setIndreAvgrensning($indreAvgrensning)
    {
      $this->indreAvgrensning = $indreAvgrensning;
      return $this;
    }

    /**
     * @return Ring
     */
    public function getYtreAvgrensning()
    {
      return $this->ytreAvgrensning;
    }

    /**
     * @param Ring $ytreAvgrensning
     * @return Flate
     */
    public function setYtreAvgrensning($ytreAvgrensning)
    {
      $this->ytreAvgrensning = $ytreAvgrensning;
      return $this;
    }

}
