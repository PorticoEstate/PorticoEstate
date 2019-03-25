<?php

	class GetTemplateResultOptions
	{

		/**
		 * @var WSCredentials $credentials
		 */
		protected $credentials = null;

		/**
		 * @param WSCredentials $credentials
		 */
		public function __construct( $credentials )
		{
			$this->credentials = $credentials;
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
		 * @return GetTemplateResultOptions
		 */
		public function setCredentials( $credentials )
		{
			$this->credentials = $credentials;
			return $this;
		}
	}