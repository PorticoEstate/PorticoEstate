<?php

class EksternNoekkel
{

    /**
     * @var string $fagsystem
     */
    protected $fagsystem = null;

    /**
     * @var string $noekkel
     */
    protected $noekkel = null;

    /**
     * @param string $fagsystem
     */
    public function __construct($fagsystem)
    {
      $this->fagsystem = $fagsystem;
    }

    /**
     * @return string
     */
    public function getFagsystem()
    {
      return $this->fagsystem;
    }

    /**
     * @param string $fagsystem
     * @return EksternNoekkel
     */
    public function setFagsystem($fagsystem)
    {
      $this->fagsystem = $fagsystem;
      return $this;
    }

    /**
     * @return string
     */
    public function getNoekkel()
    {
      return $this->noekkel;
    }

    /**
     * @param string $noekkel
     * @return EksternNoekkel
     */
    public function setNoekkel($noekkel)
    {
      $this->noekkel = $noekkel;
      return $this;
    }

}
