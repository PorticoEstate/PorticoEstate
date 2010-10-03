<?php
	/* $Id$ */
	$account_id = phpgw::get_var('account_id', 'int');
	$new_owner = phpgw::get_var('new_owner', 'int');

	if( $account_id > 0 )
	{
		$bokb = CreateObject('phpbrain.bokb');

		if((int)$_POST['new_owner'] == 0)
		{
			$bokb->delete_owner_articles($account_id);
		}
		else
		{
			$bokb->change_articles_owner($account_id, $new_owner);
		}
		unset($bokb);
	}
