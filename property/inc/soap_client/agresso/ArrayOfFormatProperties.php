<?php

	class ArrayOfFormatProperties implements \ArrayAccess, \Iterator, \Countable
	{

		/**
		 * @var FormatProperties[] $FormatProperties
		 */
		protected $FormatProperties = null;

		public function __construct()
		{

		}

		/**
		 * @return FormatProperties[]
		 */
		public function getFormatProperties()
		{
			return $this->FormatProperties;
		}

		/**
		 * @param FormatProperties[] $FormatProperties
		 * @return ArrayOfFormatProperties
		 */
		public function setFormatProperties( array $FormatProperties = null )
		{
			$this->FormatProperties = $FormatProperties;
			return $this;
		}

		/**
		 * ArrayAccess implementation
		 *
		 * @param mixed $offset An offset to check for
		 * @return boolean true on success or false on failure
		 */
		public function offsetExists( $offset )
		{
			return isset($this->FormatProperties[$offset]);
		}

		/**
		 * ArrayAccess implementation
		 *
		 * @param mixed $offset The offset to retrieve
		 * @return FormatProperties
		 */
		public function offsetGet( $offset )
		{
			return $this->FormatProperties[$offset];
		}

		/**
		 * ArrayAccess implementation
		 *
		 * @param mixed $offset The offset to assign the value to
		 * @param FormatProperties $value The value to set
		 * @return void
		 */
		public function offsetSet( $offset, $value )
		{
			if (!isset($offset))
			{
				$this->FormatProperties[] = $value;
			}
			else
			{
				$this->FormatProperties[$offset] = $value;
			}
		}

		/**
		 * ArrayAccess implementation
		 *
		 * @param mixed $offset The offset to unset
		 * @return void
		 */
		public function offsetUnset( $offset )
		{
			unset($this->FormatProperties[$offset]);
		}

		/**
		 * Iterator implementation
		 *
		 * @return FormatProperties Return the current element
		 */
		public function current()
		{
			return current($this->FormatProperties);
		}

		/**
		 * Iterator implementation
		 * Move forward to next element
		 *
		 * @return void
		 */
		public function next()
		{
			next($this->FormatProperties);
		}

		/**
		 * Iterator implementation
		 *
		 * @return string|null Return the key of the current element or null
		 */
		public function key()
		{
			return key($this->FormatProperties);
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
			reset($this->FormatProperties);
		}

		/**
		 * Countable implementation
		 *
		 * @return FormatProperties Return count of elements
		 */
		public function count()
		{
			return count($this->FormatProperties);
		}
	}