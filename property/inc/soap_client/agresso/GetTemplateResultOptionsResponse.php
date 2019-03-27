<?php

	class GetTemplateResultOptionsResponse
	{

		/**
		 * @var TemplateResultOptions $GetTemplateResultOptionsResult
		 */
		protected $GetTemplateResultOptionsResult = null;

		/**
		 * @param TemplateResultOptions $GetTemplateResultOptionsResult
		 */
		public function __construct( $GetTemplateResultOptionsResult )
		{
			$this->GetTemplateResultOptionsResult = $GetTemplateResultOptionsResult;
		}

		/**
		 * @return TemplateResultOptions
		 */
		public function getGetTemplateResultOptionsResult()
		{
			return $this->GetTemplateResultOptionsResult;
		}

		/**
		 * @param TemplateResultOptions $GetTemplateResultOptionsResult
		 * @return GetTemplateResultOptionsResponse
		 */
		public function setGetTemplateResultOptionsResult( $GetTemplateResultOptionsResult )
		{
			$this->GetTemplateResultOptionsResult = $GetTemplateResultOptionsResult;
			return $this;
		}
	}