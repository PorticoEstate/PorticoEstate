<?php

class NySaksmappeResponse
{

    /**
     * @var Saksmappe $return
     */
    protected $return = null;

    /**
     * @param Saksmappe $return
     */
    public function __construct($return)
    {
      $this->return = $return;
    }

    /**
     * @return Saksmappe
     */
    public function getReturn()
    {
      return $this->return;
    }

    /**
     * @param Saksmappe $return
     * @return NySaksmappeResponse
     */
    public function setReturn($return)
    {
      $this->return = $return;
      return $this;
    }

}
