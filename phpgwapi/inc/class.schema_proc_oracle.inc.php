<?php
	/**
	* Database schema abstraction class for Oracle
	* @author Yoshihiro Kamimura <your@itheart.com>
	* @copyright Portions Copyright (C) 2003,2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage database
	* @version $Id$
	*/
 
	/**
	* Database schema abstraction class for Oracle
	* 
	* @package phpgwapi
	* @subpackage database
	*/
	class schema_proc_oracle
	{
		var $m_sStatementTerminator;
		var $m_sSequencePrefix;
		var $m_sIndexPrefix;
		var $indexcount;
		var $indexes_sql = array();
		var $check_contstaints = array();

		/* Following added to convert sql to array */
		var $sCol = array();
		var $pk = array();
		var $fk = array();
		var $ix = array();
		var $uc = array();

		function __construct()
		{
			$this->m_sStatementTerminator = ';';
			$this->m_sSequencePrefix = 'sq';
			$this->m_sIndexPrefix = 'ix';
		}

		/* Return a type suitable for DDL */
		function TranslateType($sType, $iPrecision = 0, $iScale = 0, $sTableName='', $sFieldName='')
		{
			$sTranslated = '';
			switch($sType)
			{
				case 'auto':
					$sTranslated = 'number(11)';
					break;
				case 'blob':
					$sTranslated = 'blob';
					break;
				case 'char':
				case 'varchar':
					if ($iPrecision > 4000)
					{
						$sTranslated =  'clob';
					}
					else
					{
						$sTranslated =  sprintf("varchar2(%d)", $iPrecision);
					}
					break;
				case 'date':
					$sTranslated =  'date';
					break;
				case 'decimal':
					$sTranslated =  sprintf("number(%d,%d)", $iPrecision, $iScale);
					break;
				case 'float':
					$sTranslated = 'number';
					break;
				case 'int':
					$sTranslated = 'number(38)';
					break;
				case 'longtext':
				case 'text':
					$sTranslated = 'varchar2(4000)';
					break;
				case 'timestamp':
					$sTranslated = 'timestamp';
					break;
				case 'bool':
					$sTranslated = 'number(1)';
					break;
				case 'xml':
					$sTranslated = 'sys.xmltype';
					break;
				case 'json':
				case 'jsonb':
					$sTranslated = 'varchar2(4000)';
					$this->check_contstaints[] = "CONSTRAINT ensure_json_{$sTableName}_" . trim($sFieldName,'"') . " CHECK ({$sFieldName} IS JSON)";
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
			else if ($sDefault == 'current_date')
			{
				$ret= 'sysdate';
			}
			else if ($sDefault == 'current_timestamp')
			{
				$ret= 'systimestamp';
			}		
			else
			{
				$ret= "'" . $sDefault . "'";			
			}
			return $ret;
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
				case 'bool':
					$sTranslated =  "'type' => 'number', 'precision' => 1";
					break;
				case 'xml':
					$sTranslated =  "'type' => 'sys.xmltype'";
					break;
				case 'json':
				case 'jsonb':
					$sTranslated =  "'type' => 'varchar2', 'precision' => 4000";						
			}
			return $sTranslated;
		}

		function GetPKSQL($sFields)
		{
			return "PRIMARY KEY($sFields)";
		}

		function GetUCSQL($sFields)
		{
			return "";
		}

		function GetIXSQL($sFields)
		{
			$this->indexcount++;
			$this->indexes_sql[$this->indexcount] = "CREATE INDEX __index_name__ ON __table_name__ ($sFields)";
			return '';
		}

			   // foreign key supports needs MySQL 3.23.44 and up with InnoDB or MySQL 5.1
			   // or other versions the syntax is parsed in table create commands
			   // see chapter 1.8.4.5
			   function GetFKSQL($sFields)
			   {
				 if (preg_match("/\((.*)\)/", $sFields, $regs))
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
			$oProc->m_odb->query("DESCRIBE $sTableName", __LINE__, __FILE__);
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
				$prec = str_replace(')','',$colinfo[1]);
				$scales = explode(',',$prec);

				if($colinfo[0] == 'enum')
				{
					/* set prec to length of longest enum-value */
					//for($prec=0; list($nul,$name) = @each($scales);)
					$prec = 0;
					foreach($scales as $nul => $name)
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

		function GetSequenceForTable($oProc, $table, &$sSequenceName)
		{
			$sSQL = sprintf("SELECT SEQUENCE_NAME FROM ALL_SEQUENCES WHERE SEQUENCE_NAME LIKE '%s_%s' ORDER BY SEQUENCE_NAME",
				strtoupper($this->m_sSequencePrefix),
				strtoupper($table)
				);
			$oProc->m_odb->query($sSQL, __LINE__, __FILE__);
			$oProc->m_odb->next_record();

			if ($oProc->m_odb->f('sequence_name'))
			{
				$sSequenceName = $oProc->m_odb->f('sequence_name');
			}
			return True;
		}

		function DropSequenceForTable($oProc,$table)
		{
			$this->GetSequenceForTable($oProc, $table, $sSequenceName);
			if ($sSequenceName)
			{
				$oProc->m_odb->query("DROP SEQUENCE " . $sSequenceName, __LINE__, __FILE__);
			}
			return True;
		}

		function DropTable($oProc, &$aTables, $sTableName)
		{
			$this->DropSequenceForTable($oProc,$sTableName);

			return !!($oProc->m_odb->query("DROP TABLE " . $sTableName, __LINE__, __FILE__));
		}

		function DropView($oProc, $sViewName)
		{
			return !!($oProc->m_odb->query("DROP VIEW " . $sViewName . ' CASCADE CONSTRAINT'));
		}

		function DropColumn($oProc, &$aTables, $sTableName, $aNewTableDef, $sColumnName, $bCopyData = true)
		{
			return !!($oProc->m_odb->query("ALTER TABLE $sTableName DROP COLUMN $sColumnName", __LINE__, __FILE__));
		}

		function RenameTable($oProc, &$aTables, $sOldTableName, $sNewTableName)
		{
			return !!($oProc->m_odb->query("ALTER TABLE $sOldTableName RENAME $sNewTableName", __LINE__, __FILE__));
		}

		function RenameColumn($oProc, &$aTables, $sTableName, $sOldColumnName, $sNewColumnName, $bCopyData = true)
		{
			/*
			 TODO: This really needs testing - it can affect primary keys, and other table-related objects
			 like sequences and such
			*/
			if ($oProc->_GetFieldSQL($aTables[$sTableName]["fd"][$sNewColumnName], $sNewColumnSQL, $sTableName, $sOldColumnName))
			{
				return !!($oProc->m_odb->query("ALTER TABLE $sTableName CHANGE $sOldColumnName $sNewColumnName " . $sNewColumnSQL, __LINE__, __FILE__));
			}
			return false;
		}

		function AlterColumn($oProc, &$aTables, $sTableName, $sColumnName, &$aColumnDef, $bCopyData = true)
		{
			if ($oProc->_GetFieldSQL($aTables[$sTableName]["fd"][$sColumnName], $sNewColumnSQL, $sTableName, $sColumnName))
			{
				return !!($oProc->m_odb->query("ALTER TABLE $sTableName MODIFY $sColumnName " . $sNewColumnSQL, __LINE__, __FILE__));
				/* return !!($oProc->m_odb->query("ALTER TABLE $sTableName CHANGE $sColumnName $sColumnName " . $sNewColumnSQL)); */
			}

			return false;
		}

		function AddColumn($oProc, &$aTables, $sTableName, $sColumnName, &$aColumnDef)
		{
			$oProc->_GetFieldSQL($aColumnDef, $sFieldSQL, $sTableName, $sColumnName);
			$query = "ALTER TABLE $sTableName ADD COLUMN $sColumnName $sFieldSQL";

			return !!($oProc->m_odb->query($query, __LINE__, __FILE__));
		}

		function GetSequenceSQL($sTableName, &$sSequenceSQL)
		{
			$sSequenceSQL = sprintf("CREATE SEQUENCE %s_%s", strtoupper($this->m_sSequencePrefix), strtoupper($sTableName));
			return false;
		}

		function GetTriggerSQL($sTableName, $sColumnNames, &$sTriggerSQL)
		{
			$sTriggerSQL = ''; 
			if(is_array($sColumnNames) && count($sColumnNames) > 0)
			{
				$sTriggerSQL =  sprintf("CREATE OR REPLACE TRIGGER TRG_%s ", strtoupper($sTableName));
				$sTriggerSQL .= sprintf("BEFORE INSERT ON %s ", $sTableName); 
				$sTriggerSQL .= "FOR EACH ROW "; 
				$sTriggerSQL .= "BEGIN "; 

				foreach($sColumnNames as $sColumnName)
				{
					$sTriggerSQL .= sprintf("IF :NEW.%s IS NULL ", $sColumnName); 
					$sTriggerSQL .= "THEN ";
					$sTriggerSQL .= sprintf("SELECT %s_%s.NEXTVAL INTO :NEW.%s FROM DUAL; ",
										strtoupper($this->m_sSequencePrefix),
										$sTableName,
										$sColumnName); 
					$sTriggerSQL .= "END IF; ";
				}
 
				$sTriggerSQL .= "END; ";
				
				return true; 
			}
			else
			{
				return false;
			}
		}

		function CreateTable($oProc, $aTables, $sTableName, $aTableDef, $bCreateSequence = true)
		{
			global $DEBUG;
			$this->indexes_sql= array();
			$this->indexcount = 0;
			$sSequenceSQL = '';
			$sTriggerSQL = '';

			if ($oProc->_GetTableSQL($sTableName, $aTableDef, $sTableSQL, $sSequenceSQL, $sTriggerSQL))
			{
				if ($bCreateSequence && $sSequenceSQL != '')
				{
					if ($DEBUG) { echo '<br>Making sequence using: ' . $sSequenceSQL; }
					$oProc->m_odb->query($sSequenceSQL, __LINE__, __FILE__);
				}

				$query = "CREATE TABLE $sTableName ($sTableSQL)";

				$result = !!($oProc->m_odb->query($query, __LINE__, __FILE__));
				if($result==True)
				{
					if ($DEBUG)
					{
						echo  '<pre>';
						print_r($this->indexes_sql);
						echo '</pre>';
					}

					if(is_array($this->indexes_sql) && count($this->indexes_sql) > 0)
					{
						foreach($this->indexes_sql as $key => $sIndexSQL)
						{
							$ix_name = strtoupper($this->m_sIndexPrefix) . "_" . $sTableName . '_' . $key;
							$IndexSQL = str_replace(array('__index_name__','__table_name__'), array($ix_name,$sTableName), $sIndexSQL);
							$oProc->m_odb->query($IndexSQL, __LINE__, __FILE__);
						}
					}			

					if ($sTriggerSQL != '')
					{
						if ($DEBUG)
						{
							echo  '<pre>';
							print_r($sTriggerSQL);
							echo '</pre>';
						}			
						$oProc->m_odb->query($sTriggerSQL, __LINE__, __FILE__);
					}
				}
				return $result;
			}

			return false;
		}
	}

