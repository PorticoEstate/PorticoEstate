<?php

	class StatisticalFormula
	{

		/**
		 * @var int $ReturnCode
		 */
		protected $ReturnCode = null;

		/**
		 * @var string $Status
		 */
		protected $Status = null;

		/**
		 * @var ArrayOfStatisticalFormulaProperties $StatisticalFormulaPropertiesList
		 */
		protected $StatisticalFormulaPropertiesList = null;

		/**
		 * @param int $ReturnCode
		 */
		public function __construct( $ReturnCode )
		{
			$this->ReturnCode = $ReturnCode;
		}

		/**
		 * @return int
		 */
		public function getReturnCode()
		{
			return $this->ReturnCode;
		}

		/**
		 * @param int $ReturnCode
		 * @return StatisticalFormula
		 */
		public function setReturnCode( $ReturnCode )
		{
			$this->ReturnCode = $ReturnCode;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getStatus()
		{
			return $this->Status;
		}

		/**
		 * @param string $Status
		 * @return StatisticalFormula
		 */
		public function setStatus( $Status )
		{
			$this->Status = $Status;
			return $this;
		}

		/**
		 * @return ArrayOfStatisticalFormulaProperties
		 */
		public function getStatisticalFormulaPropertiesList()
		{
			return $this->StatisticalFormulaPropertiesList;
		}

		/**
		 * @param ArrayOfStatisticalFormulaProperties $StatisticalFormulaPropertiesList
		 * @return StatisticalFormula
		 */
		public function setStatisticalFormulaPropertiesList( $StatisticalFormulaPropertiesList )
		{
			$this->StatisticalFormulaPropertiesList = $StatisticalFormulaPropertiesList;
			return $this;
		}
	}