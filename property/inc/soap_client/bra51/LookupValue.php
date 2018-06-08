<?php

class LookupValue
{

    /**
     * @var string $Id
     */
    protected $Id = null;

    /**
     * @var string $Description
     */
    protected $Description = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return string
     */
    public function getId()
    {
      return $this->Id;
    }

    /**
     * @param string $Id
     * @return LookupValue
     */
    public function setId($Id)
    {
      $this->Id = $Id;
      return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
      return $this->Description;
    }

    /**
     * @param string $Description
     * @return LookupValue
     */
    public function setDescription($Description)
    {
      $this->Description = $Description;
      return $this;
    }

}
