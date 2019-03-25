<?php

	class AboutResponse
	{

		/**
		 * @var string $AboutResult
		 */
		protected $AboutResult = null;

		/**
		 * @param string $AboutResult
		 */
		public function __construct( $AboutResult )
		{
			$this->AboutResult = $AboutResult;
		}

		/**
		 * @return string
		 */
		public function getAboutResult()
		{
			return $this->AboutResult;
		}

		/**
		 * @param string $AboutResult
		 * @return AboutResponse
		 */
		public function setAboutResult( $AboutResult )
		{
			$this->AboutResult = $AboutResult;
			return $this;
		}
	}