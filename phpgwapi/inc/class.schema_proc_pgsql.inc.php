<?php
	/**
	* Database schema abstraction class for PostgreSQL
	* @author Tobias Ratschiller <tobias@dnet.it>
	* @author Dan Wilson <phpPgAdmin@acucore.com>
	* @author Michael Dean <mdean@users.sourceforge.net>
	* @author Miles Lott <milosch@phpgroupware.org>
	* @copyright Copyright (C) 1998-1999 Tobias Ratschiller
	* @copyright Copyright (C) 1999-2000 Dan Wilson
	* @copyright Copyright (C) ? Michael Dean, Miles Lott
	* @copyright Portions Copyright (C) 2003,2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage database
	* @version $Id$
	* @link http://www.greatbridge.org/project/phppgadmin
	* @internal SQL for table properties taken from phpPgAdmin Version 2.2.1
	*/

	/**
	* Database schema abstraction class for PostgreSQL
	* 
	* @package phpgwapi
	* @subpackage database
	*/
	class schema_proc_pgsql
	{
		var $m_sStatementTerminator;
		/* Following added to convert sql to array */
		var $sCol = array();
		var $pk = array();
		var $fk = array();
		var $ix = array();
		var $uc = array();
		var $indexes_sql = array();

		function __construct()
		{
			$this->m_sStatementTerminator = ';';
		}

		/* Return a type suitable for DDL */
		function TranslateType($sType, $iPrecision = 0, $iScale = 0)
		{
			switch($sType)
			{
				case 'auto':
					$sTranslated = 'int4';
					break;
				case 'blob':
					$sTranslated = 'text';
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
					if ($iPrecision == 4 || $iPrecision == 8)
					{
						$sTranslated =  sprintf("float%d", $iPrecision);
					}
					break;
				case 'int':
					if ($iPrecision == 2 || $iPrecision == 4 || $iPrecision == 8)
					{
						$sTranslated =  sprintf("int%d", $iPrecision);
					}
					break;
				case 'longtext':
					$sTranslated = 'text';
					break;
				case 'text':
					$sTranslated = 'text';
					break;
				case 'time':
					$sTranslated = 'time';
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
				case 'bool':
					$sTranslated = 'boolean';
					break;
				case 'xml':
					$sTranslated = 'xml';
					break;
				case 'json':
					$sTranslated = 'json';
					break;
				case 'jsonb':
					$sTranslated = 'jsonb';
					break;
			}
			return $sTranslated;
		}

		function TranslateDefault($sDefault, $sType)
		{
			// Need Strict comparisons for true/false in case of datatype bolean
			if ($sDefault === true || $sDefault === 'true' || $sDefault === 'True')
			{
				$ret= 'True';
			}
			else if ($sDefault === false || $sDefault === 'false' || $sDefault === 'False')
			{
				$ret= 'False';
			}
			else if ($sDefault == 'current_date' || $sDefault == 'current_timestamp')
			{
				if(preg_match('/int/i', $sType))
				{
					$ret= "extract( epoch from now())";
				}
				else
				{
					$ret= "now()";
				}
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
			switch($sType)
			{
				case 'serial':
					$sTranslated = "'type' => 'auto'";
					break;
				case 'int2':
					$sTranslated = "'type' => 'int', 'precision' => 2";
					break;
				case 'int4':
					$sTranslated = "'type' => 'int', 'precision' => 4";
					break;
				case 'int8':
					$sTranslated = "'type' => 'int', 'precision' => 8";
					break;
				case 'bpchar':
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
				case 'numeric':
					if($iPrecision == -1)
					{
						/* Borrowed from phpPgAdmin */
						$iPrecision = ($iScale >> 16) & 0xffff;
						$iScale     = ($iScale - 4) & 0xffff;
					}
					$sTranslated = "'type' => 'decimal', 'precision' => $iPrecision, 'scale' => $iScale";
					break;
				case 'float':
				case 'float4':
				case 'float8':
				case 'double':
					$sTranslated = "'type' => 'float', 'precision' => $iPrecision";
					break;
				case 'datetime':
				case 'timestamp':
					$sTranslated = "'type' => 'timestamp'";
					break;
				case 'time':
					$sTranslated = "'type' => 'time'";
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
				case 'text':
				case 'blob':
				case 'date':
				case 'json':
				case 'jsonb':
					$sTranslated = "'type' => '$sType'";
					break;
				case 'bool':
				case 'boolean':
					$sTranslated = "'type' => 'bool'";
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

		function GetIXSQL($sFields, $field_type = '')
		{
			$index_type = 'btree';
			if($field_type == 'jsonb')
			{
				$index_type = 'gin';
			}

			$this->indexes_sql[str_replace(',','_',$sFields)] = "CREATE INDEX __index_name__ ON __table_name__ USING {$index_type} ($sFields)";
			return '';
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
			
		function _GetColumns($oProc, $sTableName, &$sColumns, $sDropColumn = '', $sAlteredColumn = '', $sAlteredColumnType = '')
		{
			$sdb = clone($oProc->m_odb);
			$sdc = clone($oProc->m_odb);

			$sColumns = '';
			$this->pk = array();
			$this->fk = array();
			$this->ix = array();
			$this->uc = array();

			$query = "SELECT a.attname,a.attnum FROM pg_attribute a,pg_class b WHERE ";
			$query .= "b.oid=a.attrelid AND a.attnum>0 and b.relname='$sTableName'";
			if ($sDropColumn != '')
			{
				$query .= " AND a.attname != '$sDropColumn'";
			}
			$query .= ' ORDER BY a.attnum';

			$sdb->query($query, __LINE__, __FILE__);
			while ($oProc->m_odb->next_record())
			{
				if ($sColumns != '')
				{
					$sColumns .= ',';
				}

				$sFieldName = $oProc->m_odb->f(0);
				$sColumns .= $sFieldName;
				if ($sAlteredColumn == $sFieldName && $sAlteredColumnType != '')
				{
					$sColumns .= '::' . $sAlteredColumnType;
				}
			}
			//$qdefault = "SELECT substring(d.adsrc for 128) FROM pg_attrdef d, pg_class c "
			//	. "WHERE c.relname = $sTableName AND c.oid = d.adrelid AND d.adnum =" . $oProc->m_odb->f(1);
			$sql_get_fields = "
				SELECT
					a.attnum,
					a.attname AS field,
					t.typname AS type,
					a.attlen AS length,
					a.atttypmod AS lengthvar,
					a.attnotnull AS notnull
				FROM
					pg_class c,
					pg_attribute a,
					pg_type t
				WHERE
					c.relname = '$sTableName'
					and a.attnum > 0
					and a.attrelid = c.oid
					and a.atttypid = t.oid
					ORDER BY a.attnum";
			/* attnum field type length lengthvar notnull(Yes/No) */
			$sdb->query($sql_get_fields, __LINE__, __FILE__);
			while ($sdb->next_record())
			{
				$colnum  = $sdb->f(0);
				$colname = $sdb->f(1);

				if ($sdb->f(5) == 'f')
				{
					$null = "'nullable' => False";
				}
				else
				{
					$null = "'nullable' => True";
				}

				if ($sdb->f(2) == 'numeric')
				{
					$prec  = $sdb->f(3);
					$scale = $sdb->f(4);
				}
				elseif ($sdb->f(3) > 0)
				{
					$prec  = $sdb->f(3);
					$scale = 0;
				}
				elseif ($sdb->f(4) > 0)
				{
					$prec = $sdb->f(4) - 4;
					$scale = 0;
				}
				else
				{
					$prec = 0;
					$scale = 0;
				}

				$type = $this->rTranslateType($sdb->f(2), $prec, $scale);

				$sql_get_default = "
					SELECT d.adsrc AS rowdefault
						FROM pg_attrdef d, pg_class c
						WHERE
							c.relname = '$sTableName' AND
							c.oid = d.adrelid AND
							d.adnum = $colnum
					";
				$sdc->query($sql_get_default, __LINE__, __FILE__);
				$sdc->next_record();
				if ($sdc->f(0) != '')
				{
					if (preg_match('/nextval/',$sdc->f(0)))
					{
						$default = '';
						$nullcomma = '';
						$type = "'type' => 'auto'";
					}
					else
					{
						if(preg_match('/(now()|::timestamp(6) without time zone$|::timestamp without time zone$)/i', $sdc->f(0)))
						{
							$default =  "'default' =>'current_timestamp'";
						}
						else
						{
							$default = "'default' => '" . str_replace(array('::bpchar','::character varying'),array('',''),$sdc->f(0));						
						}
						
						// For db-functions - add an apos
						if(substr($default,-1)!= "'")
						{
							$default .= "'"; 
						}
						$nullcomma = ',';
					}
				}
				else
				{
					$default = '';
					$nullcomma = '';
				}
				$default = str_replace("''","'",$default);

				$this->sCol[] = "\t\t\t\t'" . $colname . "' => array(" . $type . ',' . $null . $nullcomma . $default . '),' . "\n";
			}
			$sql_pri_keys = "
				SELECT
					ic.relname AS index_name,
					bc.relname AS tab_name,
					ta.attname AS column_name,
					i.indisunique AS unique_key,
					i.indisprimary AS primary_key
				FROM
					pg_class bc,
					pg_class ic,
					pg_index i,
					pg_attribute ta,
					pg_attribute ia
				WHERE
					bc.oid = i.indrelid
					AND ic.oid = i.indexrelid
					AND ia.attrelid = i.indexrelid
					AND ta.attrelid = bc.oid
					AND bc.relname = '$sTableName'
					AND ta.attrelid = i.indrelid
					AND ta.attnum = i.indkey[ia.attnum-1]
				ORDER BY
					index_name, tab_name, column_name";
			$sdc->query($sql_pri_keys, __LINE__, __FILE__);
			while ($sdc->next_record())
			{
				//echo '<br> checking: ' . $sdc->f(4);
				if ($sdc->f(4) == 't')
				{
					$this->pk[] = $sdc->f(2);
				}
				else if ($sdc->f(3) == 't')
				{
					$this->uc[] = $sdc->f(2);
				}
			}

/*
			$ForeignKeys = $sdc->MetaForeignKeys($sTableName);

			foreach($ForeignKeys as $table => $keys)
			{
				$keystr = array();
				foreach ($keys as $keypair)
				{
					$keypair = explode('=',$keypair);
					$keystr[] = "'" . $keypair[0] . "' => '" . $keypair[1] . "'";
				}
				$this->fk[] = $table . "' => array(" . implode(', ',$keystr)  . ')';
			}
*/

			$sql_f_keys = "SELECT
				pc.conname,
				pg_catalog.pg_get_constraintdef(pc.oid, true) AS consrc,
				pc.contype,
				CASE WHEN pc.contype='u' OR pc.contype='p' THEN (
					SELECT
						indisclustered
					FROM
						pg_catalog.pg_depend pd,
						pg_catalog.pg_class pl,
						pg_catalog.pg_index pi
					WHERE
						pd.refclassid=pc.tableoid
						AND pd.refobjid=pc.oid
						AND pd.objid=pl.oid
						AND pl.oid=pi.indexrelid
				) ELSE
					NULL
				END AS indisclustered
			FROM
				pg_catalog.pg_constraint pc
			WHERE
				pc.conrelid = (SELECT oid FROM pg_catalog.pg_class WHERE relname='$sTableName'
					AND relnamespace = (SELECT oid FROM pg_catalog.pg_namespace
					WHERE nspname='public'))
			ORDER BY
				1";

			$oProc->m_odb->query($sql_f_keys, __LINE__, __FILE__);
			while ($oProc->m_odb->next_record())
			{
				if($oProc->m_odb->f('contype') == 'f')
				{
					$f_temp = preg_split("/FOREIGN KEY|REFERENCES|[()]/",$oProc->m_odb->f('consrc'));
					$f_temp_primary = explode(', ',$f_temp[2]);
					$f_temp_foreign = explode(', ',$f_temp[5]);

					$keystr = array();
					for ($i=0;$i<count($f_temp_primary);$i++)
					{
						$keystr[] = "'" . $f_temp_primary[$i] . "' => '" . $f_temp_foreign[$i] . "'";
					}				
					
					$this->fk[] = "'" . trim($f_temp[4]) . "' => array(" . implode(', ',$keystr)  . ')';
				}
			}
			unset($keystr);
			unset($f_temp);
			unset($f_temp_primary);
			unset($f_temp_foreign);


			$metaindexes = $sdc->metaindexes($sTableName);

			//FIXME: looks like unique is reported as index
			foreach($metaindexes as $key => $index)
			{
				if(count($index['columns']) > 1)
				{
					$this->ix[] = $index['columns'];
				}
				else
				{
					$this->ix[] = $index['columns'][0];	
				}
			}

			/* ugly as heck, but is here to chop the trailing comma on the last element (for php3) */
			if($this->sCol)
			{
				$this->sCol[count($this->sCol) - 1] = substr($this->sCol[count($this->sCol) - 1],0,-2) . "\n";
			}

			return false;
		}

		function GetSequenceForTable($oProc,$table,&$sSequenceName)
		{
			global $DEBUG;
			if($DEBUG) { echo '<br>GetSequenceForTable: ' . $table; }

			$oProc->m_odb->query("SELECT relname FROM pg_class WHERE NOT relname ~ 'pg_.*' AND relname LIKE 'seq_$table' AND relkind='S' ORDER BY relname",__LINE__,__FILE__);
			$oProc->m_odb->next_record();
			if ($oProc->m_odb->f('relname'))
			{
				$sSequenceName = $oProc->m_odb->f('relname');
			}
			return True;
		}

		function GetSequenceFieldForTable($oProc,$table,&$sField)
		{
			global $DEBUG;
			if($DEBUG) { echo '<br>GetSequenceFieldForTable: You rang?'; }

//			$oProc->m_odb->query("SELECT a.attname FROM pg_attribute a, pg_class c, pg_attrdef d WHERE c.relname='$table' AND c.oid=d.adrelid AND d.adsrc LIKE '%seq_$table%' AND a.attrelid=c.oid AND d.adnum=a.attnum", __LINE__, __FILE__);

			$sql = "SELECT table_name, column_name, column_default"
				. " FROM information_schema.columns"
				. " WHERE table_name='{$table}'"
				. " AND column_default LIKE '%seq_{$table}%'";

			$oProc->m_odb->query($sql, __LINE__, __FILE__);

			$oProc->m_odb->next_record();
			$column_name = $oProc->m_odb->f('column_name');
			if ($column_name)
			{
				$sField = $column_name;
			}
			return True;
		}

		function DropSequenceForTable($oProc,$table)
		{
			global $DEBUG;
			if($DEBUG) { echo '<br>DropSequenceForTable: ' . $table; }

			$this->GetSequenceForTable($oProc,$table,$sSequenceName);
			if ($sSequenceName)
			{
				$oProc->m_odb->query("DROP SEQUENCE " . $sSequenceName . " CASCADE",__LINE__,__FILE__);
			}
			return True;
		}

		function DropTable($oProc, &$aTables, $sTableName)
		{
			$this->DropSequenceForTable($oProc,$sTableName);

			return $oProc->m_odb->query("DROP TABLE " . $sTableName . " CASCADE", __LINE__, __FILE__) &&
				   $this->DropSequenceForTable($oProc, $sTableName);
		}

		function DropColumn($oProc, &$aTables, $sTableName, $aNewTableDef, $sColumnName, $bCopyData = true)
		{
			$query = "ALTER TABLE $sTableName DROP COLUMN $sColumnName CASCADE";
			$bRet = !!($oProc->m_odb->query($query, __LINE__, __FILE__));
			return $bRet;
		}

		function RenameTable($oProc, &$aTables, $sOldTableName, $sNewTableName)
		{
			global $DEBUG;
			$Ok = false;
			if ($DEBUG) { echo '<br>RenameTable():' . $sOldTableName . 'to: '. $sNewTableName; }
			if ($DEBUG) { echo '<br>RenameTable(): Fetching old sequence for: ' . $sOldTableName; }
			$this->GetSequenceForTable($oProc,$sOldTableName,$sSequenceName);
			if ($DEBUG) { echo ' - ' . $sSequenceName; }
			if ($DEBUG) { echo '<br>RenameTable(): Fetching sequence field for: ' . $sOldTableName; }
			$this->GetSequenceFieldForTable($oProc,$sOldTableName,$sField);
			if ($DEBUG) { echo ' - ' . $sField; }

			if ($sSequenceName)
			{
				$oProc->m_odb->query("SELECT last_value FROM seq_$sOldTableName",__LINE__,__FILE__);
				$oProc->m_odb->next_record();
				$lastval = $oProc->m_odb->f(0);

				if ($lastval)
				{
					$lastval = ' start ' . $lastval;
				}
				$this->GetSequenceSQL($sNewTableName,$sSequenceSQL);
				if ($DEBUG) { echo '<br>RenameTable(): Making new sequence using: ' . $sSequenceSQL . $lastval; }
				$oProc->m_odb->query($sSequenceSQL . $lastval,__LINE__,__FILE__);
				if ($DEBUG) { echo '<br>RenameTable(): Altering column default for: ' . $sField; }
			}

			$oProc->m_odb->query("ALTER TABLE $sOldTableName RENAME TO $sNewTableName", __LINE__, __FILE__);
			if ($sSequenceName)
			{
				$Ok = !!$oProc->m_odb->query("ALTER TABLE $sNewTableName ALTER $sField SET DEFAULT nextval('seq_" . $sNewTableName . "')", __LINE__, __FILE__);
				$this->DropSequenceForTable($oProc,$sOldTableName);
			}
			
			return $Ok;

		/* todo - fix index-renaming.
			$indexnames = $oProc->m_odb->index_names();
			while(list($key,$val) = @each($indexnames))
			{
				$indexes[] = $val['index_name'];
			}
			if(!in_array($sOldTableName . '_pkey',$indexes))	// no idea how this can happen
			{
				$oProc->m_odb->query("DROP INDEX " . $sOldTableName . "_pkey",__LINE__,__FILE__);
			}
			else	// rename the index
			{
				$oProc->m_odb->query('ALTER TABLE '.$sOldTableName.'_pkey RENAME TO '.$sNewTableName.'_pkey');
			}
		

			return !!($oProc->m_odb->query("ALTER TABLE $sOldTableName RENAME TO $sNewTableName"));
		*/
		}

		function RenameColumn($oProc, &$aTables, $sTableName, $sOldColumnName, $sNewColumnName, $bCopyData = true)
		{
			$query = "ALTER TABLE $sTableName RENAME COLUMN $sOldColumnName TO $sNewColumnName";
			return !!($oProc->m_odb->query($query, __LINE__, __FILE__));
		}

		function AlterColumn($oProc, &$aTables, $sTableName, $sColumnName, &$aColumnDef, $bCopyData = true)
		{
			$sType = '';
			$iPrecision = 0;
			$iScale = 0;
			$sDefault = '';
			$bNullable = true;

			//reset($aColumnDef);
			//while(list($sAttr, $vAttrVal) = each($aColumnDef))
			if (is_array($aColumnDef))
			{
				foreach($aColumnDef as $sAttr => $vAttrVal)
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
							break;
						case 'nullable':
							$bNullable = $vAttrVal;
							break;
					}
				}
			}

			$sFieldSQL = $this->TranslateType($sType, $iPrecision, $iScale);
			$query = "ALTER TABLE $sTableName ALTER COLUMN $sColumnName TYPE $sFieldSQL";
			$Ok = !!($oProc->m_odb->query($query, __LINE__, __FILE__));

			if($bNullable === False || $bNullable === 'False')
			{
				$sFieldSQL = ' SET NOT NULL';
			}
			else
			{
				$sFieldSQL = ' DROP NOT NULL';			
			}

			$query = "ALTER TABLE $sTableName ALTER COLUMN $sColumnName $sFieldSQL";
			$Ok = !!$oProc->m_odb->query($query, __LINE__, __FILE__);

			if($sDefault == '0')
			{
				$defaultSQL = " DEFAULT 0";
			}								
			elseif(!is_numeric($sDefault) && $sDefault != '')
			{
				$sTranslatedDefault = $this->TranslateDefault($sDefault, $sType);
				$defaultSQL = " DEFAULT $sTranslatedDefault";
			}
			elseif($sDefault)
			{
				$defaultSQL = " DEFAULT $sDefault";
			}

			if(isset($defaultSQL) && $defaultSQL)
			{
				$query = "ALTER TABLE $sTableName ALTER COLUMN $sColumnName SET $defaultSQL";
				$Ok = !!$oProc->m_odb->query($query, __LINE__, __FILE__);
			}

			return $Ok;
		}

		function AddColumn($oProc, &$aTables, $sTableName, $sColumnName, &$aColumnDef)
		{
			if (!isset($aColumnDef['nullable']) || (isset($aColumnDef['nullable']) && ($aColumnDef['nullable'] === 'False' || $aColumnDef['nullable'] === false)) )	// pgsql cant add a column not nullable if there is data in the table
			{
				if(!isset($aColumnDef['default']))
				{
					$oProc->m_odb->query("SELECT count(*) as cnt FROM $sTableName", __LINE__, __FILE__);
					$oProc->m_odb->next_record();
					if($oProc->m_odb->f(0))
					{
						throw new Exception(lang('ERROR: The column %1 in table %2 cannot be added as NOT NULL as there is data in the table', $sColumnName, $sTableName));
					}
				}
				else
				{
					trigger_error(lang('column %1 for table %2 will be added as NULLABLE with DEFAULT value %3', $classname, $sTableName, $aColumnDef['default']), E_USER_WARNING);
				}
			}

			$oProc->_GetFieldSQL($aColumnDef, $sFieldSQL);
			$query = "ALTER TABLE $sTableName ADD COLUMN $sColumnName $sFieldSQL";

			return !!$oProc->m_odb->query($query, __LINE__, __FILE__);
		}

		function GetSequenceSQL($sTableName, &$sSequenceSQL)
		{
			$sSequenceSQL = sprintf("CREATE SEQUENCE seq_%s", $sTableName);
			return true;
		}

		function GetTriggerSQL($sTableName, $sColumnNames, &$sTriggerSQL)
		{
			$sTriggerSQL = ''; 
			return false;
		}

		function CreateTable($oProc, $aTables, $sTableName, $aTableDef, $bCreateSequence = true)
		{
			global $DEBUG;
			unset($this->indexes_sql);
			if ($oProc->_GetTableSQL($sTableName, $aTableDef, $sTableSQL, $sSequenceSQL, $sTriggerSQL))
			{
				/* create sequence first since it will be needed for default */
				if ($bCreateSequence && $sSequenceSQL != '')
				{
					if ($DEBUG) { echo '<br>Making sequence using: ' . $sSequenceSQL; }
					$oProc->m_odb->query($sSequenceSQL, __LINE__, __FILE__);
				}

				$query = "CREATE TABLE $sTableName ($sTableSQL)";
				//echo 'sql' .$query . "\n";

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
				//return !!($oProc->m_odb->query($query));				
			}

			return false;
		}
	}

