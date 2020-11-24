<?php

class Personidentifikator
{

    /**
     * @var string $personidentifikatorNr
     */
    protected $personidentifikatorNr = null;

    /**
     * @var PersonidentifikatorType $personidentifikatorType
     */
    protected $personidentifikatorType = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return string
     */
    public function getPersonidentifikatorNr()
    {
      return $this->personidentifikatorNr;
    }

    /**
     * @param string $personidentifikatorNr
     * @return Personidentifikator
     */
    public function setPersonidentifikatorNr($personidentifikatorNr)
    {
      $this->personidentifikatorNr = $personidentifikatorNr;
      return $this;
    }

    /**
     * @return PersonidentifikatorType
     */
    public function getPersonidentifikatorType()
    {
      return $this->personidentifikatorType;
    }

    /**
     * @param PersonidentifikatorType $personidentifikatorType
     * @return Personidentifikator
     */
    public function setPersonidentifikatorType($personidentifikatorType)
    {
      $this->personidentifikatorType = $personidentifikatorType;
      return $this;
    }

}
