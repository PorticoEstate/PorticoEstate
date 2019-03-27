<?php

	class TemplateResultOptions
	{

		/**
		 * @var boolean $ShowDescriptions
		 */
		protected $ShowDescriptions = null;

		/**
		 * @var boolean $Aggregated
		 */
		protected $Aggregated = null;

		/**
		 * @var boolean $OverrideAggregation
		 */
		protected $OverrideAggregation = null;

		/**
		 * @var boolean $CalculateFormulas
		 */
		protected $CalculateFormulas = null;

		/**
		 * @var boolean $FormatAlternativeBreakColumns
		 */
		protected $FormatAlternativeBreakColumns = null;

		/**
		 * @var boolean $RemoveHiddenColumns
		 */
		protected $RemoveHiddenColumns = null;

		/**
		 * @var string $Filter
		 */
		protected $Filter = null;

		/**
		 * @var int $FirstRecord
		 */
		protected $FirstRecord = null;

		/**
		 * @var int $LastRecord
		 */
		protected $LastRecord = null;

		/**
		 * @param boolean $ShowDescriptions
		 * @param boolean $Aggregated
		 * @param boolean $OverrideAggregation
		 * @param boolean $CalculateFormulas
		 * @param boolean $FormatAlternativeBreakColumns
		 * @param boolean $RemoveHiddenColumns
		 * @param int $FirstRecord
		 * @param int $LastRecord
		 */
		public function __construct( $ShowDescriptions, $Aggregated, $OverrideAggregation, $CalculateFormulas, $FormatAlternativeBreakColumns, $RemoveHiddenColumns, $FirstRecord, $LastRecord )
		{
			$this->ShowDescriptions				 = $ShowDescriptions;
			$this->Aggregated					 = $Aggregated;
			$this->OverrideAggregation			 = $OverrideAggregation;
			$this->CalculateFormulas			 = $CalculateFormulas;
			$this->FormatAlternativeBreakColumns = $FormatAlternativeBreakColumns;
			$this->RemoveHiddenColumns			 = $RemoveHiddenColumns;
			$this->FirstRecord					 = $FirstRecord;
			$this->LastRecord					 = $LastRecord;
		}

		/**
		 * @return boolean
		 */
		public function getShowDescriptions()
		{
			return $this->ShowDescriptions;
		}

		/**
		 * @param boolean $ShowDescriptions
		 * @return TemplateResultOptions
		 */
		public function setShowDescriptions( $ShowDescriptions )
		{
			$this->ShowDescriptions = $ShowDescriptions;
			return $this;
		}

		/**
		 * @return boolean
		 */
		public function getAggregated()
		{
			return $this->Aggregated;
		}

		/**
		 * @param boolean $Aggregated
		 * @return TemplateResultOptions
		 */
		public function setAggregated( $Aggregated )
		{
			$this->Aggregated = $Aggregated;
			return $this;
		}

		/**
		 * @return boolean
		 */
		public function getOverrideAggregation()
		{
			return $this->OverrideAggregation;
		}

		/**
		 * @param boolean $OverrideAggregation
		 * @return TemplateResultOptions
		 */
		public function setOverrideAggregation( $OverrideAggregation )
		{
			$this->OverrideAggregation = $OverrideAggregation;
			return $this;
		}

		/**
		 * @return boolean
		 */
		public function getCalculateFormulas()
		{
			return $this->CalculateFormulas;
		}

		/**
		 * @param boolean $CalculateFormulas
		 * @return TemplateResultOptions
		 */
		public function setCalculateFormulas( $CalculateFormulas )
		{
			$this->CalculateFormulas = $CalculateFormulas;
			return $this;
		}

		/**
		 * @return boolean
		 */
		public function getFormatAlternativeBreakColumns()
		{
			return $this->FormatAlternativeBreakColumns;
		}

		/**
		 * @param boolean $FormatAlternativeBreakColumns
		 * @return TemplateResultOptions
		 */
		public function setFormatAlternativeBreakColumns( $FormatAlternativeBreakColumns )
		{
			$this->FormatAlternativeBreakColumns = $FormatAlternativeBreakColumns;
			return $this;
		}

		/**
		 * @return boolean
		 */
		public function getRemoveHiddenColumns()
		{
			return $this->RemoveHiddenColumns;
		}

		/**
		 * @param boolean $RemoveHiddenColumns
		 * @return TemplateResultOptions
		 */
		public function setRemoveHiddenColumns( $RemoveHiddenColumns )
		{
			$this->RemoveHiddenColumns = $RemoveHiddenColumns;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getFilter()
		{
			return $this->Filter;
		}

		/**
		 * @param string $Filter
		 * @return TemplateResultOptions
		 */
		public function setFilter( $Filter )
		{
			$this->Filter = $Filter;
			return $this;
		}

		/**
		 * @return int
		 */
		public function getFirstRecord()
		{
			return $this->FirstRecord;
		}

		/**
		 * @param int $FirstRecord
		 * @return TemplateResultOptions
		 */
		public function setFirstRecord( $FirstRecord )
		{
			$this->FirstRecord = $FirstRecord;
			return $this;
		}

		/**
		 * @return int
		 */
		public function getLastRecord()
		{
			return $this->LastRecord;
		}

		/**
		 * @param int $LastRecord
		 * @return TemplateResultOptions
		 */
		public function setLastRecord( $LastRecord )
		{
			$this->LastRecord = $LastRecord;
			return $this;
		}
	}