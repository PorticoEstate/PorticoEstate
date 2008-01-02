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
    class dlgFilterList extends Dialog {
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
        function dlgFilterList(&$application, &$parent, $name) {
            $this->Dialog($application, $parent, $name, "dlgFilterList.tpl.html");
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
                $menu[] = ""; //break
            }
            $menu[] = array("caption"=>"Account", "action"=>"EditAccount", "params"=>array("dn"=>$this->_MailAccount->getAttribute("dn")));
            $menu[] = ""; //break
            $menu[] = array("caption"=>"Neuer Mailfilter", "action"=>"EditFilter", "params"=>array("dn"=>$this->_MailAccount->getAttribute("dn"),"type"=>1));
            $menu[] = array("caption"=>"Neue Abwesenheitsnachr.", "action"=>"EditFilter", "params"=>array("dn"=>$this->_MailAccount->getAttribute("dn"),"type"=>2));

            return ($menu);
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

            //create MailDomain data object
            $this->_MailDomain = &new MailDomain($this->getApplication(), $this->_MailAccount->getAttribute("domain"));
            $this->_MailDomain->setFilter($this->_MailAccount->getAttribute("domain"));
        }

        /**
         * Called for each applications authobject. This function should check authorization for this dialog
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
         * C alled with posted data from form; after authentication and before OnPrepareDialog
         *
         * @param array $formdata
         */
        function OnFormData($formdata) {
            debug(dbgAccount, 1, "OnFormData", $formdata);
            $filters=$this->_MailAccount->getAttribute("filters");

            //set activation status of each filter
            while(!empty($filters) && list($id,$filter)=each($filters)) {
                debug (dbgAccount, 1, "changing active flag, check $id", $formdata["activate"][$id]);
                $filters[$id]["active"] = $formdata["activate"][$id] == "on";
            }

            debug (dbgAccount, 1, "changing active flag", $filters);
            $this->_MailAccount->setAttribute("filters", $filters);

            //delete filter
            if (!empty($formdata["btnDelete"]) && $formdata["RUSureResult"] != 0) {
                $deleteId = key($formdata["btnDelete"]);
                debug(dbgData, 1, "remove filter id $deleteId");
                unset($filters[$deleteId]);

                $this->_MailAccount->setAttribute("filters", $filters);
            }

            //save filters
            if ($attrs=$this->_MailAccount->isDirty()) {
                $this->_MailAccount->save($attrs);
            }
        }

        /**
         * On Dialog Prepare
         *
         * qparam object $dialog
         */
        function OnDialogPrepare(&$dialog) {
            debug (dbgDialog, 3, "OnDialogPrepare ".__FILE__." ".__LINE__);

            //read filterarray from Mailaccount
            $filters = $this->_MailAccount->getAttribute("filters");
            $dialog->setVariable("l_mail_filter", $this->_application->lang("mail filter"));
            $dialog->setVariable("l_absence", $this->_application->lang("absence"));


            //fill template
            while(!empty($filters) && list($id,$filter)=each($filters)) {
                debug (dbgAccount, 1, "filter", array("id"=>$id,"filter"=>$filter));

                if ($filter['type'] <= 2) {
									$dialog->setVariable("fldActive", $filter["active"]?"checked":"");
									$dialog->setVariable("l_delete", $this->_application->lang("delete"));
									$dialog->setVariable('imgDir', $GLOBALS['phpgw_info']['server']['webserver_url'].
																								'/pbwebmaui/templates/'.'default'.'/' // hardcoded :-(
																			);
									$dialog->setVariable("fldName", $filter["name"]);
									$dialog->setVariable("fldId", $id);

									//active time
									if ($time = $filter["time"]) {
											$from=$time["from"];
											$to=$time["to"];
											$dialog->setVariable("fldTime", $from." - ".$to);
									}
								}
                switch ($filter["type"]) {
                    case 1: //Filter
                        $dialog->setVariable("fldLink", $_SERVER["PHP_SELF"]."?action=EditFilter".
                                                                 "&dn=".urlencode($this->_MailAccount->getAttribute("dn"))."&id=".$id
                                                                 );
                        $dialog->setVariable('fldLink', $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'pbwebmaui.uipbwebmaui.edit_mailFilter',
                                                                                'dn' => urlencode($this->_MailAccount->getAttribute('dn')),
                                                                                'id' => $id
                                                                                ))
                                            );

                        $dialog->parse("FilterList");
                        break;

                    case 2: //Absence message
                        $dialog->setVariable('fldLink', $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'pbwebmaui.uipbwebmaui.edit_mailFilter',
                                                                                'dn' => urlencode($this->_MailAccount->getAttribute('dn')),
                                                                                'id' => $id
                                                                                ))
                                            );
                        $dialog->parse("AbsentList");
                        break;
                }
            } //foreach $filters
        }
    }
?>
