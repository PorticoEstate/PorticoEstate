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
     * Dialog for mailaccounts filters
     * @package pbWebMAUI
     */
    class dlgFilter extends Dialog {
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
        function dlgFilter(&$application, &$parent, $name) {
            $this->Dialog($application, $parent, $name, "dlgFilter.tpl.html");
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
                $menu[] = ""; //break;
            }

            $menu[] = array("caption"=>"Liste", "action"=>"FilterList", "params"=>array("dn"=>$this->_MailAccount->getAttribute("dn")));
            return $menu;
        }

        /**
         * Get title
         *
         * @return string Title for Dialog
         */
        function getTitle() {
            return "Mailfilter & Abwesenheit<br>".$this->_MailAccount->getAttribute("uid")." ".$this->_MailAccount->getAttribute("domain");
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

            $this->filterid = $params["id"];
            $this->filtertype = $params["type"];
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
         * Normalize time
         *
         * @param $time
         * @param $ts
         * @return string
         * @access private
         */
        function _NormalizeTime($time, &$ts) {
            list($d,$m,$y) = split("[/.-]", $time);
            return date("d.m.Y", $ts = mktime(0,0,0,$m,$d,$y));
        }

        /**
         * Called with posted data from form; after authentication and before OnPrepareDialog
         *
         * @param array $formdata
         * @return array
         */
        function OnFormData($formdata) {
            debug(dbgAccount, 1, "OnFormData", $formdata);
            $filters=$this->_MailAccount->getAttribute("filters");

            $filterid = $this->filterid;

            //activate filter by default
            $filter["active"] = 1;

            //set filtertype
            if (!empty($formdata["fldType"])) {
                $filter["type"] = $formdata["fldType"];
            }

            //set filtername
            if (!empty($formdata["fldName"])) {
                $filter["name"] = $formdata["fldName"];
            }

            //set active time
            if (!empty($formdata["fldTime"])) {
                $time = $formdata["fldTime"];
                if ($time["mode"] == 1) {
                    $filter["time"]["from"] = $this->_NormalizeTime($time["from"], $tsfrom);
                    $filter["time"]["to"] = $this->_NormalizeTime($time["to"], $tsto);

                    if ($tsto<$tsfrom) {
                        $errs[] = "Startzeit muß vor Stopzeit liegen";
                    }
                    debug (dbgAccount, 1, "set filtertime", array("time"=>$filter["time"]));
                }
            }

            //handle btnDeleteRule and remove data from formdata
            if (!empty($formdata["btnDeleteRule"]) && $formdata["RUSureResult"] != 0) {
                $deleteId = key($formdata["btnDeleteRule"]);
                unset($formdata["fldRules"][$deleteId]);
            }

            //handle btnDeleteAction and remove data from formdata
            if (!empty($formdata["btnDeleteAction"]) && $formdata["RUSureResult"] != 0) {
                $deleteId = key($formdata["btnDeleteAction"]);
                unset($formdata["fldActions"][$deleteId]);
            }

            //build rules
            while (is_array($formdata["fldRules"]) && list($id, $rule) = each($formdata["fldRules"])) {
                if (strlen($rule["pattern"])) {
                    $filter["rules"]["op"] = $formdata["fldRulesMode"];
                    $filterrule = array("pattern"=>$rule["pattern"],
                                                "not"=>$rule["not"],
                                                "where"=>$rule["field"]);

                    $filter["rules"][] = $filterrule;
                    debug (dbgAccount,2, "built rule", array("id"=>$id,"filterrule"=>$filterrule,"filter"=>$filter));
                    if ($rule["field"] < 0) {
                        $errs[] = "Kein Vergleichsfeld ausgewählt für Regel ".$rule["pattern"];
                    }
                }
            }

            //build actions
            while (!empty($formdata["fldActions"]) && list($id, $action) = each($formdata["fldActions"])) {
                if (strlen($action["target"]) || $action["action"] == 4) {
                    switch ($action["action"]) {
                        case 0:
                            $filter["to"] = $action["target"];
                            break;
                        case 1:
                            $filter["cc"][] = $action["target"];
                            break;
                        case 2:
                            $filter["to"] = "!".$action["target"];
                            break;
                        case 3:
                            $filter["cc"][] = "!".$action["target"];
                            break;
                        case 4:
                            $filter["to"] = "/dev/null";
                            break;
                    }
                }
            }

            //autoreply text
            debug (dbgData, 1, "reading file", $_FILES);
            if ($_FILES["fldAutoreplyFile"]["size"]) {
                //read given file into fldAutoreplyText
                debug (dbgData, 1, "reading autoreply text file", array("_FILES"=>$_FILES));

                if ($_FILES["fldAutoreplyFile"]["type"] == "text/plain") {
                    if ($fp = fopen($_FILES["fldAutoreplyFile"]["tmp_name"], "r")) {
                        $formdata["fldAutoreplyText"]="";
                        while (!feof($fp)) {
                            $formdata["fldAutoreplyText"] .= fgets($fp, 1024);
                        }
                        debug (dbgData, 1, "read file", $formdata["fldAutoreplyText"]);
                    }
                }
                else {
                    $errs[] = "Nur Textdateien (mime-typ 'text/plain') sind erlaubt; ".$_FILES["fldAutoreplyFile"]["name"]." ist vom Typ '".$_FILES["fldAutoreplyFile"]["type"]."'";
                }
            }

            if (strlen($formdata["fldAutoreplyText"])) {
                $filter["autoreply"] = $formdata["fldAutoreplyText"];
            }

            if (strlen($formdata["fldVertreter"])) {
                $filter["cc"][0] = "!".$formdata["fldVertreter"];
            }

            //check for some action or autoreply entered
            switch ($formdata["fldType"]) {
                case 1:
                    if (empty($filter["cc"]) && empty($filter["to"]))
                        $errs[] = "Keine Aktion eingegeben";
                    break;

                case 2:
                    if (!strlen($filter["autoreply"]))
                        $errs[] = "Kein Antworttext eingegeben";
                    break;
            }

            //put new filter into filter array
            if ($filterid == "new" || !strlen($filterid)) {
                //find new filterid
                if (!empty($filters)) {
                    $keys = array_keys($filters);
                    rsort($keys);
                    $filterid = $keys[0]+1;
                }
                else
                    $filterid = 0;
            }

            $filters[$filterid] = $filter;
            $this->_MailAccount->setAttribute("filters", $filters);

            //save filterarray
            if (empty($errs) && ($attrs=$this->_MailAccount->isDirty())) {
                $this->_MailAccount->save($attrs);

                $uri = $_SERVER["PHP_SELF"]."?action=EditFilter&dn=".urlencode($this->_MailAccount->getAttribute("dn"));
                $uri .= "&id=".$filterid;
                $this->_application->redirect($uri);
            }
            else {
                $this->_application->setGlobal("filter", $filter);
                $uri = $_SERVER["PHP_SELF"]."?action=EditFilter&dn=".urlencode($this->_MailAccount->getAttribute("dn"));
                $uri .= '&id='.$filterid.'&type='.$formdata["fldType"];
                $this->_application->redirect($uri);
            }

            return $errs;
        }

        /**
         * On Dialog Prepare
         *
         * @param object $dialog
         */
        function OnDialogPrepare(&$dialog) {
            debug (dbgDialog, 3, "OnDialogPrepare ".__FILE__." ".__LINE__);

            //read filterarray from Mailaccount
            if (strlen($this->filterid)) {
                $this->_application->setGlobal("filter", "");
                $filters = $this->_MailAccount->getAttribute("filters");
                $filter = $filters[$this->filterid];
            }
            else {
                $filter = $this->_application->getGlobal("filter");
            }

            if (empty($filter["type"])) {
                $filter["type"] = $this->filtertype;
                if ($filter["type"] == 2) {
                    $filter["time"]["from"] = " ";//date("d.m.Y", time());
                }
            }

            debug (dbgAccount, 1, "filter", array("filter"=>$filter));

            //fill template
            $dialog->setVariable("fldType", $filter["type"]);
            $dialog->setVariable("fldName", $filter["name"]);
            $dialog->setVariable("l_filter_name", $this->_application->lang("filter name"));
            $dialog->setVariable("l_always_if", $this->_application->lang("always if"));
            $dialog->setVariable("l_if_in_period", $this->_application->lang("if in period"));
            $dialog->setVariable("l_from", $this->_application->lang("from"));
            $dialog->setVariable("l_until", $this->_application->lang("until"));
            $dialog->setVariable("phpgwurl", $GLOBALS['phpgw']->link(''));

            if (!empty($filter["time"])) {
                $dialog->setVariable("fldTimeModeSel1", "SELECTED");
                $dialog->setVariable("fldTimeFrom", $filter["time"]["from"]);
                $dialog->setVariable("fldTimeTo", $filter["time"]["to"]);
                $dialog->setVariable("fldTimeRangeVisibility", "visible");
            }
            else {
                $dialog->setVariable("fldTimeModeSel0", "SELECTED");
                $dialog->setVariable("fldTimeRangeVisibility", "hidden");
            }

            //rules
            if ($filter["type"] == 1) {
                $rules = $filter["rules"];
                switch ($rules[op]) {
                    case "||":
                        $dialog->setVariable("fldRulesModeSelOr", "SELECTED");
                        break;

                    case "&&":
                        $dialog->setVariable("fldRulesModeSelAnd", "SELECTED");
                        break;
                }
                $dialog->_template->touchBlock("RulesMode");
                $dialog->setVariable("l_one_of_the_following_rules_apply", $this->_application->lang("one of the following rules apply"));
            		$dialog->setVariable("l_all_of_the_following_rules_apply", $this->_application->lang("all of the following rules apply"));
            		$dialog->setVariable("l_rules", $this->_application->lang("rules"));

                //prepare new rule
                $dialog->setVariable("fldPatternId", "new");
                $dialog->setVariable('imgDir', $GLOBALS['phpgw_info']['server']['webserver_url'].
                                               '/pbwebmaui/templates/'.'default'.'/' // hardcoded :-(
                                    );
                $dialog->setVariable("l_not", $this->_application->lang("not"));
            		$dialog->setVariable("l_select_comparison_field", $this->_application->lang("select comparison field"));
            		$dialog->setVariable("l_in_recipient", $this->_application->lang("in recipient"));
            		$dialog->setVariable("l_in_addressor", $this->_application->lang("in addressor"));
            		$dialog->setVariable("l_in_subject", $this->_application->lang("in subject"));
            		$dialog->setVariable("l_in_text", $this->_application->lang("in text"));
            		$dialog->setVariable("l_in_subject_or_text", $this->_application->lang("in subject or text"));
                $dialog->parse("RulesList");

                //available rules
                unset ($rules["op"]);
                debug (dbgAccount, 1, "rules", array("rules"=>$rules));

                while(!empty($rules) && list($id, $rule) = each($rules)) {
                    $dialog->setVariable("fldPatternId", $id);
                    $dialog->setVariable("fldPattern", $rule["pattern"]);

                    //translate some old "where"-Attrs
                    switch($rule["where"]) {
                        case "subject": $rule["where"] = 2;
                            break;
                        case "from,to": $rule["where"] = 0;
                            break;
                    }

                    $dialog->setVariable("fldRulesFieldSel".$rule["where"], "SELECTED");
                    $dialog->setVariable("fldRulesNotSel".$rule["not"], "SELECTED");
                    $dialog->setVariable('imgDir', $GLOBALS['phpgw_info']['server']['webserver_url'].
                                                   '/pbwebmaui/templates/'.'default'.'/' // hardcoded :-(
                                        );
                                                            $dialog->setVariable("l_delete", $this->_application->lang("delete"));
                		$dialog->setVariable("l_not", $this->_application->lang("not"));
            				$dialog->setVariable("l_select_comparison_field", $this->_application->lang("select comparison field"));
            				$dialog->setVariable("l_in_recipient", $this->_application->lang("in recipient"));
            				$dialog->setVariable("l_in_addressor", $this->_application->lang("in addressor"));
            				$dialog->setVariable("l_in_subject", $this->_application->lang("in subject"));
            				$dialog->setVariable("l_in_text", $this->_application->lang("in text"));
            				$dialog->setVariable("l_in_subject_or_text", $this->_application->lang("in subject or text"));
                    $dialog->parse("RulesList");
                }
            }

            //actions
            if ($filter["type"] == 1) { //define actions only for filters
                //prepare new action
                $dialog->setVariable('imgDir', $GLOBALS['phpgw_info']['server']['webserver_url'].
                                               '/pbwebmaui/templates/'.'default'.'/' // hardcoded :-(
                                    );
                $dialog->setVariable("l_actions", $this->_application->lang("actions"));
                $dialog->setVariable("fldActionId", "new");
                $dialog->setVariable("selectFolderVisibility", "hidden");
                $dialog->setVariable("fldDN", urlencode($this->_MailAccount->getAttribute("dn")));
                $dialog->setVariable("l_delete", $this->_application->lang("delete"));
                $dialog->setVariable("l_select_new_action", $this->_application->lang("select new action"));
            		$dialog->setVariable("l_move_mail_in_the_folder", $this->_application->lang("move mail in the folder"));
            		$dialog->setVariable("l_copy_mail_in_the_folder", $this->_application->lang("copy mail in the folder"));
            		$dialog->setVariable("l_mail_redirect_to_recipient", $this->_application->lang("mail redirect to recipient"));
            		$dialog->setVariable("l_mail_copy_to_recipient", $this->_application->lang("mail copy to recipient"));
            		$dialog->setVariable("l_delete_mail", $this->_application->lang("delete mail"));
            		$dialog->setVariable("l_select_folder", $this->_application->lang("select folder")); 
                

                $dialog->parse("ActionsList");

                //cc-actions
                while(!empty($filter["cc"]) && list($id, $cc) = each($filter["cc"])) {
                    $dialog->setVariable('imgDir', $GLOBALS['phpgw_info']['server']['webserver_url'].
                                                   '/pbwebmaui/templates/'.'default'.'/' // hardcoded :-(
                                        );
                    $dialog->setVariable("fldActionId", $id);
                    $dialog->setVariable("fldDN", urlencode($this->_MailAccount->getAttribute("dn")));
                    $dialog->setVariable("l_delete", $this->_application->lang("delete"));
                		$dialog->setVariable("l_select_new_action", $this->_application->lang("select new action"));
            				$dialog->setVariable("l_move_mail_in_the_folder", $this->_application->lang("move mail in the folder"));
            				$dialog->setVariable("l_copy_mail_in_the_folder", $this->_application->lang("copy mail in the folder"));
            				$dialog->setVariable("l_mail_redirect_to_recipient", $this->_application->lang("mail redirect to recipient"));
            				$dialog->setVariable("l_mail_copy_to_recipient", $this->_application->lang("mail copy to recipient"));
            				$dialog->setVariable("l_delete_mail", $this->_application->lang("delete mail"));
            				$dialog->setVariable("l_select_folder", $this->_application->lang("select folder"));
                    
                    if (substr($cc, 0, 1) == "!") {
                        //cc to email
                        $dialog->setVariable("fldActionSel3", "SELECTED");
                        $dialog->setVariable("fldActionTarget", substr($cc,1));
                        $dialog->setVariable("selectFolderVisibility", "hidden");
                    }
                    else {
                        //cc to folder
                        $dialog->setVariable("fldActionSel1", "SELECTED");
                        $dialog->setVariable("fldActionTarget", $cc);
                        $dialog->setVariable("selectFolderVisibility", "visible");
                    }

                    $dialog->parse("ActionsList");
                }

                //to-action
                if (!empty($filter["to"])) {
                    $dialog->setVariable('imgDir', $GLOBALS['phpgw_info']['server']['webserver_url'].
                                                   '/pbwebmaui/templates/'.'default'.'/' // hardcoded :-(
                                        );
                    $dialog->setVariable("fldActionId", "to");
                    $dialog->setVariable("fldDN", urlencode($this->_MailAccount->getAttribute("dn")));

                    if (substr($filter["to"], 0, 1) == "!") {
                        //move to email
                        $dialog->setVariable("fldActionSel2", "SELECTED");
                        $dialog->setVariable("fldActionTarget", substr($filter["to"],1));
                        $dialog->setVariable("selectFolderVisibility", "hidden");
                    }
                    else if ($filter["to"]=="/dev/null") {
                        //remove mail
                        $dialog->setVariable("fldActionSel4", "SELECTED");
                    }
                    else
                    {
                        //move to folder
                        $dialog->setVariable("fldActionSel0", "SELECTED");
                        $dialog->setVariable("fldActionTarget", $filter["to"]);
                        $dialog->setVariable("selectFolderVisibility", "visible");
                    }

										$dialog->setVariable("l_delete", $this->_application->lang("delete"));
                		$dialog->setVariable("l_select_new_action", $this->_application->lang("select new action"));
            				$dialog->setVariable("l_move_mail_in_the_folder", $this->_application->lang("move mail in the folder"));
            				$dialog->setVariable("l_copy_mail_in_the_folder", $this->_application->lang("copy mail in the folder"));
            				$dialog->setVariable("l_mail_redirect_to_recipient", $this->_application->lang("mail redirect to recipient"));
            				$dialog->setVariable("l_mail_copy_to_recipient", $this->_application->lang("mail copy to recipient"));
            				$dialog->setVariable("l_delete_mail", $this->_application->lang("delete mail"));
            				$dialog->setVariable("l_select_folder", $this->_application->lang("select folder"));
                    $dialog->parse("ActionsList");
                }

            } //type == 1

            if ($filter["type"] == 2) { //define autoreply
                $dialog->setVariable("fldAutoreply", $filter["autoreply"]);
                $dialog->setVariable("l_message", $this->_application->lang("message"));
                $dialog->setVariable("l_text_file", $this->_application->lang("text file"));
                $dialog->setVariable("l_forwarding_to_agent", $this->_application->lang("forwarding to agent"));
                //cc-actions
                if (!empty($filter["cc"]) && list($id, $cc) = each($filter["cc"])) {
                    //just use first entry of cc
                    $dialog->setVariable("fldVertreter", substr($cc,1));
                }

                $dialog->_template->touchBlock("Autoreply");
            }

        }
    }
?>
