<?php

class searchAndGetDocuments
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
     * @var string $where
     */
    protected $where = null;

    /**
     * @var string $maxhits
     */
    protected $maxhits = null;

    /**
     * @param string $secKey
     * @param string $baseclassname
     * @param string $classname
     * @param string $where
     * @param string $maxhits
     */
    public function __construct($secKey, $baseclassname, $classname, $where, $maxhits)
    {
      $this->secKey = $secKey;
      $this->baseclassname = $baseclassname;
      $this->classname = $classname;
      $this->where = $where;
      $this->maxhits = $maxhits;
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
     * @return searchAndGetDocuments
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
     * @return searchAndGetDocuments
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
     * @return searchAndGetDocuments
     */
    public function setClassname($classname)
    {
      $this->classname = $classname;
      return $this;
    }

    /**
     * @return string
     */
    public function getWhere()
    {
      return $this->where;
    }

    /**
     * @param string $where
     * @return searchAndGetDocuments
     */
    public function setWhere($where)
    {
      $this->where = $where;
      return $this;
    }

    /**
     * @return string
     */
    public function getMaxhits()
    {
      return $this->maxhits;
    }

    /**
     * @param string $maxhits
     * @return searchAndGetDocuments
     */
    public function setMaxhits($maxhits)
    {
      $this->maxhits = $maxhits;
      return $this;
    }

}
