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
     * mailaccount include file
     * @see mailaccount
     */
    require_once "class.mailaccount.php";
    /**
     * maildrop include file
     * @see maildrop
     */
    require_once "class.maildrop.php";

    /**
     * Dialog for mailaccount moving
     * @package pbWebMAUI
     */
    class dlgMoveAccount extends Dialog {
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
        function dlgMoveAccount(&$application, &$parent, $name) {
            $this->Dialog($application, $parent, $name, "dlgMoveAccount.tpl.html");
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

            $menu[] = array("caption"=>"Domain zeigen", "action"=>"ViewDomain", "params"=>array("domain"=>$this->_MailAccount->getAttribute("domain")));
            return $menu;
        }

        /**
         * Get title
         *
         * @return string Title for Dialog
         */
        function getTitle() {
            return "Mailaccount verschieben<br>".$this->_MailAccount->getAttribute("uid")." ".$this->_MailAccount->getAttribute("domain");
        }

        /**
         * Set params
         *
         * @param array $params Parameters to use with this dialog:
         *                       - "mail": mailaddress of account to edit
         *                       - "domain": domain for new account
         */
        function setParams($params) {
            //extract parameters needed
            $this->_dn = urldecode($params["dn"]);
            $this->_domain = urldecode($params["domain"]);
            $this->_uid = urldecode($params["uid"]);

            $this->_MailAccount = &new MailAccount($this->getApplication(), $this->_dn);
            if (empty($this->_domain))
                $this->_domain = $this->_MailAccount->getAttribute("domain");
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

                default:
                    return 0;
            }
        }

        /**
         * Called with posted data from form; after authentication and before OnPrepareDialog
         *
         * @param $formdata
         */
        function OnFormData($formdata) {
            //redirect to self with get params for uid and new domain, so we can check authentification
            $dn = $this->_MailAccount->getAttribute("dn");
            debug (dbgData,1,"OnFormData in dlgAccount",array("dn"=>$dn,"formdata"=>$formdata));

            $newdomain = $formdata["fldNewDomain"];
            $newuid = $formdata["fldNewUid"];

            $uri = $_SERVER["PHP_SELF"]."?action=MoveAccount&dn=".urlencode($this->_MailAccount->getAttribute("dn"));
            $uri .= "&uid=".urlencode($newuid);
            $uri .= "&domain=".urlencode($newdomain);
            $this->_application->redirect($uri);
        }

        /**
         * Move account
         *
         * @param string $uid User id
         * @param string $domain Domain name
         * @return array
         */
        function MoveAccount($uid, $domain) {
            $mail = $uid."@".$domain;
            $newAccount = &new MailAccount($this->getApplication(), "", "", $mail, $domain);
            $newDrop = &new MailDrop($this->getApplication(), $domain, $mail);

            if (!$newAccount->exists() && !$newDrop->exists()) {
                //set attributes of new account
                $newAccount->setAttribute("cn", $this->_MailAccount->getAttribute("cn"));
                $newAccount->setAttribute("mail", array($mail), true); //set again to set dirty flag
                $newAccount->setAttribute("cryptpassword", $this->_MailAccount->getAttribute("cryptpassword"));
                $newAccount->setAttribute("md", $this->_MailAccount->getAttribute("md"));
                $newAccount->setAttribute("bukr", $this->_MailAccount->getAttribute("bukr"));
                $newAccount->setAttribute("kstl", $this->_MailAccount->getAttribute("kstl"));
                $newAccount->setAttribute("quota", $this->_MailAccount->getAttribute("quota"));
                $newAccount->setAttribute("inetaccess", $this->_MailAccount->getAttribute("inetaccess"));
                $newAccount->save();

                //copy maildrops
                $maildrops = $this->_MailAccount->getAttribute("maildrops");
                if (!empty($maildrops)) {
                    foreach ($maildrops as $maildrop) {
                        $mdtargets = $maildrop->getAttribute("accounts");
                        $mdtargets[] = $mail;
                        $maildrop->setAttribute("accounts", $mdtargets);
                        $maildrop->save();
                    }
                }

                //save aliases
                $newaliases[] = $mail;
                $oldaliases = $this->_MailAccount->getAttribute("mail");

                //remove account
                $delresult = $this->_MailAccount->delete();
                if ($delresult["state"] == 1) {
                    $archive = $delresult["archive"];
                    debug(dbgData,1,"archive", $archive);
                }
                else {
                    $result[] = $delresult["message"];
                }

                //copy old aliases
                for ($i=0; $i<count($oldaliases); $i++) {
                    debug(dbgData,1,"check alias",array("aliases"=>$oldaliases, "i"=>$i));
                    $alias = $oldaliases[$i];
                    if ("$alias" != $this->_MailAccount->getAttribute("uid")."@".$this->_MailAccount->getAttribute("domain")) {
                        list($oldname,$olddomain) = split("@", $alias);
                        if (ereg("([^0-9]*)([0-9]*)", $oldname, $oldname_reg)) {
                            debug(dbgData,1,"split alias true",array($oldname,$oldname_reg));
                            $oldname = $oldname_reg[1];
                            $alias_lfn = $oldname_reg[2];
                        }
                        else {
                            debug(dbgData,1,"split alias false",array($oldname,$oldname_reg));
                            $alias_lfn = "";
                        }

                        do {
                            if (empty($olddomain)) {
                                $newalias = $oldname.$alias_lfn;
                            }
                            else if ("$olddomain" == $this->_MailAccount->getAttribute("domain")) {
                                $newalias = $oldname.$alias_lfn."@".$domain;
                            }
                            else {
                                $newalias = $oldname.$alias_lfn."@".$olddomain;
                            }

                            $chkAlias = &new MailAccount($this->getApplication(), "", "", $newalias, $domain);
                            if ($chkAlias->exists()) {
                                $alias_lfn += 1;
                            }
                        } while ($chkAlias->exists());

                        $newaliases[] = $newalias;
                    }
                }

                debug(dbgData,1,"new aliases",$newaliases);
                $newAccount->setAttribute("mail", $newaliases);
                $newAccount->save();

                //import account archive
                if (!empty($archive)) {
                    $newAccount->importmaildir($archive);
                }

                $uri = $_SERVER["PHP_SELF"]."?action=EditAccount"."&dn=".urlencode($newAccount->getAttribute("dn"));
                $this->_application->redirect($uri);
            }
            else {
                $result[] = "Es existiert schon ein Account oder Maildrop mit der Mailadresse ".$mail;
            }

            return $result;
        }

        /**
         * OnDialogPrepare
         *
         * @param object $dialog
         */
        function OnDialogPrepare(&$dialog) {
            if (!empty($this->_uid) && !empty($this->_domain)) {
                $moveresult = $this->MoveAccount($this->_uid, $this->_domain);

                if (is_array($moveresult)) {
                    foreach($moveresult as $comment) {
                        $dialog->setVariable("Comment", $comment);
                        $dialog->parse("Comment");
                    }
                }
            }

            $server = & new Mailserver($this->getApplication());
            $domains = $server->getAttribute("domains");
            if (is_array($domains)) {
                foreach ($domains as $domain) {
                    $domainname = $domain->getAttribute("domain");
                    $dialog->setVariable("fldNewDomain", $domainname);
                    $dialog->setVariable("fldNewDomainSelected", $this->_domain=="$domainname"?"SELECTED":"");

                    $dialog->parse("DomainList");
                }
            }

            $dialog->setVariable("fldOldMail", $this->_MailAccount->getAttribute("uid")."@".$this->_MailAccount->getAttribute("domain"));
            $dialog->setVariable("fldNewUid", $this->_uid);
        }
    }
?>
