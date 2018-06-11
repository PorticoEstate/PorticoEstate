<?php

class getDocumentSplitTypes
{

    /**
     * @var string $seckey
     */
    protected $seckey = null;

    /**
     * @var int $docClassID
     */
    protected $docClassID = null;

    /**
     * @param string $seckey
     * @param int $docClassID
     */
    public function __construct($seckey, $docClassID)
    {
      $this->seckey = $seckey;
      $this->docClassID = $docClassID;
    }

    /**
     * @return string
     */
    public function getSeckey()
    {
      return $this->seckey;
    }

    /**
     * @param string $seckey
     * @return getDocumentSplitTypes
     */
    public function setSeckey($seckey)
    {
      $this->seckey = $seckey;
      return $this;
    }

    /**
     * @return int
     */
    public function getDocClassID()
    {
      return $this->docClassID;
    }

    /**
     * @param int $docClassID
     * @return getDocumentSplitTypes
     */
    public function setDocClassID($docClassID)
    {
      $this->docClassID = $docClassID;
      return $this;
    }

}
