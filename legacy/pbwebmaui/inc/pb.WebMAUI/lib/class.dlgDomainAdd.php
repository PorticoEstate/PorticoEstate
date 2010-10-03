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
    class dlgDomainAdd extends Dialog {
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
        function dlgDomainAdd(&$application, &$parent, $name) {
            $this->Dialog($application, $parent, $name, "dlgDomainAdd.tpl.html");
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

            $menu[] = array("caption"=>"Domain zeigen", "action"=>"ViewDomain", "params"=>array("domain"=>$this->_domain));
            
            return($menu);
        }

        /**
         * Get title
         *
         * @return string Title for Dialog
         */
        function getTitle() {
            return "Maildomain<br>".$this->_MailDomain->getAttribute("domain");
        }

        /**
         * Set params
         *
         * @param array $params Parameters to use with this dialog:
         *                       - "mail": mailaddress of account to edit
         *                       - "domain": domain for new account
         */
        function setParams($params) {
            debug(dbgDomain, 2, "setParams in dlgDomainAdd", $params);

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
                $dialog->setVariable("QuotaSelected", $this->_MailDomain->getAttribute("quota") == $key?"SELECTED":"");
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
            $ia = $this->_MailDomain->getAttribute("inetaccess");

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
         * Called with posted data from form; after authentication and before OnPrepareDialog
         *
         * @param array $formdata
         */
        function OnFormData($formdata) {
            $dn = $this->_MailDomain->getAttribute("dn");
            debug (dbgData,1,"OnFormData in dlgAccountAdd",array("dn"=>$dn,"formdata"=>$formdata));

            if (!empty($formdata["btnOk"])) {
                $this->_MailDomain->setAttribute("md", $formdata["fldMD"]);
                $this->_MailDomain->setAttribute("bukr", $formdata["fldBUKR"]);
                $this->_MailDomain->setAttribute("kstl", $formdata["fldKSTL"]);
                $this->_MailDomain->setAttribute("quota", $formdata["fldQuota"]);

                if ($formdata["fldInternetaccess"] == "date") {
                    list($d,$m,$y) = split("[/.-]", $formdata["fldInternetaccessDate"]);
                    if ($y<100) $y+=2000;
                    $ia = sprintf("%04d%02d%02d", $y,$m,$d);
                    debug(dbgData, 1, "inetaccess calculated", array("d"=>$d,"m"=>$m,"y"=>$y,"results in"=>$ia));
                    $this->_MailDomain->setAttribute("inetaccess", $ia);
                }
                else {
                    $this->_MailDomain->setAttribute("inetaccess", $formdata["fldInternetaccess"]);
                }

                if ($attrs=$this->_MailDomain->isDirty()) {
                    $attrs=array("accesslevel","quota","inetaccess","md","bukr","kstl");
                    $this->_MailDomain->save($attrs);
                }
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
            $md = $this->_MailDomain->getAttribute("md");

            $dialog->setVariable("fldMD", empty($md)?" ":$md);
            if ($this->_accesslevel == alSysadmin) {
                $dialog->parse("SysadminMandant");
            }
            else {
                $dialog->parse("DomainadminMandant");
            }

            //BUKR,KSTL
            $dialog->setVariable("fldBUKR", $this->_MailDomain->getAttribute("bukr"));
            $dialog->setVariable("fldKSTL", $this->_MailDomain->getAttribute("kstl"));

            //Quota
            $this->_prepQuota($dialog, $this->_accesslevel);

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
