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
     * Not really a dialog - we want to ask for authentication and redirect the user to the page he is authenticated for
     * @package pbWebMAUI
     */
    class dlgStart extends Dialog {
        /**
        * Constructor
        * @param string $name name within parent template
        */
        function dlgStart(&$application, &$parent, $name) {
            $this->Dialog($application, $parent, $name, "");
        }

        /**
         * Get menu
         *
         * @return array Menuitems. each item itself is an array
         */
        function getMenu() {
        }

        /**
         * Get title
         *
         * @return string Title for Dialog
         */
        function getTitle() {
        }

        /**
         * Set params
         *
         * @param array $params Parameters to use with this dialog
         */
        function setParams($params) {
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
            switch ($auth_param["level"]) {
                case alSysadmin:
                    $this->_accesslevel = alSysadmin;
                    return 2;

                case alDomainadmin:
                case alDomainmaster:
                    $this->_accesslevel = $auth_param["level"];
                    $this->_domain = $auth_param["domain"];
                    return 1;

                case alMailaccount:
                    $this->_accesslevel = alMailaccount;
                    $this->_domain = $auth_param["domain"];
                    $this->_account = $auth_param["account"];
                    return 1;

                default:
                    return 0;
            }
        }

        /**
         * On Dialog Prepare
         *
         * @param object $dialog
         */
        function OnDialogPrepare(&$dialog) {
            //redirect only
            switch ($this->_accesslevel) {
                case alSysadmin:
                    $uri = $_SERVER["PHP_SELF"]."?action=Server";
                    break;

                case alDomainmaster:
                case alDomainadmin:
                    $uri = $_SERVER["PHP_SELF"]."?action=ViewDomain&domain=".urlencode($this->_domain);
                    break;

                case alMailaccount:
                    $uri = $_SERVER["PHP_SELF"]."?action=EditAccount&mail=".urlencode($this->_account);
                    break;

                default:
                    die ("unknown error redirecting from startpage");
            }

            header ("Location: ".$uri);
        }
    }
?>
