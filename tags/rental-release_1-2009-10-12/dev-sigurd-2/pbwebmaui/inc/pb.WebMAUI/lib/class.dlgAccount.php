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
     * mailaccount include file
     * @see mailaccount
     */
    require_once "class.mailaccount.php";
    /**
     * maildomain include file
     * @see maildomain
     */
    require_once "class.maildomain.php";
    /**
     * maildrop include file
     * @see maildrop
     */
    require_once "class.maildrop.php";

    /**
     * Dialog for mailaccount
     * @package pbWebMAUI
     */
    class dlgAccount extends Dialog {
        /**
         * Domain
         *
         * @var $_domain
         * @access private
         */
        var $_domain;
        /**
         * User id
         *
         * @var $_uid
         * @access private
         */
        var $_uid;
        /**
         * Distingushed name
         *
         * @var $_dn
         * @access private
         */
        var $_dn;
        /**
         * Main account
         *
         * @var object $_MailAccount
         * @access private
         */
        var $_MailAccount;
        /**
         * Main domain
         *
         * @var object $_MailDomain
         * @access private
         */
        var $_MailDomain;
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
        function dlgAccount(&$application, &$parent, $name) {
            $this->Dialog($application, $parent, $name, "dlgAccount.tpl.html", array(1));
        }

        /**
         * Get menu
         *
         * @return array Menuitems. Each item itself is an array
         */
        function getMenu() {
            debug (dbgAccount,1,"getMenu in dlgAccount, accesslevel=".$this->_accesslevel);
            $uid = $this->_MailAccount->getAttribute("uid");
            debug (dbgAccount, 2, "using Mailaccount object", array("object"=>$this->_MailAccount,"uid"=>$uid));

            if ($this->_accesslevel == alSysadmin) {
                $menu[] = array("caption"=>"Domainübersicht", "action"=>"Server");
                $menu[] = "";
            }

            if ($this->_accesslevel >= alDomainmaster) {
                $menu[] = array("caption"=>"Domain zeigen", "action"=>"ViewDomain", "params"=>array("domain"=>$this->_domain));
                $menu[] = array("caption"=>"Maildrops", "action"=>"ViewDrops", "params"=>array("domain"=>$this->_domain));
                $menu[] = array("caption"=>"Domainverwalter", "action"=>"DomainAdmins", "params"=>array("domain"=>$this->_domain));
                $menu[] = "";
                if (strlen($uid)) {
                    $menu[] = array("caption"=>"Umbennenen/Verschieben", "action"=>"MoveAccount", "params"=>array("dn"=>$this->_MailAccount->getAttribute("dn")));
                    $menu[] = "";
                    $menu[] = array("caption"=>"Zusätzliche Einstellungen", "action"=>"EditAccountAdd", "params"=>array("dn"=>$this->_MailAccount->getAttribute("dn")));
                }
            }

            if (strlen($uid)) {
                $menu[] = array("caption"=>"Mailfilter & Abwesenheit", "action"=>"FilterList", "params"=>array("dn"=>$this->_MailAccount->getAttribute("dn")));
                $menu[] = array("caption"=>"Speichernutzung", "action"=>"AccountQuota", "params"=>array("dn"=>$this->_MailAccount->getAttribute("dn")));
            }

            return $menu;
        }

        /**
         * Get title
         *
         * @return string Title for Dialog
         */
        function getTitle() {
            return "Mailaccount<br>".$this->_MailAccount->getAttribute("uid")." ".$this->_MailAccount->getAttribute("domain");
        }

        /**
         * Set params
         *
         * @param array $params array of parameters to use with this dialog:
         *                       - "mail": mailaddress of account to edit
         *                       - "domain": domain for new account
         */
        function setParams($params) {
            debug(dbgAccount, 1, "setParams in dlgAccount", $params);

            //extract parameters needed
            $this->_domain = urldecode($params["domain"]);

            $this->_dn = urldecode($params["dn"]);
            $this->_mail = urldecode($params["mail"]);

            //create MailAccount data object
            if (empty($this->_domain)) {
                $this->_MailAccount = &new MailAccount($this->getApplication(), $this->_dn, "", $this->_mail);
                $this->_domain = $this->_MailAccount->getAttribute("domain");
                $this->_uid = $this->_MailAccount->getAttribute("uid");
            }
            else {
                $this->_MailAccount = &new MailAccount($this->getApplication(), "", "", "", $this->_domain);
                $this->_domain = $this->_MailAccount->getAttribute("domain");
            }

            debug (dbgAccount, 2, "created Mailaccount object", $this->_MailAccount);

            //create MailDomain data object
            $this->_MailDomain = &new MailDomain($this->getApplication(), $this->_MailAccount->getAttribute("domain"));
            $this->_MailDomain->setFilter($this->_MailAccount->getAttribute("domain"));
        }

        /**
         * Prepare address block
         *
         * 
         * @param object $dialog
         * @param integer $al
         */
        function _prepAddressBlock($dialog, $al) {
            switch ($al) {
                case alSysadmin:
                case alDomainadmin:
                case alDomainmaster:
                    //may edit addresses/Aliases
                    $dialog->setVariable("fldNewDomain", $this->_MailAccount->getAttribute("domain"));
                    $dialog->parse("AddressesEdit");
                    /* Fall Thru */

                case alMailaccount:
                    //list addresses/Aliases
                    debug(dbgAccount, 2, "_prepAdressBlock mails", $this->_MailAccount->getAttribute("mail"));
                    if ($mails = $this->_MailAccount->getAttribute("mail")) {
                        while (list($key,$value) = each($mails)) {
                            if (!empty($value)) {
                                if ($al > alMailAccount) {
                                    $dialog->setVariable("btnDeleteMailKey", "[".$value."][]");
                                    $dialog->setVariable("l_delete", $this->_application->lang("delete"));
                                    $dialog->setVariable("fldMail", $value);
                                    $dialog->setVariable('imgDir', $GLOBALS['phpgw_info']['server']['webserver_url'].
                                                         '/pbwebmaui/templates/'.'default'.'/' // hardcoded :-(
                                                        );
                                    $dialog->parse("AddressesDelete");
                                }

                                $dialog->setVariable("fldMail", $value);
                                $dialog->parse("AddressesList");
                            }
                        }
                    }
                    break;
            }
        }

        /**
         * Prepare drops block
         *
         * @param object $dialog
         * @param integer $al
         */
        function _prepDropsBlock($dialog, $al) {
            switch ($al) {
                case alSysadmin:
                case alDomainadmin:
                case alDomainmaster:
                    //may edit addresses/Aliases
/* deactivated because of problems adding and deleting data
                    if ($maildrops = $this->_MailDomain->getAttribute("maildrops")) {
                        foreach ($maildrops as $maildrop) {
                            //$maildrop = array(...,"mail"=>array("count"=>,"0"=>name))
                            $dialog->setVariable("fldNewMaildrop", $maildrop->getAttribute("maildrop"));
                            $dialog->parse("DropsEditList");
                        }
                    }
                    $dialog->parse("DropsEdit");
*/
                    /* Fall Thru */
                case alMailaccount:
                    //list addresses/Aliases
                    if (is_array($maildrops = $this->_MailAccount->getAttribute("maildrops"))) {
                        while (list($key, $value) = each($maildrops)) {
/* deactivated because of problems adding and deleting data
                            if ($al > alMailAccount) {
                                $dialog->setVariable("fldMaildropIdx", $key);
                                $dialog->parse("DropsDelete");
                            }
*/
                            $dialog->setVariable("fldMaildrop", $value->getAttribute("maildrop"));
                            $dialog->parse("DropsList");
                        }
                    }

                    break;
            }
        }

        /**
         * Prepare access level block
         *
         * @param object $dialog
         * @param integer $al
         */
        function _prepAccesslevelBlock($dialog, $al) {
            $alAccount = $this->_MailAccount->getAttribute("accesslevel");
            switch ($al) {
                case alSysadmin:
                    $dialog->setVariable(array("AccesslevelValue"=>alSysadmin,
                                            "AccesslevelOption"=>"Systemadmin",
                                            "AccesslevelSelected"=>($alAccount==alSysadmin)?"SELECTED":""));
                    $dialog->parse("AccesslevelOptions");

                case alDomainadmin:
                    $dialog->setVariable(array("AccesslevelValue"=>alDomainadmin,
                                            "AccesslevelOption"=>"Domainsysadmin",
                                            "AccesslevelSelected"=>($alAccount==alDomainadmin)?"SELECTED":""));
                    $dialog->parse("AccesslevelOptions");

                case alDomainmaster:
                    $dialog->setVariable(array("AccesslevelValue"=>alDomainmaster,
                                            "AccesslevelOption"=>"Domainmaster",
                                            "AccesslevelSelected"=>($alAccount==alDomainmaster)?"SELECTED":""));
                    $dialog->parse("AccesslevelOptions");

                case alMailaccount:
                    $dialog->setVariable(array("AccesslevelValue"=>alMailaccount,
                                            "AccesslevelOption"=>"Mailaccount",
                                            "AccesslevelSelected"=>($alAccount==alMailaccount)?"SELECTED":""));
                    $dialog->parse("AccesslevelOptions");
            }
        }

        /**
         * Prepare comments block
         *
         * @param object $dialog
         * @param integer $al
         */
        function _prepCommentsBlock($dialog, $al) {
            debug(dbgAccount, 2, "_prepCommentsBlock mails", array("al"=>$al));
            //$text = htmlentities("not yet implemented"); //
            $text = htmlentities($this->_MailAccount->getAttribute("comment")." ");

            switch ($al) {
                case alSysadmin:
                case alDomainadmin:
                case alDomainmaster:
                    //may edit comments
                    $dialog->setVariable("fldCommentEdit", $text);
                    $dialog->setVariable("l_comment1", $this->_application->lang("comment"));
                    $dialog->parse("CommentsEdit");
                    break;

                case alMailaccount:
                    //list comments
                    $dialog->setVariable("fldComment", $text);
                    $dialog->setVariable("l_comment2", $this->_application->lang("comment"));
                    $dialog->parse("CommentsList");
                    break;
            }
        }

        /**
         * Prepare changes block
         *
         * @param object $dialog
         * @param integer $al
         */
        function _prepChangesBlock($dialog, $al) {
            debug(dbgAccount, 2, "_prepChangesBlock mails", array("al"=>$al));
            $changes = $this->_MailAccount->getAttribute("changes");
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
         * Called for each applications auth object from its parent. This function should check authorization for this dialog
         *
         * @param array $auth_param Authenticated options parameter for one authentication
         * @return integer 0, not yet authenticated to show dialog
         *                  1, authenticated to show, but request for further authentication
         *                  2, full authentification
         */
        function OnAuth($auth_param) {
            debug(dbgAuth,1,"OnAuth in dlgAccount", array("auth_param"=>$auth_param, "this->_accesslevel"=>$this->_accesslevel));

            switch ($auth_param["level"]) {
                case alSysadmin:
                    $this->_accesslevel = alSysadmin;
                    return 2;

                case alDomainadmin:
                case alDomainmaster:
                    //if (!strcmp($auth_param["domain"], $this->_MailAccount->getAttribute("domain"))) {
                        $this->_accesslevel = $auth_param["level"];
                        return 1;
                    //}
                    //else return 0;

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
         * Called with posted data from form; called after authentication and before OnPrepareDialog
         *
         * @param array $formdata
         * @return array Error messages
         */
        function OnFormData($formdata) {
        	  $dn = $this->_MailAccount->getAttribute('dn');
            $cn = $this->_MailAccount->getAttribute('cn');
            debug (dbgData,1,"OnFormData in dlgAccount",array("dn"=>$dn,"formdata"=>$formdata));

            if (!empty($formdata["fldPassword"])) {
                $pw1 = $formdata["fldPassword"];
                $pw2 = $formdata["fldPassword2"];

                if ("$pw1" != "$pw2") {
                    $err[] = "Passwortwiederholung nicht identisch mit Passwort";
                }
                $this->_MailAccount->setAttribute("password", $formdata["fldPassword"]);
            }

            if (!empty($formdata["fldName"])) {
                $this->_MailAccount->setAttribute("cn", $formdata["fldName"]);
            }

            if (!empty($formdata["fldAliases"])) {
                $this->_MailAccount->setAttribute("mail", $formdata["fldAliases"]);
            }

            if (!empty($formdata["fldNewMail"])) {
                $newmail = $formdata["fldNewMail"].(!empty($formdata["fldNewMaildomain"])?"@".$formdata["fldNewMaildomain"]:"");

                //check for existing mail or maildrop with this name
                $domain = $this->_domain;
                $chkAccount = &new MailAccount($this->getApplication(), "", "", $newmail, $domain);
                $chkDrop = &new MailDrop($this->getApplication(), $domain, $newmail);

                if (!$chkAccount->exists() && !$chkDrop->exists()) {
                    $mails=$this->_MailAccount->getAttribute("mail");
                    $mails[]=$newmail;

                    $this->_MailAccount->setAttribute("mail", $mails);
                }
                else {
                    $err[] = "Es existiert schon ein Account oder Maildrop mit der Mailadresse ".$newmail;
                }
            }
            else {
                $mails = $this->_MailAccount->getAttribute("mail");
                debug (dbgData,1,"mails",array("mails"=>$mails, "count"=>count($mails)));
                if (empty($mails[0])) {
                    $err[] = "Keine Mailadresse angegeben";
                }
            }

            if (strlen($formdata["fldAccesslevel"])) {
                $this->_MailAccount->setAttribute("accesslevel", $formdata["fldAccesslevel"]);
            }
            
            if (!$this->_MailAccount->getAttribute('uidnumber'))
            {
            	$this->_MailAccount->setAttribute('uidnumber', -1);
            }
            
            if (!$this->_MailAccount->getAttribute('gidnumber'))
            {
            	$this->_MailAccount->setAttribute('gidnumber', -1);
            }

            
            $this->_MailAccount->setAttribute("comment", trim($formdata["fldComment"]));

            if (!empty($formdata["btnDeleteMail"]) && $formdata["RUSureResult"] != 0) {
                $deleteMail = key($formdata["btnDeleteMail"]);
                debug(dbgData,1,"remove mail $deleteMail");
                $delresult = $this->_MailAccount->removealias($deleteMail, false);
                if (!empty($delresult)) {
                    $err[] = $delresult;
                }
            }

            if (empty($err) && $attrs=$this->_MailAccount->isDirty()) {
                $saveresult = $this->_MailAccount->save($attrs);
                if (empty($saveresult)) {
                    if (empty ($formdata["btnOkNew"]))
                        $uri = $_SERVER["PHP_SELF"]."?action=EditAccount&dn=".urlencode($this->_MailAccount->getAttribute("dn"));
                    else
                    {
                        $uri = $_SERVER["PHP_SELF"]."?action=EditAccount&domain=".$this->_domain;
                    }
                    $this->_application->redirect($uri/*, true*/);
                }
                else {
                    $err[] = $saveresult;
                }
            }

            return ($err);
        }

        /**
         * On dialog prepare
         *
         * @param object $dialog
         */
        function OnDialogPrepare(&$dialog) {
            debug (dbgApplication, 2, "dlgAccount::OnDialogPrepare()", array("name"=>$this->_name, "getUsername"=>$this->_application->getUsername()) );
            debug (dbgDialog,1,"OnDialogPrepare in dlgAccount, accesslevel=".$this->_accesslevel);
            debug (dbgDialog, 3, "OnDialogPrepare ".__FILE__." ".__LINE__);
            $dialog->setVariable("fldName", $this->_MailAccount->getAttribute("cn"));
            $dialog->setVariable("l_name", $this->_application->lang("name"));
            $dialog->setVariable("l_password", $this->_application->lang("password"));
            $dialog->setVariable("l_repeat", $this->_application->lang("repeat"));
            $dialog->setVariable("l_mail_address", $this->_application->lang("email address"));
            $dialog->setVariable("l_maildrops", $this->_application->lang("maildrops"));
            $dialog->setVariable("l_accesslevel", $this->_application->lang("accesslevel"));         
            
            $this->_prepAddressBlock($dialog, $this->_accesslevel);
            $this->_prepDropsBlock($dialog, $this->_accesslevel);
            $this->_prepAccesslevelBlock($dialog, $this->_accesslevel);
            $this->_prepCommentsBlock($dialog, $this->_accesslevel);
            $this->_prepChangesBlock($dialog, $this->_accesslevel);
        }
    }
?>
