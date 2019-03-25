<?php

	function autoload_8741c9c788a4ac58aa56f712f7206de1( $class )
	{
		$classes = array(
			'QueryEngineV201101'					 => __DIR__ . '/QueryEngineV201101.php',
			'GetTemplateProperties'					 => __DIR__ . '/GetTemplateProperties.php',
			'WSCredentials'							 => __DIR__ . '/WSCredentials.php',
			'GetTemplatePropertiesResponse'			 => __DIR__ . '/GetTemplatePropertiesResponse.php',
			'TemplateProperties'					 => __DIR__ . '/TemplateProperties.php',
			'GetTemplateResultAsDataSet'			 => __DIR__ . '/GetTemplateResultAsDataSet.php',
			'InputForTemplateResult'				 => __DIR__ . '/InputForTemplateResult.php',
			'TemplateResultOptions'					 => __DIR__ . '/TemplateResultOptions.php',
			'ArrayOfSearchCriteriaProperties'		 => __DIR__ . '/ArrayOfSearchCriteriaProperties.php',
			'SearchCriteriaProperties'				 => __DIR__ . '/SearchCriteriaProperties.php',
			'GetTemplateResultAsDataSetResponse'	 => __DIR__ . '/GetTemplateResultAsDataSetResponse.php',
			'TemplateResultAsDataSet'				 => __DIR__ . '/TemplateResultAsDataSet.php',
			'TemplateResult'						 => __DIR__ . '/TemplateResult.php',
			'GetTemplateResultOptions'				 => __DIR__ . '/GetTemplateResultOptions.php',
			'GetTemplateResultOptionsResponse'		 => __DIR__ . '/GetTemplateResultOptionsResponse.php',
			'GetTemplateResultAsXML'				 => __DIR__ . '/GetTemplateResultAsXML.php',
			'GetTemplateResultAsXMLResponse'		 => __DIR__ . '/GetTemplateResultAsXMLResponse.php',
			'TemplateResultAsXML'					 => __DIR__ . '/TemplateResultAsXML.php',
			'GetSearchCriteria'						 => __DIR__ . '/GetSearchCriteria.php',
			'GetSearchCriteriaResponse'				 => __DIR__ . '/GetSearchCriteriaResponse.php',
			'SearchCriteria'						 => __DIR__ . '/SearchCriteria.php',
			'GetExpression'							 => __DIR__ . '/GetExpression.php',
			'GetExpressionResponse'					 => __DIR__ . '/GetExpressionResponse.php',
			'Expression'							 => __DIR__ . '/Expression.php',
			'ArrayOfExpressionProperties'			 => __DIR__ . '/ArrayOfExpressionProperties.php',
			'ExpressionProperties'					 => __DIR__ . '/ExpressionProperties.php',
			'GetStatisticalFormula'					 => __DIR__ . '/GetStatisticalFormula.php',
			'GetStatisticalFormulaResponse'			 => __DIR__ . '/GetStatisticalFormulaResponse.php',
			'StatisticalFormula'					 => __DIR__ . '/StatisticalFormula.php',
			'ArrayOfStatisticalFormulaProperties'	 => __DIR__ . '/ArrayOfStatisticalFormulaProperties.php',
			'StatisticalFormulaProperties'			 => __DIR__ . '/StatisticalFormulaProperties.php',
			'GetFormatInfo'							 => __DIR__ . '/GetFormatInfo.php',
			'GetFormatInfoResponse'					 => __DIR__ . '/GetFormatInfoResponse.php',
			'FormatInfo'							 => __DIR__ . '/FormatInfo.php',
			'ArrayOfFormatProperties'				 => __DIR__ . '/ArrayOfFormatProperties.php',
			'FormatProperties'						 => __DIR__ . '/FormatProperties.php',
			'GetTemplateList'						 => __DIR__ . '/GetTemplateList.php',
			'GetTemplateListResponse'				 => __DIR__ . '/GetTemplateListResponse.php',
			'TemplateList'							 => __DIR__ . '/TemplateList.php',
			'ArrayOfTemplateHeader'					 => __DIR__ . '/ArrayOfTemplateHeader.php',
			'TemplateHeader'						 => __DIR__ . '/TemplateHeader.php',
			'GetTemplateMetaData'					 => __DIR__ . '/GetTemplateMetaData.php',
			'GetTemplateMetaDataResponse'			 => __DIR__ . '/GetTemplateMetaDataResponse.php',
			'TemplateMetaData'						 => __DIR__ . '/TemplateMetaData.php',
			'About'									 => __DIR__ . '/About.php',
			'AboutResponse'							 => __DIR__ . '/AboutResponse.php'
		);
		if (!empty($classes[$class]))
		{
			include $classes[$class];
		};
	}
	spl_autoload_register('autoload_8741c9c788a4ac58aa56f712f7206de1');

// Do nothing. The rest is just leftovers from the code generation.
	{

	}
