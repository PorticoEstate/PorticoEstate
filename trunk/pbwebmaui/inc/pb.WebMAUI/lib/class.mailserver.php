<?php
/**
 * pbWebMAUI
 * @author Andreas Schiller <aschiller@probusiness.de>
 * @copyright Copyright (C) 2002-2003 probusiness AG
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package pbWebMAUI
 */
    /**
     * data include file
     * @see data
     */
    require_once "class.data.php";
    /**
     * maildomain include file
     * @see maildomain
     */
    require_once "class.maildomain.php";

    /**
     * Mailserver
     * @package pbWebMAUI
     */
    class Mailserver extends Data {
        /**
         * Filter
         *
         * @var string $_filter
         */
        var $_filter;

        /**
         * Constructor
         *
         * @param $application
         */
        function Mailserver(&$application) {
            $this->Data($application);
        }

        /**
         * Set filter
         *
         * @param $value
         */
        function setFilter($value) {
            $this->_filter = $value;
            $this->clearAttribute("domains");
        }

        /**
         * Override method from class data
         *
         * @param string $attr
         * @access private
         */
        function _readAttribute($attr) {
            switch ($attr) {
                case "domains":
                    if($domains = $this->_getRows("server",
                                                    array("filter"=>empty($this->_filter)?"":$this->_filter),
                                                    array("ou"),
                                                    "ou")) {

                        //copy rows to buffer
                        foreach ($domains as $domain) {
                            $this->_Buffer[$attr]["value"][] = &new MailDomain($this->getApplication(), $domain["ou"][0]);
                        }
                    }
                    $this->_Buffer[$attr]["state"] = stateRead;
                    break;
            }
        }
    }

?>
