<?php

	class GetFormatInfoResponse
	{

		/**
		 * @var FormatInfo $GetFormatInfoResult
		 */
		protected $GetFormatInfoResult = null;

		/**
		 * @param FormatInfo $GetFormatInfoResult
		 */
		public function __construct( $GetFormatInfoResult )
		{
			$this->GetFormatInfoResult = $GetFormatInfoResult;
		}

		/**
		 * @return FormatInfo
		 */
		public function getGetFormatInfoResult()
		{
			return $this->GetFormatInfoResult;
		}

		/**
		 * @param FormatInfo $GetFormatInfoResult
		 * @return GetFormatInfoResponse
		 */
		public function setGetFormatInfoResult( $GetFormatInfoResult )
		{
			$this->GetFormatInfoResult = $GetFormatInfoResult;
			return $this;
		}
	}