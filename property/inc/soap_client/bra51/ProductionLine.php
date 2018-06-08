<?php

class ProductionLine
{

    /**
     * @var int $ProductionLineID
     */
    protected $ProductionLineID = null;

    /**
     * @var string $Name
     */
    protected $Name = null;

    /**
     * @var boolean $Enabled
     */
    protected $Enabled = null;

    /**
     * @var boolean $Default
     */
    protected $Default = null;

    /**
     * @param int $ProductionLineID
     * @param boolean $Enabled
     * @param boolean $Default
     */
    public function __construct($ProductionLineID, $Enabled, $Default)
    {
      $this->ProductionLineID = $ProductionLineID;
      $this->Enabled = $Enabled;
      $this->Default = $Default;
    }

    /**
     * @return int
     */
    public function getProductionLineID()
    {
      return $this->ProductionLineID;
    }

    /**
     * @param int $ProductionLineID
     * @return ProductionLine
     */
    public function setProductionLineID($ProductionLineID)
    {
      $this->ProductionLineID = $ProductionLineID;
      return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
      return $this->Name;
    }

    /**
     * @param string $Name
     * @return ProductionLine
     */
    public function setName($Name)
    {
      $this->Name = $Name;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getEnabled()
    {
      return $this->Enabled;
    }

    /**
     * @param boolean $Enabled
     * @return ProductionLine
     */
    public function setEnabled($Enabled)
    {
      $this->Enabled = $Enabled;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getDefault()
    {
      return $this->Default;
    }

    /**
     * @param boolean $Default
     * @return ProductionLine
     */
    public function setDefault($Default)
    {
      $this->Default = $Default;
      return $this;
    }

}
