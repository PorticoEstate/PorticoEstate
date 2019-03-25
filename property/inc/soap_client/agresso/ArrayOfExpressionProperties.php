<?php

	class ArrayOfExpressionProperties implements \ArrayAccess, \Iterator, \Countable
	{

		/**
		 * @var ExpressionProperties[] $ExpressionProperties
		 */
		protected $ExpressionProperties = null;

		public function __construct()
		{

		}

		/**
		 * @return ExpressionProperties[]
		 */
		public function getExpressionProperties()
		{
			return $this->ExpressionProperties;
		}

		/**
		 * @param ExpressionProperties[] $ExpressionProperties
		 * @return ArrayOfExpressionProperties
		 */
		public function setExpressionProperties( array $ExpressionProperties = null )
		{
			$this->ExpressionProperties = $ExpressionProperties;
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
			return isset($this->ExpressionProperties[$offset]);
		}

		/**
		 * ArrayAccess implementation
		 *
		 * @param mixed $offset The offset to retrieve
		 * @return ExpressionProperties
		 */
		public function offsetGet( $offset )
		{
			return $this->ExpressionProperties[$offset];
		}

		/**
		 * ArrayAccess implementation
		 *
		 * @param mixed $offset The offset to assign the value to
		 * @param ExpressionProperties $value The value to set
		 * @return void
		 */
		public function offsetSet( $offset, $value )
		{
			if (!isset($offset))
			{
				$this->ExpressionProperties[] = $value;
			}
			else
			{
				$this->ExpressionProperties[$offset] = $value;
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
			unset($this->ExpressionProperties[$offset]);
		}

		/**
		 * Iterator implementation
		 *
		 * @return ExpressionProperties Return the current element
		 */
		public function current()
		{
			return current($this->ExpressionProperties);
		}

		/**
		 * Iterator implementation
		 * Move forward to next element
		 *
		 * @return void
		 */
		public function next()
		{
			next($this->ExpressionProperties);
		}

		/**
		 * Iterator implementation
		 *
		 * @return string|null Return the key of the current element or null
		 */
		public function key()
		{
			return key($this->ExpressionProperties);
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
			reset($this->ExpressionProperties);
		}

		/**
		 * Countable implementation
		 *
		 * @return ExpressionProperties Return count of elements
		 */
		public function count()
		{
			return count($this->ExpressionProperties);
		}
	}