<?php

	class GetTemplatePropertiesResponse
	{

		/**
		 * @var TemplateProperties $GetTemplatePropertiesResult
		 */
		protected $GetTemplatePropertiesResult = null;

		/**
		 * @param TemplateProperties $GetTemplatePropertiesResult
		 */
		public function __construct( $GetTemplatePropertiesResult )
		{
			$this->GetTemplatePropertiesResult = $GetTemplatePropertiesResult;
		}

		/**
		 * @return TemplateProperties
		 */
		public function getGetTemplatePropertiesResult()
		{
			return $this->GetTemplatePropertiesResult;
		}

		/**
		 * @param TemplateProperties $GetTemplatePropertiesResult
		 * @return GetTemplatePropertiesResponse
		 */
		public function setGetTemplatePropertiesResult( $GetTemplatePropertiesResult )
		{
			$this->GetTemplatePropertiesResult = $GetTemplatePropertiesResult;
			return $this;
		}
	}