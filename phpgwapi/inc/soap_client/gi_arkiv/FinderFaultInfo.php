<?php

class FinderFaultInfo
{

    /**
     * @var FinderFault $error
     */
    protected $error = null;

    /**
     * @param FinderFault $error
     */
    public function __construct($error)
    {
      $this->error = $error;
    }

    /**
     * @return FinderFault
     */
    public function getError()
    {
      return $this->error;
    }

    /**
     * @param FinderFault $error
     * @return FinderFaultInfo
     */
    public function setError($error)
    {
      $this->error = $error;
      return $this;
    }

}
