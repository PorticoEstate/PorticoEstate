<?php

	class GetSearchCriteria
	{

		/**
		 * @var int $templateId
		 */
		protected $templateId = null;

		/**
		 * @var boolean $hideUnused
		 */
		protected $hideUnused = null;

		/**
		 * @var WSCredentials $credentials
		 */
		protected $credentials = null;

		/**
		 * @param int $templateId
		 * @param boolean $hideUnused
		 * @param WSCredentials $credentials
		 */
		public function __construct( $templateId, $hideUnused, $credentials )
		{
			$this->templateId	 = $templateId;
			$this->hideUnused	 = $hideUnused;
			$this->credentials	 = $credentials;
		}

		/**
		 * @return int
		 */
		public function getTemplateId()
		{
			return $this->templateId;
		}

		/**
		 * @param int $templateId
		 * @return GetSearchCriteria
		 */
		public function setTemplateId( $templateId )
		{
			$this->templateId = $templateId;
			return $this;
		}

		/**
		 * @return boolean
		 */
		public function getHideUnused()
		{
			return $this->hideUnused;
		}

		/**
		 * @param boolean $hideUnused
		 * @return GetSearchCriteria
		 */
		public function setHideUnused( $hideUnused )
		{
			$this->hideUnused = $hideUnused;
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
		 * @return GetSearchCriteria
		 */
		public function setCredentials( $credentials )
		{
			$this->credentials = $credentials;
			return $this;
		}
	}