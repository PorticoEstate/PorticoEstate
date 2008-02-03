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
    class dlgViewQuota extends Dialog {
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
        function dlgViewQuota(&$application, &$parent, $name) {
            $this->Dialog($application, $parent, $name, "dlgViewQuota.tpl.html", "");
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

            if (!empty($this->_domain)) {
                $menu[] = array("caption"=>"Domain zeigen", "action"=>"ViewDomain", "params"=>array("domain"=>$this->_domain));
                $menu[] = "";
            }
            $menu[] = array("caption"=>"Download CSV", "action"=>"ViewDomain", "params"=>array_merge($this->_params, array("download"=>true)));

            return($menu);
        }

        /**
         * Get title
         *
         * @return string Title for Dialog
         */
        function getTitle() {
            return "Speicheranzeige<br>".(empty($this->_domain)?"Alle Domains":$this->_domain);
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
            $this->_sortfield = $params["sort"];
            $this->_sortorder = $params["order"];
            $this->_download = $params["download"];

            $this->_params = $params;
            unset($this->_params["formdata"]);

            //create MailDomain data object
            if (!empty($this->_domain))
                $this->_MailDomain = &new Maildomain($this->getApplication(), $this->_domain);
        }

        /**
         * Comparision between to quotalist items, used with usort
         *
         * @param $a
         * @param $b
         * @return integer
         */
        function _compare($a, $b) {
            switch ($this->_sortfield) {
                case "size":
                case "usage":
                    $ret =  $a[$this->_sortfield] - $b[$this->_sortfield];
                    break;

                case "uid":
                default:
                    $ret =  strcasecmp($a["domain"].$a["uid"], $b["domain"].$b["uid"]);
            }

            if ($this->_sortorder=="desc") $ret *= -1;
            return ($ret==0)?0:$ret/abs($ret); //sign($ret)
        }

        /**
         * Create quota list
         *
         * @param object $domain
         * @return mixed
         */
        function _create_quotalist($domain) {
            //global $cfg_quota;

            $domainname = $domain->getAttribute("domain");

            $du = $domain->getAttribute("diskusage");
            $quotas = $domain->getAttribute("quotas");

            while (is_array($du) && (list($uid, $duaccount) = each($du))) {
                $sum = 0;
                foreach($duaccount as $dufolder) {
                    $sum += $dufolder;
                }

                $usage = ($quotas[$uid] > 0)? $sum / $quotas[$uid] : ($sum > 0 ? $cfg_quota["high"] / 100 : "0");

                debug(dbgData,1,"loop thru du array", array("uid"=>$uid, "duaccount"=>$duaccount, "sum"=>$sum, "usage"=>$usage, "usagepercent"=>$usagepercent));

                $mailaccount = new MailAccount($this->getApplication(), "", "", $uid."@".$domainname);
                $cnt = $mailaccount->getAttribute("quotawarnings");

                $list[] = array("uid"=>$uid,"domain"=>$domainname,"mailto"=>$uid."@".$domainname,"size"=>$sum,"usage"=>$usage, "warnings"=>$cnt);
            }

            if (is_array($list))
                usort($list, array($this, "_compare"));
            return $list;
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
            if ($auth_param["level"] == alSysadmin) {
                $this->_accesslevel = alSysadmin;
                return 2;
            }

            return 0;
        }

        /**
         * OnPrepareDialog
         *
         * @param object $dialog Component which is being prepared
         */
        function OnDialogPrepare(&$dialog) {
            //global $_sortfield;
            //global $_sortorder;

            //create list array
            if (empty($this->_domain)) {
                $_MailServer = &new Mailserver($this->getApplication());
                if (is_array($domains = $_MailServer->getAttribute("domains"))) {
                    foreach ($domains as $domain) {
                        if (empty($list)) {
                            $list = $this->_create_quotalist($domain);
                        }
                        else {
                            $list = array_merge($list, $this->_create_quotalist($domain));
                        }
                    }
                }

                if (is_array($list)) {
                    usort($list, array($this, "_compare"));
                }
            }
            else {
                $list = $this->_create_quotalist($this->_MailDomain);
            }

            if ($this->_download) {
                header("Cache-Control: ");
                header("Pragma: ");
                header("Content-Type: application/x-csv");
                header("Content-Disposition: attachment; filename=usage.".date("Ymd",time()).".".(empty($this->_domain)?"all":$this->_domain).".csv");

                if (is_array($list)) {
                    foreach($list as $account) {
                        printf("\"%s\",\"%s\",\"%s\",\"%s\",\"%d\"\n", date("d.m.Y", time()),
                                                            $account["mailto"],
                                                            $account["size"],
                                                            $account["usage"]*100,
                                                            $account["warnings"]
                        );
                    }
                    exit;
                }
            }
            else {
                $url = $_SERVER["PHP_SELF"]."?action=ViewQuota&domain=".$this->_domain;
                $dialog->setVariable("LinkSortMail", $url."&sort=uid".($this->_sortfield=="uid" && $this->_sortorder!="desc"?"&order=desc":""));
                $dialog->setVariable("LinkSortSize", $url."&sort=size".($this->_sortfield=="size" && $this->_sortorder!="desc"?"&order=desc":""));
                $dialog->setVariable("LinkSortPercent", $url."&sort=usage".($this->_sortfield=="usage" && $this->_sortorder!="desc"?"&order=desc":""));

                if (is_array($list)) {
                    $sum=0;
                    foreach($list as $account) {
                        $sum += $account["size"];
                        $dialog->setVariable("ListClass", "list".($i++ % 2));
                        $dialog->setVariable("fldUID", $account["uid"]);
                        $dialog->setVariable("fldMail", $account["mailto"]);
                        $dialog->setVariable("fldSize", $account["size"]);
                        $dialog->setVariable("fldSizeKB", sprintf("%2.1f", $account["size"] / 1024));
                        $dialog->setVariable("fldSizeMB", sprintf("%2.2f", $account["size"] / 1024 / 1024));
                        $dialog->setVariable("fldUsage", sprintf("%2.4f", $account["usage"]));
                        $dialog->setVariable("fldUsagePercent", sprintf("%2.2f", $account["usage"]*100));
                        $dialog->setVariable("fldWarningCounter", sprintf("%d", $account["warnings"]));

                        $dialog->parse("AccountUsage");
                    }

                    $dialog->setVariable("fldSumMB", sprintf("%2.2f", $sum / 1024 / 1024));
                }
            }
        }
    }
?>
