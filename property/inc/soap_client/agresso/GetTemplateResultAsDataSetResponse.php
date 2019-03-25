<?php

	class GetTemplateResultAsDataSetResponse
	{

		/**
		 * @var TemplateResultAsDataSet $GetTemplateResultAsDataSetResult
		 */
		protected $GetTemplateResultAsDataSetResult = null;

		/**
		 * @param TemplateResultAsDataSet $GetTemplateResultAsDataSetResult
		 */
		public function __construct( $GetTemplateResultAsDataSetResult )
		{
			$this->GetTemplateResultAsDataSetResult = $GetTemplateResultAsDataSetResult;
		}

		/**
		 * @return TemplateResultAsDataSet
		 */
		public function getGetTemplateResultAsDataSetResult()
		{
			return $this->GetTemplateResultAsDataSetResult;
		}

		/**
		 * @param TemplateResultAsDataSet $GetTemplateResultAsDataSetResult
		 * @return GetTemplateResultAsDataSetResponse
		 */
		public function setGetTemplateResultAsDataSetResult( $GetTemplateResultAsDataSetResult )
		{
			$this->GetTemplateResultAsDataSetResult = $GetTemplateResultAsDataSetResult;
			return $this;
		}
	}