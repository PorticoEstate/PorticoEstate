<?php

class OperationalFaultInfo
{

    /**
     * @var OperationalFault $error
     */
    protected $error = null;

    /**
     * @param OperationalFault $error
     */
    public function __construct($error)
    {
      $this->error = $error;
    }

    /**
     * @return OperationalFault
     */
    public function getError()
    {
      return $this->error;
    }

    /**
     * @param OperationalFault $error
     * @return OperationalFaultInfo
     */
    public function setError($error)
    {
      $this->error = $error;
      return $this;
    }

}
