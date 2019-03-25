<?php

	class TemplateResultAsXML
	{

		/**
		 * @var int $ReturnCode
		 */
		protected $ReturnCode = null;

		/**
		 * @var string $Status
		 */
		protected $Status = null;

		/**
		 * @var string $TemplateResult
		 */
		protected $TemplateResult = null;

		/**
		 * @param int $ReturnCode
		 */
		public function __construct( $ReturnCode )
		{
			$this->ReturnCode = $ReturnCode;
		}

		/**
		 * @return int
		 */
		public function getReturnCode()
		{
			return $this->ReturnCode;
		}

		/**
		 * @param int $ReturnCode
		 * @return TemplateResultAsXML
		 */
		public function setReturnCode( $ReturnCode )
		{
			$this->ReturnCode = $ReturnCode;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getStatus()
		{
			return $this->Status;
		}

		/**
		 * @param string $Status
		 * @return TemplateResultAsXML
		 */
		public function setStatus( $Status )
		{
			$this->Status = $Status;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getTemplateResult()
		{
			return $this->TemplateResult;
		}

		/**
		 * @param string $TemplateResult
		 * @return TemplateResultAsXML
		 */
		public function setTemplateResult( $TemplateResult )
		{
			$this->TemplateResult = $TemplateResult;
			return $this;
		}
	}