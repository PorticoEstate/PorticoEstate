<?php
/**
 * pbWebMAUI
 *
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
     * Dialog for server (lost of domains)
     * @package pbWebMAUI
     */
    class dlgViewdrops extends Dialog {
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
        function dlgViewdrops(&$application, &$parent, $name) {
            $this->Dialog($application, $parent, $name, "dlgViewdrops.tpl.html", "");
        }

        /**
         * Get menu
         *
         * @return array Menuitems. each item itself is an array
         */
        function getMenu() {
            $menu[] = array("caption"=>"Domainübersicht", "action"=>"Server");
            $menu[] = ""; //break
            $menu[] = array("caption"=>"Mailaccounts", "action"=>"ViewDomain", "params"=>array("domain"=>$this->_domain));
            $menu[] = array("caption"=>"Domainverwalter", "action"=>"DomainAdmins", "params"=>array("domain"=>$this->_domain));
            $menu[] = ""; //break
            $menu[] = array("caption"=>"Neuer Maildrop", "action"=>"EditDrop", "params"=>array("domain"=>$this->_domain));

            return $menu;
        }

        /**
         * Get title
         *
         * @return string Title for Dialog
         */
        function getTitle() {
            return "Maildrops<br>".$this->_MailDomain->getAttribute("domain");
        }

        /**
         * Set params
         *
         * @param array $params Parameters to use with this dialog
         */
        function setParams($params) {
            debug(dbgDialog, 2, "setParams in dlgAccount", $params);
            //extract parameters needed
            $this->_domain = $params["domain"];
            $this->_filter = $params["formdata"]["fldFilter"];

            //create MailDomain data object
            $this->_MailDomain = &new Maildomain($this->getApplication(), $this->_domain);
            if (!empty($this->_filter)) {
                $this->_MailDomain->setDropsFilter($this->_filter);
            }

            debug(dbgDomain, 2, "created Maildomain object", $this->_MailDomain);
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
            debug(dbgAuth,2,"dlgViewdrops::OnAuth", array("auth_param"=>$auth_param, "this->_domain"=>$this->_domain));
            switch ($auth_param["level"]) {
                case alSysadmin:
                    $this->_accesslevel = alSysadmin;
                    return 2;

                case alDomainadmin:
                case alDomainmaster:
                    if (!strcmp($auth_param["domain"], $this->_domain)) {
                        $this->_accesslevel = $auth_param["level"];
                        return 1;
                    }
                    else {
                        return 0;
                    };

                case alMailaccount:
                default:
                    return 0;
            }
        }

        /**
         * OnFormData is called with posted data from form
         *
         * @param array $formdata
         */
        function OnFormData($formdata) {
            debug (dbgData,1,"OnFormData in ViewDrops",array("formdata"=>$formdata));

            if (!empty($formdata["btnDelete"]) && $formdata["RUSureResult"] != 0) {
                $deleteMail = key($formdata["btnDelete"]);
                debug(dbgData,1,"remove mail $deleteMail");

                if ($drops = $this->_MailDomain->getAttribute("maildrops")) {
                    foreach ($drops as $drop) {
                        if ($drop->getAttribute("maildrop") == "$deleteMail") {
                            $drop->delete();
                            $deleted = true;
                        }
                    }
                }
            }

            if (deleted) {
/*
                $uri = $_SERVER["PHP_SELF"]."?action=EditDrop&maildrop=".urlencode($this->_MailDrop->getAttribute("maildrop"));
                header ("Location: ".$uri);
*/
                $this->_MailDomain->clearAttribute("maildrops");
            }
        }

        /**
         * OnPrepareDialog
         *
         * @param object $dialog
         */
        function OnDialogPrepare(&$dialog) {
            debug(dbgDomain, 2, "created OnDialogPrepare ", $this->_MailDomain);

            $dialog->setVariable("fldFilter", $this->_filter);
            $dialog->setVariable("l_distributor", $this->_application->lang("distributor"));
            $dialog->setVariable("l_mail_account", $this->_application->lang("mail/account"));

            if ($drops = $this->_MailDomain->getAttribute("maildrops")) {
                foreach ($drops as $drop) {
                    $dialog->setVariable("ListClass", "list".($i++%2));
                    $dialog->setVariable("Drop", $drop->getAttribute("maildrop"));
                    /*
                    $dialog->setVariable("LinkDrop", $_SERVER["PHP_SELF"]."?action=EditDrop".
                                                                 //"&domain=".urlencode($account->getAttribute("domain")).
                                                                 //"&uid=".urlencode($account->getAttribute("uid"))
                                                                 "&maildrop=".urlencode($drop->getAttribute("maildrop"))
                                                                 );
                    */
                    $dialog->setVariable('LinkDrop', $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'pbwebmaui.uipbwebmaui.edit_mailDrop',
                                                                                                'maildrop'   => urlencode($drop->getAttribute('maildrop'))
                                                                                               ))
                                        );
                    
                    $dialog->setVariable("btnDeleteKey", "[".$drop->getAttribute("maildrop")."][]");
                    $dialog->setVariable('imgDir', $GLOBALS['phpgw_info']['server']['webserver_url'].
                                                   '/pbwebmaui/templates/'.'default'.'/' // hardcoded :-(
                                        );
                    

                    if (is_array($mails = $drop->getAttribute("accounts"))) {
                        debug(dbgDomain, 2, "OnDialogPrepare getAttribute(mail) lieferte ", $mails);

                        foreach ($mails as $mail) {
                            $dialog->setVariable("Address", $mail);
                            $dialog->parse("Address");
                        }
                    }

                    $dialog->parse("Drop");
                }
            }

        }
    }
?>
