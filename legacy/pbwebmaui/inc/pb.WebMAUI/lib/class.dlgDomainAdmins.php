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
     * Dialog for maildomain, lists domainadmins
     * @package pbWebMAUI
     */
    class dlgDomainAdmins extends Dialog {
        /**
         * Domain
         *
         * @var $_domain
         * @access private
         */
        var $_domain;
         /**
         * Filter
         *
         * @var object $_filter
         * @access private
         */
        var $_filter;
        /**
         * Main domain
         *
         * @var object $_MailDomain
         * @access private
         */
        var $_MailDomain;  

        /**
         * Constructor
         *
         * @param $application
         * @param $parent
         * @param $name
         */
        function dlgDomainAdmins(&$application, &$parent, $name) {
            $this->Dialog($application, $parent, $name, "dlgDomainAdmins.tpl.html", "");
        }

        /**
         * Get menu
         *
         * @return array Menuitems. each item itself is an array
         */
        function getMenu() {
            $menu[] = array("caption"=>"Domainübersicht", "action"=>"Server");
            $menu[] = "";
            $menu[] = array("caption"=>"Mailaccounts", "action"=>"ViewDomain", "params"=>array("domain"=>$this->_domain));
            $menu[] = array("caption"=>"Maildrops", "action"=>"ViewDrops", "params"=>array("domain"=>$this->_domain));

            return($menu);
        }

        /**
         * Get title
         *
         * @return string Title for Dialog
         */
        function getTitle() {
            return "Domainverwalter<br>".$this->_MailDomain->getAttribute("domain");
        }

        /**
         * Set params
         *
         * @param array $params Parameters to use with this dialog:
         *                       - "mail": mailaddress of account to edit
         *                       - "domain": domain for new account
         */
        function setParams($params) {
            debug(dbgDomain, 2, "setParams in dlgDomain", $params);

            //extract parameters needed
            $this->_domain = $params["domain"];
            $this->_filter = $params["formdata"]["fldFilter"];

            //create MailDomain data object
            $this->_MailDomain = &new Maildomain($this->getApplication(), $this->_domain);
            if (!empty($this->_filter)) {
                $this->_MailDomain->setFilter($this->_filter);
            }

            debug(dbgDomain, 2, "created Maildomain object", $this->_MailDomain);
        }

        /**
         * Called for each applications authobject from its parent. This function should check authorization for this dialog
         *
         * @param array $auth_param Params holding the authenticated options for one authentication
         * @return integer 0, not yet authenticated to show dialog
         *                  1, authenticated to show, but request for further authentication
         *                  2, full authentification
         */
        function OnAuth($auth_param) {
            switch($auth_param["level"]) {
                case alSysadmin:
                    $this->_accesslevel = alSysadmin;
                    return 2;

                case alDomainadmin:
                case alDomainmaster:
                    if (!strcmp($auth_param["domain"], $this->_domain)) {
                        $this->_accesslevel = $auth_param["level"];
                        return 1;
                    }
                    else return 0;

                    //Fall Thru
                default:
                    return 0;
            }
        }

        /**
         * Called with posted data from form; after authentication and before OnPrepareDialog
         *
         * @param array $formdata
         * @return mixed
         */
        function OnFormData($formdata) {
            debug (dbgData,1,"OnFormData in dlgDomain",array("formdata"=>$formdata));
            if (!empty($formdata["fldNewDomainadmin"])) {
                $account = &new Mailaccount($this->getApplication(), $formdata["fldNewDomainadmin"]);
                $account->setAttribute("accesslevel", alDomainmaster);
                $account->save();
            }

            return $ret;
        }

        /**
         * OnPrepareDialog
         *
         * @param object $dialog component which is being prepared
         */
        function OnDialogPrepare(&$dialog) {
            //debug (dbgDomain, 2, "OnDialogPrepare ".__FILE__, $this->_MailDomain);
            $dialog->setVariable("fldFilter", $this->_filter);

            if ($accounts = $this->_MailDomain->getAttribute("accounts")) {
                foreach ($accounts as $account) {
                    if ($account->getAttribute("accesslevel") >= alDomainmaster) {
                        $dialog->setVariable("ListClass", "list".($i++%2));
                        $dialog->setVariable("Name", $account->getAttribute("cn"));
                        $dialog->setVariable("LinkAccount", $_SERVER["PHP_SELF"]."?action=EditAccount".
                                                                    //"&domain=".urlencode($account->getAttribute("domain")).
                                                                    //"&uid=".urlencode($account->getAttribute("uid"))
                                                                    "&dn=".urlencode($account->getAttribute("dn"))
                                                                    );

                        if (is_array($mails = $account->getAttribute("mail"))) {
                            debug(dbgDomain, 2, "OnDialogPrepare getAttribute(mail) lieferte ", $mails);

                            foreach ($mails as $mail) {
                                $dialog->setVariable("Address", $mail);
                                $dialog->parse("Address");
                            }
                        }

/*
                        $dialog->setVariable("btnDeleteKey", "[".$account->getAttribute("dn")."][]");
*/
                        switch ($account->getAttribute("accesslevel")) {
                            case alSysadmin:
                                $dialog->setVariable("Accesslevel", "Systemadmin");
                                break;
                            case alDomainadmin:
                                $dialog->setVariable("Accesslevel", "Domainsysadmin");
                                break;
                            case alDomainmaster:
                                $dialog->setVariable("Accesslevel", "Domainmaster");
                                break;
                        }
                        $dialog->parse("Account");
                    } //if accesslevel >= alDomainadmin
                    else {
                        $dialog->setVariable("NewDomainadminDN", $account->getAttribute("dn"));
                        $dialog->setVariable("NewDomainadmin", $account->getAttribute("cn"));
                        $dialog->parse("NewDomainadmin");
                    }
                }
            }
        }
    }
?>
