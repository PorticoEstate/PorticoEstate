<?php

	class TemplateHeader
	{

		/**
		 * @var int $TemplateId
		 */
		protected $TemplateId = null;

		/**
		 * @var string $CompanyCode
		 */
		protected $CompanyCode = null;

		/**
		 * @var string $Frame
		 */
		protected $Frame = null;

		/**
		 * @var string $FunctionId
		 */
		protected $FunctionId = null;

		/**
		 * @var string $Module
		 */
		protected $Module = null;

		/**
		 * @var string $Name
		 */
		protected $Name = null;

		/**
		 * @var string $UserId
		 */
		protected $UserId = null;

		/**
		 * @param int $TemplateId
		 */
		public function __construct( $TemplateId )
		{
			$this->TemplateId = $TemplateId;
		}

		/**
		 * @return int
		 */
		public function getTemplateId()
		{
			return $this->TemplateId;
		}

		/**
		 * @param int $TemplateId
		 * @return TemplateHeader
		 */
		public function setTemplateId( $TemplateId )
		{
			$this->TemplateId = $TemplateId;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getCompanyCode()
		{
			return $this->CompanyCode;
		}

		/**
		 * @param string $CompanyCode
		 * @return TemplateHeader
		 */
		public function setCompanyCode( $CompanyCode )
		{
			$this->CompanyCode = $CompanyCode;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getFrame()
		{
			return $this->Frame;
		}

		/**
		 * @param string $Frame
		 * @return TemplateHeader
		 */
		public function setFrame( $Frame )
		{
			$this->Frame = $Frame;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getFunctionId()
		{
			return $this->FunctionId;
		}

		/**
		 * @param string $FunctionId
		 * @return TemplateHeader
		 */
		public function setFunctionId( $FunctionId )
		{
			$this->FunctionId = $FunctionId;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getModule()
		{
			return $this->Module;
		}

		/**
		 * @param string $Module
		 * @return TemplateHeader
		 */
		public function setModule( $Module )
		{
			$this->Module = $Module;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getName()
		{
			return $this->Name;
		}

		/**
		 * @param string $Name
		 * @return TemplateHeader
		 */
		public function setName( $Name )
		{
			$this->Name = $Name;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getUserId()
		{
			return $this->UserId;
		}

		/**
		 * @param string $UserId
		 * @return TemplateHeader
		 */
		public function setUserId( $UserId )
		{
			$this->UserId = $UserId;
			return $this;
		}
	}