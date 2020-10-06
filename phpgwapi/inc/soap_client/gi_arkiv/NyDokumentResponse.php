<?php

class NyDokumentResponse
{

    /**
     * @var Dokument $return
     */
    protected $return = null;

    /**
     * @param Dokument $return
     */
    public function __construct($return)
    {
      $this->return = $return;
    }

    /**
     * @return Dokument
     */
    public function getReturn()
    {
      return $this->return;
    }

    /**
     * @param Dokument $return
     * @return NyDokumentResponse
     */
    public function setReturn($return)
    {
      $this->return = $return;
      return $this;
    }

}
