<?php

class Document
{

    /**
     * @var ArrayOfAttribute $Attributes
     */
    protected $Attributes = null;

    /**
     * @var string $ID
     */
    protected $ID = null;

    /**
     * @var string $BFDocKey
     */
    protected $BFDocKey = null;

    /**
     * @var string $BFNoSheets
     */
    protected $BFNoSheets = null;

    /**
     * @var boolean $BFDoubleSided
     */
    protected $BFDoubleSided = null;

    /**
     * @var boolean $BFSeparateKeySheet
     */
    protected $BFSeparateKeySheet = null;

    /**
     * @var string $BBRegTime
     */
    protected $BBRegTime = null;

    /**
     * @var string $Name
     */
    protected $Name = null;

    /**
     * @var boolean $Classified
     */
    protected $Classified = null;

    /**
     * @var int $Priority
     */
    protected $Priority = null;

    /**
     * @var int $ProductionLineID
     */
    protected $ProductionLineID = null;

    /**
     * @var int $DocSplitTypeID
     */
    protected $DocSplitTypeID = null;

    /**
     * @var string $ClassName
     */
    protected $ClassName = null;

    /**
     * @var string $BaseClassName
     */
    protected $BaseClassName = null;

    /**
     * @param boolean $BFDoubleSided
     * @param boolean $BFSeparateKeySheet
     * @param boolean $Classified
     * @param int $Priority
     */
    public function __construct($BFDoubleSided, $BFSeparateKeySheet, $Classified, $Priority)
    {
      $this->BFDoubleSided = $BFDoubleSided;
      $this->BFSeparateKeySheet = $BFSeparateKeySheet;
      $this->Classified = $Classified;
      $this->Priority = $Priority;
    }

    /**
     * @return ArrayOfAttribute
     */
    public function getAttributes()
    {
      return $this->Attributes;
    }

    /**
     * @param ArrayOfAttribute $Attributes
     * @return Document
     */
    public function setAttributes($Attributes)
    {
      $this->Attributes = $Attributes;
      return $this;
    }

    /**
     * @return string
     */
    public function getID()
    {
      return $this->ID;
    }

    /**
     * @param string $ID
     * @return Document
     */
    public function setID($ID)
    {
      $this->ID = $ID;
      return $this;
    }

    /**
     * @return string
     */
    public function getBFDocKey()
    {
      return $this->BFDocKey;
    }

    /**
     * @param string $BFDocKey
     * @return Document
     */
    public function setBFDocKey($BFDocKey)
    {
      $this->BFDocKey = $BFDocKey;
      return $this;
    }

    /**
     * @return string
     */
    public function getBFNoSheets()
    {
      return $this->BFNoSheets;
    }

    /**
     * @param string $BFNoSheets
     * @return Document
     */
    public function setBFNoSheets($BFNoSheets)
    {
      $this->BFNoSheets = $BFNoSheets;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getBFDoubleSided()
    {
      return $this->BFDoubleSided;
    }

    /**
     * @param boolean $BFDoubleSided
     * @return Document
     */
    public function setBFDoubleSided($BFDoubleSided)
    {
      $this->BFDoubleSided = $BFDoubleSided;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getBFSeparateKeySheet()
    {
      return $this->BFSeparateKeySheet;
    }

    /**
     * @param boolean $BFSeparateKeySheet
     * @return Document
     */
    public function setBFSeparateKeySheet($BFSeparateKeySheet)
    {
      $this->BFSeparateKeySheet = $BFSeparateKeySheet;
      return $this;
    }

    /**
     * @return string
     */
    public function getBBRegTime()
    {
      return $this->BBRegTime;
    }

    /**
     * @param string $BBRegTime
     * @return Document
     */
    public function setBBRegTime($BBRegTime)
    {
      $this->BBRegTime = $BBRegTime;
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
     * @return Document
     */
    public function setName($Name)
    {
      $this->Name = $Name;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getClassified()
    {
      return $this->Classified;
    }

    /**
     * @param boolean $Classified
     * @return Document
     */
    public function setClassified($Classified)
    {
      $this->Classified = $Classified;
      return $this;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
      return $this->Priority;
    }

    /**
     * @param int $Priority
     * @return Document
     */
    public function setPriority($Priority)
    {
      $this->Priority = $Priority;
      return $this;
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
     * @return Document
     */
    public function setProductionLineID($ProductionLineID)
    {
      $this->ProductionLineID = $ProductionLineID;
      return $this;
    }

    /**
     * @return int
     */
    public function getDocSplitTypeID()
    {
      return $this->DocSplitTypeID;
    }

    /**
     * @param int $DocSplitTypeID
     * @return Document
     */
    public function setDocSplitTypeID($DocSplitTypeID)
    {
      $this->DocSplitTypeID = $DocSplitTypeID;
      return $this;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
      return $this->ClassName;
    }

    /**
     * @param string $ClassName
     * @return Document
     */
    public function setClassName($ClassName)
    {
      $this->ClassName = $ClassName;
      return $this;
    }

    /**
     * @return string
     */
    public function getBaseClassName()
    {
      return $this->BaseClassName;
    }

    /**
     * @param string $BaseClassName
     * @return Document
     */
    public function setBaseClassName($BaseClassName)
    {
      $this->BaseClassName = $BaseClassName;
      return $this;
    }

}
