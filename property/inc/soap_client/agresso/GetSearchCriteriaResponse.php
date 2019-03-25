<?php

	class GetSearchCriteriaResponse
	{

		/**
		 * @var SearchCriteria $GetSearchCriteriaResult
		 */
		protected $GetSearchCriteriaResult = null;

		/**
		 * @param SearchCriteria $GetSearchCriteriaResult
		 */
		public function __construct( $GetSearchCriteriaResult )
		{
			$this->GetSearchCriteriaResult = $GetSearchCriteriaResult;
		}

		/**
		 * @return SearchCriteria
		 */
		public function getGetSearchCriteriaResult()
		{
			return $this->GetSearchCriteriaResult;
		}

		/**
		 * @param SearchCriteria $GetSearchCriteriaResult
		 * @return GetSearchCriteriaResponse
		 */
		public function setGetSearchCriteriaResult( $GetSearchCriteriaResult )
		{
			$this->GetSearchCriteriaResult = $GetSearchCriteriaResult;
			return $this;
		}
	}