<?php

class Filinnhold extends Fil
{

    /**
     * @var base64Binary $base64
     */
    protected $base64 = null;

    /**
     * @param string $filnavn
     * @param string $mimeType
     * @param base64Binary $base64
     */
    public function __construct($filnavn, $mimeType, $base64)
    {
      parent::__construct($filnavn, $mimeType);
      $this->base64 = $base64;
    }

    /**
     * @return base64Binary
     */
    public function getBase64()
    {
      return $this->base64;
    }

    /**
     * @param base64Binary $base64
     * @return Filinnhold
     */
    public function setBase64($base64)
    {
      $this->base64 = $base64;
      return $this;
    }

}
