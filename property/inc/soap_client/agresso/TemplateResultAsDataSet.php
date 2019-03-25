<?php

	class TemplateResultAsDataSet
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
		 * @var TemplateResult $TemplateResult
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
		 * @return TemplateResultAsDataSet
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
		 * @return TemplateResultAsDataSet
		 */
		public function setStatus( $Status )
		{
			$this->Status = $Status;
			return $this;
		}

		/**
		 * @return TemplateResult
		 */
		public function getTemplateResult()
		{
			return $this->TemplateResult;
		}

		/**
		 * @param TemplateResult $TemplateResult
		 * @return TemplateResultAsDataSet
		 */
		public function setTemplateResult( $TemplateResult )
		{
			$this->TemplateResult = $TemplateResult;
			return $this;
		}
	}