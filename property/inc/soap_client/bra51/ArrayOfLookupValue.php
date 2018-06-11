<?php

class ArrayOfLookupValue implements \ArrayAccess, \Iterator, \Countable
{

    /**
     * @var LookupValue[] $LookupValue
     */
    protected $LookupValue = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return LookupValue[]
     */
    public function getLookupValue()
    {
      return $this->LookupValue;
    }

    /**
     * @param LookupValue[] $LookupValue
     * @return ArrayOfLookupValue
     */
    public function setLookupValue(array $LookupValue = null)
    {
      $this->LookupValue = $LookupValue;
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
      return isset($this->LookupValue[$offset]);
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to retrieve
     * @return LookupValue
     */
    public function offsetGet($offset)
    {
      return $this->LookupValue[$offset];
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to assign the value to
     * @param LookupValue $value The value to set
     * @return void
     */
    public function offsetSet($offset, $value)
    {
      if (!isset($offset)) {
        $this->LookupValue[] = $value;
      } else {
        $this->LookupValue[$offset] = $value;
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
      unset($this->LookupValue[$offset]);
    }

    /**
     * Iterator implementation
     *
     * @return LookupValue Return the current element
     */
    public function current()
    {
      return current($this->LookupValue);
    }

    /**
     * Iterator implementation
     * Move forward to next element
     *
     * @return void
     */
    public function next()
    {
      next($this->LookupValue);
    }

    /**
     * Iterator implementation
     *
     * @return string|null Return the key of the current element or null
     */
    public function key()
    {
      return key($this->LookupValue);
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
      reset($this->LookupValue);
    }

    /**
     * Countable implementation
     *
     * @return LookupValue Return count of elements
     */
    public function count()
    {
      return count($this->LookupValue);
    }

}
