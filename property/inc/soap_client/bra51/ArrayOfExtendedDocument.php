<?php

class ArrayOfExtendedDocument implements \ArrayAccess, \Iterator, \Countable
{

    /**
     * @var ExtendedDocument[] $ExtendedDocument
     */
    protected $ExtendedDocument = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return ExtendedDocument[]
     */
    public function getExtendedDocument()
    {
      return $this->ExtendedDocument;
    }

    /**
     * @param ExtendedDocument[] $ExtendedDocument
     * @return ArrayOfExtendedDocument
     */
    public function setExtendedDocument(array $ExtendedDocument = null)
    {
      $this->ExtendedDocument = $ExtendedDocument;
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
      return isset($this->ExtendedDocument[$offset]);
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to retrieve
     * @return ExtendedDocument
     */
    public function offsetGet($offset)
    {
      return $this->ExtendedDocument[$offset];
    }

    /**
     * ArrayAccess implementation
     *
     * @param mixed $offset The offset to assign the value to
     * @param ExtendedDocument $value The value to set
     * @return void
     */
    public function offsetSet($offset, $value)
    {
      if (!isset($offset)) {
        $this->ExtendedDocument[] = $value;
      } else {
        $this->ExtendedDocument[$offset] = $value;
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
      unset($this->ExtendedDocument[$offset]);
    }

    /**
     * Iterator implementation
     *
     * @return ExtendedDocument Return the current element
     */
    public function current()
    {
      return current($this->ExtendedDocument);
    }

    /**
     * Iterator implementation
     * Move forward to next element
     *
     * @return void
     */
    public function next()
    {
      next($this->ExtendedDocument);
    }

    /**
     * Iterator implementation
     *
     * @return string|null Return the key of the current element or null
     */
    public function key()
    {
      return key($this->ExtendedDocument);
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
      reset($this->ExtendedDocument);
    }

    /**
     * Countable implementation
     *
     * @return ExtendedDocument Return count of elements
     */
    public function count()
    {
      return count($this->ExtendedDocument);
    }

}
