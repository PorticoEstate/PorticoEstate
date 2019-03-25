<?php

	class ExpressionProperties
	{

		/**
		 * @var string $Formula
		 */
		protected $Formula = null;

		/**
		 * @var string $ColName
		 */
		protected $ColName = null;

		/**
		 * @var boolean $IsRunningTotal
		 */
		protected $IsRunningTotal = null;

		/**
		 * @var boolean $HasSum
		 */
		protected $HasSum = null;

		/**
		 * @var boolean $HasVerticalSum
		 */
		protected $HasVerticalSum = null;

		/**
		 * @param boolean $IsRunningTotal
		 * @param boolean $HasSum
		 * @param boolean $HasVerticalSum
		 */
		public function __construct( $IsRunningTotal, $HasSum, $HasVerticalSum )
		{
			$this->IsRunningTotal	 = $IsRunningTotal;
			$this->HasSum			 = $HasSum;
			$this->HasVerticalSum	 = $HasVerticalSum;
		}

		/**
		 * @return string
		 */
		public function getFormula()
		{
			return $this->Formula;
		}

		/**
		 * @param string $Formula
		 * @return ExpressionProperties
		 */
		public function setFormula( $Formula )
		{
			$this->Formula = $Formula;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getColName()
		{
			return $this->ColName;
		}

		/**
		 * @param string $ColName
		 * @return ExpressionProperties
		 */
		public function setColName( $ColName )
		{
			$this->ColName = $ColName;
			return $this;
		}

		/**
		 * @return boolean
		 */
		public function getIsRunningTotal()
		{
			return $this->IsRunningTotal;
		}

		/**
		 * @param boolean $IsRunningTotal
		 * @return ExpressionProperties
		 */
		public function setIsRunningTotal( $IsRunningTotal )
		{
			$this->IsRunningTotal = $IsRunningTotal;
			return $this;
		}

		/**
		 * @return boolean
		 */
		public function getHasSum()
		{
			return $this->HasSum;
		}

		/**
		 * @param boolean $HasSum
		 * @return ExpressionProperties
		 */
		public function setHasSum( $HasSum )
		{
			$this->HasSum = $HasSum;
			return $this;
		}

		/**
		 * @return boolean
		 */
		public function getHasVerticalSum()
		{
			return $this->HasVerticalSum;
		}

		/**
		 * @param boolean $HasVerticalSum
		 * @return ExpressionProperties
		 */
		public function setHasVerticalSum( $HasVerticalSum )
		{
			$this->HasVerticalSum = $HasVerticalSum;
			return $this;
		}
	}