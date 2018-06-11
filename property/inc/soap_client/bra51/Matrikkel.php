<?php

class Matrikkel
{

    /**
     * @var string $GNr
     */
    protected $GNr = null;

    /**
     * @var string $BNr
     */
    protected $BNr = null;

    /**
     * @var string $FNr
     */
    protected $FNr = null;

    /**
     * @var string $SNr
     */
    protected $SNr = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return string
     */
    public function getGNr()
    {
      return $this->GNr;
    }

    /**
     * @param string $GNr
     * @return Matrikkel
     */
    public function setGNr($GNr)
    {
      $this->GNr = $GNr;
      return $this;
    }

    /**
     * @return string
     */
    public function getBNr()
    {
      return $this->BNr;
    }

    /**
     * @param string $BNr
     * @return Matrikkel
     */
    public function setBNr($BNr)
    {
      $this->BNr = $BNr;
      return $this;
    }

    /**
     * @return string
     */
    public function getFNr()
    {
      return $this->FNr;
    }

    /**
     * @param string $FNr
     * @return Matrikkel
     */
    public function setFNr($FNr)
    {
      $this->FNr = $FNr;
      return $this;
    }

    /**
     * @return string
     */
    public function getSNr()
    {
      return $this->SNr;
    }

    /**
     * @param string $SNr
     * @return Matrikkel
     */
    public function setSNr($SNr)
    {
      $this->SNr = $SNr;
      return $this;
    }

}
