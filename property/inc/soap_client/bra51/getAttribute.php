<?php

class getAttribute
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
     * @var string $attributeName
     */
    protected $attributeName = null;

    /**
     * @param string $secKey
     * @param string $documentId
     * @param string $attributeName
     */
    public function __construct($secKey, $documentId, $attributeName)
    {
      $this->secKey = $secKey;
      $this->documentId = $documentId;
      $this->attributeName = $attributeName;
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
     * @return getAttribute
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
     * @return getAttribute
     */
    public function setDocumentId($documentId)
    {
      $this->documentId = $documentId;
      return $this;
    }

    /**
     * @return string
     */
    public function getAttributeName()
    {
      return $this->attributeName;
    }

    /**
     * @param string $attributeName
     * @return getAttribute
     */
    public function setAttributeName($attributeName)
    {
      $this->attributeName = $attributeName;
      return $this;
    }

}
