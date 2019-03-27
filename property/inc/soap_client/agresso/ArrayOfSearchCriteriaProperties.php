<?php

	class ArrayOfSearchCriteriaProperties implements \ArrayAccess, \Iterator, \Countable
	{

		/**
		 * @var SearchCriteriaProperties[] $SearchCriteriaProperties
		 */
		protected $SearchCriteriaProperties = null;

		public function __construct()
		{

		}

		/**
		 * @return SearchCriteriaProperties[]
		 */
		public function getSearchCriteriaProperties()
		{
			return $this->SearchCriteriaProperties;
		}

		/**
		 * @param SearchCriteriaProperties[] $SearchCriteriaProperties
		 * @return ArrayOfSearchCriteriaProperties
		 */
		public function setSearchCriteriaProperties( array $SearchCriteriaProperties = null )
		{
			$this->SearchCriteriaProperties = $SearchCriteriaProperties;
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
			return isset($this->SearchCriteriaProperties[$offset]);
		}

		/**
		 * ArrayAccess implementation
		 *
		 * @param mixed $offset The offset to retrieve
		 * @return SearchCriteriaProperties
		 */
		public function offsetGet( $offset )
		{
			return $this->SearchCriteriaProperties[$offset];
		}

		/**
		 * ArrayAccess implementation
		 *
		 * @param mixed $offset The offset to assign the value to
		 * @param SearchCriteriaProperties $value The value to set
		 * @return void
		 */
		public function offsetSet( $offset, $value )
		{
			if (!isset($offset))
			{
				$this->SearchCriteriaProperties[] = $value;
			}
			else
			{
				$this->SearchCriteriaProperties[$offset] = $value;
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
			unset($this->SearchCriteriaProperties[$offset]);
		}

		/**
		 * Iterator implementation
		 *
		 * @return SearchCriteriaProperties Return the current element
		 */
		public function current()
		{
			return current($this->SearchCriteriaProperties);
		}

		/**
		 * Iterator implementation
		 * Move forward to next element
		 *
		 * @return void
		 */
		public function next()
		{
			next($this->SearchCriteriaProperties);
		}

		/**
		 * Iterator implementation
		 *
		 * @return string|null Return the key of the current element or null
		 */
		public function key()
		{
			return key($this->SearchCriteriaProperties);
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
			reset($this->SearchCriteriaProperties);
		}

		/**
		 * Countable implementation
		 *
		 * @return SearchCriteriaProperties Return count of elements
		 */
		public function count()
		{
			return count($this->SearchCriteriaProperties);
		}
	}