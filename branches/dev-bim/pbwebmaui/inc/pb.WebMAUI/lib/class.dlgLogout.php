<?php
/**
 * pbWebMAUI
 * @author Andreas Schiller <aschiller@probusiness.de>
 * @copyright Copyright (C) 2002-2003 probusiness AG
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package pbWebMAUI
 */
    /**
     * constants include file
     * @see constants
     */
    require_once "constants.inc.php";
    /**
     * dialog include file
     * @see dialog
     */
    require_once "class.dialog.php";
    /**
     * application include file
     * @see application
     */
    require_once "class.application.php";

    /**
     * Logout dialog
     * @package pbWebMAUI
     */
    class dlgLogout extends Dialog {
        /**
         * Constructor
         *
         * @param $application
         * @param $parent
         * @param string $name name within parent template
         */
        function dlgLogout(&$application, &$parent, $name) {
            $this->Dialog($application, $parent, $name, "dlgLogout.tpl.html", "");
        }

        /**
         * Get title
         *
         * @return string Title for Dialog
         */
        function getTitle() {
            return "";
        }

        /**
         * Called for each applications authobject by its parent. This function should check authorization for this dialog
         *
         * @param array $auth_param array of params holding the authenticated options for one authentication
         * @return integer 0, not yet authenticated to show dialog
         *                  1, authenticated to show, but request for further authentication
         *                  2, full authentification
         */
        function OnAuth($auth_param) {
            return 2;
        }

        /**
         * OnDialogPrepare
         *
         * @param object $dialog
         */
        function OnDialogPrepare(&$dialog) {
            $app = $this->getApplication();
            $app->discard_auths();
        }
    }
?>