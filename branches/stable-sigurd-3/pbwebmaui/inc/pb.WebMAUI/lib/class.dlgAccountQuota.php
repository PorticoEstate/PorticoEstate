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
     * Dialog showing disk usage
     * @package pbWebMAUI
     */
    class dlgAccountQuota extends Dialog {
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
         * @var $_filter
         * @access private
         */
        var $_filter;
        /**
         * Main domain
         *
         * @var object $_MailDomain
         * @access private
         */
        var $_MailDomain;   //data object


        /**
         * Constructor
         *
         * @param $application
         * @param $parent
         * @param $name
         */
        function dlgAccountQuota(&$application, &$parent, $name) {
            $this->Dialog($application, $parent, $name, "dlgAccountQuota.tpl.html", "");
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
            return "Speicheranzeige<br>".$this->_MailAccount->getAttribute("uid")." ".$this->_MailAccount->getAttribute("domain");
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
            $this->_sortfield = $params["sort"];
            $this->_sortorder = $params["order"];

            //create MailAccount data object
            $this->_MailAccount = &new MailAccount($this->getApplication(), $this->_dn, "", $this->_mail);
            $this->_domain = $this->_MailAccount->getAttribute("domain");
            $this->_uid = $this->_MailAccount->getAttribute("uid");

            //create MailDomain data object
            $this->_MailDomain = &new MailDomain($this->getApplication(), $this->_MailAccount->getAttribute("domain"));
            $this->_MailDomain->setFilter($this->_MailAccount->getAttribute("domain"));
        }

        /**
         * Comparision between to quotalist items, used with usort
         *
         * @param mixed $a
         * @param mixed $b
         * @return integer
         */
        function _compare($a, $b) {
            switch ($this->_sortfield) {
                case "size":
                    $ret =  $a[$this->_sortfield] - $b[$this->_sortfield];
                    break;

                case "folder":
                default:
                    $ret =  strcasecmp($a["folder"], $b["folder"]);
            }

            if ($this->_sortorder=="desc") $ret *= -1;
            debug(dbgData,2,"_compare",array("a"=>$a,"b"=>$b,"sort"=>$this->_sortfield,"ret"=>$ret));
            return ($ret==0)?0:$ret/abs($ret); //sign($ret)
        }

        /**
         * Create quota list
         *
         * @return mixed
         */
        function _create_quotalist() {
            $du = $this->_MailAccount->getAttribute("diskusage");

            while (is_array($du) && (list($folder, $dufolder) = each($du))) {
                $list[] = array("folder"=>$folder,"size"=>$dufolder);
                debug(dbgData,1,"loop thru du",array("folder"=>$folder,"size"=>$dufolder));
            }

            if (is_array($list))
                usort($list, array($this, "_compare"));

            return $list;
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
         * OnPrepareDialog
         *
         * @param object $dialog Component which is being prepared
         */
        function OnDialogPrepare(&$dialog) {
            //global $cfg_quota;

            $list = $this->_create_quotalist();

            $url = $_SERVER["PHP_SELF"]."?action=AccountQuota&dn=".$this->_dn;
            $dialog->setVariable("LinkSortFolder", $url."&sort=folder".($this->_sortfield=="folder" && $this->_sortorder!="desc"?"&order=desc":""));
            $dialog->setVariable("LinkSortSize", $url."&sort=size".($this->_sortfield=="size" && $this->_sortorder!="desc"?"&order=desc":""));

            if (is_array($list)) {
                foreach($list as $folder) {
                    $dialog->setVariable("ListClass", "list".($i++ % 2));
                    $dialog->setVariable("fldFolder", $folder["folder"]);
                    $dialog->setVariable("fldSize", $folder["size"]);
                    $dialog->setVariable("fldSizeKB", sprintf("%2.1f", $folder["size"] / 1024));
                    $dialog->setVariable("fldSizeMB", sprintf("%2.2f", $folder["size"] / (1024 * 1024)));

                    $dialog->parse("FolderUsage");
                    $sum += $folder["size"];
                }

                $dialog->setVariable("fldSum", $sum);
                $dialog->setVariable("fldSumKB", sprintf("%2.1f", $sum / 1024));
                $dialog->setVariable("fldSumMB", sprintf("%2.2f", $sum / (1024*1024)));

                if ($this->_MailAccount->getAttribute("quota") > 0) 
                    $quotabytes = $this->_MailAccount->getAttribute("quotabytes");
                else
                    $quotabytes = $this->_MailDomain->getAttribute("quotabytes");

                $usage = ($quotabytes > 0)? $sum / $quotabytes : ($sum > 0 ? $cfg_quota["high"] / 100 : "0");
                $dialog->setVariable("fldUsagePercent", sprintf("%2.2f", $usage*100));
            }
        }
    }
?>
