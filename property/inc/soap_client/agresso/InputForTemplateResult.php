<?php

	class InputForTemplateResult
	{

		/**
		 * @var int $TemplateId
		 */
		protected $TemplateId = null;

		/**
		 * @var TemplateResultOptions $TemplateResultOptions
		 */
		protected $TemplateResultOptions = null;

		/**
		 * @var ArrayOfSearchCriteriaProperties $SearchCriteriaPropertiesList
		 */
		protected $SearchCriteriaPropertiesList = null;

		/**
		 * @var string $PipelineAssociatedName
		 */
		protected $PipelineAssociatedName = null;

		/**
		 * @param int $TemplateId
		 */
		public function __construct( $TemplateId )
		{
			$this->TemplateId = $TemplateId;
		}

		/**
		 * @return int
		 */
		public function getTemplateId()
		{
			return $this->TemplateId;
		}

		/**
		 * @param int $TemplateId
		 * @return InputForTemplateResult
		 */
		public function setTemplateId( $TemplateId )
		{
			$this->TemplateId = $TemplateId;
			return $this;
		}

		/**
		 * @return TemplateResultOptions
		 */
		public function getTemplateResultOptions()
		{
			return $this->TemplateResultOptions;
		}

		/**
		 * @param TemplateResultOptions $TemplateResultOptions
		 * @return InputForTemplateResult
		 */
		public function setTemplateResultOptions( $TemplateResultOptions )
		{
			$this->TemplateResultOptions = $TemplateResultOptions;
			return $this;
		}

		/**
		 * @return ArrayOfSearchCriteriaProperties
		 */
		public function getSearchCriteriaPropertiesList()
		{
			return $this->SearchCriteriaPropertiesList;
		}

		/**
		 * @param ArrayOfSearchCriteriaProperties $SearchCriteriaPropertiesList
		 * @return InputForTemplateResult
		 */
		public function setSearchCriteriaPropertiesList( $SearchCriteriaPropertiesList )
		{
			$this->SearchCriteriaPropertiesList = $SearchCriteriaPropertiesList;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getPipelineAssociatedName()
		{
			return $this->PipelineAssociatedName;
		}

		/**
		 * @param string $PipelineAssociatedName
		 * @return InputForTemplateResult
		 */
		public function setPipelineAssociatedName( $PipelineAssociatedName )
		{
			$this->PipelineAssociatedName = $PipelineAssociatedName;
			return $this;
		}
	}