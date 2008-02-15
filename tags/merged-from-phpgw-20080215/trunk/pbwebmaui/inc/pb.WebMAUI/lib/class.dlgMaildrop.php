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
     * maildrop include file
     * @see maildrop
     */
    require_once "class.maildrop.php";

    /**
     * Dialog for mailaccount
     * @package pbWebMAUI
     */
    class dlgMaildrop extends Dialog {
        /**
         * Domain
         *
         * @var $_domain
         * @access private
         */
        var $_domain;
        /**
         * Mail
         *
         * @var $_mail
         * @access private
         */
        var $_mail;
        /**
         * Mail drop
         *
         * @var object $_MailDrop
         * @access private
         */
        var $_MailDrop; 
        /**
         * Access level
         *
         * @var $_accesslevel
         * @access private
         */
        var $_accesslevel;


        /**
         * Constructor
         *
         * @param $application
         * @param $parent
         * @param $name
         */
        function dlgMaildrop(&$application, &$parent, $name) {
            $this->Dialog($application, $parent, $name, "dlgMaildrop.tpl.html");
        }

        /**
         * Get menu
         *
         * @return array Menuitems. each item itself is an array
         */
        function getMenu() {
            if ($this->_accesslevel == alSysadmin) {
                $menu[] = array("caption"=>"Domainübersicht", "action"=>"Server");
                $menu[] = "";
            }

            $menu[] = array("caption"=>"Domain zeigen", "action"=>"ViewDrops", "params"=>array("domain"=>$this->_domain));
            $menu[] = array("caption"=>"Mailaccounts", "action"=>"ViewDomain", "params"=>array("domain"=>$this->_domain));

            return $menu;
        }

        /**
         * Get title
         *
         * @return string Title for Dialog
         */
        function getTitle() {
            return "Maildrop<br>".$this->_MailDrop->getAttribute("maildrop");
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
            $this->_domain = urldecode($params["domain"]);
            $this->_mail = urldecode($params["maildrop"]);

            //create MailDrop data object
            if (empty($this->_mail)) {
                $this->_MailDrop = &new MailDrop($this->getApplication(), $this->_domain, "", "", true);
            }
            else {
                $this->_MailDrop = &new MailDrop($this->getApplication(), "", $this->_mail);
            }

            $this->_domain = $this->_MailDrop->getAttribute("domain");
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
            debug(dbgAuth,2,"dlgMaildrop::OnAuth", array("auth_param"=>$auth_param, "this->_domain"=>$this->_domain));
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
         * Called with posted data from form; after authentication and before OnPrepareDialog
         *
         * @param array $formdata
         * @return array Error messages
         */
        function OnFormData($formdata) {
            $dn = $this->_MailDrop->getAttribute("dn");

            debug (dbgData,1,"OnFormData in Maildrop",array("dn"=>$dn,"formdata"=>$formdata));

            if (!empty($formdata["fldName"])) {
                $name = $formdata["fldName"]."@".$this->_MailDrop->getAttribute("domain");
                $newAccount = &new MailAccount($this->getApplication(), "", "", $name, $domain);
                $newDrop = &new MailDrop($this->getApplication(), $domain, $name);

                if (!empty($dn) || (!$newAccount->exists() && !$newDrop->exists())) {
                    $this->_MailDrop->setAttribute("maildrop", $name);
                }
                else {
                    $err[] = "Es existiert schon ein Account oder Maildrop mit der Mailadresse ".$name;
                }
            }
            else {
                $name = $this->_MailDrop->getAttribute("maildrop");
                if (empty($name))
                    $err[] = "Keine Maildropname eingegeben";
            }

            if (!empty($formdata["fldNewMail"])) {
                $mails=$this->_MailDrop->getAttribute("accounts");
                $mails[]=$formdata["fldNewMail"];
                $this->_MailDrop->setAttribute("accounts", $mails);
            }

            if (!empty($formdata["btnDelete"]) && $formdata["RUSureResult"] != 0) {
                $mails=$this->_MailDrop->getAttribute("accounts");
                $deleteMail = key($formdata["btnDelete"]);
                debug(dbgData,1,"remove mail $deleteMail");

                while (list($key,$value)=each($mails)) {
                    if ($value=="$deleteMail") {
                        debug(dbgData,1,"unset mails[$key]");
                        unset($mails[$key]);
                    }
                }

                $this->_MailDrop->setAttribute("accounts", array_values($mails));
            }

            if (empty($err) && $attrs=$this->_MailDrop->isDirty()) {
                $this->_MailDrop->save($attrs);

                if (in_array("maildrop", $attrs)) {
                    $uri = $_SERVER["PHP_SELF"]."?action=EditDrop&maildrop=".urlencode($this->_MailDrop->getAttribute("maildrop"));
                    $this->_application->redirect($uri);
                }
            }

            return $err;
        }

        /**
         * Prepare changes block
         *
         * @param object $dialog
         * @param integer $al
         * @access private
         */
        function _prepChangesBlock($dialog, $al) {
            debug(dbgAccount, 2, "_prepChangesBlock mails", array("al"=>$al));
            $changes = $this->_MailDrop->getAttribute("changes");
            $dialog->setVariable("l_name", $this->_application->lang("name"));
            $dialog->setVariable("l_mail_address", $this->_application->lang("mail address"));
            $dialog->setVariable("l_last_changes", $this->_application->lang("last changes"));

            switch ($al) {
                case alSysadmin:
                case alDomainadmin:
                case alDomainmaster:
                case alMailaccount:
                    //list comments
                    if (is_array($changes)) {
                        foreach ($changes as $time=>$user) {
                            $dialog->setVariable("fldChanges", date("d.m.Y H:i", $time) . " -- " . $user);
                            $dialog->parse("ChangesList");
                        }
                    }
                    $dialog->parse("Changes");
                    break;
            }
        }

        /**
         * OnPrepareDialog
         *
         * @param object $dialog
         */
        function OnDialogPrepare(&$dialog) {
            debug (dbgDialog, 3, "OnDialogPrepare ".__FILE__." ".__LINE__);

            $maildrop = $this->_MailDrop->getAttribute("maildrop");
            if (!empty($maildrop))
            {
            	$this->_MailDrop->clearAttribute("accounts");
              if (list($mail,$domain) = split("@", $maildrop, 2)) {
                  $dialog->setVariable("fldName", $mail);
                  $dialog->setVariable("fldDomain", $domain);
              }
              else
                  $dialog->setVariable("fldName", $maildrop);
            }
            else
            {
                $dialog->setVariable("fldDomain", $this->_MailDrop->getAttribute("domain"));
            }


            //list addresses/Aliases
            
            if ($mails = $this->_MailDrop->getAttribute("accounts")) {
                while (list($key,$value) = each($mails)) {
                    $dialog->setVariable("btnDeleteKey", "[".$value."][]");
                    $dialog->setVariable("l_delete", $this->_application->lang("delete"));
                    $dialog->setVariable("fldMail", $value);
                    $dialog->setVariable('imgDir', $GLOBALS['phpgw_info']['server']['webserver_url'].
                                                            '/pbwebmaui/templates/'.'default'.'/' // hardcoded :-(
                                        );
                    $dialog->parse("AddressesDelete");

                    $dialog->setVariable("fldMail", $value);
                    $dialog->parse("AddressesList");
                }
            }

            $this->_prepChangesBlock($dialog, $this->_accesslevel);
        }
    }
?>
