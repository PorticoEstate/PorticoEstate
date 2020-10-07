<?php

class Fil
{

    /**
     * @var string $filnavn
     */
    protected $filnavn = null;

    /**
     * @var string $mimeType
     */
    protected $mimeType = null;

    /**
     * @param string $filnavn
     * @param string $mimeType
     */
    public function __construct($filnavn, $mimeType)
    {
      $this->filnavn = $filnavn;
      $this->mimeType = $mimeType;
    }

    /**
     * @return string
     */
    public function getFilnavn()
    {
      return $this->filnavn;
    }

    /**
     * @param string $filnavn
     * @return Fil
     */
    public function setFilnavn($filnavn)
    {
      $this->filnavn = $filnavn;
      return $this;
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
      return $this->mimeType;
    }

    /**
     * @param string $mimeType
     * @return Fil
     */
    public function setMimeType($mimeType)
    {
      $this->mimeType = $mimeType;
      return $this;
    }

}
