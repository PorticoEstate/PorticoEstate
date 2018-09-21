<?php
	/**
	* Database abstraction class
	* @author NetUSE AG Boris Erdmann, Kristian Koehntopp
   	* @author Dan Kuykendall, Dave Hall and others
   	* @author Sigurd Nes
	* @copyright Copyright (C) 1998-2000 NetUSE AG Boris Erdmann, Kristian Koehntopp
	* @copyright Portions Copyright (C) 2001-2006 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/lgpl.html GNU Lesser General Public License
	* @link http://www.sanisoft.com/phplib/manual/DB_sql.php
	* @package phpgwapi
	* @subpackage database
	* @version $Id$
	*/

	/**
	* Database abstraction class to allow phpGroupWare to use multiple database backends
	*
	* @package phpgwapi
	* @subpackage database
	*/
	abstract class phpgwapi_db
	{
		/**
		* @var object $adodb holds the legacy ADOdb object
		*/
		var $adodb;

		/**
		* @var object $db holds the db object
		*/
		var $db;

		/**
		* @var string $Host database hostname
		*/
		var $Host;

		/**
		 * @var string $join the sql syntax to use for JOIN
		 */
		 var $join = 'INNER JOIN';


		 var $left_join = 'LEFT JOIN';
		/**
		 * @var string $like the sql syntax to use for a case insensitive LIKE
		 */
		 var $like = 'LIKE';

		/**
		* @var string $Type RDBMS server ??
		*/
		var $Type;

		/**
		* @var string $Database name of database
		*/
		var $Database;

		/**
		* @var string $User name of user used to connect to database
		*/
		var $User;

		/**
		* @var string $Password password used to connect to database
		*/
		var $Password;

		/**
		* @var integer $Port Port used to connect to database
		*/
		var $Port;

		/**
		* @var bool $debug enable debugging
		*/
		var $debug = false;

		/**
		* @var string $Halt_On_Error "yes" (halt with message), "no" (ignore errors quietly), "report" (ignore errror, but spit a warning)
		*/
		var $Halt_On_Error = 'yes';

		/*
		 * @var boolean $Exception_On_Error should SQL throw exception on error ?
		 */
		var $Exception_On_Error = false;
		/**
		* @var bool $auto_stripslashes automagically remove slashes from field values returned?
		*/
		var $auto_stripslashes = false;

		var $resultSet = array();

		var $fetchmode = 'ASSOC';//'BOTH';

		protected $Transaction  = false;

		var $persistent = false;
		var $delayPointer = false;

		var $error_message = '';
		/**
		* Constructor
		* @param string $query query to be executed (optional)
		* @param string $db_type the database engine being used
		*/
		public function __construct($query = null, $db_type = null, $delay_connect = null)
		{
			if ( is_null($db_type) )
			{
				$db_type = $this->Type ? $this->Type : $GLOBALS['phpgw_info']['server']['db_type'];
			}

			$this->Type = $db_type;

			$_key = $GLOBALS['phpgw_info']['server']['setup_mcrypt_key'];
			$_iv  = $GLOBALS['phpgw_info']['server']['mcrypt_iv'];
			$crypto = createObject('phpgwapi.crypto',array($_key, $_iv));

			$this->Database		= $crypto->decrypt($GLOBALS['phpgw_info']['server']['db_name']);
			$this->Host			= $crypto->decrypt($GLOBALS['phpgw_info']['server']['db_host']);
			$this->Port			= $crypto->decrypt($GLOBALS['phpgw_info']['server']['db_port']);
			$this->User			= $crypto->decrypt($GLOBALS['phpgw_info']['server']['db_user']);
			$this->Password		= $crypto->decrypt($GLOBALS['phpgw_info']['server']['db_pass']);

			$this->persistent = isset($GLOBALS['phpgw_info']['server']['db_persistent']) && $GLOBALS['phpgw_info']['server']['db_persistent'] ? true : false;

			// We do it this way to allow it to be easily extended in the future
			switch ( $this->Type )
			{
				case 'postgres':
					$this->join = "JOIN";
					$this->like = "ILIKE";
					break;
				default:
					//do nothing for now
			}

			if( !$delay_connect )
			{
				try
				{
					$this->connect();
				}
				catch(Exception $e)
				{
					throw $e;
				}
			}

			if ( !is_null($query) )
			{
				$this->query($query);
			}
		}

		/**
		* Called when object is cloned
		*/
		public function __clone()
		{

		}


		/**
		* Destructor
		*/
		public function __destruct()
		{

		}

		public function get_error_message( )
		{
			return $this->error_message;
		}
		/**
		* Backward compatibility for get current connection id
		* @return bool true
		*/
		function link_id()
		{
			if(!$this->db)
			{
				$this->connect();
			}
			return true;
		}


		/**
		* Open a connection to a database
		*
		* @param string $Database name of database to use (optional)
		* @param string $Host database host to connect to (optional)
		* @param string $User name of database user (optional)
		* @param string $Password password for database user (optional)
		* @param int    $Port Port for database host (optional)
		*/
		abstract public function connect($Database = null, $Host = null, $User = null, $Password = null, $Port = null);

		/**
		* set_fetch_single:fetch single record from pdo-object, no inpact on adodb
		*
		* @param bool    $value true/false
		*/
		abstract public function set_fetch_single($value = false);



		/**
		* Close a connection to a database - only needed for persistent connections
		*/
		abstract public function disconnect();

		/**
		* Escape strings before sending them to the database
		*
		* @param string $str the string to be escaped
		* @return string escaped sting
		*/
		abstract public function db_addslashes($str);

		/**
		* Convert a unix timestamp to a rdms specific timestamp
		*
		* @param int unix timestamp
		* @return string rdms specific timestamp
		*/
		abstract public function to_timestamp($epoch);
		/**
		* Convert a rdms specific timestamp to a unix timestamp
		*
		* @param string rdms specific timestamp
		* @return int unix timestamp
		*/
		abstract public function from_timestamp($timestamp);

		/**
		* Execute a query with limited result set
		* @param integer $start Row to start from
		* @deprecated
		* @see limit_query()
		*/
		public function limit($start)
		{
			die('where is the sql string?');
		}

		/**
		* Discard the current query result
		*/
		final public function free()
		{
			//unset($this->resultSet);
			$this->resultSet = array();
			return true;
		}

		/**
		* Execute a query
		*
		* @param string $sql the query to be executed
		* @param mixed $line the line method was called from - use __LINE__
		* @param string $file the file method was called from - use __FILE__
		* @param bool $exec true for exec, false for query
		* @return integer current query id if sucesful and null if fails
		*/
		abstract public function query($sql, $line = '', $file = '', $exec = false, $fetch_single = false);

		/**
		* Get the limit statement for a query with limited result set
		*
		* @param string $sql the query to be executed
		* @param integer $offset row to start from
		* @param integer $num_rows number of rows to return (optional), if unset will use $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs']
		* @return string offset and limit
		*/
		function get_offset($sql = '', $offset, $num_rows = 0)
		{
			$offset		=  $offset < 0 ? 0 : (int)$offset;
			$num_rows	= (int)$num_rows;

			if ($num_rows <= 0)
			{
				$maxmatches = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
				$num_rows = isset($maxmatches) && $maxmatches ? (int)$maxmatches : 15;
			}

			switch ( $this->Type )
			{
				case 'mssql':
					$sql = str_replace('SELECT ', 'SELECT TOP ', $sql);
					$sql = str_replace('SELECT TOP DISTINCT', 'SELECT DISTINCT TOP ', $sql);
					$sql = str_replace('TOP ', 'TOP ' . ($offset + $num_rows) . ' ', $sql);
					break;
				case 'oci8':
				case 'oracle':
					//http://www.oracle.com/technology/oramag/oracle/06-sep/o56asktom.html
					//http://dibiphp.com
					if ($offset > 0)
					{
						$sql = 'SELECT * FROM (SELECT t.*, ROWNUM AS "__rownum" FROM (' . $sql . ') t ' . ($num_rows >= 0 ? 'WHERE ROWNUM <= '
						 . ( $offset + $num_rows) : '') . ') WHERE "__rownum" > '.  $offset;
					}
					elseif ($num_rows >= 0)
					{
						$sql = "SELECT * FROM ({$sql}) WHERE ROWNUM <= {$num_rows}";
					}

					break;
				default:
					$sql .= " LIMIT {$num_rows}";
					$sql .=  $offset ? " OFFSET {$offset}" : '';
			}
			return $sql;
		}

		/**
		* Execute a query with limited result set
		*
		* @param string $Query_String the query to be executed
		* @param integer $offset row to start from
		* @param integer $line the line method was called from - use __LINE__
		* @param string $file the file method was called from - use __FILE__
		* @param integer $num_rows number of rows to return (optional), if unset will use $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs']
		* @return integer current query id if sucesful and null if fails
		*/

		abstract function limit_query($Query_String, $offset, $line = '', $file = '', $num_rows = 0);

		/**
		* Move to the next row in the results set
		*
		* @return bool was another row found?
		*/
		abstract public function next_record();

		/**
		* Move to position in result set
		*
		* @param int $pos required row (optional), default first row
		* @return int 1 if sucessful or 0 if not found
		*/
		abstract public function seek($pos = 0);

		/**
		* Begin transaction
		*
		* @return integer|boolean current transaction id
		*/
		abstract public function transaction_begin();

		/**
		* Complete the transaction
		*
		* @return boolean True if sucessful, False if fails
		*/
		abstract public function transaction_commit();

		/**
		* Rollback the current transaction
		*
		* @return boolean True if sucessful, False if fails
		*/
		abstract public function transaction_abort();

		/**
		* Find the value of the last insertion on the current db connection
		* To use this function safely in Postgresql you MUST wrap it in a beginTransaction() commit() block
		*
		* @param string $table name of table the insert was performed on
		* @param string $field not needed - kept for backward compatibility
		* @return integer the id, -1 if fails
		*/
		abstract public function get_last_insert_id($table, $field = '');

		/**
		* Lock a table
		*
		* @param string $table name of table to lock
		* @param string $mode type of lock required (optional), default write
		* @return boolean True if sucessful, False if fails
		*/
		abstract public function lock($table, $mode='write');


		/**
		* Unlock a table
		*
		* @return boolean True if sucessful, False if fails
		*/
		abstract public function unlock();

		/**
		 * Prepare the VALUES component of an INSERT sql statement by guessing data types
		 *
		 * It is not a good idea to rely on the data types determined by this method if
		 * you are inserting numeric data into varchar/text fields, such as street numbers
		 *
		 * @param array $value_set array of values to insert into the database
		 * @return string the prepared sql, empty string for invalid input
		 */
		final public function validate_insert($values)
		{
			if ( !is_array($values) || !count($values) )
			{
				return '';
			}

			$insert_value = array();
			foreach ( $values as $value )
			{
				if($value && $this->isJson($value))
				{
					$insert_value[]	= "'" . $this->db_addslashes($value) . "'";
				}
				else if($value || (is_numeric($value) && $value == 0) )
				{
					if ( is_numeric($value) )
					{
						$insert_value[]	= "'{$value}'";
					}
					else
					{
						$insert_value[]	= "'" . $this->db_addslashes(stripslashes($value)) . "'"; //in case slashes are already added.
					}
				}
				else
				{
					$insert_value[]	= 'NULL';
				}
			}
			return implode(",", $insert_value);
		}

		final public function isJson($string)
		{
			if(!preg_match('/^{/', $string))
			{
				return false;
			}
			json_decode($string);
			return (json_last_error() == JSON_ERROR_NONE);
		}

		/**
		 * Prepare the SET component of an UPDATE sql statement
		 *
		 * @param array $value_set associative array of values to update the database with
		 * @return string the prepared sql, empty string for invalid input
		 */
		final public function validate_update($value_set)
		{
			if ( !is_array($value_set) || !count($value_set) )
			{
				return '';
			}

			$value_entry = array();
			foreach ( $value_set as $field => $value )
			{
				if($value && $this->isJson($value))
				{
					$value_entry[]= "{$field}='" . $this->db_addslashes($value) . "'";
				}
				else if($value || (is_numeric($value) && $value == 0) )
				{
					if ( is_numeric($value) )
					{
						if((strlen($value) > 1 && strpos($value,'0') === 0))
						{
							$value_entry[]= "{$field}='{$value}'";
						}
						else
						{
							$value_entry[]= "{$field}={$value}";
						}
					}
					else
					{
						$value_entry[]= "{$field}='" . $this->db_addslashes(stripslashes($value)) . "'"; //in case slashes are already added.
					}
				}
				else
				{
					$value_entry[]= "{$field}=NULL";
				}
			}
			return implode(',', $value_entry);
		}

		final public function stripslashes( $value )
		{
			$str =  preg_replace_callback('/u([0-9a-fA-F]{4})/', function ($match)
			{
				return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UTF-16BE');
			}, $value);

			return  htmlspecialchars_decode(stripslashes(str_replace(array('&amp;','&#40;', '&#41;', '&#61;','&#8722;&#8722;','&#59;'), array('&','(', ')', '=', '--',';'), $str)),ENT_QUOTES);
		}
		/**
		* Get the number of rows affected by last update
		*
		* @return integer number of rows
		*/
		abstract public function affected_rows();

		/**
		* Number of rows in current result set
		*
		* @return integer number of rows
		*/
		abstract public function num_rows();
		/**
		* Number of fields in current row
		*
		* @return integer number of fields
		*/
		abstract public function num_fields();

		/**
		* Short hand for num_rows()
		* @return integer Number of rows
		* @see num_rows()
		*/
		abstract public function nf();

		/**
		* Short hand for print @see num_rows
		*/
		abstract public function np();

		/**
		* Return the value of a filed
		*
		* @param string $String name of field
		* @param boolean $strip_slashes string escape chars from field(optional), default false
		* @return string the field value
		*/
		abstract public function f($name, $strip_slashes = False);

		/**
		* Print the value of a field
		*
		* @param string $field name of field to print
		* @param bool $strip_slashes string escape chars from field(optional), default false
		*/
		abstract public function p($field, $strip_slashes = True);

		/**
		* Get the id for the next sequence - not implemented!
		*
		* @param string $seq_name name of the sequence
		* @return integer sequence id
		*/
		public function nextid($seq_name)
		{
			//echo "depi: nextid";
		}

		/**
		* Get description of a table
		*
		* @param string $table name of table to describe
		* @param boolean $full optional, default False summary information, True full information
		* @return array Table meta data
		*/
		abstract public function metadata($table,$full = false);

		/**
		* Returns an associate array of foreign keys, or false if not supported.
		*
		* @param string $table name of table to describe
		* @param boolean $owner optional, default False. The optional schema or owner can be defined in $owner.
		* @param boolean $upper optional, default False. If $upper is true, then the table names (array keys) are upper-cased.
		* @return array Table meta data
		*/
		abstract public function MetaForeignKeys($table, $owner=false, $upper=false);

		/**
		* Returns an associate array of foreign keys, or false if not supported.
		*
		* @param string $table name of table to describe
		* @param boolean $primary optional, default False.
		* @return array Index data
		*/

		abstract public function metaindexes($table, $primary = false);

		/**
		* Error handler
		*
		* @param string $msg error message
		* @param int $line line of calling method/function (optional)
		* @param string $file file of calling method/function (optional)
		*/
		abstract public function halt($msg, $line = '', $file = '');

		/**
		* Get a list of table names in the current database
		*
		* @return array list of the tables
		*/
		abstract public function table_names();

		/**
		* Return a list of indexes in current database
		*
		* @return array List of indexes
		*/
		public function index_names()
		{
			//echo "depi: index_names";
			return array();
		}

		/**
		* Create a new database
		*
		* @param string $adminname Name of database administrator user (optional)
		* @param string $adminpasswd Password for the database administrator user (optional)
		* @returns bool was the new db created?
		* @throws Exception invalid db-name
		*/
		abstract public function create_database($adminname = '', $adminpasswd = '');

		/**
		* Get the correct date format for DATE field for a particular RDBMS
		*
		* @internal the string is compatiable with PHP's date()
		* @return string the date format string
		*/
		final public static function date_format()
		{
			static $date_format = null;
			if ( is_null($date_format) )
			{
				switch($GLOBALS['phpgw_info']['server']['db_type'])
				{
					case 'mssql':
						$date_format 		= 'M d Y';
						break;
					case 'mysql':
					case 'pgsql':
					case 'postgres':
					default:
						$date_format 		= 'Y-m-d';
				}
			}
			return $date_format;
	 	}

		/**
		* Get the correct datetime format for DATETIME field for a particular RDBMS
		*
		* @internal the string is compatiable with PHP's date()
		* @return string the date format string
		*/
		final public static function datetime_format()
		{
			static $datetime_format = null;
			if ( is_null($datetime_format) )
			{
				switch($GLOBALS['phpgw_info']['server']['db_type'])
				{
					case 'mssql':
						$datetime_format 		= 'M d Y g:iA';
						break;
					case 'mysql':
					case 'pgsql':
					case 'postgres':
					default:
						$datetime_format 		= 'Y-m-d G:i:s';
				}
			}
			return $datetime_format;
	 	}

		/**
		* Get the correct datetime format for MONEY field for a particular RDBMS
		*
		* @return string the formatted string
		*/
		final public static function money_format($amount)
		{
			if ($GLOBALS['phpgw_info']['server']['db_type']=='mssql')
			{
				return "CONVERT(MONEY,'{$amount}',0)";
			}
			else
			{
				return "'{$amount}'";
			}
		}

		/**
		 * Execute prepared SQL statement for insert
		 *
		 * @param string $sql_string
		 * @param array $valueset  values,id and datatypes for the insert
		 * Use type = PDO::PARAM_STR for strings and type = PDO::PARAM_INT for integers
		 * @return boolean TRUE on success or FALSE on failure
		 */

		abstract public function insert($sql_string, $valueset, $line = '', $file = '');

		/**
		 * Execute prepared SQL statement for select
		 *
		 * @param string $sql_string
		 * @param array $params conditions for the select
		 * @return boolean TRUE on success or FALSE on failure
		 */

		abstract public function select($sql_string, $params, $line = '', $file = '');

		/**
		* Finds the next ID for a record at a table
		*
		* @param string $table tablename in question
		* @param array $key conditions
		* @return int the next id
		*/

		final public function next_id($table='',$key='')
		{
			$where = '';
			$condition = array();
			if(is_array($key))
			{
				foreach ($key as $column => $value)
				{
					if($value)
					{
						$condition[] = $column . "='" . $value;
					}
				}

				if( $condition )
				{
					$where='WHERE ' . implode("' AND ", $condition) . "'";
				}
			}

			$this->query("SELECT max(id) as maximum FROM $table $where",__LINE__,__FILE__);
			$this->next_record();
			$next_id = (int)$this->f('maximum')+1;
			return $next_id;
		}

		final public function get_transaction()
		{
			return $this->Transaction;
		}

		final public function sanitize($sql)
		{
//			return;
			$sql_parts = preg_split('/where/i', $sql);
			if (is_array($sql_parts) && count($sql_parts) > 1 )
			{
				switch ( $this->Type )
				{
					case 'postgres':
						$pattern = "/((?=.*\bUNION\b)(?=.*\bALL\b)|\bPG_SLEEP\b|\bCHR\b|\bGENERATE_SERIES\b)/i";
						break;
					default:
						$pattern = "/((?=.*\bUNION\b)(?=.*\bALL\b)|\bCHR\b)/i";
				}

				$first_element = true;
				foreach ($sql_parts as $sql_part)
				{
					if($first_element == true)
					{
						$first_element = false;
						continue;
					}
					if(preg_match($pattern, $sql))
					{
						$this->transaction_abort();
						trigger_error('Attempt on SQL-injection', E_USER_ERROR);
						exit;
					}
				}
			}
		}
				/**
		 * Marshal values according to type
		 * @param $value the value
		 * @param $type the type of value
		 * @return database value
		 */
		final public function marshal( $value, $type )
		{
			if ($value === null)
			{
				return 'NULL';
			}
			else if ($type == 'int')
			{
				if ($value == '')
				{
					return 'NULL';
				}
				return intval($value);
			}
			else if ($type == 'float')
			{
				return str_replace(',', '.', $value);
			}
			else if ($type == 'field')
			{
				return $this->db_addslashes($value);
			}
			else if ($type == 'jsonb')
			{
				return "'" . json_encode($value) . "'";
			}
			return "'" . $this->db_addslashes($value) . "'";
		}

		/**
		 * Unmarshal database values according to type
		 * @param $value the field value
		 * @param $type	a string dictating value type
		 * @return the php value
		 */
		final public function unmarshal( $value, $type )
		{
			if ($type == 'bool')
			{
				return (bool)$value;
			}
			elseif ($type == 'int')
			{
				return (int)$value;
			}
			elseif ($value === null || $value == 'NULL' || ($type != 'string' && (strlen(trim($value)) === 0)))
			{
				return null;
			}
			elseif ($type == 'float')
			{
				return floatval($value);
			}
			else if ($type == 'json')
			{
				return json_decode($value, true);
			}
			else if ($type == 'datestring')
			{
				return date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], strtotime($value));
			}
			return $this->stripslashes($value);
		}

	}
