<?php

class Ansvarlig extends Kriterie
{

    /**
     * @var AnsvarligEnum $eier
     */
    protected $eier = null;

    /**
     * @param AnsvarligEnum $eier
     */
    public function __construct($eier)
    {
      $this->eier = $eier;
    }

    /**
     * @return AnsvarligEnum
     */
    public function getEier()
    {
      return $this->eier;
    }

    /**
     * @param AnsvarligEnum $eier
     * @return Ansvarlig
     */
    public function setEier($eier)
    {
      $this->eier = $eier;
      return $this;
    }

}
