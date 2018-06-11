<?php

class fileTransferSendChunkedInit
{

    /**
     * @var string $secKey
     */
    protected $secKey = null;

    /**
     * @var string $docid
     */
    protected $docid = null;

    /**
     * @var string $filename
     */
    protected $filename = null;

    /**
     * @param string $secKey
     * @param string $docid
     * @param string $filename
     */
    public function __construct($secKey, $docid, $filename)
    {
      $this->secKey = $secKey;
      $this->docid = $docid;
      $this->filename = $filename;
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
     * @return fileTransferSendChunkedInit
     */
    public function setSecKey($secKey)
    {
      $this->secKey = $secKey;
      return $this;
    }

    /**
     * @return string
     */
    public function getDocid()
    {
      return $this->docid;
    }

    /**
     * @param string $docid
     * @return fileTransferSendChunkedInit
     */
    public function setDocid($docid)
    {
      $this->docid = $docid;
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
     * @return fileTransferSendChunkedInit
     */
    public function setFilename($filename)
    {
      $this->filename = $filename;
      return $this;
    }

}
