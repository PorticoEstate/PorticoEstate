<?php
/**************************************************************************\
 * phpGroupWare - Addressbook                                               *
 * http://www.phpgroupware.org                                              *
 * Written by Joseph Engo <jengo@phpgroupware.org> and                      *
 * Miles Lott <miloschphpgroupware.org>                                     *
 * -----------------------------------------------                          *
 *  This program is free software; you can redistribute it and/or modify it *
 *  under the terms of the GNU General Public License as published by the   *
 *  Free Software Foundation; either version 2 of the License, or (at your  *
 *  option) any later version.                                              *
 \**************************************************************************/

/* $Id$ */
	
	phpgw::import_class('phpgwapi.uicommon');

	class uifields extends phpgwapi_uicommon
	{
		var $public_functions = array
		(
			'index'  => true,
			'save'   => true,
			'edit'   => true,
			'delete' => true
		);
		
		private $receipt = array();

		function __construct()
		{
			parent::__construct();
			
			$this->template	= &$GLOBALS['phpgw']->template;
			$this->config = createObject('phpgwapi.config','addressbook');
			self::set_active_menu("admin::{$this->currentapp}::custom_fields");
		}

		function index()
		{
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}
		
			$tabs = array();
			$tabs['tab_field'] = array('label' => lang('Custom fields'), 'link' => '#tab_field');

			$tabletools[] = array
				(
				'my_name' => 'delete',
				'text' => lang('delete'),
				'type' => 'custom',
				'custom_code' => "
					var oArgs = " . json_encode(array(
					'menuaction' => "{$this->currentapp}.uifields.delete",
					'phpgw_return_as' => 'json'
				)) . ";
					var parameters = " . json_encode(array('parameter' => array(array('name' => 'name',
							'source' => 'name')))) . ";
					deleteField(oArgs, parameters);
				"
			);

			$tabletools[] = array
				(
				'my_name' => 'edit',
				'text' => lang('edit'),
				'type' => 'custom',
				'custom_code' => "
					var oArgs = " . json_encode(array(
					'menuaction' => "{$this->currentapp}.uifields.edit",
					'phpgw_return_as' => 'json'
				)) . ";
					var parameters = " . json_encode(array('parameter' => array(array('name' => 'name',
							'source' => 'name')))) . ";
					editField(oArgs, parameters);
				"
			);
			
			$datatable_def[] = array
				(
				'container' => 'datatable-container_0',
				'requestUrl' => json_encode(self::link(array('menuaction' => "{$this->currentapp}.uifields.index", 
						'phpgw_return_as' => 'json'))),
				'data' => json_encode(array()),
				'ColumnDefs' => array(
					array('key' => 'id', 'label' => lang('id'), 'className' => '', 'sortable' => false, 'hidden' => true),
					array('key' => 'name', 'label' => lang('name'), 'className' => '', 'sortable' => false, 'hidden' => true),
					array('key' => 'title', 'label' => lang('Field name'), 'className' => '', 'sortable' => false, 'hidden' => false),
					array('key' => 'apply', 'label' => lang('Apply for'), 'className' => '', 'sortable' => false, 'hidden' => false)
				),
				'tabletools' => $tabletools,
				'config' => array(
					array('disableFilter' => true,
						'singleSelect' => true,
						'allrows' => false)
				)
			);
				
			$data = array(
				'datatable_def' => $datatable_def,
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, 0),
				'confirm_msg' => lang('do you really want to delete this entry'),
				'lang_field_name' => lang('Please enter a field name'),
				'value_active_tab' => 0
			);
			
			self::add_javascript('addressbook', 'portico', 'fields.js');
			self::render_template_xsl(array('fields', 'datatable_inline'), array('index' => $data));			
		}

		public function query($relaxe_acl = false)
		{
			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');			
			$draw = phpgw::get_var('draw', 'int');
			$apply = phpgw::get_var('apply_for');
			$start = phpgw::get_var('start', 'int', 'REQUEST', 0);
			$num_rows = phpgw::get_var('length', 'int', 'REQUEST', 10);

			$values = $this->read_custom_fields($search['value'], $apply);

			if ($num_rows == -1)
			{
				$out = $values;
			}
			else
			{
				$page = ceil(( $start / $num_rows));
				$files_part = array_chunk($values, $num_rows);
				$out = $files_part[$page];
			}
			
			switch($order[0]['dir'])
			{
				case 'DESC';
					krsort($out);
					break;
				case 'ASC':
				default:
					ksort($out);
			}
			
			$result_data = array('results' => $out);
			$result_data['total_records'] = count($values);
			$result_data['draw'] = $draw;

			return $this->jquery_results($result_data);
		}
		
		private function _populate( $data = array() )
		{
			$values['name'] = phpgw::get_var('name');
			$values['field_name'] = phpgw::get_var('field_name');
			$values['apply_for'] = phpgw::get_var('apply_for');

			if (!$values['field_name'])
			{
				$this->receipt['error'][] = array('msg' => lang('Please enter a field name !'));
			}
			
			if (!$values['name'])
			{
				$fields = $this->read_custom_fields($values['field_name']);
				if ( isset($fields[0]['name']) )
				{
					$this->receipt['error'][] = array('msg' => lang('That field name has been used already !'));
				}
			}
			
			return $values;
		}
		
		public function save($ajax = false)
		{
			$values = $this->_populate();

			if ($this->receipt['error'])
			{
				return $this->receipt;
			}
			
			$field      = $values['name'];
			$field_name = $values['field_name'];
			$apply_for  = $values['apply_for'];
			
			try
			{
				if ($field)
				{						
					$this->save_custom_field($field, $field_name, $apply_for);
				}
				else
				{ 
					$this->save_custom_field('', $field_name, $apply_for);
				}					
			}
			catch (Exception $e)
			{
				if ($e)
				{
					$this->receipt['error'][] = array('msg' => $e->getMessage());				
				}
			}
			
			$this->receipt['message'][] = array('msg' => lang('Filed name has been saved'));
			
			return $this->receipt;
		}
		
		function delete()
		{
			$fields = phpgw::get_var('name');

			foreach($fields as $field)
			{
				$this->save_custom_field($field, '', '');
			}
			
			return true;
		}
		
		function edit()
		{
			$name = phpgw::get_var('name');
			
			$record = $this->read_custom_fields($name[0]);
		
			return array('name'=>$record[0]['name'], 'title'=>$record[0]['title'], 'apply'=>$record[0]['apply']);
		}

		function read_custom_fields($query='', $apply='both')
		{
			$i = 0;
			$this->config->read();

			$all_custom_fields = array();
			if($apply=='person')
			{
				$this->per_custom_fields = isset($this->config->config_data['custom_fields']) ? $this->config->config_data['custom_fields'] : array();
				$all_custom_fields = isset($this->config->config_data['custom_fields']) ? $this->config->config_data['custom_fields'] : array();
			}
			elseif($apply=='org')
			{
				$this->org_custom_fields = isset($this->config->config_data['custom_org_fields']) ? $this->config->config_data['custom_org_fields'] : array();
				$all_custom_fields = isset($this->config->config_data['custom_org_fields']) ? $this->config->config_data['custom_org_fields'] : array();
			}
			else
			{
				$this->per_custom_fields = isset($this->config->config_data['custom_fields']) ? $this->config->config_data['custom_fields'] : array();
				$this->org_custom_fields = isset($this->config->config_data['custom_org_fields']) ? $this->config->config_data['custom_org_fields'] : array();

				if($this->per_custom_fields!='' && $this->org_custom_fields!='')
				{
					$all_custom_fields = array_merge($this->per_custom_fields,$this->org_custom_fields);
				}
				elseif($this->per_custom_fields!='')
				{
					$all_custom_fields = $this->per_custom_fields;
				}
				elseif($this->org_custom_fields!='')
				{
					$all_custom_fields = $this->org_custom_fields;
				}
			}

			$values = array();
			
			if ( is_array($all_custom_fields) && count($all_custom_fields) )
			{
				foreach ( $all_custom_fields as $name => $descr )
				{
					$test = strtolower($name);
					//if($query && !strstr($test,strtolower($query)))
					if( !$query || ($query == $test))
					{
						$values[$i]['name'] = $name;
						$values[$i]['title'] = $descr;
						$values[$i]['id'] = $i;
						$values[$i]['apply'] = $this->get_apply($name);
						$i++;
					}
				}
			}
			
			return $values;
		}

		function get_apply($key)
		{
			if(isset($this->per_custom_fields) && (is_array($this->per_custom_fields) && isset($this->org_custom_fields) && is_array($this->org_custom_fields)) && 
					array_key_exists($key, $this->per_custom_fields) && array_key_exists($key, $this->org_custom_fields))
			{
				return 'both';
			}
			elseif(isset($this->per_custom_fields) && is_array($this->per_custom_fields) && array_key_exists($key, $this->per_custom_fields))
			{
				return 'person';
			}
			elseif(isset($this->org_custom_fields) && is_array($this->org_custom_fields) && array_key_exists($key, $this->org_custom_fields))
			{
				return 'org';
			}
		}

		function save_custom_field($old='',$new='',$apply_for='')
		{
			$edit_contacts = False;
			$this->config->read();

			switch($apply_for)
			{
				case 'person':
					if(!is_array($this->config->config_data['custom_fields']))
					{
						$this->config->config_data['custom_fields'] = array();
					}

					if($old)
					{
						$edit_contacts = True;
						$old_field = $this->config->config_data['custom_fields'][$old];
						if(!$old_field)
						{
							$old_field = $this->config->config_data['custom_org_fields'][$old];
						}
						unset($this->config->config_data['custom_fields'][$old]);
						unset($this->config->config_data['custom_org_fields'][$old]);
					}
					if($new)
					{
						$tmp = strtolower(preg_replace('/ /','_',$new));
						$this->config->config_data['custom_fields'][$tmp] = $new;
					}
					break;
				case 'org':
					if(!is_array($this->config->config_data['custom_org_fields']))
					{
						$this->config->config_data['custom_org_fields'] = array();
					}

					if($old)
					{
						$edit_contacts = True;
						$old_field = $this->config->config_data['custom_fields'][$old];
						if(!$old_field)
						{
							$old_field = $this->config->config_data['custom_org_fields'][$old];
						}
						unset($this->config->config_data['custom_org_fields'][$old]);
						unset($this->config->config_data['custom_fields'][$old]);
					}
					if($new)
					{
						$tmp = strtolower(preg_replace('/ /','_',$new));
						$this->config->config_data['custom_org_fields'][$tmp] = $new;
					}
					break;
				default:
					$old_field = '';
					if ( isset($this->config->config_data['custom_fields'][$old]) )
					{
						$old_field = $this->config->config_data['custom_org_fields'][$old];
					}

					if(!is_array($this->config->config_data['custom_fields']))
					{
						$this->config->config_data['custom_fields'] = array();
					}

					if($old)
					{
						$edit_contacts = True;
						unset($this->config->config_data['custom_fields'][$old]);
					}
					if($new)
					{
						$tmp = strtolower(preg_replace('/ /','_',$new));
						$this->config->config_data['custom_fields'][$tmp] = $new;
					}
					if(!is_array($this->config->config_data['custom_org_fields']))
					{
						$this->config->config_data['custom_org_fields'] = array();
					}

					if($old)
					{
						$edit_contacts = True;
						unset($this->config->config_data['custom_org_fields'][$old]);
					}
					if($new)
					{
						$tmp = strtolower(preg_replace('/ /','_',$new));
						$this->config->config_data['custom_org_fields'][$tmp] = $new;
					}
					break;
			}

			if(count($this->config->config_data['custom_fields']) == 0)
			{
				$this->config->config_data['custom_fields'] = '';
			}

			if(count($this->config->config_data['custom_org_fields']) == 0)
			{
				$this->config->config_data['custom_org_fields'] = '';
			}

			$this->config->save_repository();

			if($edit_contacts)
			{
				$owner = isset($GLOBALS['phpgw_info']['server']['addressmaster']) ? $GLOBALS['phpgw_info']['server']['addressmaster'] : 0;
				$contacts = createObject('phpgwapi.contacts');
				$contacts->edit_other_by_owner($owner, $new, $old_field, 'other_name');
			}
		}
	}
