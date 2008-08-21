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

	if ( empty($GLOBALS['phpgw_info']['server']['db_type']) )
	{
		$GLOBALS['phpgw_info']['server']['db_type'] = 'mysql';
	}

	/**
	* Database abstraction class to allow phpGroupWare to use multiple database backends
	* 
	* @package phpgwapi
	* @subpackage database
	*/
	class phpgwapi_db
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
		 var $join = ' INNER JOIN ';
		 
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
		* @var bool $debug enable debugging
		*/
		var $debug = false;

		/**
		* @var string $Halt_On_Error should connection and script be terminated on error?
		*/
		var $Halt_On_Error = 'yes'; // should be true or false

		/**
		* @var bool $auto_stripslashes automagically remove slashes from field values returned?
		*/
		var $auto_stripslashes = false;
		
		var $resultSet;
		
		var $fetchmode = 'BOTH';
		
		/**
		* Constructor
		* @param string $query query to be executed (optional)
		* @param string $db_type the database engine being used
		*/
		public function __construct($query = null, $db_type = null)
		{
			if ( is_null($db_type) )
			{
				$db_type = $this->Type ? $this->Type : $GLOBALS['phpgw_info']['server']['db_type'];
			}

			$this->Type = $db_type;

			$this->Database = $GLOBALS['phpgw_info']['server']['db_name']; 
			$this->Host = $GLOBALS['phpgw_info']['server']['db_host']; 
			$this->User = $GLOBALS['phpgw_info']['server']['db_user']; 
			$this->Password = $GLOBALS['phpgw_info']['server']['db_pass']; 

			// We do it this way to allow it to be easily extended in the future
			switch ( $this->Type )
			{
				case 'postgres':
					$this->join = " JOIN ";
					$this->like = "ILIKE";
					break;
				default:
					//do nothing for now
			}
			
			$this->connect();

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
		*/
		public function connect($Database = null, $Host = null, $User = null, $Password = null)
		{
			if ( !is_null($Database) )
			{
				$this->Database = $Database;
			}

			if ( !is_null($Host) )
			{
				$this->Host = $Host;
			}

			if ( !is_null($User) )
			{
				$this->User = $User;
			}

			if ( !is_null($Password) )
			{
				$this->Password = $Password;
			}

			$persistent = isset($GLOBALS['phpgw_info']['server']['db_persistent']) && $GLOBALS['phpgw_info']['server']['db_persistent'] ? true : false;
			switch ( $this->Type )
			{
				case 'postgres':
					$this->db = new PDO("pgsql:dbname={$this->Database};host={$this->Host}", $this->User, $this->Password, array(PDO::ATTR_PERSISTENT => $persistent));
					break;
				case 'mysql':
					$this->db = new PDO("mysql:host={$this->Host};dbname={$this->Database}", $this->User, $this->Password, array(PDO::ATTR_PERSISTENT => $persistent));
					break;
				case 'sybase':
				case 'mssql':
					/*
					* On Windows, you should use the PDO_ODBC  driver to connect to Microsoft SQL Server and Sybase databases,
					* as the native Windows DB-LIB is ancient, thread un-safe and no longer supported by Microsoft.
					*/
					$this->db = new PDO("mssql:host={$this->Host},1433;dbname={$this->Database}", $this->User, $this->Password, array(PDO::ATTR_PERSISTENT => $persistent));
					break;
				case 'oracle':
					$this->db = new PDO("OCI:dbname={$this->Database};charset=UTF-8", $this->User, $this->Password);
					break;
				case 'db2':
					$port     = 50000; // configurable?
					$this->db = new PDO("ibm:DRIVER={IBM DB2 ODBC DRIVER};DATABASE={$this->Database}; HOSTNAME={$this->Host};PORT=50000;PROTOCOL=TCPIP;", $this->User, $this->Password);
					break;
				case 'MSAccess':
					$this->db = new PDO("odbc:Driver={Microsoft Access Driver (*.mdb)};Dbq=C:\accounts.mdb;Uid=Admin"); // FIXME: parameter for database location
					break;
				case 'dblib':
					$port     = 10060; // configurable?
					$this->db = new PDO("dblib:host={$this->Host}:{$port};dbname={$this->Database}", $this->User, $this->Password);
					break;
				case 'Firebird':
					$this->db = new PDO("firebird:dbname=localhost:C:\Programs\Firebird\DATABASE.FDB", $this->User, $this->Password);// FIXME: parameter for database location
					break;
				case 'Informix':
					//connect to an informix database cataloged as InformixDB in odbc.ini
					$this->db = new PDO("informix:DSN=InformixDB", $this->User, $this->Password);
					break;
				case 'SQLite':
					$this->db = new PDO("sqlite:/path/to/database.sdb"); // FIXME: parameter for database location
					break;
				case 'odbc':
					$dsn = 'something';// FIXME
					/*$dsn refers to the $dsn data source configured in the ODBC driver manager.*/
					$this->db = new PDO("odbc:DSN={$dsn}", $this->User, $this->Password);
				//	$this->db = new PDO("odbc:{$dsn}", $this->User, $this->Password);
					break;
				default:
					//do nothing for now
			}

			if($this->Halt_On_Error == 'yes')
			{
				$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}
		}

		/**
		* Legacy supprt for quyering metadata from database
		*
		*/
		protected function _connect_adodb()
		{
			require_once PHPGW_API_INC . '/adodb/adodb.inc.php';
			$this->adodb = newADOConnection($this->Type);
			$this->adodb->SetFetchMode(ADODB_FETCH_BOTH);
			return @$this->adodb->connect($this->Host, $this->User, $this->Password, $this->Database);
		}

		/**
		* Close a connection to a database - only needed for persistent connections
		*/
		public function disconnect()
		{
			$this->db = null;
		}

		/**
		* Escape strings before sending them to the database
		*
		* @param string $str the string to be escaped
		* @return string escaped sting
		*/
		public function db_addslashes($str)
		{
			if ( is_null($str) )
			{
				return '';
			}

			if ( !is_object($this->db) )  //workaround
			{
				return addslashes($str);
			}

			return substr($this->db->quote($str), 1, -1);
		}

		/**
		* Convert a unix timestamp to a rdms specific timestamp
		*
		* @param int unix timestamp
		* @return string rdms specific timestamp
		*/
		public function to_timestamp($epoch)
		{
			return date($this->datetime_format(), $epoch);
		}

		/**
		* Convert a rdms specific timestamp to a unix timestamp 
		*
		* @param string rdms specific timestamp
		* @return int unix timestamp
		*/
		public function from_timestamp($timestamp)
		{
			trigger_error('Error: not working - use alterative', E_USER_ERROR);
			return false;
		}

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
		public function free()
		{
			unset($this->resultSet);
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
		public function query($sql, $line = '', $file = '', $exec = false)
		{
			if ( !$this->db )
			{
				$this->connect();
			}

			try
			{
				if($exec)
				{
					return $this->affected_rows = $this->db->exec($sql);
				}
				else
				{
					$statement_object = $this->db->query($sql);
					if($this->fetchmode == 'ASSOC')
					{
						$this->resultSet = $statement_object->fetchAll(PDO::FETCH_ASSOC);
					}
					else
					{
						$this->resultSet = $statement_object->fetchAll(PDO::FETCH_BOTH);
					}
				}
			}

			catch(PDOException $e)
			{
				if ( $e && $this->Halt_On_Error == 'yes' )
				{
					if($file)
					{
						trigger_error('Error: ' . $e->getMessage() . "<br>SQL: $sql\n in File: $file\n on Line: $line\n", E_USER_ERROR);
					}
					else
					{
						trigger_error("$sql\n". $e->getMessage(), E_USER_ERROR);
					}
					$this->transaction_abort();
					exit;
				}
			}

			$this->delayPointer = true;
			return true;
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

		function limit_query($Query_String, $offset, $line = '', $file = '', $num_rows = 0)
		{
			$offset		= intval($offset);
			$num_rows	= intval($num_rows);

			if ($num_rows == 0)
			{
				$maxmatches = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
				$num_rows = (isset($maxmatches)?intval($maxmatches):15);
			}

			if( $this->Type == 'mssql' )
			{
				$Query_String = str_replace('SELECT ', 'SELECT TOP ', $Query_String);
				$Query_String = str_replace('SELECT TOP DISTINCT', 'SELECT DISTINCT TOP ', $Query_String);
				$Query_String = str_replace('TOP ', 'TOP ' . ($offset + $num_rows) . ' ', $Query_String);

			}
			else
			{
				if ($offset == 0)
				{
					$Query_String .= ' LIMIT ' . $num_rows;
				}
				else
				{
					$Query_String .= ' LIMIT ' . $num_rows . ' OFFSET ' . $offset;
				}
			}

			if ($this->debug)
			{
				printf("Debug: limit_query = %s<br />offset=%d, num_rows=%d<br />\n", $Query_String, $offset, $num_rows);
			}

			try
			{
				$statement_object = $this->db->query($Query_String);
				if($this->fetchmode == 'ASSOC')
				{
					$this->resultSet = $statement_object->fetchAll(PDO::FETCH_ASSOC);
				}
				else
				{
					$this->resultSet = $statement_object->fetchAll(PDO::FETCH_BOTH);
				}
			}

			catch(PDOException $e)
			{
				if ( $e && $this->Halt_On_Error == 'yes' )
				{
					if($file)
					{
						trigger_error('Error: ' . $e->getMessage() . "<br>SQL: $sql\n in File: $file\n on Line: $line\n", E_USER_ERROR);
					}
					else
					{
						trigger_error("$sql\n". $e->getMessage(), E_USER_ERROR);
					}
					$this->transaction_abort();
					exit;
				}
			}

			$this->delayPointer = true;
			return true;
		}
		
		/**
		* Move to the next row in the results set
		*
		* @return bool was another row found?
		*/
		public function next_record()
		{
			if($this->resultSet && current($this->resultSet))
			{
				if($this->delayPointer)
				{
					$this->delayPointer = false;
					$this->Record = current($this->resultSet);
					return true;
				}
	
				$row = next($this->resultSet);
				$this->Record =& $row;
				return !!$row;
			}
			return false;
		}

		/**
		* Move to position in result set
		*
		* @param int $pos required row (optional), default first row
		* @return int 1 if sucessful or 0 if not found
		*/
		public function seek($pos = 0)
		{
			if($this->resultSet)
			{
				reset($this->resultSet);
				for ($i=0; $i<$pos; $i++)
				{
					$row = next($this->resultSet);
				}
				return $row;
			}
			return false;
		}

		/**
		* Begin transaction
		*
		* @return integer|boolean current transaction id
		*/
		public function transaction_begin()
		{
			return $this->db->beginTransaction();
		}
		
		/**
		* Complete the transaction
		*
		* @return boolean True if sucessful, False if fails
		*/ 
		public function transaction_commit()
		{
			return $this->db->commit();
		}
		
		/**
		* Rollback the current transaction
		*
		* @return boolean True if sucessful, False if fails
		*/
		public function transaction_abort()
		{
			return $this->db->rollBack();
		}

		/**
		* Find the value of the last insertion on the current db connection
		* To use this function safely in Postgresql you MUST wrap it in a beginTransaction() commit() block
		*
		* @param string $table name of table the insert was performed on
		* @param string $field not needed - kept for backward compatibility
		* @return integer the id, -1 if fails
		*/
		public function get_last_insert_id($table, $field = '')
		{			
			switch ( $GLOBALS['phpgw_info']['server']['db_type'] )
			{
				case 'postgres':
					$sequence = $this->_get_sequence_field_for_table($table);
					$ret = $this->db->lastInsertId($sequence);
					break;
				case 'mssql':
					$this->query("SELECT @@identity", __LINE__, __FILE__);
					$this->next_record();
					$ret = $this->f(0);
					break;
				default:
					$ret = $this->db->lastInsertId();
			}

			if($ret)
			{
				return $ret;
			}
			return -1;
		}

		/**
		* Find the name of sequense for postgres
		*
		* @param string $table name of table
		* @return string name of the sequense, false if fails
		*/
		protected function _get_sequence_field_for_table($table)
		{
			$sql = "SELECT relname FROM pg_class WHERE NOT relname ~ 'pg_.*'"
				. " AND relname LIKE '%$table%' AND relkind='S' ORDER BY relname";
			$this->query($sql,__LINE__,__FILE__);
			if ($this->next_record())
			{
				return $this->f('relname');
			}
			return false;
		}


		/**
		* Lock a table
		*
		* @param string $table name of table to lock
		* @param string $mode type of lock required (optional), default write
		* @return boolean True if sucessful, False if fails
		*/
		public function lock($table, $mode='write')
		{
			//$this->transaction_begin();
		}
		
		
		/**
		* Unlock a table
		*
		* @return boolean True if sucessful, False if fails
		*/
		public function unlock()
		{
			//$this->db->commit();
		}
		
		/**
		 * Prepare the VALUES component of an INSERT sql statement by guessing data types
		 *
		 * It is not a good idea to rely on the data types determined by this method if 
		 * you are inserting numeric data into varchar/text fields, such as street numbers
		 * 
		 * @param array $value_set array of values to insert into the database
		 * @return string the prepared sql, empty string for invalid input
		 */
		public function validate_insert($values)
		{
			if ( !is_array($values) || !count($values) )
			{
				return '';
			}
			
			$insert_value = array();
			foreach ( $values as $value )
			{
				if($value || (is_numeric($value) && $value == 0) )
				{
					if ( is_numeric($value) )
					{
						$insert_value[]	= "$value";
					}
					else
					{
						$insert_value[]	= "'$value'";
					}
				}
				else
				{
					$insert_value[]	= 'NULL';
				}
			}
			return implode(",", $insert_value);
		}

		/**
		 * Prepare the SET component of an UPDATE sql statement
		 * 
		 * @param array $value_set associative array of values to update the database with
		 * @return string the prepared sql, empty string for invalid input
		 */
		public function validate_update($value_set)
		{
			if ( !is_array($value_set) || !count($value_set) )
			{
				return '';
			}
			
			$value_entry = array();
			foreach ( $value_set as $field => $value )
			{
				if($value || (is_numeric($value) && $value == 0) )
				{
					if ( is_numeric($value) )
					{
						$value_entry[]= "{$field}={$value}";
					}
					else
					{
						$value_entry[]= "{$field}='{$value}'";
					}
				}
				else
				{
					$value_entry[]= "{$field}=NULL";
				}
			}
			return implode(',', $value_entry);
		}

		/**
		* Get the number of rows affected by last update
		*
		* @return integer number of rows
		*/
		public function affected_rows()
		{
			return $this->affected_rows;
		}
		
		/**
		* Number of rows in current result set
		*
		* @return integer number of rows
		*/
		public function num_rows()
		{
			if($this->resultSet)
			{
				return count($this->resultSet);
			}
			return 0;
		}

		/**
		* Number of fields in current row
		*
		* @return integer number of fields
		*/
		public function num_fields()
		{
			if($this->resultSet)
			{
				return $this->resultSet->fieldCount();
			}
			return 0;
		}

		/**
		* Short hand for num_rows()
		* @return integer Number of rows
		* @see num_rows()
		*/
		public function nf()
		{
			return $this->num_rows();
		}

		/**
		* Short hand for print @see num_rows
		*/
		public function np()
		{
			print $this->num_rows();
		}

		/**
		* Return the value of a filed
		* 
		* @param string $String name of field
		* @param boolean $strip_slashes string escape chars from field(optional), default false
		* @return string the field value
		*/
		public function f($name, $strip_slashes = False)
		{
			if($this->resultSet)
			{
				if( isset($this->Record[$name]) )
				{
					if ($strip_slashes || ($this->auto_stripslashes && ! $strip_slashes))
					{
						return stripslashes($this->Record[$name]);
					}
					else
					{
						return $this->Record[$name];
					}
				}
				return '';
			}
		}

		/**
		* Print the value of a field
		* 
		* @param string $field name of field to print
		* @param bool $strip_slashes string escape chars from field(optional), default false
		*/
		public function p($field, $strip_slashes = True)
		{
			//echo "depi: p";
			print $this->f($field, $strip_slashes);
		}

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
		public function metadata($table = '',$full = false)
		{			
			if(!$this->adodb || !$this->adodb->IsConnected())
			{
				$this->_connect_adodb();
			}
			if(!($return =& $this->adodb->MetaColumns($table,$full)))
			{
				$return = array();
			}
			$this->adodb->close();
			return $return;
			
			/*
			 * Due to compatibility problems with Table we changed the behavior
			 * of metadata();
			 * depending on $full, metadata returns the following values:
			 *
			 * - full is false (default):
			 * $result[]:
			 *   [0]["table"]  table name
			 *   [0]["name"]   field name
			 *   [0]["type"]   field type
			 *   [0]["len"]    field length
			 *   [0]["flags"]  field flags
			 *
			 * - full is true
			 * $result[]:
			 *   ["num_fields"] number of metadata records
			 *   [0]["table"]  table name
			 *   [0]["name"]   field name
			 *   [0]["type"]   field type
			 *   [0]["len"]    field length
			 *   [0]["flags"]  field flags
			 *   ["meta"][field name]  index of field named "field name"
			 *   The last one is used, if you have a field name, but no index.
			 *   Test:  if (isset($result['meta']['myfield'])) { ...
			 */
		}

		/**
		* Returns an associate array of foreign keys, or false if not supported.
		*
		* @param string $table name of table to describe
		* @param boolean $owner optional, default False. The optional schema or owner can be defined in $owner.
		* @param boolean $upper optional, default False. If $upper is true, then the table names (array keys) are upper-cased.
		* @return array Table meta data
		*/  
		public function MetaForeignKeys($table = '', $owner=false, $upper=false)
		{
			if(!$this->adodb || !$this->adodb->IsConnected())
			{
				$this->_connect_adodb();
			}
			if(!($return =& $this->adodb->MetaForeignKeys($table, $owner, $upper)))
			{
				$return = array();
			}
			$this->adodb->close();
			return $return;
		}

		/**
		* Error handler
		*
		* @param string $msg error message
		* @param int $line line of calling method/function (optional)
		* @param string $file file of calling method/function (optional)
		*/
		public function halt($msg, $line = '', $file = '')
		{
			$this->db->rollBack();
		}
		
		/**
		* Get a list of table names in the current database
		*
		* @return array list of the tables
		*/
		public function table_names()
		{
			if(!$this->adodb || !$this->adodb->IsConnected())
			{
				$this->_connect_adodb();
			}

			$return = $this->adodb->MetaTables('TABLES');
			$this->adodb->close();
			if ( !$return )
			{
				return array();
			}
			return $return;
		}

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
		public function create_database($adminname = '', $adminpasswd = '')
		{
			//THIS IS CALLED BY SETUP DON'T KILL IT!
			if ( $this->db )
			{
				$this->db = null; //close the dead connection to be safe
			}

			$this->connect();
			
			if ( !$this->db )
			{
				echo 'Connection FAILED<br />';
				return False;
			}

			if( !preg_match('/^[a-z0-9_]+$/i', $this->Database) )
			{
				throw new Exception(lang('ERROR: the name %1 contains illegal charackter for cross platform db-support', $this->Database));
			}

			//create the db
			$this->db->exec("CREATE DATABASE {$this->Database}");
		
			//Grant rights on the db
			switch ($GLOBALS['phpgw_info']['server']['db_type'])
			{
				case 'mysql':
					$this->db->exec("GRANT ALL ON {$this->Database}.*"
							. " TO {$this->User}@{$_SERVER['SERVER_NAME']}"
							. " IDENTIFIED BY '{$this->Password}'");
				default:
					//do nothing
			}
			$this->db = null;
			return True;
		}
		/**
		* Get the correct date format for DATE field for a particular RDBMS
		*
		* @internal the string is compatiable with PHP's date()
		* @return string the date format string
		*/
		public static function date_format()
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
		public static function datetime_format()
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
		 * Execute prepared SQL statement for insert
		 *
		 * @param string $sql_string 
		 * @param array $valueset  values,id and datatypes for the insert 
		 * Use type = PDO::PARAM_STR for strings and type = PDO::PARAM_INT for integers
		 * @return boolean TRUE on success or FALSE on failure
		 */

		public function insert($sql_string, $valueset, $line = '', $file = '')
		{		
			try
			{
				$statement_object = $this->db->prepare($sql_string);
				foreach($valueset as $fields)
				{
					foreach($fields as $field => $entry)
					{
						$statement_object->bindParam($field, $entry['value'], $entry['type']);
					}
					$ret = $statement_object->execute();
				}
			}

			catch(PDOException $e)
			{
				trigger_error('Error: ' . $e->getMessage() . "<br>SQL: $sql\n in File: $file\n on Line: $line\n", E_USER_ERROR);
			}
			return $ret;
		}

		/**
		 * Execute prepared SQL statement for select
		 *
		 * @param string $sql_string 
		 * @param array $params conditions for the select 
		 * @return boolean TRUE on success or FALSE on failure
		 */

		public function select($sql_string, $params, $line = '', $file = '')
		{		
			try
			{
				$statement_object = $this->db->prepare($sql_string);
				$statement_object->execute($params);
				if($this->fetchmode == 'ASSOC')
				{
					$this->resultSet = $statement_object->fetchAll(PDO::FETCH_ASSOC);
				}
				else
				{
					$this->resultSet = $statement_object->fetchAll(PDO::FETCH_BOTH);
				}
			}
			catch(PDOException $e)
			{
				trigger_error('Error: ' . $e->getMessage() . "<br>SQL: $sql\n in File: $file\n on Line: $line\n", E_USER_ERROR);
			}
			$this->delayPointer = true;
		}
	}
