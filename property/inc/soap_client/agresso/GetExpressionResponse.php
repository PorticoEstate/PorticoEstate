<?php

	class GetExpressionResponse
	{

		/**
		 * @var Expression $GetExpressionResult
		 */
		protected $GetExpressionResult = null;

		/**
		 * @param Expression $GetExpressionResult
		 */
		public function __construct( $GetExpressionResult )
		{
			$this->GetExpressionResult = $GetExpressionResult;
		}

		/**
		 * @return Expression
		 */
		public function getGetExpressionResult()
		{
			return $this->GetExpressionResult;
		}

		/**
		 * @param Expression $GetExpressionResult
		 * @return GetExpressionResponse
		 */
		public function setGetExpressionResult( $GetExpressionResult )
		{
			$this->GetExpressionResult = $GetExpressionResult;
			return $this;
		}
	}