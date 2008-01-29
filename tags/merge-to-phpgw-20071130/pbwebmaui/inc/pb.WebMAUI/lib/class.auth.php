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
     * data include file
     * @see data
     */
    require_once "class.data.php";

    /**
     * debug include file
     * @see debug
     */
    require_once "debug.inc.php";


    /**
     * Authentication
     * @package pbWebMAUI
     */
    class pbAuth {
        /**
         * State of authentication
         * @access protected
         * @var mixed $_valid
         */
        var $_valid;
        
        /**
         * State of object; true means, user and password are not yet read from input
         * @access protected
         * @var mixed $_new
         */
        var $_new;
        
        /**
         * Username
         * @access protected
         * @var string $_user
         */
        var $_user;


        /**
         * Password
         * @access protected
         * @var string $_password
         */
        var $_password;
        
        /**
         * Options
         * @access protected
         * @var mixed $_options
         */
        var $_options;

        /**
         * Data object
         * @access protected
         * @var mixed $_data
         */
        var $_data;


        /**
         * Application
         * @access protected
         * @var mixed $_application
         */
        var $_application;


        /**
         * Constructor
         * @param mixed $application
         * @param string $user User name
         * @param string $password Users password
         */
        function pbAuth ($user="", $password="") {
            $this->_application =& Application::getInstance(null);
            if (!empty($user))
            {
                $this->_application->setGlobal("user", $user);
                $this->_application->setGlobal("password", $password);
            }
            /*
            else
            {
                //construct (necessary for empty QUERY_STRING) and save REQUEST_URI
                $REQUEST_URI = $_SERVER["PHP_SELF"];
                if (!empty($_SERVER["QUERY_STRING"])) {
                    $REQUEST_URI .= "?".$_SERVER["QUERY_STRING"];
                }
                else {
                    $REQUEST_URI .= "?action=Start";
                }

                $this->_application->setGlobal("request_uri", $REQUEST_URI);

                //redirect to login
                $uri = $_SERVER["PHP_SELF"]."?action=Login";
                $this->_application->redirect($uri);
            }
            */
            $this->_new = true;
            $this->_valid = false;
        }

        /**
         * Checks given password for user
         *
         * @param string $user User name
         * @param string $pw Password for user
         * @return boolean true - password ok otherwise false
         */
        function _checkPassword($user, $pw) {
        	  
            //global $superusers;

            debug (dbgAuth, 2, "_checkPassword($user, $pw)");

            //check for superusers
            if (is_array($superusers[$user])) {
                return ($superusers[$user]["password"] == "$pw");
            }

            //call account from data; username=mail
            $this->_data = new data($this->_application);
            return true;
            if (!($account = $this->_data->getAccount(array("mail"=>$user)))) {
                debug (dbgAuth, 2, "could not get data for $user");
                return false;
            };

            //check password against password of account
            $userpassword = $account["userPassword"];
            if (stristr($userpassword, "{CRYPT}")) {
                //check crypted password
                $userpassword = substr($userpassword, 7);
                $salt = substr($userpassword,0,9);
                $encpw = crypt($pw, $userpassword);

                debug (dbgAuth, 2, sprintf("checked crypted password; (%s == %s) = %s", $userpassword, $encpw, ($userpassword == "$encpw")));
                return ($userpassword == "$encpw");
            }
            else {
                //check cleartext password
                debug (dbgAuth, 2, sprintf("checked cleartext password %s", ($userpassword == "$pw")));
                return ($userpassword == "$pw");
            }

        }


        /**
         * Read options for user
         *
         * @param string $user User name
         * @return array
         * @access private
         
         */
        function _readOptions($user) {
            //global $superusers;

            debug (dbgAuth, 1, "readOptions for user $user");

            // check for superusers
            if (is_array($superusers[$user])) {
                return ($superusers[$user]["options"]);
            }
            $user = $GLOBALS['phpgw']->account->fullname;
            $level = 1;
						if ($GLOBALS['phpgw']->applications->data['admin']['enabled'])
						{
							$level++;
						}
            if (isset($this->_data) ) //&& ($account = $this->_data->getAccount(array("mail"=>$user))))
            {
                return array("domain"=>$_GET['domain'],"account"=>$user,"level"=>$level);
            }
        }

        /**
         * Checks, whether this object is validated already, tries to validate otherwise
         *
         * @return boolean False if not valid, ie. user could not be authenticated
         */
        function isValid(){

            $PHP_AUTH_USER = $this->_application->getGlobal("user");
            $PHP_AUTH_PW = $this->_application->getGlobal("password");

            //if this object is new, then we have to read authentication input
            if ($this->_new) {
                debug (dbgAuth, 1, "reading new auth object");
                $this->_new = false;

                $this->_user = $PHP_AUTH_USER;
                $this->_password = $PHP_AUTH_PW;
            }

            if (!$this->_valid) {
                debug (dbgAuth, 1, "validating auth object; $this->_user, $this->_password");
                if ($this->_valid = $this->_checkPassword($this->_user, $this->_password)) {
                    $this->_options = $this->_readOptions($this->_user);
                }
            }

            //read options online, when validating - this could be a disadvantage with many users
//            else $this->_readOptions($this->_user);

            return $this->_valid;
        }


        /**
         * getOptions()
         *
         * @return mixed Authentication options array, ie access control or false
         */
        function getOptions() {
            debug (dbgAuth, 1, "getOptions(); $this->_user, $this->_password");
            if ($this->isValid()) {
                return ($this->_options);
            }
            else {
                return false;
            }
        }


        /**
         * emptyoptions()
         *
         * @return array
         */
         function emptyoptions() {
            return array("level"=>-1);
        }

    }
?>
