<?php

class createDocument
{

    /**
     * @var string $secKey
     */
    protected $secKey = null;

    /**
     * @var boolean $assignDocKey
     */
    protected $assignDocKey = null;

    /**
     * @var Document $doc
     */
    protected $doc = null;

    /**
     * @param string $secKey
     * @param boolean $assignDocKey
     * @param Document $doc
     */
    public function __construct($secKey, $assignDocKey, $doc)
    {
      $this->secKey = $secKey;
      $this->assignDocKey = $assignDocKey;
      $this->doc = $doc;
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
     * @return createDocument
     */
    public function setSecKey($secKey)
    {
      $this->secKey = $secKey;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getAssignDocKey()
    {
      return $this->assignDocKey;
    }

    /**
     * @param boolean $assignDocKey
     * @return createDocument
     */
    public function setAssignDocKey($assignDocKey)
    {
      $this->assignDocKey = $assignDocKey;
      return $this;
    }

    /**
     * @return Document
     */
    public function getDoc()
    {
      return $this->doc;
    }

    /**
     * @param Document $doc
     * @return createDocument
     */
    public function setDoc($doc)
    {
      $this->doc = $doc;
      return $this;
    }

}
