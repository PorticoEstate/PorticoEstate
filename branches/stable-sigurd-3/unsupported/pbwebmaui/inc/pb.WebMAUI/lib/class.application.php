<?php
/**
 * pbWebMAUI
 * @author Andreas Schiller <aschiller@probusiness.de>
 * @copyright Copyright (C) 2002-2003 probusiness AG
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package pbWebMAUI
 */

    /**
     * globals include file
     * @see globals
     */
    require_once "class.pbwebmauiconfig.php";

    /**
     * globals include file
     * @see globals
     */
    require_once "class.globals.php";
    /**
     * main include file
     * @see main
     */
    require_once "class.main.php";
    /**
     * auth include file
     * @see auth
     */
    require_once "class.auth.php";

    /**
     * debug include file
     * @see debug
     */
    require_once "debug.inc.php";

    /**
     * This is the main class showing the application
     * @package pbWebMAUI
     */
    class Application {
        /**
         * Directory to templates
         *
         * @var string $TemplateDirectory relative path to templates directory
         * @see Application()
         */
        var $TemplateDirectory;

        /**
         * subdirectory of templatedir, defining style
         *
         * @var string $Style relative path from templates directory
         * @see Application()
         */
        var $Style;

				var $_config;
				
        var $_globals;
        
        /**
         * array of authentication objects, used in getAuth()
         *
         * @var array $_auths
         */
        var $_auths;                      

        /**
         * Constructor
         *
         * @param array $args
         * @access private
         */
        function Application($args = array("style" => "")) {
            debug(dbgApplication, 3, "constructing application", array("args"=>$args));
            
            $this->_globals = new Globals();
            
            $this->set_TemplateDirectory('./templates');

            if (!empty($args["style"])) {
                $this->Style = $args["style"];
            }
            else {
                $globalstyle = $this->getGlobal("style");
                if (!empty($globalstyle)) {
                    $this->Style = $globalstyle;
                }
                else {
                    $this->Style = "default";
                }
            }
            $this->setGlobal("style", $this->Style);
            
            $this->_config = new pbwebmauiConfig();
        }
        
        function &getInstance($args)
        {
        	static $instance;
        	if($instance == null)
        	{
        		$instance = new Application($args);
       		}
       		return $instance;
				}

        /**
         * run
         */
        function run() {
            if (strlen($_GET["popup"])) {
                $fmMain = & new Main($this, "Popup");

                $fmMain->action($_GET["action"], array_merge($_GET, array("formdata"=>$_POST)));
                $fmMain->show();
            }
            else {
                $fmMain = & new Main($this, "Main");
                $fmMain->action($_GET["action"], array_merge($_GET, array("formdata"=>$_POST)));
                $fmMain->show();
            }
        }

        /**
         * getAuth
         *
         * @param boolean $idx
         * @param boolean $forcenew
         * @return mixed array of authentication options or false if no (more) object is found, 
         */
        function getAuth(&$idx, $forcenew=false) {
            if (!$idx) {
                //initialize array of auth objects
                if (!empty($_GET["user"])) {
                    $this->_auths[] = new pbAuth($_GET["user"], $_GET["password"]);
                    $this->_globals->setValue("auths", $this->_auths);
                }
                else if (!empty($_GET["action"])) {
                    $this->_auths = $this->_globals->getValue("auths");
                    if(!$this->_auths)
                    {
                    	$this->_auths[] = new pbAuth();
                    }
                }
                else {
                    $this->_globals->setValue("auths", "");
                }

                debug(dbgApplication, 1, sprintf("initialized auths array, count=%d", count($this->_auths)));
            }

            debug(dbgApplication, 1, sprintf("auths array, count=%d, idx=%d", count($this->_auths), $idx));
            //if ($this->i++ > 10) return; //for debugging purposes... there might be endless loops

            if (is_array($this->_auths)) {
                $keys=array_keys($this->_auths);
                while (isset($keys[$idx])) {
                    //if ($this->i++ > 10) break; //for debugging purposes... there might be endless loops

                    $key = $keys[$idx++];
                    $auth = $this->_auths[$key];

                    debug(dbgApplication, 1, sprintf("check auth key=%s; auth=%s", $key, $auth->_user));
                    if ($authoptions = $auth->getOptions()) {
                        debug(dbgAuth, 1, sprintf("got options key=%s; auth=%s", $key, $auth->_user));
                        $this->_auths[$key] = $auth;
                        $this->_globals->setValue("auths", $this->_auths);

                        $k++;
                        return $authoptions;
                    }
                    else {
                        //could not getOptions of auth object it must be invalid, remove from array
                        debug (dbgAuth, 2, "keys befor unset of $key", array_keys($this->_auths));
                        unset($this->_auths[$key]);
                        $keys=array_keys($this->_auths);
                        $this->_globals->setValue("auths", $this->_auths);
                        debug (dbgAuth, 2, "keys after unset of $key", array_keys($this->_auths));
                    }
                }
            }

            //no more authentication object found, ask for additional authentication
            if ($forcenew) {
                debug (dbgApplication, 1, "no more authentication object found, ask for additional authentication");

                $this->_auths[] = new pbAuth();
                $this->_globals->setValue("auths", $this->_auths);
            }
            return false;
        }

        /**
         * Discards every auth object (logout)
         *
         * logout from application by discarding every auth object
         */
        function discard_auths(){
            if (is_array($this->_auths)) {
                foreach(array_keys($this->_auths) as $key) {
                    debug (dbgAuth, 2, "discarding auth key $key");
                    unset($this->_auths[$key]);
                }
            }

            $this->_globals->setValue("auths", $this->_auths);
            debug (dbgAuth, 2, "discarded auth objects", array("local auths"=>$this->_auths,"global auths"=>$this->_globals->getValue("auths")));
        }

        /**
         * Reads first username from Auth Array and returns this as actual Username
         *
         * @return string (first) actual username
         */
        function getUsername() {
            debug(dbgApplication, 2, "getUsername", array("_auths"=>$this->_auths));
            if (is_array($this->_auths)) {
                $auth = $this->_auths[0];

                $username = $auth->_user;
            }
            return ($username);
        }

        /**
         * Read global value from session
         *
         * @param string $name name of global var to read
         * @return mixed value of global var
         */
        function getGlobal($name) {
            return ($this->_globals->getValue($name));
        }

        /**
         * Sets global value from session
         *
         * @param string $name name of global var
         * @param mixed $value value to set
         */
        function setGlobal($name, $value) {
            $this->_globals->setValue($name, $value);
        }

        /**
         * Redirect browser to new location
         *
         * @param string $uri location to redirect to
         * @param boolean $debug switch debug mode on/off
         */
        function redirect($uri, $debug=false)
        {
          $pieces = explode('?', $uri);
          if (!$debug)
          {
            header("Location: ".$GLOBALS['phpgw']->link('/pbwebmaui/index.php', $pieces[1]));
          }
          else
          {
            printf("debug redirect: <a href=\"%s\">%s</a>", $uri, $uri);
          }
        }
        
        function set_TemplateDirectory($path)
        {
            $this->TemplateDirectory = $path;
        }
        
        function lang($string)
        {
        	return lang($string);
        }
    }
?>
