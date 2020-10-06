<?php

class JournpostSystemID extends Journpostnoekkel
{

    /**
     * @var SystemID $systemID
     */
    protected $systemID = null;

    /**
     * @param SystemID $systemID
     */
    public function __construct($systemID)
    {
      $this->systemID = $systemID;
    }

    /**
     * @return SystemID
     */
    public function getSystemID()
    {
      return $this->systemID;
    }

    /**
     * @param SystemID $systemID
     * @return JournpostSystemID
     */
    public function setSystemID($systemID)
    {
      $this->systemID = $systemID;
      return $this;
    }

}
