<?php
//	$GLOBALS['phpgw_info']['flags']['mailparse'] = False;
	if ( !extension_loaded('mailparse') )
	{
		echo '<div class="error">';
		echo 'mailparse not loaded - see http://php.net/manual/en/mailparse.installation.php';
		echo '</div>';
	}
	else
	{
//		$GLOBALS['phpgw_info']['flags']['mailparse'] = True;
	}

