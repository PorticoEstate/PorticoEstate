<?php
/**
 * pbWebMAUI
 *
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
     * Dialog for maildomain, lists mailadresses
     * @package pbWebMAUI
     */
    class dlgDomain extends Dialog {
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
        var $_MailDomain;   //data object

        /**
         * Constructor
         *
         * @param $application
         * @param $parent
         * @param $name
         */
        function dlgDomain(&$application, &$parent, $name) {
            $this->Dialog($application, $parent, $name, "dlgDomain.tpl.html", "");
        }

        /**
         * Get menu
         *
         * @return array Menuitems. each item itself is an array
         */
        function getMenu() {
            $menu[] = array("caption"=>"Domainübersicht", "action"=>"Server");
            $menu[] = "";
            $menu[] = array("caption"=>"Maildrops", "action"=>"ViewDrops", "params"=>array("domain"=>$this->_domain));
            $menu[] = array("caption"=>"Domainverwalter", "action"=>"DomainAdmins", "params"=>array("domain"=>$this->_domain));
            $menu[] = "";
            $menu[] = array("caption"=>"Zusätzliche Einstellungen", "action"=>"EditDomainAdd", "params"=>array("domain"=>$this->_domain));
            $menu[] = "";
            $menu[] = array("caption"=>"Neuer Mailaccount", "action"=>"EditAccount", "params"=>array("domain"=>$this->_domain));

            if ($this->_accesslevel == alSysadmin) {
                $menu[] = "";
                $menu[] = array("caption"=>"Speicheranzeige", "action"=>"ViewQuota", "params"=>array("domain"=>$this->_domain));
                $menu[] = array("caption"=>"Quota-Warnung", "action"=>"WarnQuota", "params"=>array("domain"=>$this->_domain));
                $menu[] = array("caption"=>"Billing", "action"=>"Billing", "params"=>array("domain"=>$this->_domain));
            }

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
            debug(dbgDomain, 2, "setParams in dlgDomain", $params);

            //extract parameters needed
            $this->_domain = $params["domain"];
            if (strlen($params["formdata"]["fldFilter"])) {
                $this->_filter = $params["formdata"]["fldFilter"];
            }
            else {
                $this->_filter = $params["filter"];
                $this->_page = $params["page"];
            }

            //create MailDomain data object
            $this->_MailDomain = &new Maildomain($this->getApplication(), $this->_domain);
            if (!empty($this->_filter)) {
                $this->_MailDomain->setFilter($this->_filter);
            }

            debug(dbgDomain, 2, "created Maildomain object", $this->_MailDomain);
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
                    return 1;
                 
                case alDomainmaster:
                    /*
                    if (!strcmp($auth_param["domain"], $this->_domain)) {
                        $this->_accesslevel = $auth_param["level"];
                        return 1;
                    }
                    else
                    */ 
                    return 0;

                case alMailaccount:
                default:
                    return 0;
            }
        }

        /**
         * Called with posted data from form; after authentication and before OnPrepareDialog
         *
         * @param array $formdata
         * @return array
         */
        function OnFormData($formdata) {
            debug (dbgData,1,"OnFormData in dlgDomain",array("formdata"=>$formdata));
            if (!empty($formdata["btnDelete"]) && $formdata["RUSureResult"] != 0) {
                $dn = key($formdata["btnDelete"]);
                $account = &new MailAccount($this->getApplication(), $dn);
                $state = $account->delete();

                debug (dbgData,1,"delete account returned", $state);

                if (is_array($state)) {
                    if ($state["state"] == 1) {
                        $this->_archive = $state["archive"];
                        $ret = array("Der Mailaccount wurde in ".$this->_archive." archiviert.");
                    }
                    else {
                        $ret = array("Fehler beim Löschen: ". $state["state"].", ".$state["message"]);
                    }
                }
                else {
                    $ret[] = "Interner Fehler beim Löschen (account->delete)";
                }
            }

            return $ret;
        }

        /**
         * OnDialogPrepare
         *
         * @param object $dialog Component which is being prepared
         */
        function OnDialogPrepare(&$dialog) {
            $cfg_lists = $this->_application->_config->get_cfgList();
            
            debug (dbgDomain, 2, "OnDialogPrepare ".__FILE__, $this->_MailDomain);
            $dialog->setVariable("fldFilter", $this->_filter);
            $dialog->setVariable("lang_search", $this->_application->lang("search"));
            $dialog->setVariable("l_name", $this->_application->lang("name"));
            $dialog->setVariable("l_mailAccount", $this->_application->lang("mail account"));
            $dialog->setVariable("l_delete", $this->_application->lang("delete"));
            

            if ($accounts = $this->_MailDomain->getAttribute("accounts")) {
                //create navigation links
                $viewpage = $this -> _page > 0 ? $this -> _page : 1;

                //show 10 pagelinks, try to view the 5th
                $pagecount = intval((count($accounts)-1) / $cfg_lists["size"]) + 1;
                if ($viewpage > $pagecount) $viewpage = $pagecount;

                $startpage = $viewpage > 4 ? $viewpage - 4 : 1;
                $endpage = $startpage + 9 <= $pagecount ? $startpage + 9 : $pagecount;

                $link_left = $_SERVER["PHP_SELF"] . "?action=ViewDomain&domain=" . $this->_domain;
                if (strlen($this->_filter)) $link_left .= "&filter=" . $this->_filter;
                $link_left .= "&page=";

                //show link to previous pages
                if ($viewpage > 1) {
                    $dialog->setVariable("NaviLinkName", $this->_application->lang("previous"));
                    $dialog->setVariable("NaviLinkHref", $this->_translate_page_link($link_left, ($viewpage -1)));
                    $dialog->parse("NaviLinkActive");
                }
                else {
                    $dialog->setVariable("NaviLinkName", $this->_application->lang("previous"));
                    $dialog->parse("NaviLinkInactive");
                }

                //show links to 10 pages
                for ($i=$startpage; $i <= $endpage; $i++) {
                    if ($i <> $viewpage) {
                        $dialog->setVariable("NaviLinkName", $i);
                        $dialog->setVariable("NaviLinkHref", $this->_translate_page_link($link_left, $i));
                        $dialog->parse("NaviLinkActive");
                    }
                    else {
                        $dialog->setVariable("NaviLinkName", $i);
                        $dialog->parse("NaviLinkInactive");
                    }
                    $dialog->parse("NaviLink");
                }

                //show link to next pages
                if ($viewpage < $pagecount) {
                    $dialog->setVariable("NaviLinkName", $this->_application->lang("next"));
                    $dialog->setVariable("NaviLinkHref", $this->_translate_page_link($link_left, $viewpage + 1));
                    $dialog->parse("NaviLinkActive");
                }
                else {
                    $dialog->setVariable("NaviLinkName", $this->_application->lang("next"));
                    $dialog->parse("NaviLinkInactive");
                }

                //show rows
                $id_start = ($viewpage-1) * $cfg_lists["size"];
                for ($accountid=$id_start; $accountid<$id_start+$cfg_lists["size"]; $accountid++) {
                    $account = $accounts[$accountid];

                    if (is_object($account)) {
                        $dialog->setVariable("ListClass", "list".($i++%2));
                        $dialog->setVariable("Name", $account->getAttribute("cn"));
                        /*
                        $dialog->setVariable("LinkAccount", $_SERVER["PHP_SELF"]."?action=EditAccount".
                                                                    //"&domain=".urlencode($account->getAttribute("domain")).
                                                                    //"&uid=".urlencode($account->getAttribute("uid"))
                                                                    "&dn=".urlencode($account->getAttribute("dn"))
                                                                    );
                        */
                        $dialog->setVariable('LinkAccount', $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'pbwebmaui.uipbwebmaui.edit_mailAccount',
                                                                                                       'dn' => urlencode($account->getAttribute("dn"))
                                                                                                       ))
                                             );
                        $dialog->setVariable('imgDir', $GLOBALS['phpgw_info']['server']['webserver_url'].
                                                       '/pbwebmaui/templates/'.'default'.'/' // hardcoded :-(
                                            );
                        if (is_array($mails = $account->getAttribute("mail"))) {
                            debug(dbgDomain, 2, "OnDialogPrepare getAttribute(mail) lieferte ", $mails);

                            foreach ($mails as $mail) {
                                $dialog->setVariable("Address", $mail);
                                $dialog->parse("Address");
                            }
                        }

                        $dialog->setVariable("btnDeleteKey", "[".$account->getAttribute("dn")."][]");
                        $dialog->parse("Account");
                    }
                }
            }
        }
        
        function _translate_page_link($link, $pageNum)
        {
        	$link_ex = explode('&', $link);
        	for ($i = 0; $i < count($link_ex); $i++)
        	{
        		$link_ex[$i] = explode('=', $link_ex[$i]);
        		switch ($link_ex[$i][0])
        		{
        			case 'filter':
        				$filter = $link_ex[$i][1];
        				break;
        		}        			
        	}
        	
        	return $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'pbwebmaui.uipbwebmaui.list_domain',
        	                                                                   'filter' => $filter,
        	                                                                   'page'   => $pageNum
        	                                                  ));
        }
    }
?>
