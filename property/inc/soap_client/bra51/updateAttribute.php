<?php

class updateAttribute
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
     * @var string $documentId
     */
    protected $documentId = null;

    /**
     * @var string $attribute
     */
    protected $attribute = null;

    /**
     * @var ArrayOfAnyType $value
     */
    protected $value = null;

    /**
     * @param string $secKey
     * @param string $baseclassname
     * @param string $documentId
     * @param string $attribute
     * @param ArrayOfAnyType $value
     */
    public function __construct($secKey, $baseclassname, $documentId, $attribute, $value)
    {
      $this->secKey = $secKey;
      $this->baseclassname = $baseclassname;
      $this->documentId = $documentId;
      $this->attribute = $attribute;
      $this->value = $value;
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
     * @return updateAttribute
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
     * @return updateAttribute
     */
    public function setBaseclassname($baseclassname)
    {
      $this->baseclassname = $baseclassname;
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
     * @return updateAttribute
     */
    public function setDocumentId($documentId)
    {
      $this->documentId = $documentId;
      return $this;
    }

    /**
     * @return string
     */
    public function getAttribute()
    {
      return $this->attribute;
    }

    /**
     * @param string $attribute
     * @return updateAttribute
     */
    public function setAttribute($attribute)
    {
      $this->attribute = $attribute;
      return $this;
    }

    /**
     * @return ArrayOfAnyType
     */
    public function getValue()
    {
      return $this->value;
    }

    /**
     * @param ArrayOfAnyType $value
     * @return updateAttribute
     */
    public function setValue($value)
    {
      $this->value = $value;
      return $this;
    }

}
