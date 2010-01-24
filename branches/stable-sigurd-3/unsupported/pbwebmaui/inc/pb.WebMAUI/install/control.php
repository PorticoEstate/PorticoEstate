<?php
/**
 * pbWebMAUI
 * @author Andreas Schiller <aschiller@probusiness.de>
 * @copyright Copyright (C) 2002-2003 probusiness AG
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package pbWebMAUI
 */

	$rootdir = "/var/www/html/pb.WebMAUI";

	//read args from command line and create $_GET
    for($i=1; $i<$_SERVER["argc"]; $i++) {
		if (ereg("--(.*)=(.*)", $_SERVER["argv"][$i], $opt)) {
			$_GET[$opt[1]] = $opt[2];
		}
        else if (ereg("--(.*)", $_SERVER["argv"][$i], $opt)) {
            $_GET[$opt[1]] = true;
        }
	}

    chdir($rootdir);
    if ($_GET["quiet"]) {
        ob_start();
    }

    /**
     * index include file
     */
    require_once("index.php");

    if ($_GET["quiet"]) {
        ob_end_clean();
    }
?>
