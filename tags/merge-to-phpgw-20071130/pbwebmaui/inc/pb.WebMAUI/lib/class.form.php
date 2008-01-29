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
     * Component
     * @package pbWebMAUI
     */
    class Form extends Component {
        /**
         *  Constructor
         *
         * @param $application
         * @param $parent
         * @param $name
         * @param $templatefile
         */
        function Form(&$application, &$parent, $name, $templatefile) {
            $this->Component($application, $parent, $name, $templatefile);
            $this->setBlockname("");
        }
    }
?>
