<?php
/**
 * pbWebMAUI
 * @author Andreas Schiller <aschiller@probusiness.de>
 * @copyright Copyright (C) 2002-2003 probusiness AG
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package pbWebMAUI
 */

    /**
     * application include file
     * @see application
     */
    require_once "lib/class.application.php";

    if (!strlen($_GET["action"])) {
        $params["action"] = "Start";
    }

    if ($_GET["style"]) {
        $params["style"] = $_GET["style"];
    }

    $app = & new Application($params);
    $app->run();
?>
