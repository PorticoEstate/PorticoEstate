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

    // data classes needed
    /**
     * mailserver include file
     * @see mailserver
     */
    require_once "class.mailserver.php";
    /**
     * maildomain include file
     * @see maildomain
     */
    require_once "class.maildomain.php";

    /**
     * Dialog for server (lost of domains)
     * @package pbWebMAUI
     */
    class dlgServer extends Dialog {
        /**
         * Filter
         *
         * @var $_filter
         * @access private
         */
        var $_filter;
        /**
         * mail server
         *
         * @var object $_MailServer
         * @access private
         */
        var $_MailServer; 


        /**
         * Constructor
         *
         * @param $application
         * @param $parent
         * @param $name
         */
        function dlgServer(&$application, &$parent, $name) {
            $this->Dialog($application, $parent, $name, "dlgServer.tpl.html", "");
        }

        /**
         * Get menu
         *
         * @return array Menuitems. each item itself is an array
         */
        function getMenu() {
            $menu[] = array("caption"=>"Neue Domain", "action"=>"NewDomain");
            $menu[] = "";
            $menu[] = array("caption"=>"Speicheranzeige", "action"=>"ViewQuota");
            $menu[] = array("caption"=>"Quota-Warnung", "action"=>"WarnQuota");
            $menu[] = array("caption"=>"Billing", "action"=>"Billing");

            return ($menu);
        }

        /**
         * Set params
         *
         * @param array $params Parameters to use with this dialog:
         *                       - "mail": mailaddress of account to edit
         *                       - "domain": domain for new account
         */
        function setParams($params) {
            debug(dbgServer, 2, "setParams in dlgServer", $params);

            //extract parameters needed
            $this->_filter = $params["formdata"]["fldFilter"];

            //create MailServer data object
            $this->_MailServer = &new Mailserver($this->getApplication());
            if (!empty($this->_filter)) {
                $this->_MailServer->setFilter($this->_filter);
            }

            debug(dbgServer, 2, "created Mailserver object", $this->_MailServer);
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
                    return 1;

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
            debug (dbgData,1,"OnFormData in dlgServer", array("formdata"=>$formdata));
            if (!empty($formdata["btnDelete"]) && $formdata["RUSureResult"] != 0) {
                $deldomain = key($formdata["btnDelete"]);

                $domain = &new MailDomain($this->getApplication(), $deldomain);

                //remove domain
                $archive = $domain->delete();
                $ret = array("Die Domain wurde in ".$archive." archiviert.");
            }

            return $ret;
        }

        /**
         * On Dialog Prepare
         *
         * @param object $dialog
         */
        function OnDialogPrepare(&$dialog) {
            //debug (dbgDialog, 3, "OnDialogPrepare ".__FILE__." ".__LINE__);
            $dialog->setVariable("fldFilter", $this->_filter);

            if (is_array($domains = $this->_MailServer->getAttribute("domains"))) {
                foreach ($domains as $domain) {
                    $dialog->setVariable("ListClass", "list".($i++%2));
                    $dialog->setVariable("Domain", $domain->getAttribute("domain"));
                    $dialog->setVariable("LinkDomain", $_SERVER["PHP_SELF"]."?action=ViewDomain".
                                                                 "&domain=".urlencode($domain->getAttribute("domain"))
                                                                 );
                    
                    if ($this->_accesslevel >= alSysadmin) {
                        $dialog->setVariable("btnDeleteKey","[".$domain->getAttribute("domain")."][]");
                    }
                    $dialog->parse("Domain");
                }
            }
        }
    }
?>
