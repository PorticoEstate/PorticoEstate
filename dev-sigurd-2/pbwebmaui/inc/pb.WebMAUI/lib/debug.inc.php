<?php
/**
 * pbWebMAUI
 * @author Andreas Schiller <aschiller@probusiness.de>
 * @copyright Copyright (C) 2002-2003 probusiness AG
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package pbWebMAUI
 */

    // sections
    /**
     * dbgApplication
     */
    define("dbgApplication", 1<<0);
    /**
     * dbgAuth
     */
    define("dbgAuth", 1<<1);
    /**
     * dbgComponent
     */
    define("dbgComponent", 1<<2);
    /**
     * dbgDialog
     */
    define("dbgDialog", 1<<3);
    /**
     * dbgData
     */
    define("dbgData", 1<<4);
    /**
     * dbgAccount
     */
    define("dbgAccount", 1<<5);
    /**
     * dbgDomain
     */
    define("dbgDomain", 1<<6);

    // Output control
    /**
     * View sections from bitor of sections, defined above
     *
     * @var string $debug_section
     * -1 = view all
     * 0 = view nothing
     */
//  $debug_section = dbgApplication;
    $debug_section = dbgApplication|dbgComponent; // dbgAuth|dbgData;
//  $debug_section = dbgDialog;//|dbgComponent;
//  $debug_section = dbgDialog|dbgData|dbgAccount;

    /**
     * Debug level
     *
     * @var string $debug_level
     */
    $debug_level = 0;

    /**
     * Debug strings
     *
     * @var array $debug_strings
     */
    $debug_strings = array();

    /**
     * debug
     *
     * @param $section
     * @param $level
     * @param $comment
     * @param $array
     */
    function debug($section, $level, $comment, $array="")
    {
        //global $debug_section;
        //global $debug_level;
        //global $debug_strings;

      if ($level <= 2) // && ($debug_section == -1 || $debug_section & $section))
			{
				switch ($level)
				{
					case 5:
						$phpgwLevel = 'D-';
						break;
						
					case 4:
						$phpgwLevel = 'I-';
						break;

					case 3:
						$phpgwLevel = 'W-';
						break;
					
					case 2:
						$phpgwLevel = 'E-';
						break;
						
					case 1:
						$phpgwLevel = 'E-'; // should be 'F'
						break;
				}
				$param['text'] = $phpgwLevel.$comment;
				for ($i = 0; $i < count($array); $i++)
				{
					$param['p'.($i + 1)] = $array[$i];
				}
				//$param['line'] = __LINE__; // not supported by the pbwebmaui :-(
				$param['file'] = $section;
				$GLOBALS['phpgw']->log->message($param);
			}
    }

    /**
     * Show array
     *
     * @param $section
     * @param $level
     * @param $comment
     * @param $array
     * @return string
     */
    function show_array($array, $pre="&nbsp;&nbsp;") {
        if (is_array($array) || is_object($array)) {
            if (is_object($array)) {
                $s = "object(".get_class($array).")<br>\n";
                if (strpos(get_class($array), "template") > 0) return $s.$pre."..<br />\n";
            }
            else {
                $s = "array<br />\n";
            }
            if (strlen($pre) <= 5*12) {
                while (list($key, $val) = each($array)) {
                    $s .= $pre.$key."=".show_array($val, $pre."&nbsp;&nbsp;");
                }
            }
            else {
                $s .= $pre."..(more)..<br />\n";
            }
            return $s;
        }
        else return $array."<br />\n";
    }

/*
    debug(1,9,"comment", array("i"=>$i,"names"=>array("andy","christa")))

    1,9,comment,i=1
    1,9,comment...
      i=1
      names=
        [0]="andy"
        [1]="christa"
*/

    /**
     * Get debug
     *
     * @return string
     */
    function get_debug() {
    	return '';
        //global $debug_strings;

        $s = "<table width=\"800\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
        foreach($debug_strings as $array) {
            $s .= "<tr><td class=\"list".(++$i%2)."\">\n";
            $s .= sprintf("%s,%s - %s", $array["section"], $array["level"], $array["string"]);
            $s .= "\n</td></tr>\n";
        }

        $s .= "</table>\n";
        return $s;
    }
?>
