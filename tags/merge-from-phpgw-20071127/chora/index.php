<?php
/*
 * $Horde: chora/index.php,v 1.6 2001/02/27 07:06:00 avsm Exp $
 *
 * Copyright 1999, 2000, 2001 Anil Madhavapeddy <anil@recoil.org>
 *
 * See the enclosed file COPYING for license information (GPL).  If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 */

	$phpgw_info = array();
	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp' => 'chora',
		'noheader'   => True,
		'nonavbar'   => True,
		'enable_config_class' => True
	);
	include('../header.inc.php');

	define('CHORA_BASE', PHPGW_APP_ROOT);
	$chora_configured = (
		@is_readable(CHORA_BASE . '/config/conf.php') &&
		@is_readable(CHORA_BASE . '/config/cvsroots.php') &&
		@is_readable(CHORA_BASE . '/config/html.php')
	);
	//@is_readable(CHORA_BASE . '/config/mime.php'));

	if ($chora_configured)
	{
		header('Location: ' . $GLOBALS['phpgw']->link('/chora/cvs.php'));
		exit;

	/* Chora isn't configured */
	}
	else
	{
		include_once PHPGW_APP_INC . '/Chora.php';

		$title = "Chora is not fully configured.";
		include CHORA_BASE . '/templates/default/notconfigured.tpl';
		include CHORA_BASE . '/templates/default/common-footer.tpl';
	}
?>
