<?php

class SystemFaultInfo
{

    /**
     * @var SystemFault $error
     */
    protected $error = null;

    /**
     * @param SystemFault $error
     */
    public function __construct($error)
    {
      $this->error = $error;
    }

    /**
     * @return SystemFault
     */
    public function getError()
    {
      return $this->error;
    }

    /**
     * @param SystemFault $error
     * @return SystemFaultInfo
     */
    public function setError($error)
    {
      $this->error = $error;
      return $this;
    }

}
