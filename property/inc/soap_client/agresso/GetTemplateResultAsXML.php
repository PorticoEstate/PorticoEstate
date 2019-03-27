<?php

	class GetTemplateResultAsXML
	{

		/**
		 * @var InputForTemplateResult $input
		 */
		protected $input = null;

		/**
		 * @var WSCredentials $credentials
		 */
		protected $credentials = null;

		/**
		 * @param InputForTemplateResult $input
		 * @param WSCredentials $credentials
		 */
		public function __construct( $input, $credentials )
		{
			$this->input		 = $input;
			$this->credentials	 = $credentials;
		}

		/**
		 * @return InputForTemplateResult
		 */
		public function getInput()
		{
			return $this->input;
		}

		/**
		 * @param InputForTemplateResult $input
		 * @return GetTemplateResultAsXML
		 */
		public function setInput( $input )
		{
			$this->input = $input;
			return $this;
		}

		/**
		 * @return WSCredentials
		 */
		public function getCredentials()
		{
			return $this->credentials;
		}

		/**
		 * @param WSCredentials $credentials
		 * @return GetTemplateResultAsXML
		 */
		public function setCredentials( $credentials )
		{
			$this->credentials = $credentials;
			return $this;
		}
	}