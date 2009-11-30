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
     * mail include file
     * @see mail
     */
    require_once "Mail.php";
    /**
     * mime include file
     * @see mime
     */
    require_once "Mail/mime.php";

    /**
     * Dialog for quota warning
     * @package pbWebMAUI
     */
    class dlgBilling extends Dialog {
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
        function dlgBilling(&$application, &$parent, $name) {
            $this->Dialog($application, $parent, $name, "dlgBilling.tpl.html", "");
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
            return "Billing<br>".(empty($this->_domain)?"Alle Domains":$this->_domain);
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
            $this->_download = $params["download"];

            //save complete parameters to use with menu
            $this->_params = $params;
            unset($this->_params["formdata"]);

            //create MailDomain data object
            if (!empty($this->_domain))
                $this->_MailDomain = &new Maildomain($this->getApplication(), $this->_domain);
        }

        /**
         * Generate quotalist key for comparision purposes
         *
         * @param array $a
         * @return string
         */
        function _genQuotalistKey($a) {
            $key = $a["md"].",".$a["bukr"].",".$a["kstl"].",".$a["quota"];
            return $key;
        }

        /**
         * Comparision between to quotalist items, used with usort
         *
         * @param $a
         * @param $b
         * @return integer
         */
        function _compare($a, $b) {
            $keya = $this->_genQuotalistKey($a);
            $keyb = $this->_genQuotalistKey($b);
            $ret =  strcasecmp($keya, $keyb);
            debug (dbgData, 2, "_compare in Billing", array("a"=>array("uid"=>$a["uid"],"key"=>$keya), "b"=>array("uid"=>$b["uid"],"key"=>$keyb), "ret"=>$ret));
            return $ret;
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
            $billings = $domain->getAttribute("billings");

            while (is_array($billings) && (list($uid, $billing) = each($billings))) {
                $duaccount = $du[$uid];
                $sum = 0;
                if (is_array($duaccount)) {
                    foreach($duaccount as $dufolder) {
                        $sum += $dufolder;
                    }
                }
                $usage = ($quotas[$uid] > 0)? $sum / $quotas[$uid] : ($sum > 0 ? $cfg_quota["high"] / 100 : "0");

                $mailaccount = new MailAccount($this->getApplication(), "", "", $uid."@".$domainname);
                $cnt = $mailaccount->getAttribute("quotawarnings");

                $list[] = array_merge(array("uid"=>$uid,"domain"=>$domainname,"mailto"=>$uid."@".$domainname,"size"=>$sum,"usage"=>$usage, "warnings"=>$cnt,"quotaBytes"=>$quotas[$uid]), $billing);
            }

            if (is_array($list)) {
                usort($list, array($this, "_compare"));
            }
            return $list;
        }

        /**
         * Create csv data
         *
         * @param mixed $list quota list
         * @return array Associative array ("data", "mimetype", "filename")
         */
        function _create_csv($list) {
            if (is_array($list)) {
                $data = "\"md\",\"bukr\",\"kstl\",\"inetaccess\",\"quota\",\"mail\",\"size\",\"warnings\"\n";
                foreach($list as $account) {
                    $data .= sprintf("\"%s\",", $account["md"]);
                    $data .= sprintf("\"%s\",", $account["bukr"]);
                    $data .= sprintf("\"%s\",", $account["kstl"]);
                    $data .= sprintf("\"%s\",", $account["inetaccesstext"]);
                    $data .= sprintf("\"%s\",", $account["quotaBytes"]>=0? sprintf("%2.2f", $account["quotaBytes"] / 1024 / 1024):"unbegrenzt");
                    $data .= sprintf("\"%s\",", $account["mailto"]);
                    $data .= sprintf("\"%6.6f\",", $account["size"] / (1024*1024));
                    $data .= sprintf("\"%d\"\n", $account["warnings"]);
                }

                return array("data"=>$data, "mimetype"=>"application/x-csv", "filename"=>"billing.".date("Ymd",time()).".".(empty($this->_domain)?"all":$this->_domain).".csv");
            }
        }

        /**
         * Called for each applications authobject from its parent. This function should check authorization for this dialog 
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
         * OnDialogPrepare
         *
         * @param object $dialog Component which is being prepared
         */
        function OnDialogPrepare($dialog) {
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

            if ($this->_download == "mail") {
                //global $cfg_mail;

                $mailhdrs["From"] = $cfg_mail["sender"];
                $mailhdrs["To"] = $cfg_mail["billing"]["recipient"];
                $mailhdrs["Date"] = date("D, d M Y H:i:s",time());
                $mailhdrs["Subject"] = $cfg_mail["billing"]["subject"];

                $data = $this->_create_csv($list);

                $mailtpl = new HTML_Template_IT ($this->_application->TemplateDirectory);
                if (!$mailtpl->loadTemplatefile($cfg_mail["billing"]["template"], false, false)) die("error loading template ".$cfg_mail["billing"]["template"]);
                $mailbody = $mailtpl->get();

                $mime = new Mail_mime();
                $mime->setTXTBody($mailbody);
                $mime->addAttachment($data["data"], $data["mimetype"], $data["filename"], false);

                $mailbody = $mime->get();
                $mailhdrs = $mime->headers($mailhdrs);

                $mailobj = & Mail::factory($cfg_mail["backend"], $cfg_mail["params"]);

                if (is_object($sent = $mailobj->send($mailhdrs["To"], $mailhdrs, $mailbody))) {
                    $dialog->setVariable("MailResult", $sent->message);
                    print_r($sent);
                }
                else {
                    $dialog->setVariable("MailResult", "ok");
                }
                exit;
            }
            else if ($this->_download) {
                $data = $this->_create_csv($list);

                header("Cache-Control: ");
                header("Pragma: ");
                header("Content-Type: ". $data["mimetype"]);
                header("Content-Disposition: attachment; filename=". $data["filename"]);
                echo $data["data"];
                exit;
            }
            else {
                if (is_array($list)) {
                    foreach($list as $account) {
                        $dialog->setVariable("ListClass", "list".($i++ % 2));
                        $dialog->setVariable("fldUID", $account["uid"]);
                        $dialog->setVariable("fldMail", $account["mailto"]);
                        $dialog->setVariable("fldSize", $account["size"]);
                        $dialog->setVariable("fldSizeKB", sprintf("%2.1f", $account["size"] / 1024));
                        $dialog->setVariable("fldSizeMB", sprintf("%2.2f", $account["size"] / 1024 / 1024));
                        $dialog->setVariable("fldUsage", sprintf("%2.4f", $account["usage"]));
                        $dialog->setVariable("fldUsagePercent", sprintf("%2.2f", $account["usage"]*100));
                        $dialog->setVariable("fldMD", $account["md"]);
                        $dialog->setVariable("fldBUKR", $account["bukr"]);
                        $dialog->setVariable("fldKSTL", $account["kstl"]);
                        $dialog->setVariable("fldQuota", $account["quotaBytes"]>=0? sprintf("%2.2f", $account["quotaBytes"] / 1024 / 1024):"unbegrenzt");
                        $dialog->setVariable("fldInetaccess", $account["inetaccesstext"]);
                        $dialog->setVariable("fldWarningCounter", sprintf("%d", $account["warnings"]));

                        $dialog->parse("BillingAccount");
                    }
                }
            }
        }
    }
?>
