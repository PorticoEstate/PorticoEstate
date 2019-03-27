<?php

	class GetTemplateListResponse
	{

		/**
		 * @var TemplateList $GetTemplateListResult
		 */
		protected $GetTemplateListResult = null;

		/**
		 * @param TemplateList $GetTemplateListResult
		 */
		public function __construct( $GetTemplateListResult )
		{
			$this->GetTemplateListResult = $GetTemplateListResult;
		}

		/**
		 * @return TemplateList
		 */
		public function getGetTemplateListResult()
		{
			return $this->GetTemplateListResult;
		}

		/**
		 * @param TemplateList $GetTemplateListResult
		 * @return GetTemplateListResponse
		 */
		public function setGetTemplateListResult( $GetTemplateListResult )
		{
			$this->GetTemplateListResult = $GetTemplateListResult;
			return $this;
		}
	}