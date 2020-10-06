<?php

class PostadministrativeOmraader
{

    /**
     * @var string $postnummer
     */
    protected $postnummer = null;

    /**
     * @var string $poststed
     */
    protected $poststed = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return string
     */
    public function getPostnummer()
    {
      return $this->postnummer;
    }

    /**
     * @param string $postnummer
     * @return PostadministrativeOmraader
     */
    public function setPostnummer($postnummer)
    {
      $this->postnummer = $postnummer;
      return $this;
    }

    /**
     * @return string
     */
    public function getPoststed()
    {
      return $this->poststed;
    }

    /**
     * @param string $poststed
     * @return PostadministrativeOmraader
     */
    public function setPoststed($poststed)
    {
      $this->poststed = $poststed;
      return $this;
    }

}
