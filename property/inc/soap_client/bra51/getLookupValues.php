<?php

class getLookupValues
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
     * @var string $attribname
     */
    protected $attribname = null;

    /**
     * @param string $secKey
     * @param string $baseclassname
     * @param string $classname
     * @param string $attribname
     */
    public function __construct($secKey, $baseclassname, $classname, $attribname)
    {
      $this->secKey = $secKey;
      $this->baseclassname = $baseclassname;
      $this->classname = $classname;
      $this->attribname = $attribname;
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
     * @return getLookupValues
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
     * @return getLookupValues
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
     * @return getLookupValues
     */
    public function setClassname($classname)
    {
      $this->classname = $classname;
      return $this;
    }

    /**
     * @return string
     */
    public function getAttribname()
    {
      return $this->attribname;
    }

    /**
     * @param string $attribname
     * @return getLookupValues
     */
    public function setAttribname($attribname)
    {
      $this->attribname = $attribname;
      return $this;
    }

}
