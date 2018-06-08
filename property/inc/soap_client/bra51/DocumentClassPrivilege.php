<?php

class DocumentClassPrivilege
{

    /**
     * @var int $DocClassId
     */
    protected $DocClassId = null;

    /**
     * @var string $DocClassName
     */
    protected $DocClassName = null;

    /**
     * @var int $ParentId
     */
    protected $ParentId = null;

    /**
     * @var boolean $Classified
     */
    protected $Classified = null;

    /**
     * @param int $DocClassId
     * @param int $ParentId
     * @param boolean $Classified
     */
    public function __construct($DocClassId, $ParentId, $Classified)
    {
      $this->DocClassId = $DocClassId;
      $this->ParentId = $ParentId;
      $this->Classified = $Classified;
    }

    /**
     * @return int
     */
    public function getDocClassId()
    {
      return $this->DocClassId;
    }

    /**
     * @param int $DocClassId
     * @return DocumentClassPrivilege
     */
    public function setDocClassId($DocClassId)
    {
      $this->DocClassId = $DocClassId;
      return $this;
    }

    /**
     * @return string
     */
    public function getDocClassName()
    {
      return $this->DocClassName;
    }

    /**
     * @param string $DocClassName
     * @return DocumentClassPrivilege
     */
    public function setDocClassName($DocClassName)
    {
      $this->DocClassName = $DocClassName;
      return $this;
    }

    /**
     * @return int
     */
    public function getParentId()
    {
      return $this->ParentId;
    }

    /**
     * @param int $ParentId
     * @return DocumentClassPrivilege
     */
    public function setParentId($ParentId)
    {
      $this->ParentId = $ParentId;
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
     * @return DocumentClassPrivilege
     */
    public function setClassified($Classified)
    {
      $this->Classified = $Classified;
      return $this;
    }

}
