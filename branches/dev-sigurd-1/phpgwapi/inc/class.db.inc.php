<?php
	/**
	* Database abstraction class
	* @author NetUSE AG Boris Erdmann, Kristian Koehntopp
   	* @author Dan Kuykendall, Dave Hall and others
	* @copyright Copyright (C) 1998-2000 NetUSE AG Boris Erdmann, Kristian Koehntopp
	* @copyright Portions Copyright (C) 2001-2006 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/lgpl.html GNU Lesser General Public License
	* @link http://www.sanisoft.com/phplib/manual/DB_sql.php
	* @package phpgwapi
	* @subpackage database
	* @version $Id$
	*/

	if (empty($GLOBALS['phpgw_info']['server']['db_type']))
	{
		$GLOBALS['phpgw_info']['server']['db_type'] = 'mysql';
	}
	/**
	* Include concrete database implementation
	*/
	require_once(PHPGW_API_INC . '/adodb/adodb.inc.php');

	/**
	* Database abstraction class to allow phpGroupWare to use multiple database backends
	* 
	* @package phpgwapi
	* @subpackage database
	*/
	class phpgwapi_db
	{

		/**
		* @var object $adodb holds the ADOdb object
		*/
		var $adodb;

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
			
			$this->adodb = newADOConnection($db_type);
			$this->adodb->SetFetchMode(3);
			if($query != '')
			{
				$this->query($query);
			}
		}

		/**
		* Get current connection id
		* @return int current connection id
		*/
		function link_id()
		{
			if(!$this->adodb->isConnected())
			{
				$this->connect();
			}
			return $this->adodb->_connectionID;
		}

		/**
		* Get current query id
		* @return int id of current query
		*/
		public function query_id()
		{
			return $this->Query_ID;
		}

		/**
		* Open a connection to a database
		*
		* @param string $Database name of database to use (optional)
		* @param string $Host database host to connect to (optional)
		* @param string $User name of database user (optional)
		* @param string $Password password for database user (optional)
		*/
		public function connect($Database = '', $Host = '', $User = '', $Password = '')
		{
			$this->Database = $Database != '' ? $Database : $this->Database;
			$this->Host = $Host != '' ? $Host : $this->Host;
			$this->User = $User != '' ? $User : $this->User;
			$this->Password = $Password != '' ? $Password : $this->Password;
			return $this->adodb->connect($this->Host, $this->User, $this->Password, $this->Database);
		}

		/**
		* Close a connection to a database - only needed for persistent connections
		*/
		public function disconnect()
		{
			$this->adodb->close();
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

			if ( !is_object($this->adodb) )  //workaround
			{
				return addslashes($str);
			}
			return substr($this->adodb->Quote($str), 1, -1);
		}

		/**
		* Convert a unix timestamp to a rdms specific timestamp
		*
		* @param int unix timestamp
		* @return string rdms specific timestamp
		*/
		public function to_timestamp($epoch)
		{
			return substr($this->adodb->DBTimeStamp($epoch), 1, -1);
		}

		/**
		* Convert a rdms specific timestamp to a unix timestamp 
		*
		* @param string rdms specific timestamp
		* @return int unix timestamp
		*/
		public function from_timestamp($timestamp)
		{
			return $this->adodb->UnixTimeStamp($timestamp);
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
		* @return integer current query id if sucesful and null if fails
		*/
		public function query($sql, $line = '', $file = '')
		{
			if ( !$this->adodb->isConnected() )
			{
				$this->connect();
			}

			$this->resultSet = $this->adodb->Execute($sql);

			if ( !$this->resultSet && $this->Halt_On_Error == 'yes' )
			{
				if($file)
				{
					trigger_error("$sql\n in File: $file\n on Line: $line\n". $this->adodb->ErrorMsg(), E_USER_ERROR);
				}
				else
				{
					trigger_error("$sql\n". $this->adodb->ErrorMsg(), E_USER_ERROR);
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
		public function limit_query($Query_String, $offset = -1, $line = '', $file = '', $num_rows = -1)
		{
			if ( (int) $num_rows <= 0 )
			{
				$num_rows = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			
			if ( !$this->adodb->isConnected() )
			{
				$this->connect();
			}
			
			$this->resultSet = $this->adodb->SelectLimit($Query_String, $num_rows, $offset);
			if(!$this->resultSet && $this->Halt_On_Error == 'yes')
			{
				trigger_error("$Query_String\n" . $this->adodb->ErrorMsg(), E_USER_ERROR);
			}
			else
			{
				$this->delayPointer = true;
				return true;
			}
		}
		
		/**
		* Move to the next row in the results set
		*
		* @return bool was another row found?
		*/
		public function next_record()
		{
			if($this->resultSet && $this->resultSet->RecordCount())
			{
				if($this->delayPointer)
				{
					$this->delayPointer = false;
					$this->Record =& $this->resultSet->fields;
					return true;
				}
	
				if(!$this->resultSet->EOF)
				{
					$row = $this->resultSet->MoveNext();
					$this->Record =& $this->resultSet->fields;
					return !!$row;
				}
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
				return $this->resultSet->Move($pos);
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
			return $this->adodb->StartTrans();
		}
		
		/**
		* Complete the transaction
		*
		* @return boolean True if sucessful, False if fails
		*/ 
		public function transaction_commit()
		{
			return $this->adodb->CompleteTrans();
		}
		
		/**
		* Rollback the current transaction
		*
		* @return boolean True if sucessful, False if fails
		*/
		public function transaction_abort()
		{
			$this->adodb->FailTrans();
			return $this->adodb->HasFailedTrans();
		}

		/**
		* Find the primary key of the last insertion on the current db connection
		*
		* @param string $table name of table the insert was performed on
		* @param string $field the autoincrement primary key of the table
		* @return integer the id, -1 if fails
		*/
		public function get_last_insert_id($table, $field)
		{
			switch ( $GLOBALS['phpgw_info']['server']['db_type'] )
			{
				case 'postgres':
					$params = explode('.',$this->adodb->pgVersion);

					if ($params[0] < 8 || ($params[0] == 8 && $params[1] ==0))
					{
						$oid = pg_getlastoid($this->adodb->_resultid);
						if ($oid == -1)
						{
							return -1;
						}

						$result = @pg_Exec($this->adodb->_connectionID, "select $field from $table where oid=$oid");
					}
					else
					{
						$result = @pg_Exec($this->adodb->_connectionID, "select lastval()");
					}
	
					if (!$result)
					{
						return -1;
					}

					$Record = @pg_fetch_array($result, 0);

					@pg_freeresult($result);
					if (!is_array($Record)) /* OID not found? */
					{
						return -1;
					}
					return $Record[0];
					break;
				case 'mssql':
					/*  MSSQL uses a query to retrieve the last
					 *  identity on the connection, so table and field are ignored here as well.
					 */
					if(!isset($table) || $table == '' || !isset($field) || $field == '')
					{
					return -1;
					}
					$result = @mssql_query("select @@identity", $this->adodb->_resultid);
					if(!$result)
					{
						return -1;
					}
					return mssql_result($result, 0, 0);
					break;
				default:
					return $this->adodb->Insert_ID($table, $field);
			}
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
			//$this->adodb->BeginTrans();
		}
		
		
		/**
		* Unlock a table
		*
		* @return boolean True if sucessful, False if fails
		*/
		public function unlock()
		{
			//$this->adodb->CommitTrans();
		}
		
		/**
		 * Prepare the VALUES component of an INSERT sql statement
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
				if($value || $value === 0)
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
				if($value || $value === 0)
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
			return $this->adodb->Affected_Rows();
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
				return $this->resultSet->RecordCount();
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
		public function f($name, $strip_slashes = false)
		{
			if($this->resultSet && get_class($this->resultSet) != 'adorecordset_empty')
			{
				if( isset($this->resultSet->fields[$name]) )
				{
					if ($strip_slashes || ($this->auto_stripslashes && ! $strip_slashes))
					{
						return stripslashes($this->resultSet->fields[$name]);
					}
					else
					{
						return $this->resultSet->fields[$name];
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
		public function p($field, $strip_slashes = true)
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
			if($this->debug)
			{
				//echo "depi: metadata";
			}
			
			if(!$this->adodb->IsConnected())
			{
				$this->connect();
			}
			if(!($return =& $this->adodb->MetaColumns($table,$full)))
			{
				$return = array();
			}
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
			if(!$this->adodb->IsConnected())
			{
				$this->connect();
			}
			if(!($return =& $this->adodb->MetaForeignKeys($table, $owner, $upper)))
			{
				$return = array();
			}
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
			$this->adodb->RollbackTrans();
		}
		
		/**
		* Get a list of table names in the current database
		*
		* @return array list of the tables
		*/
		public function table_names()
		{
			if(!$this->adodb->IsConnected())
			{
				$this->connect();
			}
			if(!($return =& $this->adodb->MetaTables('TABLES')))
			{
				$return = array();
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
		*/
		public function create_database($adminname = '', $adminpasswd = '')
		{
			//THIS IS CALLED BY SETUP DON'T KILL IT!
			if ( $this->adodb->IsConnected() )
			{
				$this->adodb->Disconnect(); //close the dead connection to be safe
			}

			$this->adodb = newADOConnection($GLOBALS['phpgw_info']['server']['db_type']);
			$this->adodb->NConnect($this->Host, $adminname, $adminpasswd);
			
			if ( !$this->adodb->IsConnected() )
			{
				echo 'Connection FAILED<br />';
				return false;
			}

			//create the db
			$this->adodb->Execute("CREATE DATABASE {$this->Database}");
		
			//Grant rights on the db
			switch ($GLOBALS['phpgw_info']['server']['db_type'])
			{
				case 'mysql':
					$this->adodb->Execute("GRANT ALL ON {$this->Database}.*"
							. " TO {$this->User}@{$_SERVER['SERVER_NAME']}"
							. " IDENTIFIED BY '{$this->Password}'");
				default:
					//do nothing
			}
			$this->adodb->Disconnect();
			return true;
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
		* Prepare SQL statement
		*
		* @param string $query SQL query
		* @return integer|boolean Result identifier for query_prepared_statement() or FALSE
		* @see query_prepared_statement()
		*/
		public function prepare_sql_statement($query)
		{
			//echo "depi";
			if (($query == '') || (!$this->connect()))
			{
				return false;
			}
			return false;
		}

		/**
		 * Execute prepared SQL statement
		 *
		 * @param resource $result_id Result identifier from prepare_sql_statement()
		 * @param array $parameters_array Parameters for the prepared SQL statement
		 * @return boolean TRUE on success or FALSE on failure
		 * @see prepare_sql_statement()
		 */
		public function query_prepared_statement($result_id, $parameters_array)
		{
			if ((!$this->connect()) || (!$result_id))
			{
				return false;
			}
			return false;
		}  

	}
