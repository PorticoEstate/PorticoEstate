<?php

class SakEksternNoekkel extends Saksnoekkel
{

    /**
     * @var EksternNoekkel $eksternnoekkel
     */
    protected $eksternnoekkel = null;

    /**
     * @param EksternNoekkel $eksternnoekkel
     */
    public function __construct($eksternnoekkel)
    {
      $this->eksternnoekkel = $eksternnoekkel;
    }

    /**
     * @return EksternNoekkel
     */
    public function getEksternnoekkel()
    {
      return $this->eksternnoekkel;
    }

    /**
     * @param EksternNoekkel $eksternnoekkel
     * @return SakEksternNoekkel
     */
    public function setEksternnoekkel($eksternnoekkel)
    {
      $this->eksternnoekkel = $eksternnoekkel;
      return $this;
    }

}
