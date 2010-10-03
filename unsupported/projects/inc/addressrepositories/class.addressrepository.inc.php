<?php
	/**
	* addressrepository
	* @author Philipp Kamps <pkamps@probsuiness.de>
	* @copyright Copyright (C) 2000-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @version
	*/

	class addressrepository
	{

		var $id;
		var $connection;
		
		var $fields_listInfo = array();
		var $fields_emailRecipient = array('id','fullname','email');
		
		var $public_functions = array
		(
			'get_list'       => true,
			'get_categories' => true
		);

		/**
		* exchange
		*
		* @param 
		*/
		function addressrepository($dsn)
		{
			$parsdDSN = $this->parseDSN($dsn);
			$this->type     = $parsdDSN['phptype'];
			$this->username = $parsdDSN['username'];
			$this->password = $parsdDSN['password'];
			$this->protocol = $parsdDSN['protocol'];
			$this->hostspec = $parsdDSN['hostspec'];
			$this->port     = $parsdDSN['port'];
			$this->category = $parsdDSN['database'];
		}

		/**
		* Get list
		*
		*/
		function get_list($category = '', $filter = '', $attributes = false)
		{
 		}

		function get_categories()
		{
		}

		function set_id($id)
		{
			$this->id = $id;
		}

		function get_id()
		{
			return $this->id;
		}

		function get_details($id)
		{	
		}

		function set_category($category)
		{
			$this->category = $category;
		}
		
		function keep_hierachy($todolist)
		{
			for ($i = 0; $i < count($todolist); $i++)
			{
				//echo $i.':'.$this->get_hierarchy_index($todolist, $todolist[$i]['parent']).'.'.$todolist[$i]['id'].'<br>';
				$todolist_hierarchy[$this->get_hierarchy_index($todolist, $todolist[$i]['parent']).'.'.$todolist[$i]['id']] = $todolist[$i];
			}
			//echo '<br><br>';
			ksort($todolist_hierarchy, SORT_STRING);

			$return = array();
			foreach($todolist_hierarchy as $key => $val)
			{
    			$level = explode('.', $key);
    			for($i=0; $i < count($level); $i++)
    			{
    				if($level[$i])
    				{
    					$val['name'] = '&nbsp;'.$val['name'];
    				}
    			}
    			$return[] = $val;
    			//echo $val['name']." <=".$key.'<br>';
			}
			return $return;
		}
		
		function get_hierarchy_index($todo, $index)
		{
			for ($i = 0; $i < count($todo); $i++)
			{
				if ($todo[$i]['id'] == $index)
				{
					if ($todo[$i]['parent'] != 0)
					{
						$return = $this->get_hierarchy_index($todo,$todo[$i]['parent']).'.'.$todo[$i]['id'];
					} else
					{
						$return = '.'.$todo[$i]['id'];
						break;
					}
				}
			}
			return $return;
 		}
		
		function parseFullName($last, $first = '', $middle = '', $title = '')
		{
			return $GLOBALS['phpgw']->common->display_fullname('',$first,$last);
		} 
		
		function get_category()
		{
			return $this->category;
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

		function sortData(&$sortDataArray, $sortCompareMethod)
		{
			if(!is_array($sortDataArray))
				return false;

			switch($sortCompareMethod)
			{
				case 'list':       $sortCompareMethod = 'sort_list';       break;
				case 'categories': $sortCompareMethod = 'sort_categories'; break;
				default: return false;
			}
			
			return usort($sortDataArray, array(get_class($this), $sortCompareMethod));
		}
		
		function sort_list($a, $b)
		{
	    return strcasecmp($a["fullname"], $b["fullname"]);
		}

		function sort_categories($a, $b)
		{
	    return strcasecmp($a["name"], $b["name"]);
		}
	}
?>