<?php
	$test[] = '0.8.1';
	function messenger_upgrade0_8_1()
	{
		return $GLOBALS['setup_info']['messenger']['currentver'] = '0.9.0';
	}
?>
