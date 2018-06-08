<?php

class fileTransferSendChunk
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
     * @var string $chunk
     */
    protected $chunk = null;

    /**
     * @param string $secKey
     * @param string $fileid
     * @param string $chunk
     */
    public function __construct($secKey, $fileid, $chunk)
    {
      $this->secKey = $secKey;
      $this->fileid = $fileid;
      $this->chunk = $chunk;
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
     * @return fileTransferSendChunk
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
     * @return fileTransferSendChunk
     */
    public function setFileid($fileid)
    {
      $this->fileid = $fileid;
      return $this;
    }

    /**
     * @return string
     */
    public function getChunk()
    {
      return $this->chunk;
    }

    /**
     * @param string $chunk
     * @return fileTransferSendChunk
     */
    public function setChunk($chunk)
    {
      $this->chunk = $chunk;
      return $this;
    }

}
