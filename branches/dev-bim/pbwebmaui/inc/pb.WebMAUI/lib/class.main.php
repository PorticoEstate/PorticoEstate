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
     * component include file
     * @see component
     */
    require_once "class.component.php";
    /**
     * form include file
     * @see form
     */
    require_once "class.form.php";

    /**
     * dlgStart include file
     * @see dlgStart
     */
    require_once "class.dlgStart.php";
    /**
     * dlgLogin include file
     * @see dlgLogin
     */
    require_once "class.dlgLogin.php";
    /**
     * dlgLogout include file
     * @see dlgLogout
     */
    require_once "class.dlgLogout.php";
    /**
     * dlgAccount include file
     * @see dlgAccount
     */
    require_once "class.dlgAccount.php";
    /**
     * dlgAccountAdd include file
     * @see dlgAccountAdd
     */
    require_once "class.dlgAccountAdd.php";
    /**
     * dlgAccountQuota include file
     * @see dlgAccountQuota
     */
    require_once "class.dlgAccountQuota.php";
    /**
     * dlgMoveAccount include file
     * @see dlgMoveAccount
     */
    require_once "class.dlgMoveAccount.php";
    /**
     * dlgFilterList include file
     * @see dlgFilterList
     */
    require_once "class.dlgFilterList.php";
    /**
     * dlgFolderList include file
     * @see dlgFolderList
     */
    require_once "class.dlgFolderList.php";
    /**
     * dlgFilter include file
     * @see dlgFilter
     */
    require_once "class.dlgFilter.php";
    /**
     * dlgDomain include file
     * @see dlgDomain
     */
    require_once "class.dlgDomain.php";
    /**
     * dlgDomainAdmins include file
     * @see dlgDomainAdmins
     */
    require_once "class.dlgDomainAdmins.php";
    /**
     * dlgDomainAdd include file
     * @see dlgDomainAdd
     */
    require_once "class.dlgDomainAdd.php";
    /**
     * dlgServer include file
     * @see dlgServer
     */
    require_once "class.dlgServer.php";
    /**
     * dlgViewdrops include file
     * @see dlgViewdrops
     */
    require_once "class.dlgViewdrops.php";
    /**
     * dlgMaildrop include file
     * @see dlgMaildrop
     */
    require_once "class.dlgMaildrop.php";
    /**
     * dlgNewDomain include file
     * @see dlgNewDomain
     */
    require_once "class.dlgNewDomain.php";
    /**
     * dlgViewQuota include file
     * @see dlgViewQuota
     */
    require_once "class.dlgViewQuota.php";
    /**
     * dlgWarnQuota include file
     * @see dlgWarnQuota
     */
    require_once "class.dlgWarnQuota.php";
    /**
     * dlgBilling include file
     * @see dlgBilling
     */
    //require_once "class.dlgBilling.php";

    /**
     * debug include file
     * @see debug
     */
    require_once "debug.inc.php";

    /**
     * This is the class showing the main form
     * @package pbWebMAUI
     */
    class Main extends Form {
        /**
         * comp title
         *
         * @var string $_compTitle
         */
        var $_compTitle;

        /**
         * comp menu
         *
         * @var string $_compMenu
         */
        var $_compMenu;

        /**
         * comp workspace
         *
         * @var string $_compWorkspace
         */
        var $_compWorkspace;

        /**
         * action
         *
         * @var string $_action
         */
        var $_action;


        /**
         * Constructor
         * 
         * @param $application
         * @param string $name
         */
        function Main(&$application, $name) {
            if ($name == "Popup") {
                $this->Form($application, $null, $name, "popup.tpl.html");
            }
            else {
                $this->Form($application, $null, $name, "main.tpl.html");
            }

            $this->_compTitle = & new Component($application, $this, "Title", "title.tpl.html");
        }


        /**
         * Action
         *
         * @param string $action
         * @param $params
         */
        function action($action, $params) {
            debug(dbgApplication, 2, "action in Main", array("action"=>$action, "params"=>$params));

            //create workspace
            switch($action) {
                case "":
                case "Start":
                    $this->_compWorkspace = & new dlgStart($this->_application, $this, "Workspace");
                    break;
                case "Login":
                    $this->_compWorkspace = & new dlgLogin($this->_application, $this, "Workspace");
                    break;
                case "Logout":
                    $this->_compWorkspace = & new dlgLogout($this->_application, $this, "Workspace");
                    break;
                case "EditAccount":
                    $this->_compWorkspace = & new dlgAccount($this->_application, $this, "Workspace");
                    break;
                case "EditAccountAdd":
                    $this->_compWorkspace = & new dlgAccountAdd($this->_application, $this, "Workspace");
                    break;
                case "AccountQuota":
                    $this->_compWorkspace = & new dlgAccountQuota($this->_application, $this, "Workspace");
                    break;
                case "MoveAccount":
                    $this->_compWorkspace = & new dlgMoveAccount($this->_application, $this, "Workspace");
                    break;
                case "FilterList":
                    $this->_compWorkspace = & new dlgFilterList($this->_application, $this, "Workspace");
                    break;
                case "FolderList":
                    $this->_compWorkspace = & new dlgFolderList($this->_application, $this, "Workspace");
                    break;
                case "EditFilter":
                    $this->_compWorkspace = & new dlgFilter($this->_application, $this, "Workspace");
                    break;
                case "ViewDomain":
                    $this->_compWorkspace = & new dlgDomain($this->_application, $this, "Workspace");
                    break;
                case "DomainAdmins":
                    $this->_compWorkspace = & new dlgDomainAdmins($this->_application, $this, "Workspace");
                    break;
                case "EditDomainAdd":
                    $this->_compWorkspace = & new dlgDomainAdd($this->_application, $this, "Workspace");
                    break;
                case "Server":
                    $this->_compWorkspace = & new dlgServer($this->_application, $this, "Workspace");
                    break;
                case "ViewDrops":
                    $this->_compWorkspace = & new dlgViewdrops($this->_application, $this, "Workspace");
                    break;
                case "EditDrop":
                    $this->_compWorkspace = & new dlgMaildrop($this->_application, $this, "Workspace");
                    break;
                case "NewDomain":
                    $this->_compWorkspace = & new dlgNewDomain($this->_application, $this, "Workspace");
                    break;
                case "ViewQuota":
                    $this->_compWorkspace = & new dlgViewQuota($this->_application, $this, "Workspace");
                    break;
                case "WarnQuota":
                    $this->_compWorkspace = & new dlgWarnQuota($this->_application, $this, "Workspace");
                    break;
                case "Billing":
                    $this->_compWorkspace = & new dlgBilling($this->_application, $this, "Workspace");
                    break;
                default:
                    die ("illegal action $action");
                    break;
            }

            $this->_compMenu = & new Component($this->_application, $this, "Menu", "menu.tpl.html");

            $this->_action = $action;
            $this->_compWorkspace->setParams($params);
        }

        /**
         * Prepare menu
         *
         * @param $comp
         * access private
         */
        function _prepMenu(&$comp) {
            $menu = $this->_compWorkspace->getMenu();

            if ($this->_action != "Logout"
                && $this->_action != "Login"
                && $this->_application->Style != "imp") {

                $menu[-2] = array("caption"=>"Logout", "action"=>"Logout");
                $menu[-1] = "";
                ksort($menu);
            }

            if (is_array($menu)) {
                foreach ($menu as $item) {
                    if (is_array($item) && !empty($item["caption"])) {
                        debug(dbgApplication, 2, "prepMenu", $item);

                        if (empty($item["url"])) {
                            //create url
                            $url = $_SERVER["PHP_SELF"];

                            //set action param in url
                            if (!empty($item["action"])) {
                                $url .= "?action=".$item["action"];

                                //create params for url
                                if (is_array($item["params"])) {
                                    while (list($key, $value) = each($item["params"])) {
                                        $url .= "&".$key."=".urlencode($value);
                                    }
                                }
                            }
                        }
                        else {
                            $url = $item["url"];
                        }

                        $comp->setVariable("MenuItemCaption", htmlentities($item["caption"]));
                        $comp->setVariable("MenuItemURL", $url);

                        $comp->parse("MenuItem");
                    }
                    else { //add break only
                        debug(dbgApplication, 2, "prepMenu BREAK");
                        $comp->setVariable("MenuBreak", "&nbsp;");
                    }
                }
            }
        }

        /**
         * On prepare
         */
        function OnPrepare() {
            debug (dbgComponent, 2, "OnPrepare im Main");

//            $this->_prepMenu($this->_compMenu);
            $this->_compTitle->setVariable("Title", "pb.WebMAUI");
            $this->_compTitle->setVariable("Title2", $this->_compWorkspace->getTitle());

            $this->_compMenu->setOnShow("\$this->_parent->OnShowMenu");
        }

        /**
         * On show
         */
        function OnShow() {
            debug (dbgComponent, 2, "OnShow im Main");
//            $this->_prepMenu($this->_compMenu);
            $this->setVariable("DebugArea", get_debug());
        }

        /**
         * On show menu
         *
         * @param $comp
         */
        function OnShowMenu(&$comp) {
            debug (dbgComponent, 2, "OnShowMenu im Main", array("get_class(this)"=>get_class($this),
                                                               "this->_name"=>$this->_name,
                                                               "this->_compWorkspace"=>$this->_compWorkspace,
                                                               "this->_compMenu"=>$this->_compMenu,
                                                               "get_class(comp)"=>get_class($comp),
                                                               "comp->_name"=>$comp->_name));

            $this->_prepMenu($comp);
        }
    }
?>
