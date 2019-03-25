<?php

	class GetStatisticalFormulaResponse
	{

		/**
		 * @var StatisticalFormula $GetStatisticalFormulaResult
		 */
		protected $GetStatisticalFormulaResult = null;

		/**
		 * @param StatisticalFormula $GetStatisticalFormulaResult
		 */
		public function __construct( $GetStatisticalFormulaResult )
		{
			$this->GetStatisticalFormulaResult = $GetStatisticalFormulaResult;
		}

		/**
		 * @return StatisticalFormula
		 */
		public function getGetStatisticalFormulaResult()
		{
			return $this->GetStatisticalFormulaResult;
		}

		/**
		 * @param StatisticalFormula $GetStatisticalFormulaResult
		 * @return GetStatisticalFormulaResponse
		 */
		public function setGetStatisticalFormulaResult( $GetStatisticalFormulaResult )
		{
			$this->GetStatisticalFormulaResult = $GetStatisticalFormulaResult;
			return $this;
		}
	}