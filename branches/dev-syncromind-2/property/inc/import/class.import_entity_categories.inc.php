<?php

	class import_entity_categories
	{

		protected $db;
		var $type = 'entity';
		var $array_entity_categories = array();
		protected $sql;
		protected $type_app = array
			(
			'entity' => 'property',
			'catch' => 'catch'
		);
		protected $entity_id_from_template = null;
		protected $cat_id_from_template = array();
		protected $array_cat_id = array();
		protected $template_id = null;

		public function __construct( $template_id )
		{
			$this->db = & $GLOBALS['phpgw']->db;
			$this->join = $this->db->join;
			$this->bo = CreateObject('property.boadmin_entity', true);
			$this->custom = CreateObject('property.custom_fields');
			//$this->config = createObject('phpgwapi.config', 'component_import');
			//$this->config_repository = $this->config->read_repository();

			$this->array_entity_categories = array(
				'0' => array('name' => '0 Generelt'),
				'01' => array('name' => '01 Informasjon og hjelp'),
				'02' => array('name' => '02 Krav til dokumentasjon'),
				'1' => array('name' => '1 Brannsikring'),
				'11' => array('name' => '11 Branntekniske krav'),
				'12' => array('name' => '12 Tegninger(.pdf)/o-planer'),
				'13' => array('name' => '13 Brannteknisk dokumentasjon')
			);

			if ($template_id)
			{
				$this->template_id = $template_id;
				$template = explode("_", $template_id);

				$this->entity_id_from_template = $template[0];
				$this->cat_id_from_template = $template[1];

				$this->array_cat_id[1] = '309';
				$this->array_cat_id[2] = '310';
				$this->array_cat_id[3] = $template[1];
				$this->array_cat_id[4] = $template[1];
			}
		}

		private function _get_cat_id_by_building_part( $building_part )
		{
			return $this->array_cat_id[strlen($building_part)];
		}

		private function _search_parent_category( &$new_categories, $list_entity_categories, $building_part )
		{
			$receipt = array();

			for ($x = 1; $x <= (strlen($building_part) - 1); $x++)
			{
				$parents[] = substr($building_part, 0, $x);
			}

			foreach ($parents as $item)
			{
				if (array_key_exists($item, $new_categories))
				{
					continue;
				}

				if (array_key_exists($item, $list_entity_categories))
				{
					continue;
				}

				$category = $this->array_entity_categories[$item];
				if (empty($category['name']))
				{
					$receipt['error'][] = array('msg' => lang('Building Part ' . $item . ' not define'));
					break;
				}

				$new_categories[$item] = $category['name'];
			}

			return $receipt;
		}

		public function prepare_entity_categories( $building_part_out_table )
		{
			$new_categories = array();
			$receipt = array();

			$list_entity_categories = $this->list_entity_categories();

			foreach ($building_part_out_table as $building_part => $name)
			{
				if (strlen($building_part) > 1)
				{
					$receipt = $this->_search_parent_category($new_categories, $list_entity_categories, $building_part);
					if ($receipt['error'])
					{
						break;
					}
				}

				$new_categories[$building_part] = $name;
			}

			if ($receipt['error'])
			{
				return $receipt;
			}

			$receipt['new_entity_categories'] = $new_categories;

			return $receipt;
		}

		public function add_entity_categories()
		{
			$receipt = array();

			//$new_categories = $this->config_repository['new_entity_categories'];
			$new_categories = phpgwapi_cache::session_get('property', 'new_entity_categories');
			$new_categories = ($new_categories) ? unserialize($new_categories) : array();

			if (!count($new_categories))
			{
				$receipt['message'][] = array('msg' => lang('Not exist new categories to insert'));
				return $receipt;
			}

			$categories = array();
			$parent_id = NULL;

			foreach ($new_categories as $building_part => $name)
			{
				if (strlen($building_part) == 1)
				{
					$parent_id = NULL;
				}
				else
				{
					$building_part_parent = substr($building_part, 0, (strlen($building_part) - 1));
					$values = $this->list_entity_categories(array('building_part' => $building_part_parent));
					$parent_id = $values[$building_part_parent]['id'];
					if (!$parent_id)
					{
						$categories['not_added'][$building_part] = array('name' => $name);
						break;
					}
				}

				$category_id = $this->_save_category($name, $parent_id, $building_part);

				if ($category_id)
				{
					$categories['added'][$building_part] = array('id' => $category_id, 'entity_id' => $this->entity_id_from_template,
						'name' => $name);
				}
				else
				{
					$categories['not_added'][$building_part] = array('name' => $name);
				}
			}

			return $categories;
		}

		private function _save_category( $name, $parent_id, $building_part )
		{
			$cat_id = $this->_get_cat_id_by_building_part($building_part);
			$entity_id = $this->entity_id_from_template;
			$values = array();

			$attrib_list = $this->bo->read_attrib(array('entity_id' => $entity_id, 'cat_id' => $cat_id,
				'allrows' => true));
			foreach ($attrib_list as $attrib)
			{
				$values['template_attrib'][] = $attrib['id'];
			}
			$values['category_template'] = $entity_id . '_' . $cat_id;
			$values['parent_id'] = $parent_id;
			$values['name'] = $name;
			$values['descr'] = $name;
			$values['entity_id'] = $entity_id;
			$values['fileupload'] = 1;
			$values['loc_link'] = 1;
			$values['is_eav'] = 1;

			$receipt = $this->bo->save_category($values);

			return $receipt['id'];
		}

		public function list_entity_categories( $data = array() )
		{
			$querymethod = '';
			if ($data['parent_id'])
			{
				$querymethod .= " AND parent_id = " . $data['parent_id'];
			}
			if ($data['building_part'])
			{
				$querymethod .= " AND name LIKE '" . $data['building_part'] . " %'";
			}

			$sql = "SELECT * FROM fm_entity_category WHERE entity_id = {$this->entity_id_from_template} {$querymethod}";
			$this->db->query($sql, __LINE__, __FILE__);

			while ($this->db->next_record())
			{
				$name_arr = explode(' ', trim($this->db->f('name')));
				$building_part = $name_arr[0];

				$values[$building_part] = array
					(
					'id' => $this->db->f('id'),
					'name' => $this->db->f('name'),
					'building_part' => $building_part,
					'location_id' => $this->db->f('location_id'),
					'parent_id' => $this->db->f('parent_id'),
					'entity_id' => $this->db->f('entity_id')
				);
			}

			return $values;
		}

		public function add_attributes_to_categories()
		{
			$receipt = array();

			//$building_part_in_table = $this->config_repository['building_part_in_table'];
			$building_part_in_table = phpgwapi_cache::session_get('property', 'building_part_in_table');
			$building_part_in_table = ($building_part_in_table) ? unserialize($building_part_in_table) : array();

			if (!count($building_part_in_table))
			{
				$receipt['message'][] = array('msg' => lang('Not exist new categories to insert'));
				return $receipt;
			}

			$count = 0;
			foreach ($building_part_in_table as $k => $v)
			{
				$values2 = array
					(
					'entity_id' => $v['entity_id'],
					'cat_id' => $v['cat_id'],
					'category_template' => $this->template_id,
					'selected' => ''
				);

				$result = $this->_add_attrib_from_template($values2);
				if (empty($result))
				{
					continue;
				}

				if ($result['error'])
				{
					foreach ($result['error'] as $error)
					{
						$receipt['error'][] = array('msg' => $error['msg'] . '. Building Part: ' . $k);
					}
				}
				else
				{
					$count++;
				}
			}

			if ($count)
			{
				$receipt['message'][] = array('msg' => lang('attributes has been added to %1 entity categories', $count));
			}

			return $receipt;
		}

		private function _add_attrib_from_template( $values )
		{
			$receipt = array();

			$template_info = explode('_', $values['category_template']);
			$template_entity_id = $template_info[0];
			$template_cat_id = $template_info[1];

			$attrib_group_list = $this->bo->read_attrib_group(array('entity_id' => $template_entity_id,
				'cat_id' => $template_cat_id, 'allrows' => true));

			foreach ($attrib_group_list as $attrib_group)
			{
				$group = array
					(
					'appname' => $this->type_app[$this->type],
					'location' => ".{$this->type}.{$values['entity_id']}.{$values['cat_id']}",
					'group_name' => $attrib_group['name'],
					'descr' => $attrib_group['descr'],
					'remark' => $attrib_group['remark']
				);
				$this->custom->add_group($group);
			}

			$attrib_list = $this->bo->read_attrib(array('entity_id' => $values['entity_id'],
				'cat_id' => $values['cat_id'], 'allrows' => true));
			$column_names = array();
			foreach ($attrib_list as $attrib)
			{
				$column_names[] = $attrib['column_name'];
			}

			$attrib_list_template = $this->bo->read_attrib(array('entity_id' => $template_entity_id,
				'cat_id' => $template_cat_id, 'allrows' => true));
			$template_attribs = array();
			foreach ($attrib_list_template as $attrib)
			{
				if (!in_array($attrib['column_name'], $column_names, true))
				{
					$template_attribs[] = $this->bo->read_single_attrib($template_entity_id, $template_cat_id, $attrib['id']);
				}
			}

			if (!count($template_attribs))
			{
				return array();
			}

			foreach ($template_attribs as $attrib)
			{
				$attrib['appname'] = $this->type_app[$this->type];
				$attrib['location'] = ".{$this->type}.{$values['entity_id']}.{$values['cat_id']}";

				$choices = array();
				if (isset($attrib['choice']) && $attrib['choice'])
				{
					$choices = $attrib['choice'];
					unset($attrib['choice']);
				}

				$id = $this->custom->add($attrib);
				if ($choices)
				{
					foreach ($choices as $choice)
					{
						$attrib['new_choice'] = $choice['value'];
						$attrib['id'] = $id;
						$this->custom->edit($attrib);
					}
				}

				if (!$id)
				{
					$receipt['error'][] = array('msg' => lang('Unable to add attribute %1 ', $attrib['column_name']));
				}
			}

			return $receipt;
		}

		public function prepare_attributes_for_template( &$columns, $attrib_names, $attrib_data_types, $attrib_precision )
		{
			$receipt = array();

			$entity_id = $this->entity_id_from_template;
			$cat_id = $this->cat_id_from_template;

			$template_attrib_list = $this->bo->read_attrib(array('entity_id' => $entity_id,
				'cat_id' => $cat_id, 'allrows' => true));
			$current_attrib_names = array();
			foreach ($template_attrib_list as $attrib)
			{
				$current_attrib_names[] = $attrib['column_name'];
			}

			$appname = $this->type_app[$this->type];
			$location = ".{$this->type}.{$entity_id}.{$cat_id}";

			$new_attributes = array();

			foreach ($columns as $_row_key => $_value_key)
			{
				$attrib = array();
				if ($_value_key == 'new_column')
				{
					$attrib['entity_id'] = $entity_id;
					$attrib['cat_id'] = $cat_id;
					$attrib['appname'] = $appname;
					$attrib['location'] = $location;

					$attrib['column_name'] = mb_strtolower($attrib_names[$_row_key], 'UTF-8');
					$attrib['input_text'] = ucfirst($attrib_names[$_row_key]);
					$attrib['statustext'] = ucfirst($attrib_names[$_row_key]);
					$attrib['column_info']['type'] = $attrib_data_types[$_row_key];
					$attrib['column_info']['precision'] = $attrib_precision[$_row_key];
					$attrib['column_info']['nullable'] = 'True';
					$attrib['search'] = 1;

					$receipt = $this->_valid_attributes($attrib);
					if ($receipt['error'])
					{
						break;
					}

					if (in_array($attrib['column_name'], $current_attrib_names, true))
					{
						$receipt['error'][] = array('msg' => lang('Attribute name %1 already exists, please choose another name', $attrib['column_name']));
						break;
					}

					$columns[$_row_key] = $attrib['column_name'];
					$new_attributes[] = $attrib;
				}
			}

			if ($receipt['error'])
			{
				return $receipt;
			}

			$receipt['new_attribs_for_template'] = $new_attributes;

			return $receipt;
		}

		public function add_attributes_to_template()
		{
			$receipt = array();

			//$attributes = $this->config_repository['new_attribs_for_template'];
			$attributes = phpgwapi_cache::session_get('property', 'new_attribs_for_template');
			$attributes = ($attributes) ? unserialize($attributes) : array();

			if (!count($attributes))
			{
				$receipt['message'][] = array('msg' => lang('Not exist attributes to insert the template'));
				return $receipt;
			}

			foreach ($attributes as $attrib)
			{
				$id = $this->custom->add($attrib);
				if ($id <= 0)
				{
					$receipt['error'][] = array('msg' => lang('Unable to add attribute %1 ', $attrib['column_name']));
					break;
				}
				else if ($id == -1)
				{
					$receipt['error'][] = array('msg' => lang('attribute %1 already exists, please choose another name', $attrib['column_name']));
					$receipt['error'][] = array('msg' => lang('Attribute %1 has NOT been saved', $attrib['column_name']));
					break;
				}
			}

			if ($receipt['error'])
			{
				return $receipt;
			}

			$receipt['message'][] = array('msg' => lang('%1 attributes has been added to template', count($attributes)));

			return $receipt;
		}

		private function _valid_attributes( $values )
		{
			$receipt = array();

			if (!$values['column_name'])
			{
				$receipt['error'][] = array('msg' => lang('Attribute name not entered!'));
			}

			if (!preg_match('/^[a-z0-9_]+$/i', $values['column_name']))
			{
				$receipt['error'][] = array('msg' => lang('Attribute name %1 contains illegal character', $values['column_name']));
			}

			if (!$values['input_text'])
			{
				$receipt['error'][] = array('msg' => lang('Input text not entered!'));
			}

			if (!$values['statustext'])
			{
				$receipt['error'][] = array('msg' => lang('Statustext not entered!'));
			}

			if (!$values['entity_id'])
			{
				$receipt['error'][] = array('msg' => lang('entity type not chosen!'));
			}

			if (!$values['column_info']['type'])
			{
				$receipt['error'][] = array('msg' => lang('Datatype type not chosen!'));
			}

			if (!ctype_digit($values['column_info']['precision']) && $values['column_info']['precision'])
			{
				$receipt['error'][] = array('msg' => lang('Please enter precision as integer !'));
			}

			if (!$values['column_info']['nullable'])
			{
				$receipt['error'][] = array('msg' => lang('Nullable not chosen!'));
			}

			return $receipt;
		}
	}