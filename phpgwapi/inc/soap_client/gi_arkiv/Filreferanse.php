<?php

class Filreferanse extends Fil
{

    /**
     * @var anyURI $uri
     */
    protected $uri = null;

    /**
     * @var anyURI $kvitteringUri
     */
    protected $kvitteringUri = null;

    /**
     * @param string $filnavn
     * @param string $mimeType
     * @param anyURI $uri
     */
    public function __construct($filnavn, $mimeType, $uri)
    {
      parent::__construct($filnavn, $mimeType);
      $this->uri = $uri;
    }

    /**
     * @return anyURI
     */
    public function getUri()
    {
      return $this->uri;
    }

    /**
     * @param anyURI $uri
     * @return Filreferanse
     */
    public function setUri($uri)
    {
      $this->uri = $uri;
      return $this;
    }

    /**
     * @return anyURI
     */
    public function getKvitteringUri()
    {
      return $this->kvitteringUri;
    }

    /**
     * @param anyURI $kvitteringUri
     * @return Filreferanse
     */
    public function setKvitteringUri($kvitteringUri)
    {
      $this->kvitteringUri = $kvitteringUri;
      return $this;
    }

}
