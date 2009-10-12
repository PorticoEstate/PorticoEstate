<?php
/*
 * $Horde: chora/css.php,v 1.3 2001/02/27 07:05:59 avsm Exp $
 *
 * Copyright 2000,2001 Charles J. Hagenbuch <chuck@horde.org>
 *
 * See the enclosed file COPYING for license information (LGPL).  If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 */

	$phpgw_info = array();
	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp' => 'chora',
		'noheader' => True,
		'nofooter' => True
	);
	include('../header.inc.php');
	include('./config/html.php');

	if($conf['css']['cached'])
	{
		$mod_gmt = gmdate('D, d M Y H:i:s', getlastmod()) . ' GMT';
		header('Last-Modified: ' . $mod_gmt);
		header('Cache-Control: public, max-age=86400');
	}
	else
	{
		header('Expires: -1');
		header('Pragma: no-cache');
		header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
	}

	header('Content-type: text/css');

	if(is_array($css))
	{
		@reset($css);
		while(list($class,$params) = @each($css))
		{
			echo "$class {\n";
			if(is_array($params))
			{
				@reset($params);
				while(list($key,$val) = @each($params))
				{
					echo "\t$key: $val;\n";
				}
			}
			echo "}\n\n";
		}
	}
?>
