<?php

class ApplicationFaultInfo
{

    /**
     * @var ApplicationFault $error
     */
    protected $error = null;

    /**
     * @param ApplicationFault $error
     */
    public function __construct($error)
    {
      $this->error = $error;
    }

    /**
     * @return ApplicationFault
     */
    public function getError()
    {
      return $this->error;
    }

    /**
     * @param ApplicationFault $error
     * @return ApplicationFaultInfo
     */
    public function setError($error)
    {
      $this->error = $error;
      return $this;
    }

}
