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
     * New domain dialog
     * @package pbWebMAUI
     */
    class dlgNewDomain extends Dialog {
        /**
         * Constructor
         *
         * @param $application
         * @param $parent
         * @param string $name name within parent template
         */
        function dlgNewDomain(&$application, &$parent, $name) {
            $this->Dialog($application, $parent, $name, "dlgNewDomain.tpl.html");
        }

        /**
         * Get menu
         *
         * @return array Menuitems. each item itself is an array
         */
        function getMenu() {
            $menu[] = array("caption"=>"Domainübersicht", "action"=>"Server");
            return $menu;
        }

        /**
         * Get title
         *
         * @return string Title for Dialog
         */
        function getTitle() {
            return "Neue Domain einrichten";
        }

        /**
         * Set params
         *
         * @param array $params Parameters to use with this dialog
         */
        function setParams($params) {
            //eg extract parameters needed
        }

        /**
         * Called for each applications authobject by its parent. This function should check authorization for this dialog
         *
         * @param  array $auth_param Authenticated options for one authentication
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
                case alMailaccount:
                default:
                    return 0;
            }
        }

        /**
         * Called with posted data from form; after authentication and before OnPrepareDialog
         *
         * @param array $formdata
         * @return array
         */
        function OnFormData($formdata) {
            $server = &new Mailserver($this->getApplication());
            $domains = $server->getAttribute("domains");

            if (is_array($domains)) {
                foreach ($domains as $domain) {
                    $domainname = $domain->getAttribute("domain");
                    if (strcasecmp($domainname, $formdata["fldNewDomain"]) == 0) {
                        $result[] = "Der angegebene Maildomainname existiert bereits";
                    }
                }
            }

            if (empty($formdata["fldDomainadminUID"])) {
                $result[] = "Kein Domainverwalter angegeben";
            }

            if (!empty($formdata["fldPassword"])) {
                $pw1 = $formdata["fldPassword"];
                $pw2 = $formdata["fldPassword2"];

                if ("$pw1" != "$pw2") {
                    $result[] = "Passwortwiederholung nicht identisch mit Passwort";
                }
            }

            $this->_MailDomain = &new Maildomain($this->getApplication(), $formdata["fldNewDomain"], true);
            if (empty($result)) {
                //save and redirect
                $this->_MailDomain->save();

                $mail = $formdata["fldDomainadminUID"]."@".$formdata["fldNewDomain"];
                $account = &new Mailaccount($this->getApplication(), "", "", "", $formdata["fldNewDomain"]);
                $account->setAttribute("cn", $formdata["fldDomainadminUID"]);
                $account->setAttribute("password", $formdata["fldPassword"]);
                $account->setAttribute("accesslevel", alDomainadmin);
                $account->setAttribute("mail", array($mail));
                $account->save();

                $uri = $_SERVER["PHP_SELF"]."?action=ViewDomain&domain=".$this->_MailDomain->getAttribute("domain");
                $this->_application->redirect($uri);
            }

            return $result;
        }

        /**
         * On Dialog Prepare
         *
         * @param object $dialog
         */
        function OnDialogPrepare(&$dialog) {
            if (!empty($this->_MailDomain)) {
                $dialog->setVariable("fldNewDomain", $this->_MailDomain->getAttribute("domain"));
            }
        }
    }
?>
