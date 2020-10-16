<?php
	$GLOBALS['phpgw_info']['flags'] = array
		(
		'noheader'	 => true,
		'nonavbar'	 => true,
		'currentapp' => 'property'
	);

	include_once('../header.inc.php');

	$subject = 'løkjlkjl[svar på auto id][PorticoTicket::89874::13716] SV: 2. gangs purring: Faktura avvises og vil ikke bli betalt(1)';


	preg_match_all("/\[[^\]]*\]/", $subject, $matches);
	$identificator_str	 = trim($matches[0][0], "[]");
	$identificator_arr	 = explode("::", $identificator_str);


	_debug_array($matches);
