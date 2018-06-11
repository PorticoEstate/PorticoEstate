<?php

class getDocument
{

    /**
     * @var string $secKey
     */
    protected $secKey = null;

    /**
     * @var string $documentId
     */
    protected $documentId = null;

    /**
     * @param string $secKey
     * @param string $documentId
     */
    public function __construct($secKey, $documentId)
    {
      $this->secKey = $secKey;
      $this->documentId = $documentId;
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
     * @return getDocument
     */
    public function setSecKey($secKey)
    {
      $this->secKey = $secKey;
      return $this;
    }

    /**
     * @return string
     */
    public function getDocumentId()
    {
      return $this->documentId;
    }

    /**
     * @param string $documentId
     * @return getDocument
     */
    public function setDocumentId($documentId)
    {
      $this->documentId = $documentId;
      return $this;
    }

}
