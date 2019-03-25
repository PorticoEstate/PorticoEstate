<?php

	class TemplateMetaData
	{

		/**
		 * @var SearchCriteria $SearchCriteria
		 */
		protected $SearchCriteria = null;

		/**
		 * @var TemplateProperties $TemplateProperties
		 */
		protected $TemplateProperties = null;

		/**
		 * @var FormatInfo $FormatInfo
		 */
		protected $FormatInfo = null;

		/**
		 * @var Expression $Expression
		 */
		protected $Expression = null;

		/**
		 * @var StatisticalFormula $StatisticalFormula
		 */
		protected $StatisticalFormula = null;

		public function __construct()
		{

		}

		/**
		 * @return SearchCriteria
		 */
		public function getSearchCriteria()
		{
			return $this->SearchCriteria;
		}

		/**
		 * @param SearchCriteria $SearchCriteria
		 * @return TemplateMetaData
		 */
		public function setSearchCriteria( $SearchCriteria )
		{
			$this->SearchCriteria = $SearchCriteria;
			return $this;
		}

		/**
		 * @return TemplateProperties
		 */
		public function getTemplateProperties()
		{
			return $this->TemplateProperties;
		}

		/**
		 * @param TemplateProperties $TemplateProperties
		 * @return TemplateMetaData
		 */
		public function setTemplateProperties( $TemplateProperties )
		{
			$this->TemplateProperties = $TemplateProperties;
			return $this;
		}

		/**
		 * @return FormatInfo
		 */
		public function getFormatInfo()
		{
			return $this->FormatInfo;
		}

		/**
		 * @param FormatInfo $FormatInfo
		 * @return TemplateMetaData
		 */
		public function setFormatInfo( $FormatInfo )
		{
			$this->FormatInfo = $FormatInfo;
			return $this;
		}

		/**
		 * @return Expression
		 */
		public function getExpression()
		{
			return $this->Expression;
		}

		/**
		 * @param Expression $Expression
		 * @return TemplateMetaData
		 */
		public function setExpression( $Expression )
		{
			$this->Expression = $Expression;
			return $this;
		}

		/**
		 * @return StatisticalFormula
		 */
		public function getStatisticalFormula()
		{
			return $this->StatisticalFormula;
		}

		/**
		 * @param StatisticalFormula $StatisticalFormula
		 * @return TemplateMetaData
		 */
		public function setStatisticalFormula( $StatisticalFormula )
		{
			$this->StatisticalFormula = $StatisticalFormula;
			return $this;
		}
	}