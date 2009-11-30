<?php
	// $Horde: chora/lib/Chora.php,v 1.1 2001/02/01 01:18:24 avsm Exp $

	/** @const CHORA_NAME The app name. */
	define('CHORA_NAME', 'Chora CVS Browser');

	if (!defined('CHORA_BASE'))
	{
		/** @const CHORA_BASE The chora fileroot. */
		define('CHORA_BASE', dirname(__FILE__) . '/..');
	}

	require_once CHORA_BASE . '/lib/version.php';
?>
