<?php
/**
 * pbWebMAUI
 * @author Andreas Schiller <aschiller@probusiness.de>
 * @copyright Copyright (C) 2002-2003 probusiness AG
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package pbWebMAUI
 */

     /**
     * component include file
     * @see component
     */
    require_once "class.component.php";
     /**
     * constants include file
     * @see constants
     */
    require_once "constants.inc.php";


    define ("csAuthFailed", "Zugriff nicht erlaubt"    /*"Authentication failed"*/);


    /**
     * Dialog component for use in workspace form and standard buttons are generated
     * @package pbWebMAUI
     */
    class Dialog extends Component {
        /**
         * Array of buttons
         *
         * @var $_Buttons
         */
        var $_Buttons; 

        /**
         * Array of predefined buttons
         *
         * @var $_PredefinedButtons
         */
        var $_PredefinedButtons;
                                     

        /**
         * Holds main Dialog-Component without buttons
         *
         * @var $_innerDialog
         */
        var $_innerDialog; 

        /**
         * Constructor
         *
         * @param $application
         * @param $parent
         * @param $name
         * @param $templatefile template of inner dialog
         * @param array $buttons
         */
        function Dialog(&$application, &$parent, $name, $templatefile, $buttons=array(1)) {
            debug(dbgDialog, 3, "constructing Dialog ".$name." ".$templatefile);
            
            $this->Component($application, $parent, $name, "dialog.tpl.html");
            $this->_innerDialog = & new Component($application, $this, "Dialog", $templatefile);
            $this->_PredefinedButtons = array (
                                               1 => array("submit", "btnOk", $this->_application->lang("save changes")),
                                               2 => array("submit", "btnCancel", $this->_application->lang("cancel")),
                                               4 => array("submit", "btnLogin", $this->_application->lang("login")),
                                              );
            $this->_Buttons = $buttons;
        }

        /**
         * Get menu
         *
         * @return array array of menuitems. each item itself is an array (look at class.main.php::_getMenu for details)
         */
        function getMenu() {
        	// return array();
        }

        /**
         * Get title
         *
         * @return string Title for Dialog
         */
        function getTitle() {
            return "";
        }

        /**
         * OnDialogPrepare
         *
         * @param object $dialog
         */
        function OnDialogPrepare(&$dialog) {
        }

        /**
         * OnPrepare
         */
        function OnPrepare() {                                              
            $this->OnDialogPrepare($this->_innerDialog);
        }

        /**
         * Callback function which is called before showing the dialog to check authentication
         * OnAuth() is called for each applications authobject. This function should be overridden
         * by any descendant from this dialog class to match their authorization requirements
         *
         * @param array $auth_param array of params holding the authenticated options for one authentication
         * @return integer 0, not yet authenticated to show dialog
         *                  1, authenticated to show, but request for further authentication
         *                  2, full authentification
         */
        function OnAuth($auth_param) {
            return -1;
        }

        /**
         * OnFormData
         *
         * @param $formdata
         */
        function OnFormData($formdata) {
        }

        /**
         * setParams override in descendant
         *
         * @param array $params array of parameters to use with this dialog
         */
        function setParams($params) {
        }

        /**
         * show
         *
         * @param boolean $print
         * @return mixed
         */
        function show($print=true) {
            //check for authentication before showing dialog
            $idx = 0;
            $ok = 0;

            //first try authorization with empty auth object
            $ok = $this->OnAuth(pbAuth::emptyoptions());

            //receive every valid auth object (ie its params) and check with this dialog
            while (($ok<2) && ($auth_param = $this->_application->getAuth($idx, !$ok))) {
                debug (dbgAuth, 1, sprintf("found auth_param..., next key=%s", $idx), $auth_param);

                $ok = max($this->OnAuth($auth_param), $ok);
            }

            debug (dbgApplication, 2, "after Authentification", array("name"=>$this->_name, "getUsername"=>$this->_application->getUsername()) );

            if ($ok) {
                //handle form data
                if (!empty($_POST)) {
                    debug(dbgDialog,2,"calling OnFormData", $_POST);
                    $errs = $this->OnFormData($_POST);
                    if (is_array($errs)) {
                        foreach($errs as $err) {
                            $this->setVariable("FormComment", htmlentities($err));
                            $this->parse("FormComment");
                        }
                    }
                }

                //create buttons
                if (is_array($this->_Buttons)) {
                    foreach ($this->_Buttons as $button) { 
                        if (is_integer($button) && (list ($btnType, $btnName, $btnCaption) = $this->_PredefinedButtons[$button])) {
                            $this->setVariable(array ("btnType"=>$btnType, "btnName"=>$btnName, "btnCaption"=>$btnCaption));
                            $this->parse("Buttons");
                        }

                        if (is_array($button) && (list ($btnType, $btnName, $btnCaption) = $button)) {
                            $this->setVariable(array ("btnType"=>$btnType, "btnName"=>$btnName, "btnCaption"=>$btnCaption));
                            $this->parse("Buttons");
                        }
                    }
                }
                 
                
                //$this->setVariable(array ('pbwebmaui_formAction' => $GLOBALS['phpgw']->link('/index.php', $_SERVER['QUERY_STRING'])));
                //$this->setVariable(array ('pbwebmaui_formAction' => '/phpgw-0.9.16/index.php?menuaction=pbwebmaui.uipbwebmaui.add_mailAccount&dn=uid%25253Dpeter%25252Cou%25253Dmailaccounts%25252Cou%25253Dpeople%25252Cou%25253Dhannover%25252Cdc%25253Dprobusiness%25252Cdc%25253Dde&sessionid=70316650a813cb5d575de57217dbaab7&kp3=d838282ed1d853a2949e062715faa5f2&domain=default&PHPSESSID=5460472501a5a46b02932397b5b2951a&click_history=49d9c554efbad5463b2e531e9d31054b'));
                return parent::show($print);
            }

            //authentication failed
            return csAuthFailed; //that's sufficient for child-components as dialog is one; we really should check $print parameter
        }
    }
?>
