<?php
	$basedir = dirname(__FILE__) . '/';

	require_once($basedir . '/sfValidatorBase.class.php');

	require_once($basedir . '/sfValidatorError.class.php');
	require_once($basedir . '/sfValidatorErrorSchema.class.php');

	require_once($basedir . '/sfValidatorSchema.class.php');
	require_once($basedir . '/sfValidatorSchemaCompare.class.php');
	require_once($basedir . '/sfValidatorSchemaFilter.class.php');
	require_once($basedir . '/sfValidatorSchemaForEach.class.php');

	require_once($basedir . '/sfValidatorString.class.php');
	require_once($basedir . '/sfValidatorBoolean.class.php');
	require_once($basedir . '/sfValidatorNumber.class.php');
	require_once($basedir . '/sfValidatorInteger.class.php');

	require_once($basedir . '/sfValidatorChoice.class.php');
	require_once($basedir . '/sfValidatorChoiceMany.class.php');
	require_once($basedir . '/sfValidatorCSRFToken.class.php');

	require_once($basedir . '/sfValidatorFile.class.php');
	require_once($basedir . '/sfValidatorPass.class.php');

	require_once($basedir . '/sfValidatorRegex.class.php');
	require_once($basedir . '/sfValidatorEmail.class.php');
	require_once($basedir . '/sfValidatorUrl.class.php');
	require_once($basedir . '/sfValidatorNorwegianSSN.class.php');
	require_once($basedir . '/sfValidatorNorwegianOrganizationNumber.class.php');

	require_once($basedir . '/sfValidatorDate.class.php');
	require_once($basedir . '/sfValidatorDateRange.class.php');
	require_once($basedir . '/sfValidatorDateTime.class.php');
	require_once($basedir . '/sfValidatorTime.class.php');

	require_once($basedir . '/sfValidatorAnd.class.php');
	require_once($basedir . '/sfValidatorOr.class.php');

	require_once($basedir . '/sfValidatorCallback.class.php');
	require_once($basedir . '/sfValidatorDecorator.class.php');
	require_once($basedir . '/sfValidatorFromDescription.class.php');

	require_once($basedir . '/i18n/sfValidatorI18nChoiceCountry.class.php');
	require_once($basedir . '/i18n/sfValidatorI18nChoiceLanguage.class.php');
