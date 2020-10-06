<?php

class SystemID
{

    /**
     * @var string $id
     */
    protected $id = null;

    /**
     * @param string $id
     */
    public function __construct($id)
    {
      $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId()
    {
      return $this->id;
    }

    /**
     * @param string $id
     * @return SystemID
     */
    public function setId($id)
    {
      $this->id = $id;
      return $this;
    }

}
