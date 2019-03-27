<?php

	class GetTemplateMetaDataResponse
	{

		/**
		 * @var TemplateMetaData $GetTemplateMetaDataResult
		 */
		protected $GetTemplateMetaDataResult = null;

		/**
		 * @param TemplateMetaData $GetTemplateMetaDataResult
		 */
		public function __construct( $GetTemplateMetaDataResult )
		{
			$this->GetTemplateMetaDataResult = $GetTemplateMetaDataResult;
		}

		/**
		 * @return TemplateMetaData
		 */
		public function getGetTemplateMetaDataResult()
		{
			return $this->GetTemplateMetaDataResult;
		}

		/**
		 * @param TemplateMetaData $GetTemplateMetaDataResult
		 * @return GetTemplateMetaDataResponse
		 */
		public function setGetTemplateMetaDataResult( $GetTemplateMetaDataResult )
		{
			$this->GetTemplateMetaDataResult = $GetTemplateMetaDataResult;
			return $this;
		}
	}