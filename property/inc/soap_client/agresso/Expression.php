<?php

	class Expression
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
		 * @var ArrayOfExpressionProperties $ExpressionPropertiesList
		 */
		protected $ExpressionPropertiesList = null;

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
		 * @return Expression
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
		 * @return Expression
		 */
		public function setStatus( $Status )
		{
			$this->Status = $Status;
			return $this;
		}

		/**
		 * @return ArrayOfExpressionProperties
		 */
		public function getExpressionPropertiesList()
		{
			return $this->ExpressionPropertiesList;
		}

		/**
		 * @param ArrayOfExpressionProperties $ExpressionPropertiesList
		 * @return Expression
		 */
		public function setExpressionPropertiesList( $ExpressionPropertiesList )
		{
			$this->ExpressionPropertiesList = $ExpressionPropertiesList;
			return $this;
		}
	}