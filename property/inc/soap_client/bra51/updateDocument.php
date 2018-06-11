<?php

class updateDocument
{

    /**
     * @var string $secKey
     */
    protected $secKey = null;

    /**
     * @var Document $document
     */
    protected $document = null;

    /**
     * @param string $secKey
     * @param Document $document
     */
    public function __construct($secKey, $document)
    {
      $this->secKey = $secKey;
      $this->document = $document;
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
     * @return updateDocument
     */
    public function setSecKey($secKey)
    {
      $this->secKey = $secKey;
      return $this;
    }

    /**
     * @return Document
     */
    public function getDocument()
    {
      return $this->document;
    }

    /**
     * @param Document $document
     * @return updateDocument
     */
    public function setDocument($document)
    {
      $this->document = $document;
      return $this;
    }

}
