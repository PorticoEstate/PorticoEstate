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
     * Dialog for mailaccounts additional parameters
     * @package pbWebMAUI
     */
    class dlgAccountAdd extends Dialog {
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
        var $_MailAccount;  //Data Object
        /**
         * Main domain
         *
         * @var object $_MailDomain
         * @access private
         */
        var $_MailDomain;   //Data Object;
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
        function dlgAccountAdd(&$application, &$parent, $name) {
            $this->Dialog($application, $parent, $name, "dlgAccountAdd.tpl.html");
            
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

            if ($this->_accesslevel >= alDomainmaster) {
                $menu[] = array("caption"=>"Domain zeigen", "action"=>"ViewDomain", "params"=>array("domain"=>$this->_domain));
                $menu[] = "";
            }

            $menu[] = array("caption"=>"Account", "action"=>"EditAccount", "params"=>array("dn"=>$this->_MailAccount->getAttribute("dn")));
            return ($menu);
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
         * @param array $params Parameters to use with this dialog:
         *                       - "mail": mailaddress of account to edit
         *                       - "domain": domain for new account
         */
        function setParams($params) {
            debug(dbgAccount, 2, "setParams in dlgAccount", $params);
            //extract parameters needed
            $this->_dn = urldecode($params["dn"]);
            $this->_mail = urldecode($params["mail"]);

            //create MailAccount data object
            $this->_MailAccount = &new MailAccount($this->getApplication(), $this->_dn, "", $this->_mail);
            $this->_domain = $this->_MailAccount->getAttribute("domain");
            $this->_uid = $this->_MailAccount->getAttribute("uid");

            //create MailDomain data object
            $this->_MailDomain = &new MailDomain($this->getApplication(), $this->_MailAccount->getAttribute("domain"));
            $this->_MailDomain->setFilter($this->_MailAccount->getAttribute("domain"));
        }

        /**
         * Prepare quota
         *
         * @param object $dialog
         * @param integer $al
         */
        function _prepQuota($dialog, $al) {
            //global $quota;

            while (list($key, $value) = each($quota)) {
                $dialog->setVariable("QuotaValue", $key);
                $dialog->setVariable("QuotaText", ($value["size"] >= 0) ? $value["size"]."MB" : "unbegrenzt");
                $dialog->setVariable("QuotaSelected", $this->_thisorthat("quota") == $key?"SELECTED":"");
                $dialog->parse("QuotaList");
            }
        }


        /**
         * Prepare internet access
         *
         * @param object $dialog
         * @param integer $al
         */
        function _prepInternetaccess($dialog, $al) {
            $ia = $this->_thisorthat("inetaccess");

            switch ($ia) {
                case "00000000":
                    if ($al >= alDomainadmin) {
                        $dialog->setVariable("InternetaccessSel0", "checked");
                    }
                    else {
                        $dialog->setVariable("fldInternetaccess", "Kein Zugriff");
                    }
                    break;

                case "99999999":
                    if ($al >= alDomainadmin) {
                        $dialog->setVariable("InternetaccessSel9", "checked");
                    }
                    else {
                        $dialog->setVariable("fldInternetaccess", "Unbegrenzter Zugriff");
                    }
                    break;

                default:
                    $y = substr($ia, 0, 4);
                    $m = substr($ia, 4, 2);
                    $d = substr($ia, 6, 2);
                    $dt = mktime(0,0,0,$m,$d,$y);

                    if ($al >= alDomainadmin) {
                        $dialog->setVariable("InternetaccessSelDate", "checked");
                        $dialog->setVariable("InternetaccessDate", date("d.m.Y", $dt));
                    }
                    else {
                        $dialog->setVariable("fldInternetaccess", "Zugriff bis ".date("d.m.Y", $dt));
                    }
            }
        }

        /**
         * Attr value from local MailAccount or its default from local Maildomain
         *
         * @param string $attr attribute name
         * @return string value of attribute
         * @access private
         */
        function _thisorthat($attr) {
            $value = $this->_MailAccount->getAttribute($attr);
            if (!strlen($value))
                $value = $this->_MailDomain->getAttribute($attr);
            
            return $value;
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
                    else return 0;

                case alMailaccount:
                default:
                    return 0;
            }
        }

        /**
         * OnFormData is called with posted data from form; called after authentication and before OnPrepareDialog
         *
         * @param array $formdata
         */
        function OnFormData($formdata) {
            $dn = $this->_MailAccount->getAttribute("dn");
            debug (dbgData,1,"OnFormData in dlgAccountAdd",array("dn"=>$dn,"formdata"=>$formdata));

            if (trim($formdata["fldMD"]) == trim($this->_MailDomain->getAttribute("md"))) {
                $this->_MailAccount->setAttribute("md", "");
            }
            else {
                $this->_MailAccount->setAttribute("md", $formdata["fldMD"]);
            }

            if (trim($formdata["fldBUKR"]) == trim($this->_MailDomain->getAttribute("bukr"))) {
                $this->_MailAccount->setAttribute("bukr", "");
            }
            else {
                $this->_MailAccount->setAttribute("bukr", $formdata["fldBUKR"]);
            }

            if (trim($formdata["fldKSTL"]) == trim($this->_MailDomain->getAttribute("kstl"))) {
                $this->_MailAccount->setAttribute("kstl", "");
            }
            else {
                $this->_MailAccount->setAttribute("kstl", $formdata["fldKSTL"]);
            }

            if (trim($formdata["fldQuota"]) == trim($this->_MailDomain->getAttribute("quota"))) {
                $this->_MailAccount->setAttribute("quota", "");
            }
            else {
                $this->_MailAccount->setAttribute("quota", $formdata["fldQuota"]);
            }

            if (!empty($formdata["btnDeleteQuotaWarning"]) && $formdata["RUSureResult"] != 0) {
                $this->_MailAccount->setAttribute("quotawarnings", "0");
            }

            if ($formdata["fldInternetaccess"] == "date") {
                list($d,$m,$y) = split("[/.-]", $formdata["fldInternetaccessDate"]);
                if ($y<100) $y+=2000;
                $ia = sprintf("%04d%02d%02d", $y,$m,$d);
                debug(dbgData, 1, "inetaccess calculated", array("d"=>$d,"m"=>$m,"y"=>$y,"results in"=>$ia));
            }
            else {
                $ia = $formdata["fldInternetaccess"];
            }

            if (trim($ia) == trim($this->_MailDomain->getAttribute("inetaccess"))) {
                $this->_MailAccount->setAttribute("inetaccess", "");
            }
            else {
                $this->_MailAccount->setAttribute("inetaccess", $ia);
            }

            if ($attrs=$this->_MailAccount->isDirty()) {
                $attrs=array(/*"accesslevel",*/"quota","inetaccess","md","bukr","kstl","quotawarnings");
                $this->_MailAccount->save($attrs);
            }
        }

        /**
         * On Dialog Prepare
         *
         * @param object $dialog
         */
        function OnDialogPrepare(&$dialog) {
            debug (dbgDialog, 3, "OnDialogPrepare ".__FILE__." ".__LINE__);

            //Mandant
            $dialog->setVariable("fldMD", $this->_thisorthat("md"));
            if ($this->_accesslevel == alSysadmin) {
                $dialog->parse("SysadminMandant");
            }
            else {
                $dialog->parse("DomainadminMandant");
            }

            //BUKR,KSTL
            $dialog->setVariable("fldBUKR", $this->_thisorthat("bukr"));
            $dialog->setVariable("fldKSTL", $this->_thisorthat("kstl"));

            //Quota
            $this->_prepQuota($dialog, $this->_accesslevel);

            $count_warnings = $this->_MailAccount->getAttribute("quotawarnings")." ";
            if (strlen($count_warnings)) {
                if ($this->_accesslevel >= alDomainadmin) {
                    $dialog->setVariable("QuotaWarningCountDelete", $count_warnings);
                    $dialog->parse(" QuotaWarningDelete");
                }
                else {
                    $dialog->setVariable("QuotaWarningCount", $count_warnings);
                    $dialog->parse(" QuotaWarning");
                }
            }

            //InetAccess
            $this->_prepInternetaccess($dialog, $this->_accesslevel);
            if ($this->_accesslevel >= alDomainadmin) {
                $dialog->parse("EditInternetaccess");
            }
            else {
                $dialog->parse("ViewInternetaccess");
            }
        }
    }
?>
