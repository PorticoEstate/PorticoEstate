<?php

class ImplementationFaultInfo
{

    /**
     * @var ImplementationFault $error
     */
    protected $error = null;

    /**
     * @param ImplementationFault $error
     */
    public function __construct($error)
    {
      $this->error = $error;
    }

    /**
     * @return ImplementationFault
     */
    public function getError()
    {
      return $this->error;
    }

    /**
     * @param ImplementationFault $error
     * @return ImplementationFaultInfo
     */
    public function setError($error)
    {
      $this->error = $error;
      return $this;
    }

}
