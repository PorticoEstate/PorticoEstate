<?php

class Stat extends Administrativenhetsnummer
{

    /**
     * @var string $landskode
     */
    protected $landskode = null;

    /**
     * @param string $landskode
     */
    public function __construct($landskode)
    {
      $this->landskode = $landskode;
    }

    /**
     * @return string
     */
    public function getLandskode()
    {
      return $this->landskode;
    }

    /**
     * @param string $landskode
     * @return Stat
     */
    public function setLandskode($landskode)
    {
      $this->landskode = $landskode;
      return $this;
    }

}
