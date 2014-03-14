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
	* @version $Id: class.db_adodb.inc.php 11611 2014-01-20 08:42:47Z sigurdne $
	*/

	if ( empty($GLOBALS['phpgw_info']['server']['db_type']) )
	{
		$GLOBALS['phpgw_info']['server']['db_type'] = 'mysql';
	}
	/**
	* Include concrete database implementation
	*/
	require_once PHPGW_API_INC . '/adodb/adodb-exceptions.inc.php';
	require_once PHPGW_API_INC . '/adodb/adodb.inc.php';

	/**
	* Database abstraction class to allow phpGroupWare to use multiple database backends
	* 
	* @package phpgwapi
	* @subpackage database
	*/
	class phpgwapi_db  extends phpgwapi_db_
	{

		/**
		* Constructor
		* @param string $query query to be executed (optional)
		* @param string $db_type the database engine being used
		*/
		public function __construct($query = null, $db_type = null, $delay_connect = null)
		{
			parent::__construct($query, $db_type, $delay_connect);
		}


		/**
		* set_fetch_single:fetch single record from pdo-object, no inpact on adodb
		*
		* @param bool    $value true/false
		*/
		public function set_fetch_single($value = false)
		{
			$this->fetch_single = $value;
		}

		/**
		* Called when object is cloned
		*/
		public function __clone()
		{
			$this->adodb = clone($this->adodb);
		}

		/**
		* Destructor
		*/
		public function __destruct()
		{
			//FIXME will be reenabled when cloning is sorted in property
	//		$this->disconnect();
		}

		/**
		* Get current connection id
		* @return int current connection id
		*/
		function link_id()
		{
			if(!$this->adodb || $this->adodb->IsConnected())
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
		public function connect($Database = null, $Host = null, $User = null, $Password = null, $Port = null)
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

			$type = $this->Type;
			if ( $type == 'mysql' )
			{
			//	$type = 'mysqlt';
				$type = 'mysqli';
			}
			$this->adodb = newADOConnection($type);

			if($this->fetchmode == 'ASSOC')
			{
				$this->adodb->SetFetchMode(ADODB_FETCH_ASSOC);
			}
			else
			{
				$this->adodb->SetFetchMode(ADODB_FETCH_BOTH);
			}

			if($this->persistent)
			{
				try
				{
					$ret = $this->adodb->PConnect($this->Host, $this->User, $this->Password, $this->Database);
				}
				catch(Exception $e){}
			}
			else
			{
				try
				{
					$ret = $this->adodb->Connect($this->Host, $this->User, $this->Password, $this->Database);
				}
				catch(Exception $e){}
			}

			if ( isset($e) && $e )
			{
				if($this->debug)
				{
					$message = $e->getMessage();
				}
				else
				{
					$message = 'could not connect to server';
				}

				throw new Exception($message);
				return false;
			}
			return $ret;
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

			if ( !$this->adodb || $this->adodb->IsConnected() )
			{
				$this->connect();
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
		* Execute a query
		*
		* @param string $sql the query to be executed
		* @param mixed $line the line method was called from - use __LINE__
		* @param string $file the file method was called from - use __FILE__
		* @return integer current query id if sucesful and null if fails
		*/
		public function query($sql, $line = '', $file = '',$exec = false, $fetch_single = false)
		{
			if ( !$this->adodb || $this->adodb->IsConnected() )
			{
				$this->connect();
			}

			try
			{
				$this->resultSet = $this->adodb->Execute($sql);
			}

			catch(Exception $e)
			{
				if ( $e && $this->Halt_On_Error == 'yes' )
				{
					$this->transaction_abort();
					if($file)
					{
						trigger_error('Error: ' . $e->getMessage() . "<br>SQL: $sql\n in File: $file\n on Line: $line\n", E_USER_ERROR);
					}
					else
					{
						trigger_error("$sql\n". $e->getMessage(), E_USER_ERROR);
					}
					exit;
				}
				else if($this->Exception_On_Error)
				{
					throw $e;
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
		public function limit_query($Query_String, $offset, $line = '', $file = '', $num_rows = 0)
		{
			$offset		= (int)$offset;
			$num_rows	= (int)$num_rows;

			if ( $num_rows <= 0 )
			{
				$num_rows = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			
			if ( !$this->adodb || $this->adodb->IsConnected() )
			{
				$this->connect();
			}
			
			try
			{
				$this->resultSet = $this->adodb->SelectLimit($Query_String, $num_rows, $offset);
			}

			catch(Exception $e)
			{
				if ( $e && $this->Halt_On_Error == 'yes' )
				{
					$this->transaction_abort();
					if($file)
					{
						trigger_error('Error: ' . $e->getMessage() . "<br>SQL: $sql\n in File: $file\n on Line: $line\n", E_USER_ERROR);
					}
					else
					{
						trigger_error("$sql\n". $e->getMessage(), E_USER_ERROR);
					}
					exit;
				}
				else if($this->Exception_On_Error)
				{
					throw $e;
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
			if(!$this->adodb || $this->adodb->IsConnected())
			{
				$this->connect();
			}

			$this->Transaction =  $this->adodb->BeginTrans();
			return $this->Transaction;
		}
		
		/**
		* Complete the transaction
		*
		* @return boolean True if sucessful, False if fails
		*/ 
		public function transaction_commit()
		{
			$this->Transaction =  $this->adodb->CommitTrans();
			return $this->Transaction;
		}
		
		/**
		* Rollback the current transaction
		*
		* @return boolean True if sucessful, False if fails
		*/
		public function transaction_abort()
		{
			$ret = false;
			$this->Transaction = false;
			try
			{
				$this->adodb->RollbackTrans();
				$ret = $this->adodb->HasFailedTrans();
			}
			catch(Exception $e)
			{
				if ( $e )
				{
					throw $e;
				}
			}
			return $ret;
		}

		/**
		* Find the primary key of the last insertion on the current db connection
		*
		* @param string $table name of table the insert was performed on
		* @param string $field the autoincrement primary key of the table
		* @return integer the id, -1 if fails
		*/
		public function get_last_insert_id($table, $field ='')
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
					$result = @mssql_query("select @@identity", $this->adodb->_queryID);
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
		public function f($name, $strip_slashes = False)
		{
			if($this->resultSet && get_class($this->resultSet) != 'adorecordset_empty')
			{
				if( isset($this->resultSet->fields[$name]) )
				{
					if ($strip_slashes || ($this->auto_stripslashes && ! $strip_slashes))
					{
						return htmlspecialchars_decode(stripslashes($this->resultSet->fields[$name]));
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
		public function metadata($table,$full = false)
		{
			if($this->debug)
			{
				//echo "depi: metadata";
			}
			
			if(!$this->adodb || $this->adodb->IsConnected())
			{
				$this->connect();
			}
			if(!($return =& $this->adodb->MetaColumns($table,$full)))
			{
				$return = array();
			}
			return $return;
		}

		/**
		* Returns an associate array of foreign keys, or false if not supported.
		*
		* @param string $table name of table to describe
		* @param boolean $owner optional, default False. The optional schema or owner can be defined in $owner.
		* @param boolean $upper optional, default False. If $upper is true, then the table names (array keys) are upper-cased.
		* @return array Table meta data
		*/  
		public function MetaForeignKeys($table, $owner=false, $upper=false)
		{
			if(!$this->adodb || $this->adodb->IsConnected())
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
		* Returns an associate array of foreign keys, or false if not supported.
		*
		* @param string $table name of table to describe
		* @param boolean $primary optional, default False.
		* @return array Index data
		*/  

		public function metaindexes($table, $primary = false)
		{
			if(!$this->adodb || $this->adodb->IsConnected())
			{
				$this->connect();
			}
			if(!($return = $this->adodb->MetaIndexes($table, $primary)))
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
			if(!$this->adodb || $this->adodb->IsConnected())
			{
				$this->connect();
			}

			$return = $this->adodb->MetaTables('TABLES');
			if ( !$return )
			{
				return array();
			}
			return $return;
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
			if ( $this->adodb && $this->adodb->IsConnected() )
			{
				$this->adodb->Disconnect(); //close the dead connection to be safe
			}

			$this->adodb = newADOConnection($GLOBALS['phpgw_info']['server']['db_type']);
			$this->adodb->NConnect($this->Host, $adminname, $adminpasswd);
			
			if ( !$this->adodb || $this->adodb->IsConnected() )
			{
				echo 'Connection FAILED<br />';
				return False;
			}

			if( !preg_match('/^[a-z0-9_]+$/i', $this->Database) )
			{
				throw new Exception(lang('ERROR: the name %1 contains illegal charackter for cross platform db-support', $this->Database));
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
					break;
				default:
					//do nothing
			}
			$this->adodb->Disconnect();
			return True;
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
				$stmt = $this->adodb->Prepare($sql_string);

				foreach($valueset as $fields)
				{
					$values = array();
					foreach($fields as $field => $entry)
					{
						$values[] = $entry['value'];
					}
					$this->adodb->Execute($stmt, $values);
				}
			}
			catch(Exception $e)
			{
				trigger_error('Error: ' . $e->getMessage() . "<br>SQL: $sql\n in File: $file\n on Line: $line\n", E_USER_ERROR);
			}
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
				if($this->fetchmode == 'ASSOC')
				{
					$this->adodb->SetFetchMode(ADODB_FETCH_ASSOC);
				}
				else
				{
					$this->adodb->SetFetchMode(ADODB_FETCH_BOTH);
				}
				$this->resultSet = $this->adodb->Execute($sql, $params);
			}
			catch(Exception $e)
			{
				trigger_error('Error: ' . $e->getMessage() . "<br>SQL: $sql\n in File: $file\n on Line: $line\n", E_USER_ERROR);
			}
			$this->delayPointer = true;
		}
	}
