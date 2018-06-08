<?php

class getAvailableAttributes
{

    /**
     * @var string $secKey
     */
    protected $secKey = null;

    /**
     * @var string $baseclassname
     */
    protected $baseclassname = null;

    /**
     * @var string $classname
     */
    protected $classname = null;

    /**
     * @param string $secKey
     * @param string $baseclassname
     * @param string $classname
     */
    public function __construct($secKey, $baseclassname, $classname)
    {
      $this->secKey = $secKey;
      $this->baseclassname = $baseclassname;
      $this->classname = $classname;
    }

    /**
     * @return string
     */
    public function getSecKey()
    {
      return $this->secKey;
    }

    /**
     * @param string $secKey
     * @return getAvailableAttributes
     */
    public function setSecKey($secKey)
    {
      $this->secKey = $secKey;
      return $this;
    }

    /**
     * @return string
     */
    public function getBaseclassname()
    {
      return $this->baseclassname;
    }

    /**
     * @param string $baseclassname
     * @return getAvailableAttributes
     */
    public function setBaseclassname($baseclassname)
    {
      $this->baseclassname = $baseclassname;
      return $this;
    }

    /**
     * @return string
     */
    public function getClassname()
    {
      return $this->classname;
    }

    /**
     * @param string $classname
     * @return getAvailableAttributes
     */
    public function setClassname($classname)
    {
      $this->classname = $classname;
      return $this;
    }

}
