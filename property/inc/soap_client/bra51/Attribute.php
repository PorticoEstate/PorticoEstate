<?php

class Attribute
{

    /**
     * @var string $Name
     */
    protected $Name = null;

    /**
     * @var boolean $UsesLookupValues
     */
    protected $UsesLookupValues = null;

    /**
     * @var braArkivAttributeType $AttribType
     */
    protected $AttribType = null;

    /**
     * @var ArrayOfAnyType $Value
     */
    protected $Value = null;

    /**
     * @param boolean $UsesLookupValues
     * @param braArkivAttributeType $AttribType
     */
    public function __construct($UsesLookupValues, $AttribType)
    {
      $this->UsesLookupValues = $UsesLookupValues;
      $this->AttribType = $AttribType;
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
     * @return Attribute
     */
    public function setName($Name)
    {
      $this->Name = $Name;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getUsesLookupValues()
    {
      return $this->UsesLookupValues;
    }

    /**
     * @param boolean $UsesLookupValues
     * @return Attribute
     */
    public function setUsesLookupValues($UsesLookupValues)
    {
      $this->UsesLookupValues = $UsesLookupValues;
      return $this;
    }

    /**
     * @return braArkivAttributeType
     */
    public function getAttribType()
    {
      return $this->AttribType;
    }

    /**
     * @param braArkivAttributeType $AttribType
     * @return Attribute
     */
    public function setAttribType($AttribType)
    {
      $this->AttribType = $AttribType;
      return $this;
    }

    /**
     * @return ArrayOfAnyType
     */
    public function getValue()
    {
      return $this->Value;
    }

    /**
     * @param ArrayOfAnyType $Value
     * @return Attribute
     */
    public function setValue($Value)
    {
      $this->Value = $Value;
      return $this;
    }

}
