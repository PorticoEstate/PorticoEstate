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
	* @version $Id: class.db_pdo.inc.php 11046 2013-04-10 08:26:47Z sigurdne $
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
	class phpgwapi_db  extends phpgwapi_db_
	{
		protected $fetch_single;
		protected $statement_object;
		protected $pdo_fetchmode;

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
		* Called when object is cloned
		*/
		public function __clone()
		{

		}


		/**
		* set_fetch_single:fetch single record from pdo-object
		*
		* @param bool    $value true/false
		*/
		public function set_fetch_single($value = false)
		{
			$this->fetch_single = $value;
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
		* @param int    $Port Port for database host (optional)
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

			if ( !is_null($Port) )
			{
				$this->Port = $Port;
			}

			switch ( $this->Type )
			{
				case 'postgres':
					try
					{
						$this->db = new PDO("pgsql:dbname={$this->Database};host={$this->Host}", $this->User, $this->Password, array(PDO::ATTR_PERSISTENT => $this->persistent));
					}
					catch(PDOException $e){}
					break;
				case 'mysql':
					try
					{
						$this->db = new PDO("mysql:host={$this->Host};dbname={$this->Database}", $this->User, $this->Password, array(PDO::ATTR_PERSISTENT => $this->persistent));
					}
					catch(PDOException $e){}
					break;
				case 'sybase':
				case 'mssql':
					/*
					* On Windows, you should use the PDO_ODBC  driver to connect to Microsoft SQL Server and Sybase databases,
					* as the native Windows DB-LIB is ancient, thread un-safe and no longer supported by Microsoft.
					*/
					try
					{
						$this->db = new PDO("mssql:host={$this->Host},1433;dbname={$this->Database}", $this->User, $this->Password, array(PDO::ATTR_PERSISTENT => $this->persistent));
					}
					catch(PDOException $e){}
					break;
				case 'oci8':
				case 'oracle':
					try
					{

/*
						$this->debug = true;
						$tns = " 
							(DESCRIPTION =
							    (ADDRESS_LIST =
							      (ADDRESS = (PROTOCOL = TCP)(HOST = {$this->Host})(PORT = 21521))
							    )
							    (CONNECT_DATA =
							      (SERVICE_NAME = FELPROD)
							    )
							  )
				       ";

					    $this->db = new PDO("oci:dbname=".$tns,$this->User,$this->Password);
*/
						$port = $this->Port ? $this->Port : 1521;

						$_charset = ';charset=AL32UTF8';
				//		$_charset = '';
						$this->db = new PDO("oci:dbname={$this->Host}:{$port}/{$this->Database}{$_charset}", $this->User, $this->Password);
						unset($_charset);
					}
					catch(PDOException $e){}
					break;
				case 'db2':
					try
					{
						$port     = 50000; // configurable?
						$this->db = new PDO("ibm:DRIVER={IBM DB2 ODBC DRIVER};DATABASE={$this->Database}; HOSTNAME={$this->Host};PORT=50000;PROTOCOL=TCPIP;", $this->User, $this->Password);
					}
					catch(PDOException $e){}
					break;
				case 'MSAccess':
					try
					{
						$this->db = new PDO("odbc:Driver={Microsoft Access Driver (*.mdb)};Dbq=C:\accounts.mdb;Uid=Admin"); // FIXME: parameter for database location
					}
					catch(PDOException $e){}
					break;
				case 'dblib':
					try
					{
						$port     = 10060; // configurable?
						$this->db = new PDO("dblib:host={$this->Host}:{$port};dbname={$this->Database}", $this->User, $this->Password);
					}
					catch(PDOException $e){}
					break;
				case 'Firebird':
					try
					{
						$this->db = new PDO("firebird:dbname=localhost:C:\Programs\Firebird\DATABASE.FDB", $this->User, $this->Password);// FIXME: parameter for database location
					}
					catch(PDOException $e){}
					break;
				case 'Informix':
					//connect to an informix database cataloged as InformixDB in odbc.ini
					try
					{
						$this->db = new PDO("informix:DSN=InformixDB", $this->User, $this->Password);
					}
					catch(PDOException $e){}
					break;
				case 'SQLite':
					try
					{
						$this->db = new PDO("sqlite:/path/to/database.sdb"); // FIXME: parameter for database location
					}
					catch(PDOException $e){}
					break;
				case 'odbc':
					try
					{
						$dsn = 'something';// FIXME
						/*$dsn refers to the $dsn data source configured in the ODBC driver manager.*/
						$this->db = new PDO("odbc:DSN={$dsn}", $this->User, $this->Password);
					//	$this->db = new PDO("odbc:{$dsn}", $this->User, $this->Password);
					}
					catch(PDOException $e){}
					break;
				default:
					throw new Exception(lang('db type %1 not supported', $this->Type));
					
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

			switch ( $this->Halt_On_Error )
			{
				case 'yes':
					$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					break;
				case 'report':
					$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
					break;
				default:
					$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
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
			return strtotime($timestamp);
		}


		protected function _get_fetchmode()
		{
			if($this->fetchmode == 'ASSOC')
			{
				$this->pdo_fetchmode =PDO::FETCH_ASSOC;
			}
			else
			{
				$this->pdo_fetchmode =PDO::FETCH_BOTH;
			}
		}


		/**
		* Execute a query
		*
		* @param string $sql the query to be executed
		* @param mixed $line the line method was called from - use __LINE__
		* @param string $file the file method was called from - use __FILE__
		* @param bool $exec true for exec, false for query
		* @param bool $fetch_single true for using fetch, false for fetchAll
		* @return integer current query id if sucesful and null if fails
		*/
		public function query($sql, $line = '', $file = '', $exec = false, $_fetch_single = false)
		{
			self::_get_fetchmode();
			self::set_fetch_single($_fetch_single);

			$fetch_single = $this->fetch_single;

			if ( !$this->db )
			{
				$this->connect();
			}

			if(!$exec)
			{
				if(preg_match('/(^INSERT INTO|^DELETE FROM|^CREATE|^DROP|^ALTER|^UPDATE)/i', $sql)) // need it for MySQL and Oracle
				{
					$exec = true;
				}
			}

			try
			{
				if($exec)
				{
					$this->affected_rows = $this->db->exec($sql);
					return true;
				}
				else
				{
					$statement_object = $this->db->query($sql);
/*
					$num_rows = $this->statement_object->rowCount();
					if($num_rows > 200)
					{
						$fetch_single = true;
						$this->fetch_single = $fetch_single;
					}
*/
					if($fetch_single)
					{
						$this->resultSet = $statement_object->fetch($this->pdo_fetchmode);
						$this->statement_object = $statement_object;
						unset($statement_object);
					}
					else
					{
						$this->resultSet = $statement_object->fetchAll($this->pdo_fetchmode);
					}
				}
			}

			catch(PDOException $e)
			{
				if ( $e && !$this->Exception_On_Error && $this->Halt_On_Error == 'yes' )
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
				else if($this->Exception_On_Error && $this->Halt_On_Error == 'yes')
				{
					$this->transaction_abort();
					throw $e;
				}
				else if($this->Exception_On_Error && $this->Halt_On_Error != 'yes')
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
		* @param string $sql the query to be executed
		* @param integer $offset row to start from
		* @param integer $line the line method was called from - use __LINE__
		* @param string $file the file method was called from - use __FILE__
		* @param integer $num_rows number of rows to return (optional), if unset will use $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs']
		* @return integer current query id if sucesful and null if fails
		*/

		function limit_query($sql, $offset, $line = '', $file = '', $num_rows = 0)
		{
			$this->_get_fetchmode();
			$offset		= (int)$offset;
			$num_rows	= (int)$num_rows;

			if ($num_rows == 0)
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

			if ($this->debug)
			{
				printf("Debug: limit_query = %s<br />offset=%d, num_rows=%d<br />\n", $sql, $offset, $num_rows);
			}

			try
			{
				$statement_object = $this->db->query($sql);
				$this->resultSet = $statement_object->fetchAll($this->pdo_fetchmode);
			}

			catch(PDOException $e)
			{
				if ( $e && !$this->Exception_On_Error && $this->Halt_On_Error == 'yes' )
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
				else if($this->Exception_On_Error && $this->Halt_On_Error == 'yes')
				{
					$this->transaction_abort();
					throw $e;
				}
				else if($this->Exception_On_Error && $this->Halt_On_Error != 'yes')
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
			if($this->fetch_single)
			{
				if($this->delayPointer)
				{
					$this->delayPointer = false;
				}
				else
				{
					$this->resultSet = $this->statement_object->fetch($this->pdo_fetchmode);
				}
				$this->Record = &$this->resultSet;
				return !!$this->Record;
			}
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
/*
			$bt = debug_backtrace();
			echo "<b>db::{$bt[0]['function']} Called from file: {$bt[0]['file']} line: {$bt[0]['line']}</b><br/>";
			unset($bt);
*/
			if(!$this->db)
			{
				$this->connect();
			}
			
			$this->Transaction = $this->db->beginTransaction();
			return $this->Transaction;
		}
		
		/**
		* Complete the transaction
		*
		* @return boolean True if sucessful, False if fails
		*/ 
		public function transaction_commit()
		{
/*
			$bt = debug_backtrace();
			echo "<b>db::{$bt[0]['function']} Called from file: {$bt[0]['file']} line: {$bt[0]['line']}</b><br/>";
			unset($bt);
*/
			$this->Transaction = false;
			return $this->db->commit();
		}
		
		/**
		* Rollback the current transaction
		*
		* @return boolean True if sucessful, False if fails
		*/
		public function transaction_abort()
		{
/*
			$bt = debug_backtrace();
			echo "<b>db::{$bt[0]['function']} Called from file: {$bt[0]['file']} line: {$bt[0]['line']}</b><br/>";
			unset($bt);
*/
			$ret = false;

			if($this->Transaction)
			{
				$this->Transaction = false;
				try
				{
					$ret = $this->db->rollBack();
				}
				catch(PDOException $e)
				{
					if ( $e )
					{
						trigger_error('Error: ' . $e->getMessage(), E_USER_ERROR);
	//					throw $e;
					}
				}
			}
			return $ret;
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
					$sequence = $this->_get_sequence_field_for_table($table, $field);
					$ret = $this->db->lastInsertId($sequence);
					break;
				case 'mssql':
					//FIXME
					$this->fetchmode = 'BOTH';
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
		protected function _get_sequence_field_for_table($table, $field ='')
		{
			//old naming of sequenses
			$sql = "SELECT relname FROM pg_class WHERE NOT relname ~ 'pg_.*'"
				. " AND relname = '{$table}_{$field}_seq' AND relkind='S' ORDER BY relname";
			$this->query($sql,__LINE__,__FILE__);
			if ($this->next_record())
			{
				return $this->f('relname');
			}
			$sql = "SELECT relname FROM pg_class WHERE NOT relname ~ 'pg_.*'"
				. " AND relname = 'seq_{$table}' AND relkind='S' ORDER BY relname";
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
			$this->transaction_begin();
		}
		
		
		/**
		* Unlock a table
		*
		* @return boolean True if sucessful, False if fails
		*/
		public function unlock()
		{
			$this->transaction_commit();
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
/*
				$num_rows = $this->statement_object->rowCount();
				if($num_rows)
				{
					return $num_rows;
				}
*/
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
						return htmlspecialchars_decode(stripslashes($this->Record[$name]));
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
		public function metadata($table, $full = false)
		{			
			if(!$this->adodb || !$this->adodb->IsConnected())
			{
				$this->_connect_adodb();
			}
			if(!($return = $this->adodb->MetaColumns($table,$full)))
			{
				$return = array();
			}
			$this->adodb->close();
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
			if(!$this->adodb || !$this->adodb->IsConnected())
			{
				$this->_connect_adodb();
			}
			if(!($return = $this->adodb->MetaForeignKeys($table, $owner, $upper)))
			{
				$return = array();
			}
			$this->adodb->close();
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
			if(!$this->adodb || !$this->adodb->IsConnected())
			{
				$this->_connect_adodb();
			}
			if(!($return = $this->adodb->MetaIndexes($table, $primary)))
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
		* @param bool $include_views include views in the listing if any (optional)
		*
		* @return array list of the tables
		*/
		public function table_names($include_views = null)
		{
			$return = array();

			switch ( $this->Type )
			{
				case 'mysql': // Not testet
					$this->query("SHOW FULL TABLES",__LINE__, __FILE__);
					foreach($this->resultSet as $entry)
					{
						if($include_views)
						{
							$return[] =  $entry["Tables_in_{$this->Database}"];
						}
						else
						{
							if ($entry['Table_type'] =='BASE TABLE')
							{
								$return[] =  $entry["Tables_in_{$this->Database}"];
							}
						}
					} 
					break;
				case 'postgres':
					$this->query("SELECT table_name as name, CAST(table_type = 'VIEW' AS INTEGER) as view
						FROM information_schema.tables
						WHERE table_schema = current_schema()",__LINE__, __FILE__);
					foreach($this->resultSet as $entry)
					{
						if($include_views)
						{
							$return[] =  $entry['name'];
						}
						else
						{
							if (!$entry['view'])
							{
								$return[] =  $entry['name'];
							}
						}
					} 
					break;
				case 'mssql': //not testet
					$this->query("SELECT name FROM sysobjects WHERE type='u' AND name != 'dtproperties'",__LINE__, __FILE__);
					foreach($this->resultSet as $entry)
					{
						$return[] =  $entry;				
					}
					break;
				case 'oci8':
				case 'oracle':
					$this->query('SELECT * FROM cat',__LINE__, __FILE__);
					foreach($this->resultSet as $entry)
					{
						if($include_views)
						{
							$return[] =  $entry['TABLE_NAME'];
						}
						else
						{
							if( $entry['TABLE_TYPE']== 'TABLE')
							{
								$return[] =  $entry['TABLE_NAME'];							
							}
						}
					} 
					break;
				default: //fallback
					if(!$this->adodb || !$this->adodb->IsConnected())
					{
						$this->_connect_adodb();
					}

					$return = $this->adodb->MetaTables('TABLES');
					$this->adodb->close();
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
			if ( $this->db )
			{
				$this->db = null; //close the dead connection to be safe
			}

			switch ($GLOBALS['phpgw_info']['server']['db_type'])
			{
				case 'postgres':
					$_database = 'postgres';
					break;
				default:
					$_database = null;
			}

			$database = $this->Database;
			try
			{
				$this->connect($_database);
			}
			catch(Exception $e)
			{
				if($e)
				{
					throw $e;
					return false;
				}
			}

			$this->Database = $database;

			if ( !$this->db )
			{
				throw new Exception('ERROR: Connection FAILED');
			}

			if( !preg_match('/^[a-z0-9_]+$/i', $this->Database) )
			{
				throw new Exception(lang('ERROR: the name %1 contains illegal charackter for cross platform db-support', $this->Database));
			}

			//create the db
			$ok = false;
			try
			{
				$ok = $this->db->exec("CREATE DATABASE {$this->Database}");
			}

			catch(PDOException $e)
			{
				if ( $e )
				{
					throw $e;
				}
			}

			//Grant rights on the db
			switch ($GLOBALS['phpgw_info']['server']['db_type'])
			{
				case 'mysql':
					$this->db->exec("GRANT ALL ON {$this->Database}.*"
							. " TO {$this->User}@{$_SERVER['SERVER_NAME']}"
							. " IDENTIFIED BY '{$this->Password}'");
					break;
				default:
					//do nothing
			}
			$this->db = null;
			return true;
		}

		/**
		 * Execute prepared SQL statement for insert
		 *
		 * @param string $sql 
		 * @param array $valueset  values,id and datatypes for the insert 
		 * Use type = PDO::PARAM_STR for strings and type = PDO::PARAM_INT for integers
		 * @return boolean TRUE on success or FALSE on failure
		 */

		public function insert($sql, $valueset, $line = '', $file = '')
		{		
			try
			{
				$statement_object = $this->db->prepare($sql);
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
		 * @param string $sql 
		 * @param array $params conditions for the select 
		 * @return boolean TRUE on success or FALSE on failure
		 */

		public function select($sql, $params, $line = '', $file = '')
		{		
			$this->_get_fetchmode();
			$this->fetch_single = false;
			try
			{
				$statement_object = $this->db->prepare($sql);
				$statement_object->execute($params);
				$this->resultSet = $statement_object->fetchAll($this->pdo_fetchmode);
			}
			catch(PDOException $e)
			{
				trigger_error('Error: ' . $e->getMessage() . "<br>SQL: $sql\n in File: $file\n on Line: $line\n", E_USER_ERROR);
			}
			$this->delayPointer = true;
		}
	}
