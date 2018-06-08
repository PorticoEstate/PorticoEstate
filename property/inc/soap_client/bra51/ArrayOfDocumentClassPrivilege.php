<?php

class ArrayOfDocumentClassPrivilege implements \ArrayAccess, \Iterator, \Countable
{

    /**
     * @var DocumentClassPrivilege[] $DocumentClassPrivilege
     */
    protected $DocumentClassPrivilege = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return DocumentClassPrivilege[]
     */
    public function getDocumentClassPrivilege()
    {
      return $this->DocumentClassPrivilege;
    }

    /**
     * @param DocumentClassPrivilege[] $DocumentClassPrivilege
     * @return ArrayOfDocumentClassPrivilege
     */
    public function setDocumentClassPrivilege(array $DocumentClassPrivilege = null)
    {
      $this->DocumentClassPrivilege = $DocumentClassPrivilege;
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
      return isset($this->DocumentClassPrivilege[$offset]);
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to retrieve
     * @return DocumentClassPrivilege
     */
    public function offsetGet($offset)
    {
      return $this->DocumentClassPrivilege[$offset];
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to assign the value to
     * @param DocumentClassPrivilege $value The value to set
     * @return void
     */
    public function offsetSet($offset, $value)
    {
      if (!isset($offset)) {
        $this->DocumentClassPrivilege[] = $value;
      } else {
        $this->DocumentClassPrivilege[$offset] = $value;
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
      unset($this->DocumentClassPrivilege[$offset]);
    }

    /**
     * Iterator implementation
     *
     * @return DocumentClassPrivilege Return the current element
     */
    public function current()
    {
      return current($this->DocumentClassPrivilege);
    }

    /**
     * Iterator implementation
     * Move forward to next element
     *
     * @return void
     */
    public function next()
    {
      next($this->DocumentClassPrivilege);
    }

    /**
     * Iterator implementation
     *
     * @return string|null Return the key of the current element or null
     */
    public function key()
    {
      return key($this->DocumentClassPrivilege);
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
      reset($this->DocumentClassPrivilege);
    }

    /**
     * Countable implementation
     *
     * @return DocumentClassPrivilege Return count of elements
     */
    public function count()
    {
      return count($this->DocumentClassPrivilege);
    }

}
