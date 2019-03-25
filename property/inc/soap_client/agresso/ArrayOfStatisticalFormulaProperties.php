<?php

	class ArrayOfStatisticalFormulaProperties implements \ArrayAccess, \Iterator, \Countable
	{

		/**
		 * @var StatisticalFormulaProperties[] $StatisticalFormulaProperties
		 */
		protected $StatisticalFormulaProperties = null;

		public function __construct()
		{

		}

		/**
		 * @return StatisticalFormulaProperties[]
		 */
		public function getStatisticalFormulaProperties()
		{
			return $this->StatisticalFormulaProperties;
		}

		/**
		 * @param StatisticalFormulaProperties[] $StatisticalFormulaProperties
		 * @return ArrayOfStatisticalFormulaProperties
		 */
		public function setStatisticalFormulaProperties( array $StatisticalFormulaProperties = null )
		{
			$this->StatisticalFormulaProperties = $StatisticalFormulaProperties;
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
			return isset($this->StatisticalFormulaProperties[$offset]);
		}

		/**
		 * ArrayAccess implementation
		 *
		 * @param mixed $offset The offset to retrieve
		 * @return StatisticalFormulaProperties
		 */
		public function offsetGet( $offset )
		{
			return $this->StatisticalFormulaProperties[$offset];
		}

		/**
		 * ArrayAccess implementation
		 *
		 * @param mixed $offset The offset to assign the value to
		 * @param StatisticalFormulaProperties $value The value to set
		 * @return void
		 */
		public function offsetSet( $offset, $value )
		{
			if (!isset($offset))
			{
				$this->StatisticalFormulaProperties[] = $value;
			}
			else
			{
				$this->StatisticalFormulaProperties[$offset] = $value;
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
			unset($this->StatisticalFormulaProperties[$offset]);
		}

		/**
		 * Iterator implementation
		 *
		 * @return StatisticalFormulaProperties Return the current element
		 */
		public function current()
		{
			return current($this->StatisticalFormulaProperties);
		}

		/**
		 * Iterator implementation
		 * Move forward to next element
		 *
		 * @return void
		 */
		public function next()
		{
			next($this->StatisticalFormulaProperties);
		}

		/**
		 * Iterator implementation
		 *
		 * @return string|null Return the key of the current element or null
		 */
		public function key()
		{
			return key($this->StatisticalFormulaProperties);
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
			reset($this->StatisticalFormulaProperties);
		}

		/**
		 * Countable implementation
		 *
		 * @return StatisticalFormulaProperties Return count of elements
		 */
		public function count()
		{
			return count($this->StatisticalFormulaProperties);
		}
	}