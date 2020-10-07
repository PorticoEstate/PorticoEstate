<?php

class Koordinat
{

    /**
     * @var float $x
     */
    protected $x = null;

    /**
     * @var float $y
     */
    protected $y = null;

    /**
     * @var float $z
     */
    protected $z = null;

    /**
     * @param float $x
     * @param float $y
     */
    public function __construct($x, $y)
    {
      $this->x = $x;
      $this->y = $y;
    }

    /**
     * @return float
     */
    public function getX()
    {
      return $this->x;
    }

    /**
     * @param float $x
     * @return Koordinat
     */
    public function setX($x)
    {
      $this->x = $x;
      return $this;
    }

    /**
     * @return float
     */
    public function getY()
    {
      return $this->y;
    }

    /**
     * @param float $y
     * @return Koordinat
     */
    public function setY($y)
    {
      $this->y = $y;
      return $this;
    }

    /**
     * @return float
     */
    public function getZ()
    {
      return $this->z;
    }

    /**
     * @param float $z
     * @return Koordinat
     */
    public function setZ($z)
    {
      $this->z = $z;
      return $this;
    }

}
