<?php

class ArrayOfVariant implements \ArrayAccess, \Iterator, \Countable
{

    /**
     * @var Variant[] $Variant
     */
    protected $Variant = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Variant[]
     */
    public function getVariant()
    {
      return $this->Variant;
    }

    /**
     * @param Variant[] $Variant
     * @return ArrayOfVariant
     */
    public function setVariant(array $Variant = null)
    {
      $this->Variant = $Variant;
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
      return isset($this->Variant[$offset]);
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to retrieve
     * @return Variant
     */
    public function offsetGet($offset)
    {
      return $this->Variant[$offset];
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to assign the value to
     * @param Variant $value The value to set
     * @return void
     */
    public function offsetSet($offset, $value)
    {
      if (!isset($offset)) {
        $this->Variant[] = $value;
      } else {
        $this->Variant[$offset] = $value;
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
      unset($this->Variant[$offset]);
    }

    /**
     * Iterator implementation
     *
     * @return Variant Return the current element
     */
    public function current()
    {
      return current($this->Variant);
    }

    /**
     * Iterator implementation
     * Move forward to next element
     *
     * @return void
     */
    public function next()
    {
      next($this->Variant);
    }

    /**
     * Iterator implementation
     *
     * @return string|null Return the key of the current element or null
     */
    public function key()
    {
      return key($this->Variant);
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
      reset($this->Variant);
    }

    /**
     * Countable implementation
     *
     * @return Variant Return count of elements
     */
    public function count()
    {
      return count($this->Variant);
    }

}
