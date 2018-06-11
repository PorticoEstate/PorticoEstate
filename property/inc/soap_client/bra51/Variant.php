<?php

class Variant
{

    /**
     * @var string $FileName
     */
    protected $FileName = null;

    /**
     * @var string $FileExtension
     */
    protected $FileExtension = null;

    /**
     * @var string $VaultName
     */
    protected $VaultName = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return string
     */
    public function getFileName()
    {
      return $this->FileName;
    }

    /**
     * @param string $FileName
     * @return Variant
     */
    public function setFileName($FileName)
    {
      $this->FileName = $FileName;
      return $this;
    }

    /**
     * @return string
     */
    public function getFileExtension()
    {
      return $this->FileExtension;
    }

    /**
     * @param string $FileExtension
     * @return Variant
     */
    public function setFileExtension($FileExtension)
    {
      $this->FileExtension = $FileExtension;
      return $this;
    }

    /**
     * @return string
     */
    public function getVaultName()
    {
      return $this->VaultName;
    }

    /**
     * @param string $VaultName
     * @return Variant
     */
    public function setVaultName($VaultName)
    {
      $this->VaultName = $VaultName;
      return $this;
    }

}
