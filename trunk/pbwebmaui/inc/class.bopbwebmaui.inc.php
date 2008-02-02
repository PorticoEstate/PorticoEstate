<?php
/**
 * pbwebmaui
 *
 * @author Philipp Kamps <pkamps@probusiness.de>
 * @copyright Copyright (C) 2003,2005 Free Software Foundation http://www.fsf.org/
 * @license http://www.fsf.org/licenses/gpl.html GNU General Public License
 * @package pbwebmaui
 * @version $Id$
 */
 

/**
* Include webmaui application class
*/
require_once(PHPGW_SERVER_ROOT . '/pbwebmaui/inc/pb.WebMAUI/lib/class.application.php');
 

/**
* pbwebmaui business object
*
* @package pbwebmaui
*/
class bopbwebmaui
{
	
	var $pbwebmaui;
	
	function bopbwebmaui()
	{
		if($GLOBALS['phpgw_info']['server']['ldap_host'])
		{
			$this->pbwebmaui = & Application::getInstance(null);
		}
	}

	function run_pbwebmaui()
	{
		if($this->pbwebmaui)
		{
			$this->pbwebmaui->set_TemplateDirectory(PHPGW_SERVER_ROOT.'/pbwebmaui/templates/');
			$this->pbwebmaui->run();
		}
		else
		{
			echo lang('Missing some configuration');
		}
	}
	
	function save_preferences($prefs)
	{
		if (is_array($prefs))
		{
			$GLOBALS['phpgw']->preferences->read_repository();
			while(list($key, $value) = each($prefs))
			{
				$GLOBALS['phpgw']->preferences->add('pbwebmaui', $key, $value, 'forced');
			}
			$GLOBALS['phpgw']->preferences->save_repository(true, 'forced');
		}
	}
	
	function read_pbwebmaui_pref()
	{
		$return =& $GLOBALS['phpgw']->preferences->read();
		return $return['pbwebmaui'];
	}

	
    // }}}
    // {{{ parseDSN()
    /**
     * Parse a data source name
     *
     * A array with the following keys will be returned:
     *  phptype: Database backend used in PHP (mysql, odbc etc.)
     *  dbsyntax: Database used with regards to SQL syntax etc.
     *  protocol: Communication protocol to use (tcp, unix etc.)
     *  hostspec: Host specification (hostname[:port])
     *  database: Database to use on the DBMS server
     *  username: User name for login
     *  password: Password for login
     *
     * The format of the supplied DSN is in its fullest form:
     *
     *  phptype(dbsyntax)://username:password@protocol+hostspec/database
     *
     * Most variations are allowed:
     *
     *  phptype://username:password@protocol+hostspec:110//usr/db_file.db
     *  phptype://username:password@hostspec/database_name
     *  phptype://username:password@hostspec
     *  phptype://username@hostspec
     *  phptype://hostspec/database
     *  phptype://hostspec
     *  phptype(dbsyntax)
     *  phptype
     *
     * @param string $dsn Data Source Name to be parsed
     *
     * @return array an associative array
     *
     * @author Tomas V.V.Cox <cox@idecnet.com>
     */
    function parseDSN($dsn)
    {
        if (is_array($dsn)) {
            return $dsn;
        }

        $parsed = array(
            'phptype'  => false,
            'dbsyntax' => false,
            'username' => false,
            'password' => false,
            'protocol' => false,
            'hostspec' => false,
            'port'     => false,
            'socket'   => false,
            'database' => false
        );

        // Find phptype and dbsyntax
        if (($pos = strpos($dsn, '://')) !== false) {
            $str = substr($dsn, 0, $pos);
            $dsn = substr($dsn, $pos + 3);
        } else {
            $str = $dsn;
            $dsn = NULL;
        }

        // Get phptype and dbsyntax
        // $str => phptype(dbsyntax)
        if (preg_match('|^(.+?)\((.*?)\)$|', $str, $arr)) {
            $parsed['phptype']  = $arr[1];
            $parsed['dbsyntax'] = (empty($arr[2])) ? $arr[1] : $arr[2];
        } else {
            $parsed['phptype']  = $str;
            $parsed['dbsyntax'] = $str;
        }

        if (empty($dsn)) {
            return $parsed;
        }

        // Get (if found): username and password
        // $dsn => username:password@protocol+hostspec/database
        if (($at = strrpos($dsn,'@')) !== false) {
            $str = substr($dsn, 0, $at);
            $dsn = substr($dsn, $at + 1);
            if (($pos = strpos($str, ':')) !== false) {
                $parsed['username'] = rawurldecode(substr($str, 0, $pos));
                $parsed['password'] = rawurldecode(substr($str, $pos + 1));
            } else {
                $parsed['username'] = rawurldecode($str);
            }
        }

        // Find protocol and hostspec

        // $dsn => proto(proto_opts)/database
        if (preg_match('|^([^(]+)\((.*?)\)/?(.*?)$|', $dsn, $match)) {
            $proto       = $match[1];
            $proto_opts  = (!empty($match[2])) ? $match[2] : false;
            $dsn         = $match[3];

        // $dsn => protocol+hostspec/database (old format)
        } else {
            if (strpos($dsn, '+') !== false) {
                list($proto, $dsn) = explode('+', $dsn, 2);
            }
            if (strpos($dsn, '/') !== false) {
                list($proto_opts, $dsn) = explode('/', $dsn, 2);
            } else {
                $proto_opts = $dsn;
                $dsn = null;
            }
        }

        // process the different protocol options
        $parsed['protocol'] = (!empty($proto)) ? $proto : 'tcp';
        $proto_opts = rawurldecode($proto_opts);
        if ($parsed['protocol'] == 'tcp') {
            if (strpos($proto_opts, ':') !== false) {
                list($parsed['hostspec'], $parsed['port']) = explode(':', $proto_opts);
            } else {
                $parsed['hostspec'] = $proto_opts;
            }
        } elseif ($parsed['protocol'] == 'unix') {
            $parsed['socket'] = $proto_opts;
        }

        // Get dabase if any
        // $dsn => database
        if (!empty($dsn)) {
            // /database
            if (($pos = strpos($dsn, '?')) === false) {
                $parsed['database'] = $dsn;
            // /database?param1=value1&param2=value2
            } else {
                $parsed['database'] = substr($dsn, 0, $pos);
                $dsn = substr($dsn, $pos + 1);
                if (strpos($dsn, '&') !== false) {
                    $opts = explode('&', $dsn);
                } else { // database?param1=value1
                    $opts = array($dsn);
                }
                foreach ($opts as $opt) {
                    list($key, $value) = explode('=', $opt);
                    if (!isset($parsed[$key])) { // don't allow params overwrite
                        $parsed[$key] = rawurldecode($value);
                    }
                }
            }
        }

        return $parsed;
    }
	
}
?>