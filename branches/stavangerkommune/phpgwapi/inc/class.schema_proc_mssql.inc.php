<?php
	/**
	* Database schema abstraction class for MSSQL
	* @author Michael Dean <mdean@users.sourceforge.net>
	* @author Miles Lott <milosch@phpgroupware.org>
	* @copyright Copyright (C) ? Michael Dean, Miles Lott
	* @copyright Portions Copyright (C) 2003,2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage database
	* @version $Id$
	*/

	/**
	* Database schema abstraction class for MSSQL
	* 
	* @package phpgwapi
	* @subpackage database
	*/
	class schema_proc_mssql
	{
		var $m_sStatementTerminator;
		/* Following added to convert sql to array */
		var $sCol = array();
		var $pk = array();
		var $fk = array();
		var $ix = array();
		var $uc = array();

		function schema_proc_mssql()
		{
			$this->m_sStatementTerminator = ';';
		}

		/* Return a type suitable for DDL */
		function TranslateType($sType, $iPrecision = 0, $iScale = 0)
		{
			$sTranslated = '';
			switch($sType)
			{
				case 'auto':
					$sTranslated = 'int identity(1,1)';
					break;
				case 'blob':
					$sTranslated = 'image'; /* wonder how well PHP will support this??? */
					break;
				case 'char':
					if ($iPrecision > 0 && $iPrecision < 256)
					{
						$sTranslated =  sprintf("char(%d)", $iPrecision);
					}
					if ($iPrecision > 255)
					{
						$sTranslated =  'text';
					}
					break;
				case 'date':
					$sTranslated = 'smalldatetime';
					break;
				case 'decimal':
					$sTranslated =  sprintf("decimal(%d,%d)", $iPrecision, $iScale);
					break;
				case 'float':
					switch ($iPrecision)
					{
						case 4:
							$sTranslated = 'float';
							break;
						case 8:
							$sTranslated = 'real';
							break;
					}
					break;
				case 'int':
					switch ($iPrecision)
					{
						case 2:
							$sTranslated = 'smallint';
							break;
						case 4:
						case 8:
							$sTranslated = 'int';
							break;
					}
					break;
				case 'longtext':
				case 'text':
					$sTranslated = 'text';
					break;
				case 'time':
					$sTranslated = 'time';
					break;
				case 'timestamp':
					$sTranslated = 'datetime';
					break;
				case 'varchar':
					if ($iPrecision > 0 && $iPrecision < 256)
					{
						$sTranslated =  sprintf("varchar(%d)", $iPrecision);
					}
					if ($iPrecision > 255)
					{
						$sTranslated =  'text';
					}
					break;
			}
			return $sTranslated;
		}

		function TranslateDefault($sDefault)
		{
			// Need Strict comparisons for true/false in case of datatype bolean
			if ($sDefault === true || $sDefault === 'true' || $sDefault === 'True')
			{
				$ret= 1;
			}
			else if ($sDefault === false || $sDefault === 'false' || $sDefault === 'False')
			{
				$ret= 0;
			}
			else if ($sDefault == 'current_date' || $sDefault == 'current_timestamp')
			{
				$ret= 'GetDate()';
			}
			else
			{
				$ret= "'" . $sDefault . "'";			
			}
			return $ret;
		}

		// Inverse of above, convert sql column types to array info
		function rTranslateType($sType, $iPrecision = 0, $iScale = 0)
		{
			$sTranslated = '';
			if ($sType == 'int' || $sType == 'tinyint' ||  $sType == 'smallint')
			{
				if ($iPrecision > 8)
				{
					$iPrecision = 8;
				}
				elseif($iPrecision > 4)
				{
					$iPrecision = 4;
				}
				else
				{
					$iPrecision = 2;
				}
			}
			switch($sType)
			{
				case 'tinyint':
				case 'smallint':
					$sTranslated = "'type' => 'int', 'precision' => 2";
					break;
				case 'int':
					$sTranslated = "'type' => 'int', 'precision' => 4";
					break;
				case 'char':
					if ($iPrecision > 0 && $iPrecision < 256)
					{
						$sTranslated = "'type' => 'char', 'precision' => $iPrecision";
					}
					if ($iPrecision > 255)
					{
						$sTranslated =  "'type' => 'text'";
					}
					break;
				case 'decimal':
					$sTranslated = "'type' => 'decimal', 'precision' => $iPrecision, 'scale' => $iScale";
					break;
				case 'float':
				case 'double':
					$sTranslated = "'type' => 'float', 'precision' => $iPrecision";
					break;
				case 'smalldatetime':
					$sTranslated = "'type' => 'date'";
					break;
				case 'datetime':
					$sTranslated = "'type' => 'timestamp'";
					break;
				case 'varchar':
					if ($iPrecision > 0 && $iPrecision < 256)
					{
						$sTranslated =  "'type' => 'varchar', 'precision' => $iPrecision";
					}
					if ($iPrecision > 255)
					{
						$sTranslated =  "'type' => 'text'";
					}
					break;
				case 'image':
					$sTranslated = "'type' => 'blob'";
					break;
				case 'text':
					$sTranslated = "'type' => '$sType'";
					break;
			}
			return $sTranslated;
		}

		function GetPKSQL($sFields)
		{
			return "PRIMARY KEY($sFields)";
		}

		function GetUCSQL($sFields)
		{
			return "UNIQUE($sFields)";
		}


		/* format:
			CREATE [ UNIQUE ] [ CLUSTERED | NONCLUSTERED ] INDEX index_name
			    ON { table | view } ( column [ ASC | DESC ] [ ,...n ] )
			[ WITH < index_option > [ ,...n] ]
			[ ON filegroup ]

			< index_option > :: =
			    { PAD_INDEX |
			        FILLFACTOR = fillfactor |
			        IGNORE_DUP_KEY |
			        DROP_EXISTING |
			    STATISTICS_NORECOMPUTE |
			    SORT_IN_TEMPDB 
			}
		*/

		function GetIXSQL($sFields,$sTableName = '')
		{
			if($sTableName)
			{
				return "CREATE NONCLUSTERED INDEX ". str_replace(',','_',$sFields).'_'.$sTableName.'_idx' ."  ON $sTableName ($sFields)";
			}
			else
			{
				$this->indexes_sql[str_replace(',','_',$sFields)] = "CREATE NONCLUSTERED INDEX __index_name__ ON __table_name__ ($sFields)";
			}
			return '';
		}

		function _GetColumns($oProc, $sTableName, &$sColumns, $sDropColumn = '')
		{
			$sColumns = '';
			$this->pk = array();
			$this->fk = array();
			$this->ix = array();
			$this->uc = array();

			$oProc->m_odb->query("EXEC sp_columns '$sTableName'", __LINE__, __FILE__);
			while ($oProc->m_odb->next_record())
			{
				$type = $default = $null = $nullcomma = $prec = $scale = $ret = $colinfo = $scales = '';
				if ($sColumns != '')
				{
					$sColumns .= ',';
				}

				$sColumns .= $oProc->m_odb->f('COLUMN_NAME');

				$type = $this->rTranslateType($oProc->m_odb->f('TYPE_NAME'),$oProc->m_odb->f('PRECISION'), $oProc->m_odb->f('SCALE'));

				if ($oProc->m_odb->f('IS_NULLABLE') == 'YES')
				{
					$null = "'nullable' => True";
				}
				else
				{
					$null = "'nullable' => False";
				}

				if ($oProc->m_odb->f('COLUMN_DEF'))
				{
					$default = "'default' => '".str_replace(array('((','))', "('", "')",'GetDate()'),array('','','','','current_timestamp'),$oProc->m_odb->f('COLUMN_DEF'))."'";
					$nullcomma = ',';
				}
				else
				{
					$default = '';
					$nullcomma = '';
				}
				if ($oProc->m_odb->f('TYPE_NAME') == 'int identity')
				{
					$type = "'type' => 'auto'";
				}
				$this->sCol[] = "\t\t\t\t'" . $oProc->m_odb->f('COLUMN_NAME')."' => array(" . $type . ',' . $null . $nullcomma . $default . '),' . "\n";
			}

			$this->pk = $oProc->m_odb->adodb->MetaPrimaryKeys($sTableName);

			$ForeignKeys =$oProc->m_odb->MetaForeignKeys($sTableName);

			foreach($ForeignKeys as $table => $keys)
			{
				$keystr = array();
				foreach ($keys as $keypair)
				{
					$keypair = explode('=',$keypair);
					$keystr[] = "'" . $keypair[0] . "' => '" . $keypair[1] . "'";
				}
				$this->fk[] = "'" . $table . "' => array(" . implode(', ',$keystr)  . ')';
			}

			/*FIXME: not working as expected */
			$oProc->m_odb->query("EXEC sp_indexes  @table_server = '{$GLOBALS['phpgw_info']['server']['db_host']}', @table_name = '$sTableName'", __LINE__, __FILE__);
			while ($oProc->m_odb->next_record())
			{
				if($oProc->m_odb->f('NON_UNIQUE') == 1)
				{
					$this->ix[] = $oProc->m_odb->f('COLUMN_NAME');
				}
				else
				{
					$this->uc[] = $oProc->m_odb->f('COLUMN_NAME');
				}
			}

			/* ugly as heck, but is here to chop the trailing comma on the last element (for php3) */
			$this->sCol[count($this->sCol) - 1] = substr($this->sCol[count($this->sCol) - 1],0,-2) . "\n";

			return false;
		}

		function DropTable($oProc, &$aTables, $sTableName)
		{
			return !!($oProc->m_odb->query("DROP TABLE " . $sTableName));
		}

		function DropColumn($oProc, &$aTables, $sTableName, $aNewTableDef, $sColumnName, $bCopyData = true)
		{
			return !!($oProc->m_odb->query("ALTER TABLE $sTableName DROP COLUMN $sColumnName"));
		}

		function RenameTable($oProc, &$aTables, $sOldTableName, $sNewTableName)
		{
			return !!($oProc->m_odb->query("EXEC sp_rename '$sOldTableName', '$sNewTableName'"));
		}

		function RenameColumn($oProc, &$aTables, $sTableName, $sOldColumnName, $sNewColumnName, $bCopyData = true)
		{
			// This really needs testing - it can affect primary keys, and other table-related objects
			// like sequences and such
			global $DEBUG;
			if ($DEBUG) { echo '<br>RenameColumn: calling _GetFieldSQL for ' . $sNewColumnName; }
			if ($oProc->_GetFieldSQL($aTables[$sTableName]["fd"][$sNewColumnName], $sNewColumnSQL))
			{
				return !!($oProc->m_odb->query("EXEC sp_rename '$sTableName.$sOldColumnName', '$sNewColumnName'"));
			}
			return false;
		}

		function AlterColumn($oProc, &$aTables, $sTableName, $sColumnName, &$aColumnDef, $bCopyData = true)
		{
			global $DEBUG;
			if ($DEBUG) { echo '<br>AlterColumn: calling _GetFieldSQL for ' . $sNewColumnName; }
			if ($oProc->_GetFieldSQL($aTables[$sTableName]["fd"][$sColumnName], $sNewColumnSQL))
			{
				return !!($oProc->m_odb->query("ALTER TABLE $sTableName ALTER COLUMN $sColumnName " . $sNewColumnSQL));
			}

			return false;
		}

		function AddColumn($oProc, &$aTables, $sTableName, $sColumnName, &$aColumnDef)
		{
			$oProc->_GetFieldSQL($aColumnDef, $sFieldSQL);
			$query = "ALTER TABLE $sTableName ADD $sColumnName $sFieldSQL";

			return !!($oProc->m_odb->query($query));
		}

		function GetSequenceSQL($sTableName, &$sSequenceSQL)
		{
			$sSequenceSQL = '';
			return false;
		}

		function GetTriggerSQL($sTableName, $sColumnNames, &$sTriggerSQL)
		{
			$sTriggerSQL = ''; 
			return false;
		}

		function CreateTable($oProc, &$aTables, $sTableName, $aTableDef)
		{
			global $DEBUG;
			unset($this->indexes_sql);
			if ($oProc->_GetTableSQL($sTableName, $aTableDef, $sTableSQL, $sSequenceSQL, $sTriggerSQL))
			{
				// create sequence first since it will be needed for default
				if ($sSequenceSQL != '')
				{
					$oProc->m_odb->query($sSequenceSQL);
				}

				$query = "CREATE TABLE $sTableName ($sTableSQL)";

				$result = !!$oProc->m_odb->query($query, __LINE__, __FILE__);
				if($result==True)
				{
					if (isset($this->indexes_sql) && $DEBUG)
					{
						echo  '<pre>';
						print_r($this->indexes_sql);
						echo '</pre>';
					}

					if(isset($this->indexes_sql) && is_array($this->indexes_sql) && count($this->indexes_sql)>0)
					{
						foreach($this->indexes_sql as $key => $sIndexSQL)
						{
							$ix_name = str_replace(',','_',$key).'_'.$sTableName.'_idx';
							$IndexSQL = str_replace(array('__index_name__','__table_name__'), array($ix_name,$sTableName), $sIndexSQL);
							$oProc->m_odb->query($IndexSQL, __LINE__, __FILE__);
						}
					}			
				}
				return $result;
			}

			return false;
		}
		function GetFKSQL($reftable, $sFields)
		{
			if(is_array($sFields))
			{
				$ret = "FOREIGN KEY (".implode(',',array_keys($sFields)).")\n" .
					"  REFERENCES $reftable(".implode(',',array_values($sFields)).")";
				return $ret;
			}
			else
			{
				return ""; // incorrect FK declaration found
			}
		}
	}
?>
