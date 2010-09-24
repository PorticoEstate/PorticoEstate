<?php
	/*
	 * Oracle/OCI8 accessor based on Session Management for PHP3
	 *
	 * (C) Copyright 1999-2000 Stefan Sels phplib@sels.com
	 *
	 * based on db_oracle.inc by Luis Francisco Gonzalez Hernandez 
	 * contains metadata() from db_oracle.inc 1.10
	 *
	 * $Id$
	 *
	 */ 

	class DB_OCI8 
	{
		var $Debug    =  0;
		var $sqoe     =  1; // show query on error
		var $Halt_On_Error = "yes"; ## "yes" (halt with message), "no" (ignore errors quietly), "report" (ignore errror, but spit a warning)

		var $Host     = "";
		var $Port     = "1521";
		/* traditionally the full TNS name is placed in $Database; if having trouble with TNS resolution (and desiring a more legible configuration), place the host IP address in $Host and the Oracle SID in $Database as a shortcut - connect() will build a valid connection string using $full_connection_string */
		var $Database = "";
		var $User     = "";
		var $Password = "";
		var $full_connection_string = "(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=%s)(PORT=%s)))(CONNECT_DATA=(SID=%s)))";

		var $Link_ID    = 0;
		var $Query_ID   = 0;
		var $Record     = array();
		var $Row;
		var $Parse;
		var $Error      = "";
		var $autoCommit = 1; // Commit on successful query
		var $autoCount  = 1; // Count num_rows on select
		
		var $share_connections = false;
		var $share_connection_name = "";
		  // Defaults to the class name - set to another class name to share connections among different class extensions
		
		var $last_query_text = "";

		var $num_rows; // Used to store the total of rows returned by a SELECT statement.
		var $auto_stripslashes = false;
		var $persistent = false;
		
		/* public: constructor */
		function DB_OCI8($query = "")
		{
			if($query)
			{
				$this->query($query);
			}
		}
		
		function link_id()
		{
			return $this->Link_ID;
		}
		
		function query_id()
		{
			return $this->Query_ID;
		}

		function connect()
		{
			if ( 0 == $this->Link_ID )
			{
				if ($this->Debug)
				{
					printf("<br>Connecting to $this->Database%s...<br>\n", (($this->Host) ? " ($this->Host)" : ""));
				}
				if($this->share_connections)
				{
					if(!$this->share_connection_name)
					{
						$this->share_connection_name = get_class($this) . "_Link_ID";
					}
					else
					{
						$this->share_connection_name .= "_Link_ID";
					}
					global ${$this->share_connection_name};
					if(${$this->share_connection_name})
					{
						$this->Link_ID = ${$this->share_connection_name};
						return true;
					}
				}
				
				if($this->persistent)
				{
					$this->Link_ID = oci_pconnect($this->User, $this->Password, (($this->Host) ? sprintf($this->full_connection_string, $this->Host, $this->Port, $this->Database) : $this->Database), 'AL32UTF8');
				}
				else
				{
					$this->Link_ID = oci_connect($this->User, $this->Password, (($this->Host) ? sprintf($this->full_connection_string, $this->Host, $this->Port, $this->Database) : $this->Database), 'AL32UTF8');
				}

				if (!$this->Link_ID)
				{
					$this->connect_failed();
					return false;
				} 
				if($this->share_connections)
				{
					${$this->share_connection_name} = $this->Link_ID;
				}
				if ($this->Debug)
				{
					printf("<br>Obtained the Link_ID: $this->Link_ID<br>\n");
				}
			}
		}
		
		function connect_failed()
		{
			$this->Halt_On_Error = "yes";
//			$this->halt(sprintf("connect ($this->User, \$Password, $this->Database%s) failed", (($this->Host) ? ", $this->Host" : "")));
			$this->halt('Connect failed');
		}
		
		function free()
		{
			if ($this->Parse)
			{
				if ($this->Debug)
				{
					printf("<br>Freeing the statement: $this->Parse<br>\n");
				}
				$result = @OCIFreeStatement($this->Parse);
				if (!$result)
				{
					$this->Error = OCIError($this->Link_ID);
					if ($this->Debug)
					{
						printf("<br>Error: %s<br>", $this->Error["message"]);
					}
				}
			} 
		}

		function query($Query_String)
		{
			$this->connect();
			$this->free();
		 
			$this->Parse = OCIParse($this->Link_ID, $Query_String);
			if (!$this->Parse)
			{
				$this->Error = OCIError($this->Parse);
			}
			else
			{
				if ($this->autoCommit)
				{
					OCIExecute($this->Parse, OCI_COMMIT_ON_SUCCESS);
				}
				else
				{
					OCIExecute($this->Parse, OCI_DEFAULT);
				}
				if ($this->autoCount)
				{
					/* need to repeat the query to count the returned rows from a "select" statement. */
					if (eregi("SELECT", $Query_String))
					{
						/* On $this->num_rows I'm storing the returned rows of the query. */
						$this->num_rows = OCIFetchStatement($this->Parse, $aux);
						OCIExecute($this->Parse, OCI_DEFAULT);
					}
				}
				$this->Error = OCIError($this->Parse);
			}
			
			$this->Row = 0;
			
			if ($this->Debug)
			{
				printf("Debug: query = %s<br>\n", $Query_String);
			}
			
			if ((1403 != $this->Error["code"]) and (0 != $this->Error["code"]) and $this->sqoe)
			{
				echo "<BR><FONT color=red><B>".$this->Error["message"]."<BR>Query :\"$Query_String\"</B></FONT>";
			}
			$this->last_query_text = $Query_String;
			return $this->Parse;
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
		public function limit_query($sql, $offset = -1, $line = '', $file = '', $nrows = -1)
		{
			$this->connect();
			$this->free();
		 
			$this->Parse = OCIParse($this->Link_ID, $sql);
			if (!$this->Parse)
			{
				$this->Error = OCIError($this->Parse);
			}

			OCIExecute($this->Parse, OCI_DEFAULT);

			if ( (int) $nrows <= 0 )
			{
				$nrows = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}

			$this->firstrows = true;
			if ($this->firstrows)
			{
				if (strpos($sql,'/*+') !== false)
				{
					$sql = str_replace('/*+ ','/*+FIRST_ROWS ',$sql);
				}
				else
				{
					$sql = preg_replace('/^[ \t\n]*select/i','SELECT /*+FIRST_ROWS*/',$sql);
				}
			}

			 // Algorithm by Tomas V V Cox, from PEAR DB oci8.php
			
			 // Let Oracle return the name of the columns
			$q_fields = "SELECT * FROM (".$sql.") WHERE NULL = NULL";

/*
			$false = false;
			if (! $stmt_arr = $this->Prepare($q_fields)) {
				return $false;
			}
			$stmt = $stmt_arr[1];
			 

			if (is_array($inputarr)) {
			 	foreach($inputarr as $k => $v) {
					if (is_array($v)) {
						if (sizeof($v) == 2) // suggested by g.giunta@libero.
							OCIBindByName($stmt,":$k",$inputarr[$k][0],$v[1]);
						else
							OCIBindByName($stmt,":$k",$inputarr[$k][0],$v[1],$v[2]);
					} else {
						$len = -1;
						if ($v === ' ') $len = 1;
						if (isset($bindarr)) {	// is prepared sql, so no need to ocibindbyname again
							$bindarr[$k] = $v;
						} else { 				// dynamic sql, so rebind every time
							OCIBindByName($stmt,":$k",$inputarr[$k],$len);
							
						}
					}
				}
			}
			
			 if (!OCIExecute($stmt, OCI_DEFAULT)) {
				 OCIFreeStatement($stmt); 
				 return $false;
			 }
			 
			 $ncols = OCINumCols($stmt);
			 for ( $i = 1; $i <= $ncols; $i++ ) {
				 $cols[] = '"'.OCIColumnName($stmt, $i).'"';
			 }

*/

			$totalReg = OCINumcols($this->Parse);
			for ($ix = 1; $ix <= $totalReg; $ix++)
			{
				$cols[] = strtoupper(OCIColumnname($this->Parse, $ix));
//				$colreturn = strtolower($col);
			}


			 OCIFreeStatement($this->Parse); 
			 $fields = implode(',', $cols);
			 if ($nrows <= 0) $nrows = 999999999999;
			 else $nrows += $offset;
			 $offset += 1; // in Oracle rownum starts at 1

			 $sql = "SELECT /*+ FIRST_ROWS */ $fields FROM".
			  "(SELECT rownum as adodb_rownum, $fields FROM".
			  " ($sql) WHERE rownum <= $nrows".
			  ") WHERE adodb_rownum >= $offset";
//_debug_array($sql);die();
			return $this->query($sql);
		}


		function commit()
		{
			if ($this->autoCommit) {
				$this->halt("Nothing to commit because AUTO COMMIT is on.");
			}
			return(OCICommit($this->Link_ID));
		}		 
		
		function rollback()
		{
			if ($this->autoCommit)
			{
				$this->halt("Nothing to rollback because AUTO COMMIT is on.");
			}
			return(OCIRollback($this->Link_ID));
		}
		
		/* This is requeried in some application. It emulates the mysql_insert_id() function. */
		/* Note: this function was copied from phpBB. */
		function insert_id($query_id = 0)
		{
			if (!$query_id)
			{
				$query_id = $this->Parse;
			}
			if ($query_id && $this->last_query_text != "")
			{
				if (eregi("^(INSERT{1}|^INSERT INTO{1})[[:space:]][\"]?([a-zA-Z0-9\_\-]+)[\"]?", $this->last_query_text[$query_id], $tablename))
				{
					$query = "SELECT ".$tablename[2]."_id_seq.CURRVAL FROM DUAL";
					$temp_q_id = @OCIParse($this->db, $query);
					@OCIExecute($temp_q_id, OCI_DEFAULT);
					@OCIFetchInto($temp_q_id, $temp_result, OCI_ASSOC+OCI_RETURN_NULLS);
					if ($temp_result)
					{
						return $temp_result["CURRVAL"];
					}
					else
					{
						return false;
					}
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}
		
		function next_record()
		{
			/* IF clause added to prevent a error when tried to read an empty "$this->Parse". */
			if ($this->autoCount and ($this->num_rows() == $this->Row))
			{
				return 0;
			}
			if (0 == OCIFetchInto($this->Parse, $result, OCI_ASSOC+OCI_RETURN_NULLS))
			{
				if ($this->Debug)
				{
					printf("<br>ID: %d, Rows: %d<br>\n", $this->Link_ID, $this->num_rows());
				}
				$this->Row += 1;
				
				$errno = OCIError($this->Parse);
				if (1403 == $errno)
				{ # 1043 means no more records found
					$this->Error = false;
					$this->disconnect();
					$stat = 0;
				}
				else
				{
					$this->Error = OCIError($this->Parse);
					if ($errno && ($this->Debug))
					{
						printf("<br>Error: %s, %s<br>",
						$errno,
						$this->Error["message"]);
					}
					$stat = 0;
				}
			}
			else
			{ 
				$this->Record = array();
				$totalReg = OCINumcols($this->Parse);
				for ($ix = 1; $ix <= $totalReg; $ix++)
				{
					$col = strtoupper(OCIColumnname($this->Parse, $ix));
					$colreturn = strtolower($col);
					$this->Record[$colreturn] = 
						(is_object($result[$col])) ? $result[$col]->load() : $result[$col];
					if ($this->Debug)
					{
						echo "<b>[$col]</b>:".$result[$col]."<br>\n";
					}
				}
				$stat = 1;
			}

			return $stat;
		}

		function seek($pos)
		{
			$this->Row = $pos;
		}

		function metadata($table, $full = false)
		{
			$count = 0;
			$id    = 0;
			$res   = array();

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
			 *   [0]["flags"]  field flags ("NOT NULL", "INDEX")
			 *   [0]["format"] precision and scale of number (eg. "10,2") or empty
			 *   [0]["index"]  name of index (if has one)
			 *   [0]["chars"]  number of chars (if any char-type)
			 *
			 * - full is true
			 * $result[]:
			 *   ["num_fields"] number of metadata records
			 *   [0]["table"]  table name
			 *   [0]["name"]   field name
			 *   [0]["type"]   field type
			 *   [0]["len"]    field length
			 *   [0]["flags"]  field flags ("NOT NULL", "INDEX")
			 *   [0]["format"] precision and scale of number (eg. "10,2") or empty
			 *   [0]["index"]  name of index (if has one)
			 *   [0]["chars"]  number of chars (if any char-type)
			 *   ["meta"][field name]  index of field named "field name"
			 *   The last one is used, if you have a field name, but no index.
			 *   Test:  if (isset($result['meta']['myfield'])) {} ...
			 */

				$this->connect();

				## This is a RIGHT OUTER JOIN: "(+)", if you want to see, what
				## this query results try the following:
				## $table = new Table; $db = new my_DB_Sql; # you have to make
				##                                          # your own class
				## $table->show_results($db->query(see query vvvvvv))
				##
				$this->query("SELECT T.table_name,T.column_name,T.data_type,".
						 "T.data_length,T.data_precision,T.data_scale,T.nullable,".
						 "T.char_col_decl_length,I.index_name".
						 " FROM ALL_TAB_COLUMNS T,ALL_IND_COLUMNS I".
						 " WHERE T.column_name=I.column_name (+)".
						 " AND T.table_name=I.table_name (+)".
						 " AND T.table_name=UPPER('$table') ORDER BY T.column_id");
				
				$i = 0;
				while ($this->next_record())
				{
					$res[$i]["table"] =  $this->Record["table_name"];
					$res[$i]["name"]  =  strtolower($this->Record["column_name"]);
					$res[$i]["type"]  =  $this->Record["data_type"];
					$res[$i]["len"]   =  $this->Record["data_length"];
					if ($this->Record["index_name"])
					{
						$res[$i]["flags"] = "INDEX ";
					}
					$res[$i]["flags"] .= ( $this->Record["nullable"] == 'N') ? '' : 'NOT NULL';
					$res[$i]["format"]=	(int)$this->Record["data_precision"].",".
															 (int)$this->Record["data_scale"];
					if ("0,0" == $res[$i]["format"])
					{
						$res[$i]["format"] = '';
					}
					$res[$i]["index"] =  $this->Record["index_name"];
					$res[$i]["chars"] =  $this->Record["char_col_decl_length"];
					if ($full)
					{
						$j = $res[$i]["name"];
						$res["meta"][$j] = $i;
						$res["meta"][strtoupper($j)] = $i;
					}
					if ($full)
					{
						$res["meta"][$res[$i]["name"]] = $i;
					}
					$i++;
				}
				if ($full)
				{
					$res["num_fields"] = $i;
				}
#			$this->disconnect();
				return $res;
		}


		function affected_rows()
		{
			return OCIRowCount($this->Parse);
		}

		function num_rows()
		{
			return $this->num_rows;
		}

		function num_fields()
		{
			return OCINumcols($this->Parse);
		}

		function nf()
		{
			return $this->num_rows();
		}

		function np()
		{
			print $this->num_rows();
		}

		function f($Name, $strip_slashes = false)
		{
			$Name = strtolower($Name);
			if( isset($this->Record[$Name]))
			{
				if ($strip_slashes || ($this->auto_stripslashes && ! $strip_slashes))
				{
					return stripslashes($this->Record[$Name]);
				}
				else
				{
					return $this->Record[$Name];
				}
			}

			return '';
		}

		function p($Name)
		{
			print $this->f($Name);
		}

		function nextid($seqname)
		{
			$this->connect();

			$Query_ID = @OCIParse($this->Link_ID, "SELECT $seqname.NEXTVAL FROM DUAL");

			if (!@OCIExecute($Query_ID))
			{
				$this->Error = @OCIError($Query_ID);
				if (2289 == $this->Error["code"])
				{
					$Query_ID = OCIParse($this->Link_ID, "CREATE SEQUENCE $seqname");
					if (!OCIExecute($Query_ID))
					{
						$this->Error = OCIError($Query_ID); 
						$this->halt("<BR> nextid() function - unable to create sequence<br>".$this->Error["message"]);
					}
					else
					{
						$Query_ID = OCIParse($this->Link_ID, "SELECT $seqname.NEXTVAL FROM DUAL");
						OCIExecute($Query_ID);
					}
				}
			}

			if (OCIFetch($Query_ID))
			{
				$next_id = OCIResult($Query_ID, "NEXTVAL");
			}
			else
			{
				$next_id = 0;
			}
			OCIFreeStatement($Query_ID);
			return $next_id;
		}

		function disconnect()
		{
			if ($this->Debug)
			{
				printf("Disconnecting...<br>\n");
			}
			oci_close($this->Link_ID);
		}
		
		function halt($msg)
		{
			if ($this->Halt_On_Error == "no")
				return;

			$this->haltmsg($msg);

			if ($this->Halt_On_Error != "report")
			{
				die("Session halted.</body></html>");
			}
		}
		
		function haltmsg($msg)
		{
			printf("<p><b>Database error:</b> %s<br>\n", $msg);
			printf("<b>Oracle Error</b>: %s</p>\n", $this->Error["message"]);
		}

		function lock($table, $mode = "write")
		{
			$this->connect();
			if ($mode == "write")
			{
				$Parse = OCIParse($this->Link_ID, "lock table $table in row exclusive mode");
				OCIExecute($Parse); 
			} else {
				$result = 1;
			}
			return $result;
		}
		
		function unlock()
		{
			return $this->query("commit");
		}

		function table_names()
		{
		 $this->connect();
		 $this->query("SELECT table_name,tablespace_name FROM user_tables");
		 $i = 0;
		 while ($this->next_record())
		 {
			 $info[$i]["table_name"] = $this->Record["table_name"];
			 $info[$i]["tablespace_name"] = $this->Record["tablespace_name"];
			 $i++;
		 } 
			return $info;
		}

		function add_specialcharacters($query)
		{
			return str_replace("'", "''", $query);
		}

		function split_specialcharacters($query)
		{
			return str_replace("''", "'", $query);
		}
		
		/* This new function is needed to write a valid db dependant date string. */
		function now()
		{
			return "SYSDATE";
		}

		function db_addslashes($str)
		{
			if (!IsSet($str) || $str == '')
			{
				return '';
			}
			return str_replace("'", "''", $str);
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
						$insert_value[]	= "'$value'";
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
		* Get the correct datetime format for MONEY field for a particular RDBMS
		*
		* @return string the formatted string
		*/
		public static function money_format($amount)
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
		* Finds the next ID for a record at a table
		*
		* @param string $table tablename in question
		* @param array $key conditions
		* @return int the next id
		*/
		public function next_id($table='',$key='')
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
			$next_id = $this->f('maximum')+1;
			return $next_id;
		}

	}

	if( !class_exists("DB_Sql"))
	{
		class DB_Sql extends DB_OCI8
		{
			function DB_Sql($query = "")
			{
				$this->DB_OCI8($query);
			}
		}
	}
