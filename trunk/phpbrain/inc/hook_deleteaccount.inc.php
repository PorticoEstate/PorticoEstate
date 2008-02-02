<?php
	/* $Id$ */

	if((int)$GLOBALS['hook_values']['account_id'] > 0)
	{
		$bokb = CreateObject('phpbrain.bokb');

		if((int)$_POST['new_owner'] == 0)
		{
			$bokb->delete_owner_articles((int)$GLOBALS['hook_values']['account_id']);
		}
		else
		{
			$bokb->change_articles_owner((int)$GLOBALS['hook_values']['account_id'],(int)$_POST['new_owner']);
		}
	}
?>
