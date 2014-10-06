<?php
	/**
	* Database schema abstraction class for SAPDB
	* @author Kai Hofmann <khofmann@probusiness.de>
	* @copyright Copyright (C) 2003,2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage database
	* @version $Id$
	*/

	/**
	 * Database schema abstraction class for SAPDB
	 *
	 * @package phpgwapi
	 * @subpackage database
	 */
	class schema_proc_sapdb
	{
		/**
		 * @var $m_sStatementTerminator
		 * @access private
		 */
		var $m_sStatementTerminator;

		/* Following added to convert sql to array */

		/**
		 * @var array $sCol
		 * @access private
		 */
		var $sCol = array();
		/**
		 * @var array $pk Primary keys
		 * @access private
		 */
		var $pk = array();
		/**
		 * @var array $fk Foreign Keys
		 * @access private
		 */
		var $fk = array();
		/**
		 * @var array $ix Indexes
		 * @access private
		 */
		var $ix = array();
		/**
		 * @var array $uc Uniques
		 * @access private
		 */
		var $uc = array();

		
		/**
		 * Constructor
		 */
		function schema_proc_sapdb()
		{
			$this->m_sStatementTerminator = ';';
		}

		/**
		 * Translate type
		 *
		 * @param string $sType
		 * @param integer $iPrecision
		 * @param integer $iScale
		 * @return string Type suitable for database layer
		 */
		function TranslateType($sType, $iPrecision = 0, $iScale = 0)
		{
			switch($sType)
			{
				case 'auto':
					$sTranslated = 'SERIAL';
					break;
				case 'blob':
					$sTranslated = 'LONG';
					break;
				case 'char':
					if ($iPrecision > 0 && $iPrecision <= 8000)
					{
						$sTranslated =  sprintf("CHAR(%d)", $iPrecision);
					}
					if ($iPrecision > 8000)
					{
						$sTranslated =  'LONG';
					}
					break;
				case 'date':
					$sTranslated =  'DATE';
					break;
				case 'decimal':
					$sTranslated =  sprintf("DECIMAL(%d,%d)", $iPrecision, $iScale);
					break;
				case 'float':
					if ($iPrecision == 4 || $iPrecision == 8)
					{
						$sTranslated =  sprintf("FLOAT(%d)", $iPrecision);
					}
					break;
				case 'int':
					switch ($iPrecision)
					{
						case 2:
							$sTranslated = 'SMALLINT';
							break;
						case 4:
							$sTranslated = 'INTEGER';
							break;
						case 8:
							$sTranslated = 'INTEGER';
							break;
					}
					break;
				case 'longtext':
					$sTranslated = 'LONG';
					break;
				case 'text':
					$sTranslated = 'LONG';
					break;
				case 'timestamp':
					$sTranslated = 'TIMESTAMP';
					break;
				case 'varchar':
					if ($iPrecision > 0 && $iPrecision <= 8000)
					{
						$sTranslated =  sprintf("VARCHAR(%d)", $iPrecision);
					}
					if ($iPrecision > 8000)
					{
						$sTranslated =  'LONG';
					}
					break;
			}
			return $sTranslated;
		}


		/**
		 * Translate default
		 *
		 * @param string $sDefault
		 * @return string
		 */
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
				$ret= 'TIMESTAMP';
			}
			else
			{
				$ret= "'" . $sDefault . "'";			
			}
			return $ret;
		}

		/**
		 * Convert SQL column types to array info
		 *
		 * @param string $sType
		 * @param integer $iPrecision
		 * @param integer $iScale
		 * @return string
		 */
		function rTranslateType($sType, $iPrecision = 0, $iScale = 0)
		{
			$sTranslated = '';
			switch($sType)
			{
				case 'SERIAL':
					$sTranslated = "'type' => 'auto'";
					break;
				case 'SMALLINT':
					$sTranslated = "'type' => 'int', 'precision' => 2";
					break;
				case 'INT':
					$sTranslated = "'type' => 'int', 'precision' => 4";
					break;
				case 'CHAR':
					if ($iPrecision > 0 && $iPrecision <= 255)
					{
						$sTranslated = "'type' => 'char', 'precision' => $iPrecision";
					}
					if ($iPrecision > 255)
					{
						$sTranslated =  "'type' => 'text'";
					}
					break;
				case 'FLOAT':
				case 'FLOAT(16)':
				case 'FLOAT(38)':
				case 'DOUBLE':
					$sTranslated = "'type' => 'float', 'precision' => $iPrecision";
					break;
				case 'TIMESTAMP':
					$sTranslated = "'type' => 'timestamp'";
					break;
				case 'VARCHAR':
					if ($iPrecision > 0 && $iPrecision <= 255)
					{
						$sTranslated =  "'type' => 'varchar', 'precision' => $iPrecision";
					}
					if ($iPrecision > 255)
					{
						$sTranslated =  "'type' => 'text'";
					}
					break;
				case 'LONG VARCHAR':
				case 'DATE':
				case 'TIME':
				case 'FIXED':
					$sTranslated = "'type' => '$sType'";
					break;
			}
			return $sTranslated;
		}

		/**
		 * Get primary key SQL string
		 *
		 * @param string $sFields
		 * @return string
		 */
		function GetPKSQL($sFields)
		{
			return "PRIMARY KEY($sFields)";
		}

		/**
		 * Get unique SQL string
		 *
		 * @param string $sFields
		 * @return string
		 */
		function GetUCSQL($sFields)
		{
			return "UNIQUE($sFields)";
		}


		/**
		 * Get index SQL string
		 *
		 * @param string $sFields
		 * @return string
		 */
		function GetIXSQL($sFields)
		{
			return "INDEX ($sFields)";
		}


		/**
		 * Get columns
		 *
		 * @param object $oProc
		 * @param string $sTableName
		 * @param string $sColumns
		 * @param string $sDropColumn
		 * @param string $sAlteredColumn
		 * @param string $sAlteredColumnType
		 * @return boolean
		 * @access private
		 */
		function _GetColumns($oProc, $sTableName, &$sColumns, $sDropColumn = '', $sAlteredColumn = '', $sAlteredColumnType = '')
		{
		  return false;
		}


		/**
		 * Copy altered table
		 *
		 * @param object $oProc
		 * @param array $aTables
		 * @param string $sSource
		 * @param string $sDest
		 * @param return boolean
		 * @access private
		 */
		function _CopyAlteredTable($oProc, &$aTables, $sSource, $sDest)
		{
			$oDB = $oProc->m_odb;
			$oProc->m_odb->query("select * from $sSource",__LINE__,__FILE__);
			while ($oProc->m_odb->next_record())
			{
				$sSQL = "INSERT INTO $sDest (";
				$i=0;
				@reset($aTables[$sDest]['fd']);
				while (list($name,$arraydef) = @each($aTables[$sDest]['fd']))
				{
					if ($i > 0)
					{
						$sSQL .= ',';
					}

					$sSQL .= $name;
					++$i;
				}

				$sSQL .= ') VALUES (';
				@reset($aTables[$sDest]['fd']);
				$i=0;
				while (list($name,$arraydef) = @each($aTables[$sDest]['fd']))
				{
					if ($i > 0)
					{
						$sSQL .= ',';
					}

					if ($oProc->m_odb->f($name) != null)
					{
						switch ($arraydef['type'])
						{
							case 'LONG':
							case 'CHAR':
							case 'DATE':
							case 'TIMESTAMP':
							case 'VARCHAR':
								$sSQL .= "'" . $oProc->m_odb->db_addslashes($oProc->m_odb->f($name)) . "'";
								break;
							default:
								$sSQL .= intval($oProc->m_odb->f($name));
						}
					}
					else
					{
						$sSQL .= 'null';
					}
					++$i;
				}
				$sSQL .= ')';

				$oDB->query($sSQL,__LINE__,__FILE__);
			}

			return true;
		}


		/**
		 * Get sequence for table
		 *
		 * @param object $oProc
		 * @param string $table
		 * @param string $sSequenceName
		 * @return boolean
		 */
		function GetSequenceForTable($oProc,$table,&$sSequenceName)
		{
			 return False;
		}


		/**
		 * Get sequence field for table
		 *
		 * @param object $oProc
		 * @param string $table
		 * @param string $sField
		 * @return boolean
		 */
		function GetSequenceFieldForTable($oProc,$table,&$sField)
		{
			return False;
		}

		/**
		 * Drop sequence for table
		 *
		 * @param object $oProc
		 * @param string $table
		 * @return boolean
		 */
		function DropSequenceForTable($oProc,$table)
		{
			return False;
		}

		/**
		 * Drop table
		 *
		 * @param object $oProc
		 * @param array $aTables
		 * @param string $sTableName
		 * @return boolean
		 */
		function DropTable($oProc, &$aTables, $sTableName)
		{
			$this->DropSequenceForTable($oProc,$sTableName);

			return $oProc->m_odb->query("DROP TABLE " . $sTableName,__LINE__,__FILE__) && $this->DropSequenceForTable($oProc, $sTableName);
		}

		/**
		 * Drop column
		 *
		 * @param object $oProc
		 * @param array $aTables
		 * @param string $sTableName
		 * @param array $aNewTableDef
		 * @param string $sColumnName
		 * @param boolean $bCopyData
		 * @return boolean
		 */
		function DropColumn($oProc, &$aTables, $sTableName, $aNewTableDef, $sColumnName, $bCopyData = true)
		{
			return !!($oProc->m_odb->query("ALTER TABLE $sTableName DROP $sColumnName",__LINE__,__FILE__));
		}

		/**
		 * Rename table
		 *
		 * @param object $oProc
		 * @param array $aTables
		 * @param string $sOldTableName
		 * @param string $sNewTableName
		 * @return boolean
		 */
		function RenameTable($oProc, &$aTables, $sOldTableName, $sNewTableName)
		{
		 return !!($oProc->m_odb->query("RENAME TABLE $sOldTableName TO $sNewTableName",__LINE__,__FILE__));
		}

		/**
		 * Rename column
		 *
		 * @param object $oProc
		 * @param array $aTables
		 * @param string $sTableName
		 * @param string $sOldColumnName
		 * @param string $sNewColumnName
		 * @param boolean $bCopyData
		 * @return boolean
		 */
		function RenameColumn($oProc, &$aTables, $sTableName, $sOldColumnName, $sNewColumnName, $bCopyData = true)
		{
		 return !!($oProc->m_odb->query("RENAME COLUMN $sTableName.$sOldColumnName TO $sNewColumnName",__LINE__,__FILE__));
		}

		/**
		 * Alter column
		 *
		 * @param object $oProc
		 * @param array $aTables
		 * @param string $sTableName
		 * @param string $sColumnName
		 * @param array $aColumnDef
		 * @param boolean $bCopyData
		 * @return boolean
		 */
		function AlterColumn($oProc, &$aTables, $sTableName, $sColumnName, &$aColumnDef, $bCopyData = true)
		{
		 return !!($oProc->m_odb->query("ALTER TABLE $sTableName MODIFY $sColumnName " . $sNewColumnSQL,__LINE__,__FILE__));
		}

		/**
		 * Add column
		 *
		 * @param object $oProc
		 * @param array $aTables
		 * @param string $sTableName
		 * @param string $sColumnName
		 * @param array $aColumnDef
		 * @return boolean
		 */
		function AddColumn($oProc, &$aTables, $sTableName, $sColumnName, &$aColumnDef)
		{
			$oProc->_GetFieldSQL($aColumnDef, $sFieldSQL);
			return !!($oProc->m_odb->query("ALTER TABLE $sTableName ADD COLUMN $sColumnName $sFieldSQL",__LINE__,__FILE__));
		}

		/**
		 * Get sequence SQL
		 *
		 * @param string $sTableName
		 * @param string $sSequenceSQL
		 * @return boolean
		 */
		function GetSequenceSQL($sTableName, &$sSequenceSQL)
		{
			$sSequenceSQL = '';
			return true;
		}

		/**
		 * Get trigger SQL
		 *
		 * @param string $sTableName
		 * @param string $sColumnNames
		 * @param string $sTriggerSQL
		 * @return boolean
		 */
		function GetTriggerSQL($sTableName, $sColumnNames, &$sTriggerSQL)
		{
			$sTriggerSQL = ''; 
			return false;
		}

		/**
		 * Create table
		 *
		 * @param object $oProc
		 * @param array $aTables
		 * @param string $sTableName
		 * @param array $aTableDef
		 * @param boolean $bCreateSequence
		 * @return boolean
		 */
		function CreateTable($oProc, $aTables, $sTableName, $aTableDef, $bCreateSequence = true)
		{
			global $DEBUG;
			if ($oProc->_GetTableSQL($sTableName, $aTableDef, $sTableSQL, $sSequenceSQL, $sTriggerSQL))
			{
				/* create sequence first since it will be needed for default */
				if ($bCreateSequence && $sSequenceSQL != '')
				{
					if ($DEBUG) { echo '<br>Making sequence using: ' . $sSequenceSQL; }
					$oProc->m_odb->query($sSequenceSQL,__LINE__,__FILE__);
				}
				return !!($oProc->m_odb->query("CREATE TABLE $sTableName ($sTableSQL)",__LINE__,__FILE__));
			}

			return false;
		}
	}
?>
