<?php

class ArrayOfAnyType implements \ArrayAccess, \Iterator, \Countable
{

    /**
     * @var anyType[] $anyType
     */
    protected $anyType = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return anyType[]
     */
    public function getAnyType()
    {
      return $this->anyType;
    }

    /**
     * @param anyType[] $anyType
     * @return ArrayOfAnyType
     */
    public function setAnyType(array $anyType = null)
    {
      $this->anyType = $anyType;
      return $this;
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset An offset to check for
     * @return boolean true on success or false on failure
     */
    public function offsetExists($offset)
    {
      return isset($this->anyType[$offset]);
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to retrieve
     * @return anyType
     */
    public function offsetGet($offset)
    {
      return $this->anyType[$offset];
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to assign the value to
     * @param anyType $value The value to set
     * @return void
     */
    public function offsetSet($offset, $value)
    {
      if (!isset($offset)) {
        $this->anyType[] = $value;
      } else {
        $this->anyType[$offset] = $value;
      }
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to unset
     * @return void
     */
    public function offsetUnset($offset)
    {
      unset($this->anyType[$offset]);
    }

    /**
     * Iterator implementation
     *
     * @return anyType Return the current element
     */
    public function current()
    {
      return current($this->anyType);
    }

    /**
     * Iterator implementation
     * Move forward to next element
     *
     * @return void
     */
    public function next()
    {
      next($this->anyType);
    }

    /**
     * Iterator implementation
     *
     * @return string|null Return the key of the current element or null
     */
    public function key()
    {
      return key($this->anyType);
    }

    /**
     * Iterator implementation
     *
     * @return boolean Return the validity of the current position
     */
    public function valid()
    {
      return $this->key() !== null;
    }

    /**
     * Iterator implementation
     * Rewind the Iterator to the first element
     *
     * @return void
     */
    public function rewind()
    {
      reset($this->anyType);
    }

    /**
     * Countable implementation
     *
     * @return anyType Return count of elements
     */
    public function count()
    {
      return count($this->anyType);
    }

}
