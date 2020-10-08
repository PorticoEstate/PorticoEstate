<?php

class OppdaterMappeStatusResponse
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
     * @return OppdaterMappeStatusResponse
     */
    public function setReturn($return)
    {
      $this->return = $return;
      return $this;
    }

}
