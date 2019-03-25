<?php

	class ArrayOfTemplateHeader implements \ArrayAccess, \Iterator, \Countable
	{

		/**
		 * @var TemplateHeader[] $TemplateHeader
		 */
		protected $TemplateHeader = null;

		public function __construct()
		{

		}

		/**
		 * @return TemplateHeader[]
		 */
		public function getTemplateHeader()
		{
			return $this->TemplateHeader;
		}

		/**
		 * @param TemplateHeader[] $TemplateHeader
		 * @return ArrayOfTemplateHeader
		 */
		public function setTemplateHeader( array $TemplateHeader = null )
		{
			$this->TemplateHeader = $TemplateHeader;
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
			return isset($this->TemplateHeader[$offset]);
		}

		/**
		 * ArrayAccess implementation
		 *
		 * @param mixed $offset The offset to retrieve
		 * @return TemplateHeader
		 */
		public function offsetGet( $offset )
		{
			return $this->TemplateHeader[$offset];
		}

		/**
		 * ArrayAccess implementation
		 *
		 * @param mixed $offset The offset to assign the value to
		 * @param TemplateHeader $value The value to set
		 * @return void
		 */
		public function offsetSet( $offset, $value )
		{
			if (!isset($offset))
			{
				$this->TemplateHeader[] = $value;
			}
			else
			{
				$this->TemplateHeader[$offset] = $value;
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
			unset($this->TemplateHeader[$offset]);
		}

		/**
		 * Iterator implementation
		 *
		 * @return TemplateHeader Return the current element
		 */
		public function current()
		{
			return current($this->TemplateHeader);
		}

		/**
		 * Iterator implementation
		 * Move forward to next element
		 *
		 * @return void
		 */
		public function next()
		{
			next($this->TemplateHeader);
		}

		/**
		 * Iterator implementation
		 *
		 * @return string|null Return the key of the current element or null
		 */
		public function key()
		{
			return key($this->TemplateHeader);
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
			reset($this->TemplateHeader);
		}

		/**
		 * Countable implementation
		 *
		 * @return TemplateHeader Return count of elements
		 */
		public function count()
		{
			return count($this->TemplateHeader);
		}
	}