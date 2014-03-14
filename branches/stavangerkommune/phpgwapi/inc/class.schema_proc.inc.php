<?php
	/**
	* Database schema abstraction class
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
	* Database schema abstraction class
	* 
	* @package phpgwapi
	* @subpackage database
	*/
	class schema_proc
	{
		var $m_oTranslator;
		var $m_oDeltaProc;
		var $m_odb;
		var $m_aTables;
		var $m_bDeltaOnly;

		function schema_proc($dbms)
		{
			switch($dbms)
			{
				case 'mysql':
				$this->m_oTranslator	= createObject('phpgwapi.schema_proc_mysql');
				break;

				case 'postgres':
				$this->m_oTranslator	= createObject('phpgwapi.schema_proc_pgsql');
				break;

				case 'oracle':
				$this->m_oTranslator	= createObject('phpgwapi.schema_proc_oracle');
				break;

				case 'sapdb':
				$this->m_oTranslator	= createObject('phpgwapi.schema_proc_sapdb');
				break;

				case 'mssql':
				$this->m_oTranslator	= createObject('phpgwapi.schema_proc_mssql');
				break;

				default:
				//what now?
			}
			$this->m_oDeltaProc		= createObject('phpgwapi.schema_proc_array');
			$this->m_aTables		= array();
			$this->m_bDeltaOnly		= False; // Default to false here in case it's just a CreateTable script
			$this->dbms				= $dbms;
		}

		function GenerateScripts($aTables, $bOutputHTML=False)
		{
			if (!is_array($aTables))
			{
				return False;
			}
			$this->m_aTables = $aTables;

			$sAllTableSQL = '';
			foreach ($this->m_aTables as $sTableName => $aTableDef)
			{
				$sSequenceSQL = '';
				$sTriggerSQL = '';
				$this->m_oTranslator->indexes_sql = array();

				try
				{
					$this->_GetTableSQL($sTableName, $aTableDef, $sTableSQL, $sSequenceSQL, $sTriggerSQL);
				}

				catch(Exception $e)
				{
					if($bOutputHTML)
					{
						print('<br>SQL:<pre>' . $sAllTableSQL . '</pre><br>');
					}

					print('<br>Error: Failed generating script for <b>' . $sTableName . '</b><br>');
					echo '<pre style="text-align: left;">'.$sTableName.' = '; print_r($aTableDef); echo "</pre>\n";
					echo $e->getMessage();
					return false;
				}

				$sTableSQL = "CREATE TABLE $sTableName (\n$sTableSQL\n)"
					. $this->m_oTranslator->m_sStatementTerminator;
				if($sSequenceSQL != '')
				{
					$sAllTableSQL .= $sSequenceSQL . "\n";
				}

				if($sTriggerSQL != '')
				{
					$sAllTableSQL .= $sTriggerSQL . "\n";
				}

				$sAllTableSQL .= $sTableSQL . "\n\n";

				// postgres and mssql
				if(isset($this->m_oTranslator->indexes_sql) && is_array($this->m_oTranslator->indexes_sql) && count($this->m_oTranslator->indexes_sql)>0)
				{
					foreach($this->m_oTranslator->indexes_sql as $key => $sIndexSQL)
					{
						$ix_name = $key.'_'.$sTableName.'_idx';
						$IndexSQL = str_replace(array('__index_name__','__table_name__'), array($ix_name,$sTableName), $sIndexSQL);
						$sAllTableSQL .= $IndexSQL . "\n\n";
					}
				}
			}

			if($bOutputHTML)
			{
				print('<pre>' . $sAllTableSQL . '</pre><br><br>');
			}

			return True;
		}

		function ExecuteScripts($aTables, $bOutputHTML=false)
		{
			if(!is_array($aTables) || !IsSet($this->m_odb))
			{
				return False;
			}

			reset($aTables);
			$this->m_aTables = $aTables;

			foreach ($aTables as $sTableName => $aTableDef)
			{
				if($this->CreateTable($sTableName, $aTableDef))
				{
					if($bOutputHTML)
					{
						echo '<br>Create Table <b>' . $sTableName . '</b>';
					}
				}
				else
				{
					if($bOutputHTML)
					{
						echo '<br>Create Table Failed For <b>' . $sTableName . '</b>';
					}

					return False;
				}
			}

			return True;
		}

		function DropAllTables($aTables, $bOutputHTML=False)
		{
			if(!is_array($aTables) || !isset($this->m_odb))
			{
				return False;
			}

			$this->m_aTables = $aTables;

			reset($this->m_aTables);

			foreach ( $this->m_aTables as $sTableName => $aTableDef)
			{
				if($this->DropTable($sTableName))
				{
					if($bOutputHTML)
					{
						echo '<br>Drop Table <b>' . $sTableSQL . '</b>';
					}
				}
				else
				{
					return False;
				}
			}

			return True;
		}

		function DropTable($sTableName)
		{
			$retVal = $this->m_oDeltaProc->DropTable($this, $this->m_aTables, $sTableName);
			if($this->m_bDeltaOnly)
			{
				return $retVal;
			}

			return $retVal && $this->m_oTranslator->DropTable($this, $this->m_aTables, $sTableName);
		}

		function DropColumn($sTableName, $aTableDef, $sColumnName, $bCopyData = true)
		{
			$retVal = $this->m_oDeltaProc->DropColumn($this, $this->m_aTables, $sTableName, $aTableDef, $sColumnName, $bCopyData);
			if($this->m_bDeltaOnly)
			{
				return $retVal;
			}

			return $retVal && $this->m_oTranslator->DropColumn($this, $this->m_aTables, $sTableName, $aTableDef, $sColumnName, $bCopyData);
		}

		function RenameTable($sOldTableName, $sNewTableName)
		{
			$retVal = $this->m_oDeltaProc->RenameTable($this, $this->m_aTables, $sOldTableName, $sNewTableName);
			if($this->m_bDeltaOnly)
			{
				return $retVal;
			}

			return $retVal && $this->m_oTranslator->RenameTable($this, $this->m_aTables, $sOldTableName, $sNewTableName);
		}

		function RenameColumn($sTableName, $sOldColumnName, $sNewColumnName, $bCopyData=True)
		{
			$retVal = $this->m_oDeltaProc->RenameColumn($this, $this->m_aTables, $sTableName, $sOldColumnName, $sNewColumnName, $bCopyData);
			if($this->m_bDeltaOnly)
			{
				return $retVal;
			}

			return $retVal && $this->m_oTranslator->RenameColumn($this, $this->m_aTables, $sTableName, $sOldColumnName, $sNewColumnName, $bCopyData);
		}

		function AlterColumn($sTableName, $sColumnName, $aColumnDef, $bCopyData=True)
		{
			$retVal = $this->m_oDeltaProc->AlterColumn($this, $this->m_aTables, $sTableName, $sColumnName, $aColumnDef, $bCopyData);
			if($this->m_bDeltaOnly)
			{
				return $retVal;
			}

			return $retVal && $this->m_oTranslator->AlterColumn($this, $this->m_aTables, $sTableName, $sColumnName, $aColumnDef, $bCopyData);
		}

		function AddColumn($sTableName, $sColumnName, $aColumnDef)
		{
			$retVal = $this->m_oDeltaProc->AddColumn($this, $this->m_aTables, $sTableName, $sColumnName, $aColumnDef);
			if($this->m_bDeltaOnly)
			{
				return $retVal;
			}

			return $retVal && $this->m_oTranslator->AddColumn($this, $this->m_aTables, $sTableName, $sColumnName, $aColumnDef);
		}

		function CreateTable($sTableName, $aTableDef)
		{
			$retVal = $this->m_oDeltaProc->CreateTable($this, $this->m_aTables, $sTableName, $aTableDef);
			if($this->m_bDeltaOnly)
			{
				return $retVal;
			}

			return $retVal && $this->m_oTranslator->CreateTable($this, $this->m_aTables, $sTableName, $aTableDef);
		}

		function f($value)
		{
			if($this->m_bDeltaOnly)
			{
				// Don't care, since we are processing deltas only
				return False;
			}

			return $this->m_odb->f($value);
		}

		function num_rows()
		{
			if($this->m_bDeltaOnly)
			{
				// If not False, we will cause while loops calling us to hang
				return False;
			}

			return $this->m_odb->num_rows();
		}

		function next_record()
		{
			if($this->m_bDeltaOnly)
			{
				// If not False, we will cause while loops calling us to hang
				return False;
			}

			return $this->m_odb->next_record();
		}

		function query($sQuery, $line='', $file='')
		{
			if($this->m_bDeltaOnly)
			{
				// Don't run this query, since we are processing deltas only
				return True;
			}

			return $this->m_odb->query($sQuery, $line, $file);
		}

		function _GetTableSQL($sTableName, $aTableDef, &$sTableSQL, &$sSequenceSQL, &$sTriggerSQL)
		{
			global $DEBUG;

			if(!is_array($aTableDef))
			{
				return False;
			}

			$sTableSQL = '';
			$sSequenceSQL = '';
			$sTriggerSQL = '';
			reset($aTableDef['fd']);
			unset($sbufTriggerFD);
			$sbufTriggerFD = array(); 
			while(list($sFieldName, $aFieldAttr) = each($aTableDef['fd']))
			{
				$sFieldSQL = '';

				try
				{
					$this->_GetFieldSQL($aFieldAttr, $sFieldSQL);
				}
				catch(Exception $e)
				{
					$_message = "Error: GetFieldSQL failed for <b>{$sTableName}::{$sFieldName}</b>. ";
					$_message .=  $e->getMessage();
					throw new Exception($_message);
					return False;
				}

				if($sTableSQL != '')
				{
					$sTableSQL .= ",\n";
				}

				$sTableSQL .= "$sFieldName $sFieldSQL";

				if($aFieldAttr['type'] == 'auto')
				{
					$sbufTriggerFD[] = $sFieldName;
					if($this->m_oTranslator->GetSequenceSQL($sTableName, $sSequenceSQL))
					{
						$sTableSQL .= sprintf(" DEFAULT nextval('seq_%s')", $sTableName);
					}
				}
			}

			$sUCSQL = '';
			$sPKSQL = '';
			$sIXSQL = '';
			$sFKSQL = '';

			if(isset($aTableDef['pk']) && is_array($aTableDef['pk']) && count($aTableDef['pk']) > 0)
			{
				if(!$this->_GetPK($aTableDef['pk'], $sPKSQL))
				{
					if($bOutputHTML)
					{
						print('<br>Failed getting primary key<br>');
					}

					return False;
				}
			}

			if(isset($aTableDef['uc']) && is_array($aTableDef['uc']) && count($aTableDef['uc']) > 0)
			{
				if(!$this->_GetUC($aTableDef['uc'], $sUCSQL))
				{
					if($bOutputHTML)
					{
						print('<br>Failed getting unique constraint<br>');
					}

					return False;
				}
			}

			if(isset($aTableDef['ix']) && is_array($aTableDef['ix']) && count($aTableDef['ix']) > 0)
			{
				if(!$this->_GetIX($aTableDef['ix'], $sIXSQL))
				{
					if($bOutputHTML)
					{
						echo '<br>Failed generating indexes<br>';
					}

					return False;
				}
			}

			if ( isset($aTableDef['fk'])
				&& is_array($aTableDef['fk'])
				&& count($aTableDef['fk']) > 0)
			{
				if(!$this->_GetFK($aTableDef['fk'], $sFKSQL))
				{
					if($bOutputHTML)
					{
						echo '<br>Failed generating foreign keys<br>';
					}

					return False;
				}
			}


			if($sPKSQL != '')
			{
				$sTableSQL .= ",\n" . $sPKSQL;
			}

			if($sIXSQL != '')
			{
				$sTableSQL .= ",\n" . $sIXSQL;
			}

			if($sUCSQL != '')
			{
				$sTableSQL .= ",\n" . $sUCSQL;
			}

					   if($sFKSQL != '')
					   {
						 $sTableSQL .= ",\n" . $sFKSQL;
					   }

			if (count($sbufTriggerFD) > 0)
			{
				$this->m_oTranslator->GetTriggerSQL($sTableName, $sbufTriggerFD, $sTriggerSQL);
			}

			return True;
		}

		// Get field DDL
		function _GetFieldSQL($aField, &$sFieldSQL)
		{
			global $DEBUG;
			if($DEBUG) { echo '<br>_GetFieldSQL(): Incoming ARRAY: '; var_dump($aField); }

			if(!is_array($aField))
			{
				return false;
			}

			$sType = '';
			$iPrecision = 0;
			$iScale = 0;
			$sDefault = '';
			$bNullable = true;

			reset($aField);
			while(list($sAttr, $vAttrVal) = each($aField))
			{
				switch ($sAttr)
				{
					case 'type':
						$sType = $vAttrVal;
						break;
					case 'precision':
						$iPrecision = (int)$vAttrVal;
						break;
					case 'scale':
						$iScale = (int)$vAttrVal;
						break;
					case 'default':
						$sDefault = $vAttrVal;
						if($DEBUG) { echo '<br>_GetFieldSQL(): Default="' . $sDefault . '"'; }
						break;
					case 'nullable':
						$bNullable = $vAttrVal;
						break;
				}
			}

			// Translate the type for the DBMS
			$sBufNullable = '';
			$sBufDefault = '';

			if($sFieldSQL = $this->m_oTranslator->TranslateType($sType, $iPrecision, $iScale))
			{
				if($bNullable === false || $bNullable === 'False')
				{
					$sBufNullable = ' NOT NULL';
					//$sFieldSQL .= ' NOT NULL';
				}
				else
				{
					$sBufNullable = ' NULL';
					//$sFieldSQL .= ' NULL';
				}

				if($sDefault === '0' || $sDefault === 0)
				{
					$sBufDefault = ' DEFAULT 0';
					//$sFieldSQL .= ' DEFAULT 0';
				}
				elseif(!is_numeric($sDefault) && $sDefault != '')
				{
					if($DEBUG) { echo '<br>_GetFieldSQL(): Calling TranslateDefault for "' . $sDefault . '"'; }
					// Get default DDL - useful for differences in date defaults (eg, now() vs. getdate())
					$sTranslatedDefault = $this->m_oTranslator->TranslateDefault($sDefault);
					$sBufDefault = " DEFAULT $sTranslatedDefault";
					//$sFieldSQL .= " DEFAULT $sTranslatedDefault";
				}
				elseif($sDefault)
				{
					$sBufDefault .= " DEFAULT $sDefault";
					//$sFieldSQL .= " DEFAULT $sDefault";
				}

				if($this->dbms == 'oracle')
				{
					$sFieldSQL .= "{$sBufDefault}{$sBufNullable}";
				}
				else
				{
					$sFieldSQL .= "{$sBufNullable}{$sBufDefault}";
				}
				if($DEBUG) { echo '<br>_GetFieldSQL(): Outgoing SQL:   ' . $sFieldSQL; }
				return true;
			}
			else
			{
				throw new Exception( 'Failed to translate field: type[' . $sType . '] precision[' . $iPrecision . '] scale[' . $iScale . ']');
			}

			return False;
		}

		function _GetPK($aFields, &$sPKSQL)
		{
			$sPKSQL = '';
			if(count($aFields) < 1)
			{
				return True;
			}

			$sFields = '';
			reset($aFields);
			while(list($key, $sField) = each($aFields))
			{
				if($sFields != '')
				{
					$sFields .= ',';
				}
				$sFields .= $sField;
			}

			$sPKSQL = $this->m_oTranslator->GetPKSQL($sFields);

			return True;
		}

		function _GetUC($aFields, &$sUCSQL)
		{
			$sUCSQL = '';
			if(count($aFields) < 1)
			{
				return True;
			}

			$sFields = '';
			reset($aFields);
			foreach($aFields as $key => $sField)
			{
				if($sFields != '')
				{
					$sFields .= ',';
				}

				if(is_array($sField))
				{
					$sField = implode(',', $sField);
				}
				$sFields .= $sField;
			}

			$sUCSQL = $this->m_oTranslator->GetUCSQL($sFields);

			return True;
		}

		function _GetIX($aFields, &$sIXSQL)
		{
			$sIXSQL = '';
			if(count($aFields) < 1)
			{
				return True;
			}

			$sFields = '';
			reset($aFields);

			$num_fields = count($aFields);
			$i = 0;
			foreach($aFields as $key => $sField)
			{
				if(@is_array($sField))
				{
					$sIXSQL .= $this->m_oTranslator->GetIXSQL(implode(',', $sField));
				}
				else
				{
					$sIXSQL .= $this->m_oTranslator->GetIXSQL($sField);
				}

				if($num_fields > 1 && $i < $num_fields-1 && $this->dbms == 'mysql')
				{
					$sIXSQL .= ', ';
				}
				++$i;
			}
			return True;
		}

		function _GetFK($aFields, &$sFKSQL)
		{
			$sFKSQL = '';
			$sFKSQLarr = array();
			if(count($aFields) < 1)
			{
				return True;
			}

			$sFields = '';
			reset($aFields);
			foreach($aFields as $reftable => $sField)
			{
				$sFKSQLarr[] = $this->m_oTranslator->GetFKSQL($reftable, $sField);
			}
			if(isset($sFKSQLarr[0]) && $sFKSQLarr[0])
			{
				$sFKSQL = implode(",\n",$sFKSQLarr);
			}

			return True;
		}

		/**
		* Create Index on tables from tables_update
		*
		* @param string|array $aFields fields hold by the index
		* @param string $sTableName table affected
		*/

		function CreateIndex($aFields, $sTableName)
		{
			if (count($aFields) < 1 || !$sTableName)
			{

				return false;
			}

			$sIXSQL ='';

			$num_fields = count($aFields);
			$i = 0;
			foreach($aFields as $key => $sField)
			{

				if(@is_array($sField))
				{
					$sIXSQL .= $this->m_oTranslator->GetIXSQL(implode(',', $sField),$sTableName);
				}
				else
				{
					$sIXSQL .= $this->m_oTranslator->GetIXSQL($sField,$sTableName);

				}

				if($num_fields > 1 && $i < $num_fields-1 && $this->dbms == 'mysql')
				{
					$sIXSQL .= ', ';
				}
				++$i;
			}

			if($this->dbms == 'mysql' && $sIXSQL)
			{
				$this->query($sIXSQL, __LINE__, __FILE__);
			}

			// postgres and mssql
			if(isset($this->m_oTranslator->indexes_sql) && is_array($this->m_oTranslator->indexes_sql) && count($this->m_oTranslator->indexes_sql)>0)
			{
				foreach($this->m_oTranslator->indexes_sql as $key => $sIndexSQL)
				{
					$ix_name = $key.'_'.$sTableName.'_idx';
					$IndexSQL = str_replace(array('__index_name__','__table_name__'), array($ix_name,$sTableName), $sIndexSQL);
					$this->query($IndexSQL, __LINE__, __FILE__);
				}
			}
		}

		/**
		 * Prepare the VALUES component of an INSERT sql statement
		 * 
		 * @param array $value_set array of values to insert into the database
		 * @return string the prepared sql, empty string for invalid input
		 */
		function validate_insert($values)
		{
			return $this->m_odb->validate_insert($values);
		}
	}
?>
