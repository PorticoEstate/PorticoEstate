<?php

class putFileAsByteArray
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
     * @var string $filename
     */
    protected $filename = null;

    /**
     * @var string $file
     */
    protected $file = null;

    /**
     * @param string $secKey
     * @param string $documentId
     * @param string $filename
     * @param string $file
     */
    public function __construct($secKey, $documentId, $filename, $file)
    {
      $this->secKey = $secKey;
      $this->documentId = $documentId;
      $this->filename = $filename;
      $this->file = $file;
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
     * @return putFileAsByteArray
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
     * @return putFileAsByteArray
     */
    public function setDocumentId($documentId)
    {
      $this->documentId = $documentId;
      return $this;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
      return $this->filename;
    }

    /**
     * @param string $filename
     * @return putFileAsByteArray
     */
    public function setFilename($filename)
    {
      $this->filename = $filename;
      return $this;
    }

    /**
     * @return string
     */
    public function getFile()
    {
      return $this->file;
    }

    /**
     * @param string $file
     * @return putFileAsByteArray
     */
    public function setFile($file)
    {
      $this->file = $file;
      return $this;
    }

}
