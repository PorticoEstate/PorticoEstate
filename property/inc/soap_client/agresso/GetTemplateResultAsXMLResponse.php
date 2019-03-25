<?php

	class GetTemplateResultAsXMLResponse
	{

		/**
		 * @var TemplateResultAsXML $GetTemplateResultAsXMLResult
		 */
		protected $GetTemplateResultAsXMLResult = null;

		/**
		 * @param TemplateResultAsXML $GetTemplateResultAsXMLResult
		 */
		public function __construct( $GetTemplateResultAsXMLResult )
		{
			$this->GetTemplateResultAsXMLResult = $GetTemplateResultAsXMLResult;
		}

		/**
		 * @return TemplateResultAsXML
		 */
		public function getGetTemplateResultAsXMLResult()
		{
			return $this->GetTemplateResultAsXMLResult;
		}

		/**
		 * @param TemplateResultAsXML $GetTemplateResultAsXMLResult
		 * @return GetTemplateResultAsXMLResponse
		 */
		public function setGetTemplateResultAsXMLResult( $GetTemplateResultAsXMLResult )
		{
			$this->GetTemplateResultAsXMLResult = $GetTemplateResultAsXMLResult;
			return $this;
		}
	}