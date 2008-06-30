<?php
	/**
	* SQL Generator - helps to construct queries statements
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
	* SQL Generator - helps to construct queries statements
	*
	* This class provide common methods to create transaction sql queries.
	* @package phpgwapi
	* @subpackage database
	*/
	class sql_builder
	{
		// Map have a list of false_fields
		// false_field => ('entity to instantiate', 'distance to central table');
		var $map;
		var $limit_str;
		var $ondebug;
		var $entities;
		var $distance;
		var $path;
		var $reverse_path;
		var $all_criteria;
		var $central_entity;
		var $operation;
		var $external_select_value;
		var $external_from_value;
		var $external_criteria_value;
		var $_criteria_built = False;

		/**
		* @var string $order_string the ORDER BY component of a SQL string
		*/
		var $order_string = '';
		/*************************************************************\
		* Entity, class and general section                           *
		\*************************************************************/

		/**
		* Class contructor
		*/
		function sql_builder()
		{
		}

		/**
		* Instance a entity if this not exist
		*
		* @param string $field  The field name
		* @return string The class which was instanced.
		*/
		function get_entity($field)
		{
			$ent = $this->ent_name($field);
			$this->ldebug('get_entity', array('Field' => $field,
					  'Entity Name' => $ent));
			if( (!isset($this->entities[$ent]) || !is_object($this->entities[$ent]) )
				&& !empty($ent))
			{
				$this->ldebug('get_entity', "Try to instantiate $ent because $field", 'msg');
				$this->instance_entity($ent);
				$this->distance[$ent] = $this->get_distance($field);
			}
			return $ent;
		}

		/**
		* @param string $field The field name for search in $this->map for a class8
		* @return integer The distance of the class that have that field defined.
		*/
		function get_distance($field)
		{
			$entity = $this->map[$field][PHPGW_SQL_ENTITY_NAME];
			if(is_array($this->distance))
			{
				$distance = array_keys($this->distance);
				if(in_array($entity, $distance))
				{
					return $this->distance[$entity];
				}
			}
			return $this->map[$field][PHPGW_SQL_DISTANCE];
		}

		/**
		* Add an entity to map for building queries
		*
		* This function waits the map variable of entity
		* @param string $entity Name of entity and class that will be in the query development.
		* @param mixed $map The same $map that is set in one entity class
		* @param integer $distance To the `$entity' to central_entity
		* <code>
		* $contact = createObject('phpgwapi.contacts');
		* $my_ent = createObject('my_app.my_entity');
		* $contact->add_entity('my_entity', $my_ent->map, 20);
		* </code>
		*/
		function add_entity($entity, $map, $distance = 200)
		{
			$fields = array_keys($map);
			foreach ($fields as $field)
			{
				$this->map[$field] = array($entity, $distance);
			}
		}

		/**
		* This function set in the distance array the distance of the entity.
		*
		* caveat: need a field :/
		* @param string $field The field name for search in $this->map
		* @param string $ent The name of the entity which want set it's entitiy
		*/
		function set_distance($field, $ent)
		{
			$this->distance[$ent] = $this->map[$field][PHPGW_SQL_DISTANCE];
		}

		/**
		* Instance entity based on the information on map
		*
		* @param string $field A false field name that could be found in the map and have and entity declared.
		* @return array Form: 'entity_name' => entity_object
		*/
		function instance_entity_by_field($field)
		{
			$entity_name = $this->ent_name($field);
			$entity = createObject($entity_name);
			return array($entity_name, &$entity);
		}

		function instance_entity($entity_name)
		{
			if( isset($this->entities[$entity_name])
				&& is_object($this->entities[$entity_name]))
			{
				$this->ldebug('instance_entity',
						  "Already an object $entity_name", 'msg');
			}
			else
			{
				$this->entities[$entity_name] = createObject($entity_name);
				$this->ldebug('instance_entity',
						  "Creating object $entity_name", 'msg');
			}
			$this->ldebug('instance_entity', array('Entity name' => $entity_name));
		}

		/*************************************************************\
		* Short path section                                          *
		\*************************************************************/

		function sort_by_distances()
		{
			// Re-sort the distances of clases that will use
			if(!is_array($this->entities) || empty($this->entities))
			{
				return;
			}
			arsort($this->distance);
			$entity_distance = current($this->distance);
			$entity_far_away = key($this->distance);
			$this->ldebug('sort_by_distances', 'entering to sort_by_distance', 'msg');
			$this->add_path($entity_far_away, TRUE);
			$this->ldebug('sort_by_distances',
					  array('Distances' => $this->distance,
						'Path' => $this->path,
						'Entity Distance' => $entity_distance,
						'Entity far away' => $entity_far_away),
					  'dump');
			$this->entity_to_center($entity_far_away,
						$entity_distance, TRUE);
			$this->merge_paths();
			$this->ldebug('sort_by_distances', 'entering to while, all the add_path must be reverse from here', 'msg');
			while(!($this->last_distance()))
			{
				if (empty($this->distance))
				{
					// Array finished?
					break;
				}
				next($this->distance);
				$entity_distance = current($this->distance);
				$entity_far_away = key($this->distance);
				$this->ldebug('sort_by_distances', array('Distance' => $entity_distance,'Name' => $entity_far_away));
				// We already found his path
				if (array_key_exists($entity_far_away, $this->path))
				{
					$this->ldebug('sort_by_distances', array('Already in path' => $entity_far_away));
					continue;
				}
				// We reach to central entity, this ends the game.
				if ($entity_distance != 0)
				{
					$this->ldebug('sort_by_distances', array('Distance2' => $entity_distance,'Name' => $entity_far_away));
					$this->add_path($entity_far_away, TRUE);
					$this->entity_to_center($entity_far_away,
								$entity_distance, TRUE);
					$this->ldebug('sort_by_distances',
								  array('Path' => array_keys($this->path)), 'dump');
					$this->merge_paths();
				}
			}
		}

		/**
		* This create a path of any identity to central entity
		*
		* @param string $entiity_name The name of the entity we try to find his path.
		* @param int $entity_distance The distance of the $entity_name.
		* @param bool $reverse TRUE if we want that path found be sorted in reverse mode (that is what we want for all case except first one).
		*/
		function entity_to_center($entity_name, $entity_distance, $reverse=FALSE)
		{
			$this->ldebug('entity_to_center', array('Entity_name' => $entity_name));
			if ($this->last_distance())
			{
				return;
			}
			$links = array_merge($this->entities[$entity_name]->get_ilinks(),
						 $this->entities[$entity_name]->get_elinks());
			$this->ldebug('entity_to_center', array('Links' => $links), 'dump');
			$min_distance = $entity_distance;
			foreach ($links as $entity => $link)
			{
				$field = $link['ffield'];
				$dist = $this->get_distance($field);
				$this->ldebug('entity_to_center',
							  array('field' => $field,
									'Min distance' => $min_distance,
									'entity this iteration' => $entity,
									'Actual distance' => $dist));
				$tmp = array_keys($this->get_path());
				if ($dist < $min_distance || ($min_distance == $entity_distance && !in_array($entity, $tmp)))
				{
					//We found a good one
					$this->ldebug('entity_to_center',
							  "Actual Distance $dist < Min Distance $min_distance",
							  'msg');
					$min_distance = $dist;
					$near_entity = $this->ent_name($field);
				}
			}

			$this->instance_entity($near_entity);
			if(is_array($this->distance))
			{
				if(array_key_exists($near_entity, $this->distance))
				{
					unset($this->distance[$near_entity]);
				}
			}
			$this->add_path($near_entity, $reverse);
			if ($min_distance == 0)
			{
				$this->central_entity = $near_entity;
			}
			else
			{
				$this->entity_to_center($near_entity, $min_distance, $reverse);
			}
		}

		function last_distance()
		{
			$curr = key($this->distance);
			@$this->ldebug('last_distance', array('Before Current' => $curr), 'dump');
			next($this->distance);
			if (@key($this->distance))
			{
				$curr = key($this->distance);
				$this->ldebug('last_distance', array('Next' => $curr), 'dump');
				prev($this->distance);
				$curr = key($this->distance);
				$this->ldebug('last_distance', array('Current No last' => $curr), 'dump');
				return FALSE;
			}
			else
			{
				$curr = key($this->distance);
				$this->ldebug('last_distance', array('Next' => $curr), 'dump');
				prev($this->distance);
				$curr = key($this->distance);
				$this->ldebug('last_distance', array('Current LAST' => $curr), 'dump');
				return TRUE;
			}
		}

		function add_path($ent, $reverse = FALSE)
		{
			$this->ldebug('add_path', array('Entity'=> $ent,
							'Reverse' => $reverse));
			if ($reverse)
			{
				$this->false_path[$ent]['identity'] = $this->entities[$ent]->get_identity();
				$this->false_path[$ent]['alias'] = $this->entities[$ent]->get_alias();
			}
			else
			{
				$this->path[$ent]['identity'] = $this->entities[$ent]->get_identity();
				$this->path[$ent]['alias'] = $this->entities[$ent]->get_alias();
			}
		}

		function get_path()
		{
			return is_array($this->path) ? $this->path : array();
		}

		function merge_paths()
		{
			$this->path = $this->get_path() + array_reverse($this->false_path);
			$this->false_path = array();
		}

		/**
		* Return the SQL select correct for all the entity map.
		*
		* This is the main functionality of this class.
		* @return String with the sql created
		*/
		function get_sql()
		{
			if(!is_array($this->entities) || empty($this->entities))
			{
				return;
			}
			$this->ldebug('get_sql',
					  array('Entities' => array_keys($this->entities),
						'Path' => $this->distance),
					  'dump');
			$this->sort_by_distances();
			foreach ($this->entities as $name => $class)
			{
				list($fields, $from, $alias, $lcriteria) = $class->get_select();
				if ($fields)
				{
					$this->select_fields[] = $fields;
				}
				$fields = '';
				if ($lcriteria && !$this->_criteria_built)
				{
					$this->all_criteria = phpgwapi_sql_criteria::append_and(array($lcriteria, $this->all_criteria));
				}
			}
			$from = $this->get_join();
			$this->fields = implode(', ', $this->select_fields);
			// hooks for add external things in my queries
			$this->fields = $this->external_select($this->fields);
			$from = $this->external_from ($from);
			$this->all_criteria = $this->external_criteria($this->all_criteria);
			$sql  = 'SELECT '.$this->fields.' FROM '.$from;
			$sql .= (empty($this->all_criteria))?'':' WHERE '. $this->all_criteria;
			$this->select_fields = NULL;
			$this->all_criteria = NULL;
			$this->entities = NULL;
			$this->path = NULL;
			$this->distance = NULL;
			$this->false_path = NULL;
			$this->_criteria_built = False;

			$sql = $this->set_order($sql);
			$this->order_string = '';
			$this->ldebug('SQL', array('SQL String' => $sql));
			return $sql;
		}

		/**
		* Allow to add fields to select part
		*
		* @param string $select Internal select part
		* @return string complete SELECT field list
		* @private
		*/
		function external_select($select)
		{
			return $select.(($this->external_select_value) ? ' '.$this->external_select_value : '');
		}

		/**
		* Allow to add joins to from clausule
		*
		* @param string $from Internal joins
		* @return string complete joins
		* @private
		*/
		function external_from($from)
		{
			return $from.(($this->external_from_value) ? ' '.$this->external_from_value : '');
		}

		/**
		* Allow to add criterias to internal WHERE
		*
		* @param string|array $criteria token list or string that have the external criterias
		* @return string complete criterias
		* @private
		*/
		function external_criteria($criteria)
		{
			if(!is_array($this->external_criteria_value))
			{
				return $criteria.(($this->external_criteria_value) ? ' '.$this->external_criteria_value : '');
			}
			else
			{
				$this->external_criteria_value[1] = $criteria;
				return phpgwapi_sql_criteria::criteria($this->external_criteria_value);
			}
		}

		// This code is the base for calling insert procedure, maybe be
		// integrated in get_sql, too ambicious? maybe.
		function get_insert()
		{
			$this->insert_entities = keys($this->entities);
			while (!empty($this->insert_entities))
			{
				$this->get_dependendencies(current($this->insert_entities));
			}
		}

		function get_dependencies($entity)
		{
			// Get the classes that we depend on
			$pre_depends = $this->entities[$entity]->get_ilinks();
			$entities_depends = keys($pre_depends);
			foreach($entities_depends as $entity_depend)
			{
				if(array_key_exists($entity_depend, $this->entities))
				{
					//We depend in entity that is in array
					$this->get_dependencies($entity_depend);
					$this->set_insert_dependency($entity, $entity_depend);
				}
			}
			$this->add_insert_array($entity);
		}

		/**
		* Get the list of false fields for $entity
		*
		* @param string $entity name of entity to search fields
		* @return array list of false fields of $entity
		*/
		function get_false_fields($entity)
		{
			if (is_object($this->entities[$entity_name]))
			{
				return $this->entities[$entity_name]->get_false_fields();
			}
			else
			{
				$entity = createObject($entity);
				$fields = $entity->get_false_fields();
				return $fields;
			}
		}

		/**
		* Use $this->path for the order, and create the joins of all identities in the query
		*
		* @return all necesary for FROM clause (the joins by magic).
		*/
		function get_join()
		{
			$path = $this->get_path();
			// from +
			// table as alias (1)
			$this->ldebug('get_join',
						  array('Path' => $path),
						  'dump');
			$prev_entity = key($path);
			$prev_data = current($path);
			$from = $prev_data['identity'];
			array_shift($path);
			// repeat in all $path
			while (list($entity, $data) = each($path))
			{
				// get_link, will determine the type of JOIN,
				// local_field, foreign_field, and the right alias,
				// and identity.
				$link = $this->find_link($entity, $prev_entity,$prev_data['alias']);

				// If we found the way to know when use other kind
				// of join change next line.
				// INNER JOIN table as alias
				$this->ldebug('get_join',
							  array('entity' => $entity,
									'prev_entity' => $prev_entity,
									'link' => $link),
							  'dump');
				$from .= $link['join'].' JOIN '.$data['identity'];
				// ON alias.lfield = prev_alias.ffield
				$from .= ' ON '.$data['alias'].'.'.
					$this->real_field($entity,$link['lfield']);
				$from .= ' = '.$link['alias'].'.'.
					$this->real_field($link['prev_entity'],
							  $link['ffield']);
				// In a strange world, this variable would be useful
				// if don't unset it, I'm under drugs right now, just
				// leave the note.
				$prev_entity = $entity;
				$prev_data = $data;
			}
			return $from;
		}

		function find_link($entity, $prev_entity, $alias)
		{
			$this->ldebug('find_link', array('prev_entity' => $prev_entity, 'entity' => $entity));
			if($return_link = $this->get_link($entity, $prev_entity))
			{
				$this->ldebug('find_link', 'I get a link', 'msg');
				$return_link['prev_entity'] = $prev_entity;
				$return_link['alias'] = $alias;
			}
			elseif($return_link = $this->get_link($entity, $this->central_entity))
			{
				$this->ldebug('find_link', 'I get to center', 'msg');
				$return_link['prev_entity'] = $this->central_entity;
				$return_link['alias'] = $this->path[$this->central_entity]['alias'];
				$this->ldebug('find_link', array('return_link' => $return_link), 'dump');
			}
			else
 			{
				// Maybe here is one amazing research about the nodes
				// entities, but not for this moment.
				return;
			}
			$this->ldebug('find_link', array('return_link' => $return_link), 'dump');
			return $return_link;
		}

		function get_link($entity, $test_entity)
		{
			$this->ldebug('get_link', array('entity' => $entity, 'test_entity' => $test_entity));
			if ($entity_link = $this->entities[$entity]->get_ilink($test_entity))
			{
				$entity_link['join'] = ($entity_link['type'] == PHPGW_SQL_REQUIRED_KEY)?
					' INNER ':' LEFT ';
				$this->ldebug('get_link', "ilink natural $entity -> $test_entity", 'msg');
			}
			elseif ($entity_link = $this->entities[$test_entity]->get_ilink($entity))
			{
				$this->ldebug('get_link', array('link' => $entity_link), 'dump');
				$entity_link['join'] = ($entity_link['type'] == PHPGW_SQL_REQUIRED_KEY)?
					' INNER ':' RIGHT ';
				$tmp_field = $entity_link['ffield'];
				$entity_link['ffield'] = $entity_link['lfield'];
				$entity_link['lfield'] = $tmp_field;
				$this->ldebug('get_link', array('link' => $entity_link), 'dump');
				$this->ldebug('get_link', 'ilink reverse', 'msg');
			}
			else
			{
				return ;
			}
			return $entity_link;
		}

		/**
		* Get the real name for $field
		*
		* @param string $ent entity name
		* @param string $field false field name
		* return string with real_name
		*/
		function real_field($ent, $field)
		{
			$this->ldebug('real_field',
					  array('Entity' => $ent,
						'Field' => $field));
			return $this->entities[$ent]->real_field($field);
		}

		/**
		* Get the real name for $field based on the map
		*
		* @param string $field false field name
		*/
		function real_name($field, $operation='insert')
		{
			list($entity_name, $entity) = $this->instance_entity_by_field($field);
			$entity->set_operation($operation);
			return $entity->real_field($entity->put_real_alias($field));
		}

		/**
		* Get the real name for $field based on the map and instantiate the entity
		*
		* @param string $field false field name
		*/
		function real_field_entity($field, $operation='')
		{
			list($entity, $instance) = $this->instance_entity_by_field($field);
			$instance->set_opertation($operation);
			$this->entities[$entity] = $entity;
			return $instance->real_field($instance->put_real_alias($field));
		}

		/**
		* Cast value for datatype
		*
		* @param mixed $value Which we go to cast
		* @param string $field field name to know the datatype
		* @param boolean $permanent decide if we want to preserv the instance of the entity in $entities array
		*/
		function cast($value, $field, $permanent=True)
		{
			$entity = $this->ent_name($field);
			$instance = $this->instance_volatile($entity, $permanent);
			return $instance->cast($value, $field);
		}


		/**
		* Instance an entity for permant usage of for remove at instant
		*
		* $permanent don't have any effect if entity already exist in $entities array.
		* @param mixed $value Which we go to cast
		* @param string $field field name to know the datatype
		* @param boolean $permanent decide if we want to preserv the instance of the entity in $entities array
		*/
		function instance_volatile($entity, $permanent=True)
		{
			if(is_object($this->entities[$entity]))
			{
				$entity =& $this->entities[$entity];
				return $entity;
			}
			else
			{
				$instance = createObject($entity);
				if($permanent)
				{
					$this->entities[$entity] = $instance;
					return $this->entities[$entity];
				}
				else
				{
					return $instance;
				}
			}
		}

		/**
		* Get the class name of field
		*
		* @param string $field The field name
		* @return string with class name.
		*/
		function ent_name($field)
		{
			if(isset($this->map[$field]))
			{
				$this->ldebug('ent_name', array("Map[$field]" => $this->map[$field]), 'dump');
				return $this->map[$field][PHPGW_SQL_ENTITY_NAME];
			}
			else
			{
				$this->ldebug('ent_name', array("Map[$field]" => '__UNDEFINED__'), 'dump');
				$this->raise_error();
				$this->ldebug('ent_name', array('Class Map' => $this->map),
						  'dump', 'error');
				$this->ldebug('ent_name', array('Field' => $field),
						 'string', 'error');
				return '';
			}
		}

		/**
		* Generate a powerfull criteria based.
		*
		* Recieve $data that is an array (operand_left, operand_right, operator)
		* If operant_left is array I call... myself :), else try to get the field name.
		* If right operand is array can myself to, else, nothing.
		* Third operator IS_ a operator name that send to sql::operate();
		* @param array $data Genertaded by calls of sql class.
		* @param string $operation Is one of: insert, select, delete, update, if is diferent to select, then the result will not include the alias for table.
		* @return Criteria for any sql query.
		*/
		function builder_criteria($token_criteria, $operation='insert')
		{
			$num_elements = count($token_criteria);
			$this->_criteria_built = True;
			switch($num_elements)
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
				if(is_array($left) && $operator != 'in')
				{
					$left = $this->builder_criteria($left, $operation);
				}
				else
				{
					$entity = $this->get_entity($left);
					$this->entities[$entity]->set_operation($operation);
					$field = $left;
					$left = $this->entities[$entity]->put_real_alias($this->real_field($entity, $left));
				}
				if(is_array($right))
				{
					if ($operator != 'in')
					{
						$right = $this->builder_criteria($right, $operation);
					}
				}
				else
				{
					if(isset($field) && $field && !($operator == 'has' || $operator == 'begin_with' || $operator == 'end_with'))
					{
						$right = $this->entities[$entity]->cast($right, $field);
					}
				}
				if ($operator == 'append_and' || $operator == 'append_or')
				{
					$param = array($left, $right);
					$local_criteria = phpgwapi_sql_criteria::operate($operator,$param);
				}
				else
				{
					$local_criteria = phpgwapi_sql_criteria::operate($operator,$left,$right);
				}
				break;
			default:
				$operator = array_pop($token_criteria);
				foreach($token_criteria as $criteria)
				{
					$criterias[] = $this->builder_criteria($criteria, $operation);
				}
				$local_criteria = phpgwapi_sql_criteria::operate($operator,$criterias);
			}
			return $local_criteria;
		}

		/**
		* This is an optional criteria generator, powerfull because use sql_criteria and sql_entity
		*/
		function criteria_token($criteria, $operation='select')
		{
			$this->all_criteria = $this->builder_criteria($criteria, $operation);
		}

		/**
		* Set the criteria to correspoding class
		*
		* @param Array $data with many criterias (how could I support that)
		*/
		function criteria($data)
		{
			// $this->ldebug('criteria', array('Query' => $data), 'dump');
			if(empty($data))
			{
				$this->ldebug('criteria', 'Oh, is empty', 'msg');
				return;
			}
			if(is_array($data))
			{
				foreach ($data as $field => $filter)
				{
					$ent = $this->get_entity($field);
					// $this->instance_entity($ent);
					$this->entities[$ent]->set_operation('select');
					$this->ldebug('criteria', array('Field' => $field,
									'Filter' => $filter,
									'Ent name' => $ent));
					$this->entities[$ent]->add_criteria($field, $filter);
				}
			}
			elseif(is_string($data))
			{
				$this->ldebug('criteria', 'Query a string is', 'msg');
				list($field, $filter) = split ('=', str_replace(' ', '', $data));
				$ent = $this->get_entity($field);
				$this->entities[$ent]->add_criteria($field, $filter);
			}
			else
			{
				// This means we have problems
				$this->ldebug('criteria', array('Data' => $data), 'dump');
				return;
			}
		}

		/**
		* Get the criteria for all instanced class
		*
		* @return the criteria for each entity.
		*/
		function get_criteria()
		{
			foreach($this->entities as $entity)
			{
				$entity->get_criteria();
			}
		}

		/**
		* Allow set an alias for an entity
		*
		* @param string $entity The name of the entity that set alias
		* @param string $alias The alias to set
		*/
		function set_alias($entity, $alias)
		{
			if(is_object($this->entities[$entity]))
			{
				$this->entities[$entity]->set_alias($alias);
			}
		}

		// FIXME: needed?
		function set_orders($fields)
		{
			if(is_array($fields))
			{
				foreach($fields as $field)
				{
					$this->set_order($field);
				}
			}
		}

		/**
		* Set field name to use in ORDER BY
		*
		* @param string $sql the string wiht thee sql that we want to limit
		* @return sql
		*/
		function set_order($sql)
		{
			return (!empty($this->order_string))? $sql.$this->order_string : $sql;
		}

		/**
		* Get the order list in string
		*
		* @return the order list.
		*/
		function get_order()
		{
			return implode(',', $this->order);
		}

		/**
		* Call to set_order to set order by field
		*
		* @param Array $field The data's list that you wan sort (based on false fields.
		*/
		function order($fields = array(), $type = 'ASC')
		{
			if ( count($fields) )
			{
				foreach($fields as $field)
				{
					$order_fields = implode(',', $fields);
				}
				$this->order_string .= " ORDER BY {$order_fields} {$type}";
			}
		}

		function inserts($datas)
		{
			foreach($datas as $data => $value)
			{
				$this->insert($data, $value);
			}
		}

		/**
		* Find the class an call it, giving to her the field name
		*
		* @param array|string $data The $data that is requested
		* @param array|string $value The $value that is requested
		*/
		function insert($data, $value = '')
		{
			$this->operation = 'insert';
			if(is_string($data))
			{
				$fields = explode(',', $data);
			}
			elseif(is_array($data))
			{
				$fields = &$data;
			}

			if(is_string($value))
			{
				$values = explode(',',$value);
			}
			elseif(is_array($value))
			{
				$values = &$value;
			}
			if ((count($values) != count($fields)) && $value != '')
			{
				//What do you think, that I'm a genious?
				$this->raise_error();
				return;
			}

			if ($value != '')
			{
				// Waiting for array_combine in php5
				$all_data = array();
				foreach($fields as $field_)
				{
					$t[$field_] = current($values);
					$all_data = $all_data + $t;
					next($values);
				}
			}
			else
			{
				$all_data = &$data;
			}

			foreach($all_data as $field_ => $value_)
			{
				$this->ldebug('insert',
						  array('Data' => $field_));
				$this->_insert($field_, $value_);
			}
		}

		function _insert($data, $value)
		{
			$ent = $this->get_entity($data);
			$this->entities[$ent]->add_insert($data, $value);
		}

		function many_inserts($multiple_data = array())
		{
			if(empty($multiple_data))
			{
				$this->raise_error();
				return;
			}
			foreach($multiple_data as $key => $insert_array)
			{
				$this->insert_indexed($key, $insert_array);
			}
		}

		function insert_indexed($key, $data)
		{
			foreach($data as $field => $data)
			{
				$ent = $this->get_entity($field);
				$this->entities[$ent]->add_insert_index($field, $data, $key);
			}
		}

		/**
		* Run the insert method of all instanced class
		*
		* @return The insert for each entity
		*/
		function run_insert()
		{
			foreach($this->entities as $entity)
			{
				array_push($this->sql_inserts, $entity->insert());
			}
			return $this->sql_inserts;
		}

		function updates($datas)
		{
			foreach($datas as $data => $value)
			{
				$this->update($data, $value);
			}
		}

		/**
		* Find the class an call it, giving to her the field name
		*
		* @param string $data The $data that is requested. ie. false field
		* @param string|array $value The $value that is requested
		*/
		function update($data, $value)
		{
			$ent = $this->get_entity($data);
			$this->entities[$ent]->add_update($data, $value);
		}

		/**
		* Run the update method of all instanced class
		*
		* @return The update for each entity
		*/
		function run_update()
		{
			foreach($this->entities as $entity)
			{
				array_push($this->sql_updates, $entity->update());
			}
			return $this->sql_updates;
		}

		/**
		* Delete a record according to criteria.
		*
		* For this delete we can only support one criteria, this is because coding the function that get the
		* right fields names and parse a tree of operations is complex (in O(n^y) terms), so it will do this less
		* scalable. I just decided dropped this feature for delete operations.
		* @param Array $criteria Form: (Field, value) or string with `field = value'
		* @param integer $action PHPGW_SQL_RETURN_SQL | PHPGW_SQL_RUN_SQL
		* @return string SQL string
		*/
		function _delete($criteria, $action=PHPGW_SQL_RETURN_SQL)
		{
			if(is_string($criteria))
			{
				//Hope that app-developer know what sends
				list($field,$value) = explode(',',$criteria);
				if(empty($field) || empty($value))
				{
					$GLOBALS['phpgw']->log->error(array('text'=>'E-ArgError, err: Wrong argument $criteria' .$criteria,
									'p1'=>'This is a grave Error',
									'file'=>__FILE__,
									'line'=>__LINE__));
				}
			}
			else
			{
				$field = array_shift($criteria);
				$value = array_shift($criteria);
			}
			$entity = $this->instance_volatile($this->ent_name($field), False);
			$real_name = $entity->real_field($field);
			$criteria = $entity->index_criteria(array('real_field' => $real_name, 'value' => $value, 'field' => $field));
			return $entity->delete($criteria, $action);
		}

		/**
		* Run the delete method of all instanced class
		*
		* @return The delete for each entity
		*/
		function run_delete()
		{
			foreach($this->entities as $entity)
			{
				array_push($this->sql_deletes, $entity->delete());
			}
			return $this->sql_deletes;
		}

		function request($data)
		{
			//FIMXE:
			$this->operation = 'select';
			if(is_string($data))
			{
				$fields = explode(',', $data);
			}
			elseif(is_array($data))
			{
				$fields = &$data;
			}
			foreach($fields as $field)
			{
				$this->ldebug('request', array('Data' => $field));
				$this->_request($field);
			}
		}

		/**
		* Find the class an call it, giving to her the field name
		*
		* @param string $data The $data that is requested
		*/
		function _request($data)
		{
			/*
			* This is the NEW parent/child fields
			* implementation, I hope this solve my
			* problems, but this don't remove that exist a
			* design problem with the n-n relations and
			* 1-1.
			*/
			if ( isset($this->map[$data][PHPGW_SQL_CHILD_FIELDS])
				&& is_array($this->map[$data][PHPGW_SQL_CHILD_FIELDS])
				&& count($this->map[$data][PHPGW_SQL_CHILD_FIELDS]) > 0 )
			{
				foreach($this->map[$data][PHPGW_SQL_CHILD_FIELDS] as $child)
				{
					$this->_request($child);
				}
			}

			$ent = $this->get_entity($data);
			$this->ldebug('_request', array('Data' => $data,
							   'Entity name' => $ent));
			if ( isset($this->entities[$ent])
				&& is_object($this->entities[$ent]) )
			{
				$this->entities[$ent]->add_select($data);
			}
			else
			{
				$this->raise_error($data);
			}
			if( isset($this->map[$data][PHPGW_SQL_CHANGE_DISTANCE])
				&& (int) $this->map[$data][PHPGW_SQL_CHANGE_DISTANCE] != 0)
			{
				$this->distance[$ent] = $this->map[$data][PHPGW_SQL_CHANGE_DISTANCE];
			}
		}

		/**
		* Add external select fields to actual list
		*
		* The string is added just before the SELECT clause be created.
		* @param string $select actual string with select part.
		*/
		function append_select($select)
		{
			$this->external_select_value = $select;
		}

		/**
		* Add external JOINS to actual FROM part
		*
		* String is added just before FROM clause be created.
		* @param string $from actual string with select part.
		*/
		function append_from($from)
		{
			$this->external_from_value = $from;
		}

		/**
		* Add external criterias to actual query
		*
		* Run just before WHERE clause be created.
		* @param Array $criteria format: (token, string) where token is a conjunction class method
		* @see or__
		* @see and__
		*/
		function append_criteria($criteria)
		{
			$this->external_criteria_value = $criteria;
		}

		/**
		* Allow add a link on the fly, usefull when you need add a link to a entity that is not part of your application db.
		*
		* @param string $local_entity this is the name of the entity on application db that external entity want to join.
		* @param string $local_field false field of $local_entity to make the join
		* @param string $external_entity name of entity that want to add to map for one query
		* @param string $external_field false field name from $enternal_entity that will be used for join
		* @param integer $key_type PHPGW_SQL_LAZY_KEY or PHPGW_SQL_REQUIRED_KEY.
		* @see set_ilinks
		* @see set_elinks
		*/
		function add_link($local_entity, $local_field, $external_entity, $external_field, $key_type=PHPGW_SQL_LAZY_KEY)
		{
			$local_entity  = $this->get_entity($local_field);
			$this->entities[$local_entity]->set_elinks($local_field, $external_entity, $external_field);
			$external_entity  = $this->get_entity($external_field);
			$this->entities[$external_entity]->set_ilinks($external_field, $local_entity, $local_field, $key_type);
		}

		/**
		* Must raise errors for this class.
		*
		* @param $data From where error happens
		*/
		function raise_error($data = '')
		{
		}

		function abort($data = '')
		{
			$GLOBALS['phpgw']->common->phpgw_exit();
		}

		function ldebug($myfoo, $data, $type = 'string', $err = '')
		{
// 			if (!((($myfoo != '') xor
// 			       ($myfoo != '')) xor
// 			      ($myfoo == '')) xor
// 			    ($myfoo == 'get_sql'))
//   			if ($myfoo != 'SQL')
// 			{
  			return;
// 			}

			$classname = '<strong>Class: '.get_class($this).
				"\n<br>Function: $myfoo\n<br></strong>";
			switch($type)
			{
			case 'string':
				foreach($data as $vari => $value)
				{
					if (is_array($value))
					{
						$this->ldebug($myfoo.' recursivecall',
								  array('&nbsp;&nbsp;-$vari: ' => $value), 'dump');
					}
					else
					{
						$output .= "&nbsp;&nbsp;-$vari = $value \n<br>";
					}
				}
				break;
			case 'dump':
				foreach($data as $vari => $value)
				{
					$output .= "&nbsp;&nbsp;-$vari = ";
					$output .= var_dump($value)."\n<br>";
					//$output .= var_export($value, True)."\n<br>";
				}
				break;
			default:
				$output .= "\n<br>$data\n<br>";
			}
			if ($err != '')
			{
				$output = $classname.'Error: '.$output."\n<br>";
			}
			else
			{
				$output = $classname.$output."\n<br>";
			}
			echo $output;
		}
	}
