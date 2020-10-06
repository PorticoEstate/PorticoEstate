<?php

class BboxKriterie extends Kriterie
{

    /**
     * @var Bbox $bbox
     */
    protected $bbox = null;

    /**
     * @param Bbox $bbox
     */
    public function __construct($bbox)
    {
      $this->bbox = $bbox;
    }

    /**
     * @return Bbox
     */
    public function getBbox()
    {
      return $this->bbox;
    }

    /**
     * @param Bbox $bbox
     * @return BboxKriterie
     */
    public function setBbox($bbox)
    {
      $this->bbox = $bbox;
      return $this;
    }

}
