<?php

class ValidationFaultInfo
{

    /**
     * @var ValidationFault $error
     */
    protected $error = null;

    /**
     * @param ValidationFault $error
     */
    public function __construct($error)
    {
      $this->error = $error;
    }

    /**
     * @return ValidationFault
     */
    public function getError()
    {
      return $this->error;
    }

    /**
     * @param ValidationFault $error
     * @return ValidationFaultInfo
     */
    public function setError($error)
    {
      $this->error = $error;
      return $this;
    }

}
