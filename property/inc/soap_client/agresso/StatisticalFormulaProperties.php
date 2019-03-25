<?php

	class StatisticalFormulaProperties
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
		 * @var boolean $ExcludeZero
		 */
		protected $ExcludeZero = null;

		/**
		 * @var string $DisplayMethod
		 */
		protected $DisplayMethod = null;

		/**
		 * @var string $DisplayColumn
		 */
		protected $DisplayColumn = null;

		/**
		 * @param boolean $ExcludeZero
		 */
		public function __construct( $ExcludeZero )
		{
			$this->ExcludeZero = $ExcludeZero;
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
		 * @return StatisticalFormulaProperties
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
		 * @return StatisticalFormulaProperties
		 */
		public function setColName( $ColName )
		{
			$this->ColName = $ColName;
			return $this;
		}

		/**
		 * @return boolean
		 */
		public function getExcludeZero()
		{
			return $this->ExcludeZero;
		}

		/**
		 * @param boolean $ExcludeZero
		 * @return StatisticalFormulaProperties
		 */
		public function setExcludeZero( $ExcludeZero )
		{
			$this->ExcludeZero = $ExcludeZero;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getDisplayMethod()
		{
			return $this->DisplayMethod;
		}

		/**
		 * @param string $DisplayMethod
		 * @return StatisticalFormulaProperties
		 */
		public function setDisplayMethod( $DisplayMethod )
		{
			$this->DisplayMethod = $DisplayMethod;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getDisplayColumn()
		{
			return $this->DisplayColumn;
		}

		/**
		 * @param string $DisplayColumn
		 * @return StatisticalFormulaProperties
		 */
		public function setDisplayColumn( $DisplayColumn )
		{
			$this->DisplayColumn = $DisplayColumn;
			return $this;
		}
	}