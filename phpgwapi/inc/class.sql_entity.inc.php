<?php
	/**
	 * SQL Generator ENTITY - helps to construct queries statements
	 * @author Edgar Antonio Luna Diaz <eald@co.com.mx>
	 * @author Alejadro Borges
	 * @author Jonathan Alberto Rivera Gomez
	 * @copyright Copyright (C) 2003,2004 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	 * @package phpgwapi
	 * @subpackage database
	 * @version $Id$
	 * @internal Development of this application was funded by http://www.sogrp.com
	 * @link http://www.sogrp.com/
	 */
	/**
	 * SQL entity alias substitution string EASS
	 */
	define('PHPGW_SQL_EASS', '|%-');

	/**
	 * SQL default method
	 */
	define('PHPGW_SQL_DEFAULT_METHOD', -1);

	/**
	 * SQL return records
	 */
	define('PHPGW_SQL_RETURN_RECORDS', 1);

	/**
	 * SQL run SQL
	 */
	define('PHPGW_SQL_RUN_SQL', 1);

	/**
	 * SQL return SQL
	 */
	define('PHPGW_SQL_RETURN_SQL', 2);


	/**
	 * SQL Generator ENTITY - helps to construct queries statements
	 *
	 * This class provide common methods to create transaction sql queries.
	 * Isolates an entity.
	 * @package phpgwapi
	 * @subpackage database
	 */
	class phpgwapi_sql_entity
	{
		/* List of fields to mantain in each query, it morph if select,
		 *  if insert, if update.
		 */

		var $fields;
		var $inserts;
		var $insert_index;
		var $operation;
		var $criteria;
		// need in list form, for easy search
		var $field_list;
		// var $field_array;
		var $alias;
		// Just INSERT (update?)
		var $values;
		// Imported links
		var $ilink = array();
		// Exported links
		var $elink = array();
		var $ondebug;
		var $table;

		function __construct( $table = '', $alias = '')
		{
			$this->table = $table;
			// Temp alias name, just if not empty
			$this->alias = ($alias) ? $alias : $alias;
			$this->ldebug('__construct', array('Table' => $table,
				'Alias' => $alias));

		}
		/*		 * ***********************************************************\
		 * Entity, class and general section                           *
		  \************************************************************ */

		/**
		 * Set the alias for the table
		 *
		 * @param string $alias The alias name
		 * @return The alias name which will be used for SELECT.
		 */
		function set_identity( $alias )
		{
			$this->alias = ($alias) ? $alias : get_class($this);
			$this->table = ($this->table) ? $this->table :	get_class($this);
			$this->ldebug('set_identity', array('Alias' => $this->alias,
				'Table' => $this->table));
		}

		function set_table_name( $table )
		{
			$this->table = $table;
		}

		function set_alias( $alias )
		{
			$this->alias = $alias;
		}

		/**
		 * Get the list of false fields from instance.
		 *
		 * @return Array List of false fields.
		 */
		function get_false_fields()
		{
			return array_keys($this->map);
		}

		/**
		 * Determines whether if operation must be changed or not.
		 *
		 * Operation is used to decide if alias is attached to fields names or not
		 * @param string $opertation Actual action that is proposed to be the operation.
		 */
		function set_operation( $operation )
		{
			if (empty($this->operation) && $operation != 'criteria' && !empty($operation))
			{
				$this->operation = $operation;
			}
		}

		/**
		 * Forced change of actual operation
		 *
		 * Operation is used to decide if alias is attached to fields names or not
		 * @param string $operation action to be set.
		 */
		function change_operation( $operation )
		{
			if (!empty($operation) && $operation != 'criteria')
			{
				$this->operation = $operation;
			}
		}
		/*		 * ***********************************************************\
		 * Alias section                                               *
		  \************************************************************ */

		/**
		 * Replace the alias string with the one true alias name
		 *
		 * @return Tow string, fields and criteria with the correct alias name.
		 */
		function run_alias()
		{
			if ($this->alias)
			{
				$this->field_alias();
				$this->fields = str_replace(PHPGW_SQL_EASS, $this->alias, $this->fields);
				$this->criteria = str_replace(PHPGW_SQL_EASS, $this->alias, $this->criteria);
				$this->ldebug('run_alias', array('Fields' => $this->fields,
					'Criteria' => $this->criteria,
					'Alias' => $this->alias));
			}
		}

		/**
		 * Set a alias to the table if is required
		 *
		 * @return string with the table name, with alias if is required.
		 */
		function get_identity()
		{
			return ($this->alias != $this->table) ? $this->table . ' ' . $this->alias : $this->table;
		}

		function get_alias()
		{
			return $this->alias;
		}

		/**
		 * @param string $field Name of the field.
		 * @return the string ready for replace s/EASS/alias/
		 */
		function put_alias( $field )
		{
			return ($this->operation == 'select') ? PHPGW_SQL_EASS . '.' . $field : $field;
		}

		/**
		 * Set the alias to a any field
		 * @param string $field Name of the field.
		 * @return the string ready for replace s/EASS/alias/
		 */
		function put_real_alias( $field )
		{
			return ($this->operation == 'select') ? $this->alias . '.' . $field : $field;
		}
		/*		 * ***********************************************************\
		 * Select section                                              *
		  \************************************************************ */

		/**
		 * Add the field to list, with alias $alias
		 *
		 * @param string $field Any sql instruction like count(field_name)
		 * @param string $alias Alias for $field
		 */
		function add_field( $alias, $field )
		{
			$this->field_list[$alias] = $field;
		}

		/**
		 * Add the field to list
		 *
		 * @param array $element with real_field and false field (field)
		 * @access private
		 */
		function _add_field( $element )
		{
			$this->add_field($element['field'], $this->put_alias($element['real_field']));
			$this->ldebug('_add_field', array('Field_list' => $this->field_list), 'dump');
		}

		/**
		 * Set the alias for the select query
		 *
		 * @param string $real_field The real name of the field
		 * @param string $alias_field the alias that field will take
		 * @access private
		 */
		function set_field_alias( $real_field, $alias_field )
		{
			$this->ldebug('set_field_alias', array('Field list' => $this->field_list), 'dump');
			$this->fields .= ', ' . $real_field . ' AS ' . $alias_field;
		}

		function field_alias()
		{
			$this->ldebug('field_alias', array('Field list' => $this->field_list), 'dump');
			if ($this->field_list)
			{
				$alias_field = key($this->field_list);
				$field = array_shift($this->field_list);
				$this->fields = $field . ' AS ' . $alias_field;
				array_walk($this->field_list, array(&$this, 'set_field_alias'));
			}
		}

		/**
		 * local select, if I'm the only one? or for catalog entities?
		 *
		 * @return string A select easy to use.
		 */
		function select()
		{
			$this->run_alias();
			$sql_select = 'SELECT ' . $this->fields .
				' FROM ' . $this->get_identity() .
				($this->get_criteria() ? ' WHERE ' . $this->get_criteria() : '');
			return $sql_select;
		}

		/**
		 * @return array (field, identity, alias, criteria) for:
		 * SELECT <field> FROM <identity> WHERE <criteria> and <alias> for the on construction depending on identity
		 */
		function get_select()
		{
			$this->run_alias();
			$select_value = array
				(
				$this->fields,
				$this->get_identity(),
				$this->alias,
				$this->get_criteria() // used below for for debug
			);
			$this->ldebug('get_select', array('Criteria' => $select_value[3]));
			return $select_value;
		}
		/*		 * ***********************************************************\
		 * Criteria section                                            *
		  \************************************************************ */

		/**
		 * Get the criteria
		 *
		 * @return string with the criteria which was autogenerate.
		 */
		function get_criteria()
		{
			return $this->criteria;
		}

		/**
		 * When no special method defined for $elemnent['field'] this is the method that will run. And add to the criteria list
		 *
		 * @param string $element['real_field'] The associated field.
		 * @param string $element['value'] Criteria for this field.
		 */
		function default_criteria( $element )
		{
			$this->ldebug('default_criteria', array('Element' => $element), 'dump');
			$field = (($this->operation == 'select') ?
				$this->put_alias($element['real_field']) :
				$element['real_field']);
			$this->ldebug('default_criteria', array('Field' => $field));

			$new_criteria = phpgwapi_sql_criteria::has($field, $element['value']);
			$this->ldebug('default_criteria', array('New Criteria' => $new_criteria));
			$this->_add_criteria($new_criteria);
		}

		/**
		 * Add criteria to list
		 *
		 * @param string $new_criteria with the new criteria which was autegenerate.
		 * @return string with the criteria.
		 */
		function _add_criteria( $new_criteria )
		{
			$this->ldebug('_add_criteria', array('New Criteria' => $new_criteria,
				'All Criteria Prev' => $this->criteria));
			$this->criteria = phpgwapi_sql_criteria::append_and(array($new_criteria,
					$this->criteria));
			$this->ldebug('_add_criteria', array('All Criteria Post' => $this->criteria));
		}

		/**
		 * Especial criteria for index or id, it decides if must call equal or in operator
		 *
		 * @param array $element with field, value, real_name
		 * @return string with a usefull criteria to use for many (a in clause) or just one id (equal).
		 */
		function index_criteria( $element )
		{
			$field = $this->put_alias($element['real_field']);
			if (is_array($element['value']))
			{
				if (count($element['value']) == 1)
				{
					$value = $this->cast(current($element['value']), $element['field']);
					return phpgwapi_sql_criteria::equal($field, $value);
				}
				elseif (count($element['value']) > 1)
				{
					return phpgwapi_sql_criteria::in($field, $element['value'], $this->get_datatype($field));
				}
			}
			else
			{
				$value = $this->cast($element['value'], $element['field']);
				return phpgwapi_sql_criteria::equal($field, $value);
			}
		}

		/**
		 * Analize a criteria created by tokens and create a string that represent it, useful for any kind of operation that use criteria I guess.
		 *
		 * @param $token_criteria array Array with all the criteria in tokens, generated with sql_criteria
		 * @return string Criteria string (All that goes in WHERE clause)
		 * @see sql_criteria
		 */
		function entity_criteria( $token_criteria )
		{
			/*
			  Things to care about:
			  - `_append_and', `_append_or' arrays have two elements: 1. array with criterias, 2. token
			  - `in' is a three element: 1. field name, 2. array with values, 3. token
			 */

			$num_elements = count($token_criteria);
			switch ($num_elements)
			{
				case 0:
				case 1:
					$local_criteria = $token_criteria;
					break;
				case 2:
				case 3:
					$operator = array_pop($token_criteria);
					$left = array_shift($token_criteria);
					$right = array_shift($token_criteria);

					if (is_array($left) && $operator != 'in')
					{
						$left = $this->entity_criteria($left);
					}
					else
					{
						$left = $this->real_field($left);
					}
					if (is_array($right))
					{
						$right = $this->entity_criteria($right);
					}
					$local_criteria = phpgwapi_sql_criteria::operate($operator, $left, $right);
					break;
				default:
					$operator = array_pop($token_criteria);
					$local_criteria = phpgwapi_sql_criteria::operate($operator, $token_criteria);
			}
			return $local_criteria;
		}
		/*		 * ***********************************************************\
		 * Insert (input data) section                                 *
		  \************************************************************ */

		/**
		 * Wrapper for calling add_insert_element, when we have $element ready.
		 *
		 * @param array $element Form: ('field', 'real_field', 'value').
		 */
		function set_insert_data( $element )
		{
			$this->add_insert_element($element['field'], $element['real_field'], $element['value']);
		}

		/**
		 * Genarete two string with fields and values list
		 *
		 * @param string $false_field Field in map.
		 * @param string $field BD field and  which use in insert.
		 * @param string $value Value for use in insert.
		 */
		function add_insert_element( $false_field, $field, $value )
		{
			$this->inserts[$this->insert_index]['data'][$false_field] = array('field' => $field,
				'value' => $value);
			$this->ldebug('add_insert_element', array
				(
				'False Field' => $false_field,
				'DB Field' => $field,
				'Value' => $value
			));
		}
		/*		 * ***********************************************************\
		 * Insert (return data) section                                *
		  \************************************************************ */

		/**
		 * Definitive interfase for get the array of inserts sql queries
		 *
		 * @param array $entities with the list of entity that are present in the transaction
		 * @return Array with the sql insert, just with imported keys missing
		 */
		function get_multiple_insert( $entities )
		{
			foreach ($entities as $entity_name)
			{
				$link = $this->get_ilink($entity_name);
				$field = $this->real_field($link['lfield']);
				$fields_to_prototype[$field] = '{' . $link['lfield'] . '}';
			}

			foreach ($this->inserts as $index => $insert)
			{
				// First element, the only one than don't begin with `,'
				$false_field = key($this->inserts[$index]['data']);
				$this->inserts[$index]['fields'] = $insert['data'][$false_field]['field'];
				$this->inserts[$index]['values'] = $insert['data'][$false_field]['value'];
				// Go for next elements
				array_walk($this->inserts[$index]['data'], array(&$this, 'set_fields_insert'), $index);
				reset($this->inserts[$index]['data']);
				array_walk($field_to_prototype, array(&$this, 'set_field_inserts_prototyped'), $index);
				$inserts[$index] = $this->insert($index);
			}
			return $inserts;
		}

		function insert( $data, $action = PHPGW_SQL_RETURN_SQL )
		{
			$this->_insert($data, 0);
			$sql = $this->get_single_insert(0);
			switch ($action)
			{
				case PHPGW_SQL_RETURN_RECORDS:
				case PHPGW_SQL_RUN_SQL:
					$GLOBALS['phpgw']->db->query($sql, __LINE__, __FILE__);
					$this->ldebug('insert', $sql, 'msg');
					break;
				case PHPGW_SQL_RETURN_SQL:
					return $sql;
			}
		}

		function _insert( $data, $index = 0 )
		{
			foreach ($data as $field => $value)
			{
				$this->add_insert($field, $value, $index);
			}
		}

		/**
		 * Get the insert sql statement for one entry
		 *
		 * @param int $index with the index of data which we want to insert
		 * @return string Corresponding sql insert.
		 */
		function get_single_insert( $index = 0 )
		{
			// First element, the only one than don't begin with `,'
			$false_field = key($this->inserts[$index]['data']);
			$this->inserts[$index]['fields'] = $this->inserts[$index]['data'][$false_field]['field'];
			$this->inserts[$index]['values'] = $this->cast($this->inserts[$index]['data'][$false_field]['value'], $false_field);
			// Go for next elements
			while (next($this->inserts[$index]['data']))
			{
				$false_field = key($this->inserts[$index]['data']);
				$this->ldebug('get_single_insert', array('data for index ' . $index => $this->inserts[$index]['data']), 'dump');
				$this->inserts[$index]['fields'] .= ', ' . $this->inserts[$index]['data'][$false_field]['field'];
				$this->inserts[$index]['values'] .= ', ' . $this->cast($this->inserts[$index]['data'][$false_field]['value'], $false_field);
			}
			return $this->_single_insert($index);
		}

		/**
		 * Get the right value for the datatype of the false field
		 *
		 * @param mixed $data value that want to cast.
		 * @param string $false_field Field for search datatype
		 * @return string Corresponding string with sql for datatype
		 */
		function cast( $data, $false_field )
		{
			if (is_array($data))
			{
				return $this->index_criteria($data);
			}
			$type = $this->get_datatype($false_field);
			return $data === phpgwapi_sql::null() ? phpgwapi_sql::null() : phpgwapi_sql::$type($data);
		}

		/**
		 * Genarete the insert string
		 *
		 * @return The string which will be use for insert query.
		 */
		function _single_insert( $index )
		{
			$sql_insert = 'INSERT INTO ' . $this->table .
				' (' . $this->get_insert_fields($index) .
				') VALUES (' . $this->get_insert_values($index) . ')';
			return $sql_insert;
		}

		function set_fields_insert_prototyped( $field, $value, $index )
		{
			if (!array_key_exist($field, $this->inserts[$index]['data']))
			{
				$this->inserts[$index]['fields'] .= ', ' . $field;
				$this->inserts[$index]['values'] .= ', ' . $value;
			}
		}

		function get_insert_fields( $index )
		{
			return $this->inserts[$index]['fields'];
		}

		function get_insert_values( $index )
		{
			return $this->inserts[$index]['values'];
		}
		/*		 * ***********************************************************\
		 * Update section                                              *
		  \************************************************************ */

		/**
		 * Create an update query for this entity
		 *
		 * @param Array $data Fields that want change value and their values
		 * @param Array $criteria With criterias that set the rows to edit
		 * @param integer action
		 * @return string SQL update string
		 */
		function update( $data, $criteria, $action = PHPGW_SQL_RETURN_SQL )
		{
			if (is_array($data) && count($data) > 0)
			{
				array_walk($data, array(&$this, 'add_update'));
			}
			else
			{
				list($field, $value) = explode('=', $data);
				$this->add_update($field, $value);
			}
			if (is_string($criteria))
			{
				$this->set_criteria($criteria);
			}
			else
			{
				$this->set_criteria(phpgwapi_sql_criteria::criteria($criteria));
			}

			if (!empty($this->values))
			{
				switch ($action)
				{
					case PHPGW_SQL_RETURN_RECORDS:
					case PHPGW_SQL_RUN_SQL:
						$sql = $this->return_update();
						$GLOBALS['phpgw']->db->query($sql, __LINE__, __FILE__);
						$this->ldebug('update', $sql, 'msg');
						return;
					case PHPGW_SQL_RETURN_SQL:
						return $this->return_update();
				}
			}
		}

		function set_criteria( $criteria )
		{
			$this->criteria = $criteria;
		}

		/**
		 * Genarete the update string
		 *
		 * @return The string which will be used for update query.
		 */
		function return_update()
		{
			$sql_update = 'UPDATE ' . $this->table .
				' SET ' . $this->values;
			if ($this->criteria)
			{
				$sql_update .= ' WHERE ' . $this->criteria;
			}
			$this->values = '';
			$this->criteria = '';
			return $sql_update;
		}

		function get_update()
		{
			return (array('fields' => $this->get_update_data(),
				'criteria' => $this->get_criteria(),
				'identity' => $this->get_identity()));
		}

		/**
		 * Genarete a string with field = value to use in update
		 *
		 * @param string $fields
		 * @param string $values
		 * @return string with field=value list.
		 */
		function set_update_data( $element )
		{
			if ($element['value'] || $element['value'] == 0)
			{
				$value = $this->cast($element['value'], $element['field']);
			}
			else
			{
				phpgwapi_sql::null();
			}

			if ($this->values)
			{
				$this->values .= ', ';
			}

			$this->values .= "{$element['real_field']} = {$value}";
		}

		/**
		 * Get the complete field=value listo to use in the update
		 *
		 * @return string with field=value comma separate.
		 */
		function get_update_data()
		{
			return $this->values;
		}

		/**
		 * Genarete the delete string
		 *
		 * @param string $criteria the criteria for select the rows to delete
		 * @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL
		 * @return string which will be used for delete query.
		 */
		function delete( $criteria, $action )
		{
			if ($criteria)
			{
				$sql = 'DELETE FROM ' . $this->table . ' WHERE ' . $criteria;

				$this->set_criteria('');
				switch ($action)
				{
					case PHPGW_SQL_RETURN_RECORDS:
					case PHPGW_SQL_RUN_SQL:
						$GLOBALS['phpgw']->db->query($sql, __LINE__, __FILE__);
						return;
					case PHPGW_SQL_RETURN_SQL:
						return $sql;
				}
			}
		}

		/**
		 * Get the complete fields list to use in the insert or select
		 *
		 * @return string with fields comma separate.
		 */
		function get_fields()
		{
			return $this->fields;
		}
		/*		 * ***********************************************************\
		 * Links and keys section                                      *
		  \************************************************************ */

		/**
		 * Genarete an array with all imported links
		 *
		 * @param string $fl with local field
		 * @param string $t with table
		 * @param string $ff with foreign field
		 * @param int $key_type PHPGW_SQL_LAZY_KEY if want that this link be joined via OUTER (LEFT o RIGHT); PHPGW_SQL_REQUIRED_KEY
		 * if want that be joined with INNER LINK. This is the setting by default, and could be changed per query execution.
		 */
		function set_ilinks( $fl, $t, $ff, $key_type = PHPGW_SQL_LAZY_KEY )
		{
			$this->ilink[$t] = array('lfield' => $fl,
				'ffield' => $ff,
				'type' => $key_type);
		}

		/**
		 * Genarete an array with all exported links
		 *
		 * @param string $fl with local field
		 * @param string $t with table
		 * @param string $ff with foreign field
		 */
		function set_elinks( $fl, $t, $ff )
		{
			$this->elink[$t] = array('lfield' => $fl,
				'ffield' => $ff);
		}

		/**
		 * Get the lfield and ffield from any of elink or ilink according to $entity.
		 *
		 * @param string $entity Name of entity to search link with.
		 * @return Array $lfield that is the local field of the link, and $ffield that is the foreign field.
		 */
		function get_link( $entity = '' )
		{
			if ($entity != '')
			{
				if (array_key_exists($entity, (isset($this->ilink) ? $this->ilink : array())))
				{
					return $this->ilink[$entity];
				}
				elseif (array_key_exists($entity, (isset($this->elink) ? $this->elink : array())))
				{
					return $this->elink[$entity];
				}
			}
			// Must raise error
			return '';
		}

		/**
		 * Get the lfield and ffield from any of ilink according to $entity.
		 *
		 * @param strnig $entity Name of entity to search link with.
		 * @return Array $lfield that is the local field of the link, and $ffield that is the foreign field.

		 */
		function get_ilink( $entity = '' )
		{
			$this->ldebug('get_ilink', array('entity' => $entity));
			$this->ldebug('get_ilink', array('ilinks' => $this->ilink), 'dump');
			if ($entity != '')
			{
				if (array_key_exists($entity, (isset($this->ilink) ? $this->ilink : array())))
				{
					return $this->ilink[$entity];
				}
			}
			// Must raise error
			return;
		}

		/**
		 * Get the lfield and ffield from any of elink according to $entity.
		 *
		 * @param string $entity Name of entity to search link with.
		 * @return Array $lfield that is the local field of the link, and $ffield that is the foreign field.
		 */
		function get_elink( $entity = '' )
		{
			if ($entity != '')
			{
				if (array_key_exists($entity, (isset($this->elink) ? $this->elink : array())))
				{
					return $this->elink[$entity];
				}
			}
			return;
		}

		/**
		 * Set an array with all imported or exported links
		 *
		 * @return array with imported or exported links.
		 */
		function get_ilinks()
		{
			return $this->ilink;
		}

		/**
		 * Set an array with all exported links
		 *
		 * @return array with imported or exported links.
		 */
		function get_elinks()
		{
			return $this->elink;
		}

		function get_fields_links( $entities )
		{
			foreach ($entities as $entity_name)
			{
				$link = get_ilink($entity_name);
				$fields_return[] = $link['ffalse'];
			}
			return $fields_return;
		}
		/*		 * ***********************************************************\
		 * add_element `Sniper' section                                *
		  \************************************************************ */

		function add_element( $action, $element )
		{
			$this->set_operation($action);
			$method = $this->get_method($action, $element['field']);
			$element['real_field'] = $this->real_field($element['field']);
			switch ($action)
			{
				case 'select':
					$method_default = '_add_field';
					break;
				case 'insert':
				case 'delete':
				case 'update':
					$method_default = 'set_' . $action . '_data';
					break;
				case 'criteria':
					$method_default = 'default_criteria';
					break;
				default:
					$this->dont_exist($action);
			}

			$this->ldebug('add_element', array('Element' => $element), 'dump');

			if ($method == PHPGW_SQL_DEFAULT_METHOD)
			{
				$this->ldebug('add_element', array
					(
					'Method_Default DEF' => $method_default,
					'Method DEF' => $method
				));
				$this->$method_default($element);
			}
			elseif ($method)
			{
				$this->ldebug('add_element', array
					(
					'Method_Default' => $method_default,
					'Method' => $method
				));
				$this->$method($element);
			}
			else
			{
				$this->ldebug('add_element', 'Never be here, hope', 'msg');
				$this->dont_exist($element);
			}
		}

		function get_method( $action, $field )
		{
			if (isset($this->map[$field]))
			{
				$method = $this->map[$field][$action];
				if (isset($this->map[$field][$action]) && method_exists($this, $method))
				{
					return $method;
				}
				elseif (method_exists($this, $action . '_' . $field))
				{
					return $action . '_' . $field;
				}
				else
				{
					return PHPGW_SQL_DEFAULT_METHOD;
				}
			}
			// this is an error :/ not $field in map
			trigger_error("Unknown method for {$field}", E_USER_NOTICE);
			return;
		}

		function real_field( $field )
		{
			if (isset($this->map[$field]))
			{
				if (isset($this->map[$field]['field']) && !empty($this->map[$field]['field']))
				{
					return $this->map[$field]['field'];
				}
				else
				{
					return $field;
				}
			}
			// this is an error :/ not $field in map
			trigger_error("Unknown real field for {$field}", E_USER_NOTICE);
			return;
		}

		/**
		 * Get the real field name with alias of table. (Used in criteria).
		 *
		 * @param string $field False field name.
		 * @return string alias.real_name
		 */
		function alias_field( $field )
		{
			return $this->get_alias() . '.' . $this->real_field($field);
		}

		function get_datatype( $field )
		{
			if (isset($this->map[$field]))
			{
				if (isset($this->map[$field]['type']) && !empty($this->map[$field]['type']))
				{
					return $this->map[$field]['type'];
				}
				else
				{
					return 'string';
				}
			}
			// this is an error :/ not $field in map
			trigger_error("Unknown field {$field}", E_USER_NOTICE);
			return;
		}

		function make_pair( $field, $value )
		{
			return array('field' => $field,
				'value' => $value);
		}
		/*		 * ***********************************************************\
		 * sql_builder API section                                     *
		  \************************************************************ */

		function add_select( $field )
		{
			if (array_key_exists($field, $this->map))
			{
				$this->add_element('select', $this->make_pair($field, ''));
				$this->ldebug('add_select', array('Field' => $field));
			}
		}

		function add_criteria( $field, $value )
		{
			if (array_key_exists($field, $this->map))
			{
				$this->add_element('criteria', $this->make_pair($field, $value));
				$this->ldebug('add_criteria', array('Field' => $field));
			}
		}

		function add_update( $value, $field )
		{
			if (array_key_exists($field, $this->map))
			{
				$this->add_element('update', $this->make_pair($field, $value));
			}
		}

		function add_delete( $field, $value )
		{
			if (array_key_exists($field, $this->map))
			{
				$this->add_element('delete', $this->make_pair($field, $value));
			}
		}

		function add_insert( $field, $value, $idx = 0 )
		{
			if (array_key_exists($field, $this->map))
			{
				$this->insert_index = $idx;
				$this->add_element('insert', $this->make_pair($field, $value));
			}
		}

		/**
		 * Must raise errors for this class, don't know if phpgw have anything already, if yes, net call it
		 *
		 * @param mixed $data What dont exist
		 */
		function dont_exist( $data )
		{
			trigger_error("Sorry, this data doesn't exist {$data}", E_USER_NOTICE);
		}

		/**
		 * Get the field name which correspond to sort
		 *
		 * @param strngi $field The field
		 * @return The alias and real name for field.
		 */
		function get_order( $field )
		{
			if ($this->map[$field]['sort'])
			{
				return $this->alias . '.' . $this->map[$field]['sort'];
			}
		}

		function ldebug( $myfoo, $data, $type = 'string', $err = '' )
		{
// 			if (!((($myfoo != '') xor
// 			       ($myfoo != 'default_criteria')) xor
// 			      ($myfoo == '')) xor
// 			    ($myfoo == ''))
// 			{
			return;
// 			}

			$classname = '<strong>Class: ' . get_class($this) . "<br>Function: $myfoo<br></strong>";

			switch ($type)
			{
				case 'string':
					foreach ($data as $vari => $value)
					{
						if (is_array($value))
						{
							$this->ldebug($myfoo . ' recursivecall', array('&nbsp;&nbsp;-$vari: ' => $value), 'dump');
						}
						else
						{
							$output .= "&nbsp;&nbsp;-$vari = $value <br>";
						}
					}
					break;
				case 'dump':
					foreach ($data as $vari => $value)
					{
						$output .= "&nbsp;&nbsp;-$vari = ";
						$output .= var_dump($value) . "<br>";
					}
					break;
				default:
					$output .= "<br>$data<br>";
			}
			if ($err != '')
			{
				$output = $classname . 'Error: ' . $output . '<br>';
			}
			else
			{
				$output = $classname . $output . '<br>';
			}
			echo $output;
		}
	}