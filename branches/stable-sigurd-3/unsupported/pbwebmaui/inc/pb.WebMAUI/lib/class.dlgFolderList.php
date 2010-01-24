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
     * maildomain include file
     * @see maildomain
     */
    require_once "class.maildomain.php";

    /**
     * Dialog showing folders from Maildir
     * @package pbWebMAUI
     */
    class dlgFolderList extends Dialog {
        /**
         * Constructor
         *
         * @param $application
         * @param $parent
         * @param $name
         */
        function dlgFolderList(&$application, &$parent, $name) {
            $this->Dialog($application, $parent, $name, "dlgFolderList.tpl.html", "");
        }

        /**
         * Get title
         *
         * @return string Title for Dialog
         */
        function getTitle() {
            return "Ordnerliste<br>".$this->_MailAccount->getAttribute("uid")." ".$this->_MailAccount->getAttribute("domain");
        }

        /**
         * Set params
         *
         * @param array $params Parameters to use with this dialog:
         *                       - "mail": mailaddress of account to edit
         *                       - "domain": domain for new account
         */
        function setParams($params) {
            debug(dbgAccount, 2, "setParams in dlgAccount", $params);
            //extract parameters needed
            $this->_dn = urldecode($params["dn"]);
            $this->_actionId = $params["actionid"];

            //create MailAccount data object
            $this->_MailAccount = &new MailAccount($this->getApplication(), $this->_dn);
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
                    if (!strcmp($auth_param["domain"], $this->_MailAccount->getAttribute("domain"))) {
                        $this->_accesslevel = $auth_param["level"];
                        return 1;
                    }
                    else return 0;

                case alMailaccount:
                    if (in_array($auth_param["account"], $this->_MailAccount->getAttribute("mail"))) {
                        $this->_accesslevel = $auth_param["level"];
                        return 1;
                    }
                    else return 0;

                default:
                    return 0;
            }
        }

        /**
         * On Dialog Prepare
         *
         * @param object $dialog Component which is being prepared
         */
        function OnDialogPrepare(&$dialog) {
            $folders = $this->_MailAccount->getAttribute("folders");
            if (is_array($folders)) {
                foreach ($folders as $folder) {
                    $foldername = substr($folder,1); //i'd like to split foldernames somewhen later;
                    $dialog->setVariable("ListClass", "list".($i++ % 2));
                    $dialog->setVariable("ActionId", $this->_actionId);
                    $dialog->setVariable("Folder", substr($folder,1));
                    $dialog->setVariable("FolderName", $foldername);
                    $dialog->parse("Folder");
                }
            }
        }
    }
?>
