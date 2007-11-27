<?php
	/**
	* Database schema abstraction class for MySQL
	* @author Michael Dean <mdean@users.sourceforge.net>
	* @author Miles Lott <milosch@phpgroupware.org>
	* @copyright Copyright (C) ? Michael Dean, Miles Lott
	* @copyright Portions Copyright (C) 2003,2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage database
	* @version $Id: class.schema_proc_mysql.inc.php 18358 2007-11-27 04:43:37Z skwashd $
	*/
 
	/**
	* Database schema abstraction class for MySQL
	* 
	* @package phpgwapi
	* @subpackage database
	*/
	class schema_proc_mysql
	{
		var $m_sStatementTerminator;
		/* Following added to convert sql to array */
		var $sCol = array();
		var $pk = array();
		var $fk = array();
		var $ix = array();
		var $uc = array();

		function schema_proc_mysql()
		{
			$this->m_sStatementTerminator = ';';
			/* The use of a temp_db is to allow this process to be run from other than setup*/

			$temp_db = createObject('phpgwapi.db');

			if( isset($GLOBALS['phpgw_info']['server']['db_name']) && strlen($GLOBALS['phpgw_info']['server']['db_name']) )
			{
				$temp_db->Host = $GLOBALS['phpgw_info']['server']['db_host'];
				$temp_db->Type = $GLOBALS['phpgw_info']['server']['db_type'];
				$temp_db->Database = $GLOBALS['phpgw_info']['server']['db_name'];
				$temp_db->User = $GLOBALS['phpgw_info']['server']['db_user'];
				$temp_db->Password = $GLOBALS['phpgw_info']['server']['db_pass'];
			}
			else
			{
				$ConfigDomain = phpgw::get_var('ConfigDomain');
				$phpgw_domain = $GLOBALS['phpgw_domain'];	

				$temp_db->Host     = $phpgw_domain[$ConfigDomain]['db_host'];
				$temp_db->Database = $phpgw_domain[$ConfigDomain]['db_name'];
				$temp_db->User     = $phpgw_domain[$ConfigDomain]['db_user'];
				$temp_db->Password = $phpgw_domain[$ConfigDomain]['db_pass'];
			}

			$temp_db->query("SELECT version() as version",__LINE__,__FILE__);
			$temp_db->next_record();
			$version = $temp_db->f('version');
			$parts   = explode('.',$version);
			if($parts[0] == 5 || ($parts[0] == 4 && $parts[1] == 1))
			{
				$this->now_statement =  "now()";
			}
			else
			{
				$this->now_statement =  "'now()'";				
			}
		}

		/* Return a type suitable for DDL */
		function TranslateType($sType, $iPrecision = 0, $iScale = 0)
		{
			$sTranslated = '';
			switch($sType)
			{
				case 'auto':
					$sTranslated = 'int(11) auto_increment not null';
					break;
				case 'blob':
					$sTranslated = 'blob';
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
					$sTranslated =  'date';
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
							$sTranslated = 'double';
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
							$sTranslated = 'int';
							break;
						case 8:
							$sTranslated = 'bigint';
							break;
					}
					break;
				case 'longtext':
					$sTranslated = 'longtext';
					break;
				case 'text':
					$sTranslated = 'text';
					break;
				case 'timestamp':
					$sTranslated = 'timestamp';
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
			switch ($sDefault)
			{
				case 'current_date':
				case 'current_timestamp':
					return $this->now_statement;

			}
			return "'" . $sDefault . "'";
		}

		/* Inverse of above, convert sql column types to array info */
		function rTranslateType($sType, $iPrecision = 0, $iScale = 0)
		{
			$sTranslated = '';
			if ($sType == 'int' || $sType == 'tinyint' ||  $sType == 'smallint' || $sType == 'bigint')
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
				case 'bigint':
					$sTranslated = "'type' => 'int', 'precision' => 8";
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
				case 'datetime':
					$sTranslated = "'type' => 'timestamp'";
					break;
				case 'enum':
					/* Here comes a nasty assumption */
					/* $sTranslated =  "'type' => 'varchar', 'precision' => 255"; */
					$sTranslated =  "'type' => 'varchar', 'precision' => $iPrecision";
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
				case 'longtext':
				case 'text':
				case 'blob':
				case 'date':
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

		function GetIXSQL($sFields, $sTableName = '')
		{
			if($sTableName)
			{
				return "CREATE INDEX ". str_replace(',','_',$sFields) ." USING BTREE ON $sTableName ($sFields)";
			}
			else
			{
				return "INDEX ($sFields)";			
			}
		}

			   // foreign key supports needs MySQL 3.23.44 and up with InnoDB or MySQL 5.1
			   // or other versions the syntax is parsed in table create commands
			   // see chapter 1.8.4.5
			   function GetFKSQL($sFields)
			   {
				 if (ereg("\((.*)\)", $sFields, $regs))
				 {
				   $ret = "FOREIGN KEY (".$regs[1].")\n" .
					 "  REFERENCES ".$sFields;
				   return $ret;
				 } else
				   return ""; // incorrect FK declaration found
			   }

		function _GetColumns($oProc, $sTableName, &$sColumns, $sDropColumn = '')
		{
			$sColumns = '';
			$this->pk = array();
			$this->fk = array();
			$this->ix = array();
			$this->uc = array();

			/* Field, Type, Null, Key, Default, Extra */
			$oProc->m_odb->query("describe $sTableName");
			while ($oProc->m_odb->next_record())
			{
				$type = $default = $null = $nullcomma = $prec = $scale = $ret = $colinfo = $scales = '';
				if ($sColumns != '')
				{
					$sColumns .= ',';
				}
				$sColumns .= $oProc->m_odb->f(0);

				/* The rest of this is used only for SQL->array */
				$colinfo = explode('(',$oProc->m_odb->f(1));
				$prec = ereg_replace(')','',$colinfo[1]);
				$scales = explode(',',$prec);

				if($colinfo[0] == 'enum')
				{
					/* set prec to length of longest enum-value */
					for($prec=0; list($nul,$name) = @each($scales);)
					{
						if($prec < (strlen($name) - 2))
						{
							/* -2 as name is like "'name'" */
							$prec = (strlen($name) - 2);
						}
					}
				}
				elseif ($scales[1])
				{
					$prec  = $scales[0];
					$scale = $scales[1];
				}
				$type = $this->rTranslateType($colinfo[0], $prec, $scale);

				if ($oProc->m_odb->f(2) == 'YES')
				{
					$null = "'nullable' => True";
				}
				else
				{
					$null = "'nullable' => False";
				}
				if ($oProc->m_odb->f(4) != '')
				{
					$default = "'default' => '".$oProc->m_odb->f(4)."'";
					$nullcomma = ',';
				}
				else
				{
					$default = '';
					$nullcomma = '';
				}
				if ($oProc->m_odb->f(5))
				{
					$type = "'type' => 'auto'";
				}
				$this->sCol[] = "\t\t\t\t'" . $oProc->m_odb->f(0)."' => array(" . $type . ',' . $null . $nullcomma . $default . '),' . "\n";
				if ($oProc->m_odb->f(3) == 'PRI')
				{
					$this->pk[] = $oProc->m_odb->f(0);
				}
				if ($oProc->m_odb->f(3) == 'UNI')
				{
					$this->uc[] = $oProc->m_odb->f(0);
				}
				/* Hmmm, MUL could also mean unique, or not... */
				if ($oProc->m_odb->f(3) == 'MUL')
				{
					$this->ix[] = $oProc->m_odb->f(0);
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
			return !!($oProc->m_odb->query("ALTER TABLE $sOldTableName RENAME $sNewTableName"));
		}

		function RenameColumn($oProc, &$aTables, $sTableName, $sOldColumnName, $sNewColumnName, $bCopyData = true)
		{
			/*
			 TODO: This really needs testing - it can affect primary keys, and other table-related objects
			 like sequences and such
			*/
			global $DEBUG;
			if ($DEBUG) { echo '<br>RenameColumn: calling _GetFieldSQL for ' . $sNewColumnName; }
			if (isset($aTables[$sTableName]["fd"][$sNewColumnName]) && $oProc->_GetFieldSQL($aTables[$sTableName]["fd"][$sNewColumnName], $sNewColumnSQL))
			{
				return !!($oProc->m_odb->query("ALTER TABLE $sTableName CHANGE $sOldColumnName $sNewColumnName " . $sNewColumnSQL));
			}
			return false;
		}

		function AlterColumn($oProc, &$aTables, $sTableName, $sColumnName, &$aColumnDef, $bCopyData = true)
		{
			global $DEBUG;
			if ($DEBUG) { echo '<br>AlterColumn: calling _GetFieldSQL for ' . $sNewColumnName; }
			if (isset($aTables[$sTableName]["fd"][$sColumnName]) && $oProc->_GetFieldSQL($aTables[$sTableName]["fd"][$sColumnName], $sNewColumnSQL))
			{
				return !!($oProc->m_odb->query("ALTER TABLE $sTableName MODIFY $sColumnName " . $sNewColumnSQL));
				/* return !!($oProc->m_odb->query("ALTER TABLE $sTableName CHANGE $sColumnName $sColumnName " . $sNewColumnSQL)); */
			}

			return false;
		}

		function AddColumn($oProc, &$aTables, $sTableName, $sColumnName, &$aColumnDef)
		{
			$oProc->_GetFieldSQL($aColumnDef, $sFieldSQL);
			$query = "ALTER TABLE $sTableName ADD COLUMN $sColumnName $sFieldSQL";

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
			if ($oProc->_GetTableSQL($sTableName, $aTableDef, $sTableSQL, $sSequenceSQL, $sTriggerSQL))
			{
				/* create sequence first since it will be needed for default */
				if ($sSequenceSQL != '')
				{
					$oProc->m_odb->query($sSequenceSQL);
				}

				$query = "CREATE TABLE $sTableName ($sTableSQL)";
				return !!($oProc->m_odb->query($query));
			}

			return false;
		}
	}
?>
