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
     * Dialog for quota warning
     * @package pbWebMAUI
     */
    class dlgWarnQuota extends Dialog {
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
        var $_MailDomain;


        /**
         * Constructor
         *
         * @param $application
         * @param $parent
         * @param $name
         */
        function dlgWarnQuota(&$application, &$parent, $name) {
            $this->Dialog($application, $parent, $name, "dlgWarnQuota.tpl.html", "");
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
            $menu[] = array("caption"=>"Warnungen mailen", "action"=>"ViewDomain", "params"=>array_merge($this->_params, array("mail"=>true,"filter_min"=>$this->_filter["min"],"filter_max"=>$this->_filter["max"])));

            return($menu);
        }

        /**
         * Get title
         *
         * @return string Title for Dialog
         */
        function getTitle() {
            return "Quotawarnung<br>".(empty($this->_domain)?"Alle Domains":$this->_domain);
        }

        /**
         * Set params
         *
         * @param array $params Parameters to use with this dialog:
         *                       - "mail": mailaddress of account to edit
         *                       - "domain": domain for new account
         */
        function setParams($params) {
            //global $_sortfield;
            //global $_sortorder;
            debug(dbgDomain, 2, "setParams in dlgDomain", $params);

            //extract parameters needed
            $this->_domain = $params["domain"];
            $this->_sortfield = $params["sort"];
            $this->_sortorder = $params["order"];
            $this->_mail = $params["mail"];

            //filter data
            $this->_filter["min"] = strlen($params["filter_min"])?$params["filter_min"]:100;
            $this->_filter["max"] = $params["filter_max"];
            $this->_filter = array_merge($this->_filter, $params["formdata"]["fldFilter"]);

            //save complete parameters to use with menu
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
         * @access private
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

                if ((!strlen($this->_filter["min"]) || ($usage * 100 >= $this->_filter["min"]))
                  &&(!strlen($this->_filter["max"]) || ($usage * 100 <= $this->_filter["max"]))) {
                    $list[] = array("uid"=>$uid,"domain"=>$domainname,"mailto"=>$uid."@".$domainname,"size"=>$sum,"usage"=>$usage,"quotaBytes"=>$quotas[$uid]);
                }
                else {
                    //if mailfunction is called, we should reset WarningsCounter of other accounts
                    if ($this->_mail) {
                        $mailaccount = new MailAccount($this->getApplication(), "", "", $uid."@".$domainname);
                        if ($mailaccount->getAttribute("quotawarnings") > 0) {
                            $mailaccount->setAttribute("quotawarnings", "0");
                            $mailaccount->save();
                        }
                    }
                }
            }

            if (is_array($list)) {
                usort($list, array($this, "_compare"));
            }
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
         * On Dialog Prepare
         *
         * @param object $dialog Component which is being prepared
         */
        function OnDialogPrepare($dialog) {
            //global $cfg_mail;

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

            if ($this->_mail && is_array($list)) {
                foreach($list as $account) {
                    $mailto = $account["mailto"];
                    $mailaccount = new MailAccount($this->getApplication(), "", "", $mailto);

                    $mailtpl = new HTML_Template_IT ($this->_application->TemplateDirectory);
                    if (!$mailtpl->loadTemplatefile($cfg_mail["quota"]["template"], true, true)) die("error loading template ".$cfg_mail["quota"]["template"]);

                    $mailtpl->setVariable("AccountName", $mailto);
                    $mailtpl->setVariable("Quota", $account["quotaBytes"]>=0? sprintf("%2.2fMB", $account["quotaBytes"] / 1024 / 1024):"unbegrenzt");
                    $mailtpl->setVariable("BelegterSpeicher", sprintf("%2.2fMB", $account["size"] / (1024*1024)));

                    $mailhdrs["From"] = $cfg_mail["sender"];
                    $mailhdrs["To"] = $mailto;
                    $mailhdrs["Date"] = date("D, d M Y H:i:s",time());
                    $mailhdrs["Subject"] = $cfg_mail["quota"]["subject"];
                    $mailhdrs["Content-Type"] = "text/plain; charset=ISO-8859-15; format=flowed";
                    $mailhdrs["Content-Transfer-Encoding"] = "8bit";

                    $mailbody = $mailtpl->get();

                    $mailobj = & Mail::factory($cfg_mail["backend"], $cfg_mail["params"]);

                    if (is_object($sent = $mailobj->send($mailto, $mailhdrs, $mailbody))) {
                        $dialog->setVariable("MailResult", $sent->message);
                    }
                    else {
                        $dialog->setVariable("MailResult", "ok");

                        //increment WarningsCounter
                        $n=$mailaccount->getAttribute("quotawarnings")+1;
                        $mailaccount->setAttribute("quotawarnings", $n);
                        $mailaccount->save();
                    }

                    $dialog->setVariable("MailTo", $mailto);
                    $dialog->setVariable("WarningCounter", $mailaccount->getAttribute("quotawarnings"));

                    $dialog->parse("QuotaMailResultList");

                    debug (dbgData,1, "send mail", array("mailto"=>$mailto,"headers"=>$mailhdrs,"body"=>$mailbody,"send returned"=>$sent));
                }

                //output result
                $dialog->parse("QuotaMailResult");
            }

            else {
                $dialog->setVariable("fldFilter_min", $this->_filter["min"]);
                $dialog->setVariable("fldFilter_max", $this->_filter["max"]);

                $url = $_SERVER["PHP_SELF"]."?action=WarnQuota&domain=".$this->_domain;
                $dialog->setVariable("LinkSortMail", $url."&sort=uid".($this->_sortfield=="uid" && $this->_sortorder!="desc"?"&order=desc":""));
                $dialog->setVariable("LinkSortSize", $url."&sort=size".($this->_sortfield=="size" && $this->_sortorder!="desc"?"&order=desc":""));
                $dialog->setVariable("LinkSortPercent", $url."&sort=usage".($this->_sortfield=="usage" && $this->_sortorder!="desc"?"&order=desc":""));

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

                        $mailaccount = new MailAccount($this->getApplication(), "", "", $account["mailto"]);
                        $dialog->setVariable("fldWarningCounter", $mailaccount->getAttribute("quotawarnings"));

                        $dialog->parse("AccountUsage");
                    }
                }

                $dialog->parse("QuotaWarning");
            }
        }
    }
?>
