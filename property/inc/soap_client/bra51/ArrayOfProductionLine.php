<?php

class ArrayOfProductionLine implements \ArrayAccess, \Iterator, \Countable
{

    /**
     * @var ProductionLine[] $ProductionLine
     */
    protected $ProductionLine = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return ProductionLine[]
     */
    public function getProductionLine()
    {
      return $this->ProductionLine;
    }

    /**
     * @param ProductionLine[] $ProductionLine
     * @return ArrayOfProductionLine
     */
    public function setProductionLine(array $ProductionLine = null)
    {
      $this->ProductionLine = $ProductionLine;
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
      return isset($this->ProductionLine[$offset]);
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to retrieve
     * @return ProductionLine
     */
    public function offsetGet($offset)
    {
      return $this->ProductionLine[$offset];
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to assign the value to
     * @param ProductionLine $value The value to set
     * @return void
     */
    public function offsetSet($offset, $value)
    {
      if (!isset($offset)) {
        $this->ProductionLine[] = $value;
      } else {
        $this->ProductionLine[$offset] = $value;
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
      unset($this->ProductionLine[$offset]);
    }

    /**
     * Iterator implementation
     *
     * @return ProductionLine Return the current element
     */
    public function current()
    {
      return current($this->ProductionLine);
    }

    /**
     * Iterator implementation
     * Move forward to next element
     *
     * @return void
     */
    public function next()
    {
      next($this->ProductionLine);
    }

    /**
     * Iterator implementation
     *
     * @return string|null Return the key of the current element or null
     */
    public function key()
    {
      return key($this->ProductionLine);
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
      reset($this->ProductionLine);
    }

    /**
     * Countable implementation
     *
     * @return ProductionLine Return count of elements
     */
    public function count()
    {
      return count($this->ProductionLine);
    }

}
