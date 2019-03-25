<?php

	class SearchCriteria
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
		 * @var ArrayOfSearchCriteriaProperties $SearchCriteriaPropertiesList
		 */
		protected $SearchCriteriaPropertiesList = null;

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
		 * @return SearchCriteria
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
		 * @return SearchCriteria
		 */
		public function setStatus( $Status )
		{
			$this->Status = $Status;
			return $this;
		}

		/**
		 * @return ArrayOfSearchCriteriaProperties
		 */
		public function getSearchCriteriaPropertiesList()
		{
			return $this->SearchCriteriaPropertiesList;
		}

		/**
		 * @param ArrayOfSearchCriteriaProperties $SearchCriteriaPropertiesList
		 * @return SearchCriteria
		 */
		public function setSearchCriteriaPropertiesList( $SearchCriteriaPropertiesList )
		{
			$this->SearchCriteriaPropertiesList = $SearchCriteriaPropertiesList;
			return $this;
		}
	}