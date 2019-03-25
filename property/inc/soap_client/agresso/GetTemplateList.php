<?php

	class GetTemplateList
	{

		/**
		 * @var string $formList
		 */
		protected $formList = null;

		/**
		 * @var string $descrList
		 */
		protected $descrList = null;

		/**
		 * @var WSCredentials $credentials
		 */
		protected $credentials = null;

		/**
		 * @param string $formList
		 * @param string $descrList
		 * @param WSCredentials $credentials
		 */
		public function __construct( $formList, $descrList, $credentials )
		{
			$this->formList		 = $formList;
			$this->descrList	 = $descrList;
			$this->credentials	 = $credentials;
		}

		/**
		 * @return string
		 */
		public function getFormList()
		{
			return $this->formList;
		}

		/**
		 * @param string $formList
		 * @return GetTemplateList
		 */
		public function setFormList( $formList )
		{
			$this->formList = $formList;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getDescrList()
		{
			return $this->descrList;
		}

		/**
		 * @param string $descrList
		 * @return GetTemplateList
		 */
		public function setDescrList( $descrList )
		{
			$this->descrList = $descrList;
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
		 * @return GetTemplateList
		 */
		public function setCredentials( $credentials )
		{
			$this->credentials = $credentials;
			return $this;
		}
	}