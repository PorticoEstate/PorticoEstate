<?php

class DocumentSplitType
{

    /**
     * @var int $DocSplitTypeID
     */
    protected $DocSplitTypeID = null;

    /**
     * @var int $DocClassID
     */
    protected $DocClassID = null;

    /**
     * @var boolean $IsConcatDocument
     */
    protected $IsConcatDocument = null;

    /**
     * @var string $Name
     */
    protected $Name = null;

    /**
     * @var int $SplitAttributeID
     */
    protected $SplitAttributeID = null;

    /**
     * @var boolean $Active
     */
    protected $Active = null;

    /**
     * @var int $NewDocSplitTypeID
     */
    protected $NewDocSplitTypeID = null;

    /**
     * @var boolean $Default
     */
    protected $Default = null;

    /**
     * @param int $DocSplitTypeID
     * @param int $DocClassID
     * @param boolean $IsConcatDocument
     * @param int $SplitAttributeID
     * @param boolean $Active
     * @param boolean $Default
     */
    public function __construct($DocSplitTypeID, $DocClassID, $IsConcatDocument, $SplitAttributeID, $Active, $Default)
    {
      $this->DocSplitTypeID = $DocSplitTypeID;
      $this->DocClassID = $DocClassID;
      $this->IsConcatDocument = $IsConcatDocument;
      $this->SplitAttributeID = $SplitAttributeID;
      $this->Active = $Active;
      $this->Default = $Default;
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
     * @return DocumentSplitType
     */
    public function setDocSplitTypeID($DocSplitTypeID)
    {
      $this->DocSplitTypeID = $DocSplitTypeID;
      return $this;
    }

    /**
     * @return int
     */
    public function getDocClassID()
    {
      return $this->DocClassID;
    }

    /**
     * @param int $DocClassID
     * @return DocumentSplitType
     */
    public function setDocClassID($DocClassID)
    {
      $this->DocClassID = $DocClassID;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIsConcatDocument()
    {
      return $this->IsConcatDocument;
    }

    /**
     * @param boolean $IsConcatDocument
     * @return DocumentSplitType
     */
    public function setIsConcatDocument($IsConcatDocument)
    {
      $this->IsConcatDocument = $IsConcatDocument;
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
     * @return DocumentSplitType
     */
    public function setName($Name)
    {
      $this->Name = $Name;
      return $this;
    }

    /**
     * @return int
     */
    public function getSplitAttributeID()
    {
      return $this->SplitAttributeID;
    }

    /**
     * @param int $SplitAttributeID
     * @return DocumentSplitType
     */
    public function setSplitAttributeID($SplitAttributeID)
    {
      $this->SplitAttributeID = $SplitAttributeID;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getActive()
    {
      return $this->Active;
    }

    /**
     * @param boolean $Active
     * @return DocumentSplitType
     */
    public function setActive($Active)
    {
      $this->Active = $Active;
      return $this;
    }

    /**
     * @return int
     */
    public function getNewDocSplitTypeID()
    {
      return $this->NewDocSplitTypeID;
    }

    /**
     * @param int $NewDocSplitTypeID
     * @return DocumentSplitType
     */
    public function setNewDocSplitTypeID($NewDocSplitTypeID)
    {
      $this->NewDocSplitTypeID = $NewDocSplitTypeID;
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
     * @return DocumentSplitType
     */
    public function setDefault($Default)
    {
      $this->Default = $Default;
      return $this;
    }

}
