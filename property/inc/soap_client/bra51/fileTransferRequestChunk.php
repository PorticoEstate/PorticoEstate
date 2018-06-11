<?php

class fileTransferRequestChunk
{

    /**
     * @var string $secKey
     */
    protected $secKey = null;

    /**
     * @var string $fileid
     */
    protected $fileid = null;

    /**
     * @var string $offset
     */
    protected $offset = null;

    /**
     * @param string $secKey
     * @param string $fileid
     * @param string $offset
     */
    public function __construct($secKey, $fileid, $offset)
    {
      $this->secKey = $secKey;
      $this->fileid = $fileid;
      $this->offset = $offset;
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
     * @return fileTransferRequestChunk
     */
    public function setSecKey($secKey)
    {
      $this->secKey = $secKey;
      return $this;
    }

    /**
     * @return string
     */
    public function getFileid()
    {
      return $this->fileid;
    }

    /**
     * @param string $fileid
     * @return fileTransferRequestChunk
     */
    public function setFileid($fileid)
    {
      $this->fileid = $fileid;
      return $this;
    }

    /**
     * @return string
     */
    public function getOffset()
    {
      return $this->offset;
    }

    /**
     * @param string $offset
     * @return fileTransferRequestChunk
     */
    public function setOffset($offset)
    {
      $this->offset = $offset;
      return $this;
    }

}
