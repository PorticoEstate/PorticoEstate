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
     * Login dialog
     * @package pbWebMAUI
     */
    class dlgLogin extends Dialog {
        /**
         * Constructor
         *
         * @param $application
         * @param $parent
         * @param string $name Name within parent template
         */
        function dlgLogin(&$application, &$parent, $name) {
            $this->Dialog($application, $parent, $name, "dlgLogin.tpl.html", array(4));
        }

        /**
         * Get menu
         *
         * @return array Menuitems. each item itself is an array
         */
        function getMenu() {
            if (!empty($_SERVER["HTTP_REFERER"])) {
                $menu[] = array("caption"=>"zurück", "url"=>$_SERVER["HTTP_REFERER"]);
            }

            return $menu;
        }

        /**
         * Get title
         *
         * @return string Title for Dialog
         */
        function getTitle() {
            return "Anmeldung";
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
         * Called with posted data from form; after authentication and before OnPrepareDialog
         *
         * @param array $formdata
         */
        function OnFormData($formdata) {
            //save input to global session var
            $this->_application->setGlobal("user", $formdata["fldUser"]);
            $this->_application->setGlobal("password", $formdata["fldPassword"]);

            //redirect to old request
            $uri = $this->_application->getGlobal("request_uri");
            $this->_application->redirect($uri);
        }
    }
?>
