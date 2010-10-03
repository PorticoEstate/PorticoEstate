<?php
/**
 * pbWebMAUI
 *
 * Storage object for global varables.
 *
 * @author Andreas Schiller <aschiller@probusiness.de>
 * @copyright Copyright (C) 2002-2003 probusiness AG
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package pbWebMAUI
 */
    /**
     * constans include file
     * @see constants
     */
    require_once "constants.inc.php";

    /**
     * Globals
     * @package pbWebMAUI
     */
    class Globals {
        /**
         * 
         *
         * @var string $__name
         * @access private
         */
        var $__name;                    
        
        /**
         * 
         *
         * @var string $__usesession
         * @access private
         */
        var $__usesession;

        /**
         * 
         *
         * @var string $__globals
         * @access private
         */
        var $__globals;


        /**
         * Constructor
         *
         * @param string $name
         */
        function Globals($name='pbwebmaui') {
            $this->__name = $name;

            if (empty($_SERVER["SERVER_PROTOCOL"])) {
                //PHP was called from command line, we should not cache global values
                $this->__usesession = false;
            }
            else {
                $this->__usesession = true;
                debug(dbgApplication, 3, "globals, started session with $name", array("_SESSION"=>$_SESSION));
            }
        }

        /**
         * Get value
         *
         * @param string $name
         * @return string
         */
        function getValue($name) {
            if ($this->__usesession) {
            		$return = $GLOBALS['phpgw']->session->appsession($name, $this->__name);
                debug(dbgApplication, 3, "globals, getValue($name)", array("_SESSION"=>$_SESSION));
                return $return;
            }
            else {
                return ($this->__globals[$name]);
            }
        }

        /**
         * Set value
         *
         * @param string $name
         * @param string $value
         */
        function setValue($name, $value) {
            if ($this->__usesession)
            {
							$GLOBALS['phpgw']->session->appsession($name, $this->__name, $data = $value);
            	debug(dbgApplication, 3, "globals, setValue($name, $value)", array("_SESSION"=>$_SESSION));
            }
            else {
                $this->__globals[$name] = $value;
            }
        }
    }
?>
