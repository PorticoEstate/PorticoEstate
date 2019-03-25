<?php

	/**
	 * Enables access to the Agresso QueryEngine through a web service interface
	 */
	class QueryEngineV201101 extends \SoapClient
	{

		/**
		 * @var array $classmap The defined classes
		 */
		private static $classmap = array(
			'GetTemplateProperties'					 => '\\GetTemplateProperties',
			'WSCredentials'							 => '\\WSCredentials',
			'GetTemplatePropertiesResponse'			 => '\\GetTemplatePropertiesResponse',
			'TemplateProperties'					 => '\\TemplateProperties',
			'GetTemplateResultAsDataSet'			 => '\\GetTemplateResultAsDataSet',
			'InputForTemplateResult'				 => '\\InputForTemplateResult',
			'TemplateResultOptions'					 => '\\TemplateResultOptions',
			'ArrayOfSearchCriteriaProperties'		 => '\\ArrayOfSearchCriteriaProperties',
			'SearchCriteriaProperties'				 => '\\SearchCriteriaProperties',
			'GetTemplateResultAsDataSetResponse'	 => '\\GetTemplateResultAsDataSetResponse',
			'TemplateResultAsDataSet'				 => '\\TemplateResultAsDataSet',
			'TemplateResult'						 => '\\TemplateResult',
			'GetTemplateResultOptions'				 => '\\GetTemplateResultOptions',
			'GetTemplateResultOptionsResponse'		 => '\\GetTemplateResultOptionsResponse',
			'GetTemplateResultAsXML'				 => '\\GetTemplateResultAsXML',
			'GetTemplateResultAsXMLResponse'		 => '\\GetTemplateResultAsXMLResponse',
			'TemplateResultAsXML'					 => '\\TemplateResultAsXML',
			'GetSearchCriteria'						 => '\\GetSearchCriteria',
			'GetSearchCriteriaResponse'				 => '\\GetSearchCriteriaResponse',
			'SearchCriteria'						 => '\\SearchCriteria',
			'GetExpression'							 => '\\GetExpression',
			'GetExpressionResponse'					 => '\\GetExpressionResponse',
			'Expression'							 => '\\Expression',
			'ArrayOfExpressionProperties'			 => '\\ArrayOfExpressionProperties',
			'ExpressionProperties'					 => '\\ExpressionProperties',
			'GetStatisticalFormula'					 => '\\GetStatisticalFormula',
			'GetStatisticalFormulaResponse'			 => '\\GetStatisticalFormulaResponse',
			'StatisticalFormula'					 => '\\StatisticalFormula',
			'ArrayOfStatisticalFormulaProperties'	 => '\\ArrayOfStatisticalFormulaProperties',
			'StatisticalFormulaProperties'			 => '\\StatisticalFormulaProperties',
			'GetFormatInfo'							 => '\\GetFormatInfo',
			'GetFormatInfoResponse'					 => '\\GetFormatInfoResponse',
			'FormatInfo'							 => '\\FormatInfo',
			'ArrayOfFormatProperties'				 => '\\ArrayOfFormatProperties',
			'FormatProperties'						 => '\\FormatProperties',
			'GetTemplateList'						 => '\\GetTemplateList',
			'GetTemplateListResponse'				 => '\\GetTemplateListResponse',
			'TemplateList'							 => '\\TemplateList',
			'ArrayOfTemplateHeader'					 => '\\ArrayOfTemplateHeader',
			'TemplateHeader'						 => '\\TemplateHeader',
			'GetTemplateMetaData'					 => '\\GetTemplateMetaData',
			'GetTemplateMetaDataResponse'			 => '\\GetTemplateMetaDataResponse',
			'TemplateMetaData'						 => '\\TemplateMetaData',
			'About'									 => '\\About',
			'AboutResponse'							 => '\\AboutResponse',
		);

		/**
		 * @param array $options A array of config values
		 * @param string $wsdl The wsdl file to use
		 */
		public function __construct( array $options = array(), $wsdl = null )
		{
			foreach (self::$classmap as $key => $value)
			{
				if (!isset($options['classmap'][$key]))
				{
					$options['classmap'][$key] = $value;
				}
			}
			$options = array_merge(array(
				'features' => 1,
				), $options);
			if (!$wsdl)
			{
				//       $wsdl = 'http://10.19.14.242/agresso-webservices/service.svc?QueryEngineService/QueryEngineV201101';
				$wsdl = 'http://10.19.14.242/UBW-webservices/service.svc?QueryEngineService/QueryEngineV201101';
			}
			parent::__construct($wsdl, $options);
		}

		/**
		 * Returns the properties associated with BRT
		 *
		 * @param GetTemplateProperties $parameters
		 * @return GetTemplatePropertiesResponse
		 */
		public function GetTemplateProperties( GetTemplateProperties $parameters )
		{
			return $this->__soapCall('GetTemplateProperties', array($parameters));
		}

		/**
		 * Returns the template result as a MS DataSet
		 *
		 * @param GetTemplateResultAsDataSet $parameters
		 * @return GetTemplateResultAsDataSetResponse
		 */
		public function GetTemplateResultAsDataSet( GetTemplateResultAsDataSet $parameters )
		{
			return $this->__soapCall('GetTemplateResultAsDataSet', array($parameters));
		}

		/**
		 * Returns the options with default values, that are used to specify what output is wanted from GetTemplateResultAsDataSet
		 *
		 * @param GetTemplateResultOptions $parameters
		 * @return GetTemplateResultOptionsResponse
		 */
		public function GetTemplateResultOptions( GetTemplateResultOptions $parameters )
		{
			return $this->__soapCall('GetTemplateResultOptions', array($parameters));
		}

		/**
		 * Returns the template result as a XML string
		 *
		 * @param GetTemplateResultAsXML $parameters
		 * @return GetTemplateResultAsXMLResponse
		 */
		public function GetTemplateResultAsXML( GetTemplateResultAsXML $parameters )
		{
			return $this->__soapCall('GetTemplateResultAsXML', array($parameters));
		}

		/**
		 * Returns all the searchable columns in the BRT
		 *
		 * @param GetSearchCriteria $parameters
		 * @return GetSearchCriteriaResponse
		 */
		public function GetSearchCriteria( GetSearchCriteria $parameters )
		{
			return $this->__soapCall('GetSearchCriteria', array($parameters));
		}

		/**
		 * Returns all expressions in the BRT
		 *
		 * @param GetExpression $parameters
		 * @return GetExpressionResponse
		 */
		public function GetExpression( GetExpression $parameters )
		{
			return $this->__soapCall('GetExpression', array($parameters));
		}

		/**
		 * Returns all statistical formulas in the BRT
		 *
		 * @param GetStatisticalFormula $parameters
		 * @return GetStatisticalFormulaResponse
		 */
		public function GetStatisticalFormula( GetStatisticalFormula $parameters )
		{
			return $this->__soapCall('GetStatisticalFormula', array($parameters));
		}

		/**
		 * Returns formatting information for a BRT as defined in the BRT definition
		 *
		 * @param GetFormatInfo $parameters
		 * @return GetFormatInfoResponse
		 */
		public function GetFormatInfo( GetFormatInfo $parameters )
		{
			return $this->__soapCall('GetFormatInfo', array($parameters));
		}

		/**
		 * Returns all the available BRT's for the current user.
		 *
		 * @param GetTemplateList $parameters
		 * @return GetTemplateListResponse
		 */
		public function GetTemplateList( GetTemplateList $parameters )
		{
			return $this->__soapCall('GetTemplateList', array($parameters));
		}

		/**
		 * GetTemplateMetaData is a helper function that gets SearchCriteria, TemplatePropertiesand FormatInfo in one go, returned as a TemplateMetaData object. GetTemplateMetaData is very performance boosting, as QE WS only needs to be called one time to get all the values.
		 *
		 * @param GetTemplateMetaData $parameters
		 * @return GetTemplateMetaDataResponse
		 */
		public function GetTemplateMetaData( GetTemplateMetaData $parameters )
		{
			return $this->__soapCall('GetTemplateMetaData', array($parameters));
		}

		/**
		 * Diagnostics method that checks for presence of nessecary components and database connection
		 *
		 * @param About $parameters
		 * @return AboutResponse
		 */
		public function About( About $parameters )
		{
			return $this->__soapCall('About', array($parameters));
		}
	}