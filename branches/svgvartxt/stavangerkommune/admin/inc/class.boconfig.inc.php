<?php
	/**************************************************************************\
	* phpGroupWare - configuration administration                              *
	* http://www.phpgroupware.org                                              *
	* Copyright (C) 2001 Loic Dachary                                          *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	class admin_boconfig
	{
		var $public_functions = array();

		var $xml_functions = array();

		var $soap_functions = array(
			'rpc_values' => array(
				'in'  => array('struct', 'struct'),
				'out' => array()
			)
		);
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $location_id = 0;


		function list_methods($_type='xmlrpc')
		{
			/*
			  This handles introspection or discovery by the logged in client,
			  in which case the input might be an array.  The server always calls
			  this function to fill the server dispatch map using a string.
			*/
			if (is_array($_type))
			{
				$_type = $_type['type'] ? $_type['type'] : $_type[0];
			}
			switch($_type)
			{
				case 'xmlrpc':
					$xml_functions = array(
						'rpc_values' => array(
							'function'  => 'rpc_values',
							'signature' => array(array(xmlrpcStruct,xmlrpcStruct)),
							'docstring' => lang('Set preference values.')
						),
						'list_methods' => array(
							'function'  => 'list_methods',
							'signature' => array(array(xmlrpcStruct,xmlrpcString)),
							'docstring' => lang('Read this list of methods.')
						)
					);
					return $xml_functions;
					break;
				case 'soap':
					return $this->soap_functions;
					break;
				default:
					return array();
					break;
			}
		}

		// xmlrpc functions

		function rpc_values($data)
		{
			exit;

			$newsettings = $data['newsettings'];
			if (!$data['appname'])
			{
				$errors[] = "Missing appname";
			}
			if (!is_array($newsettings))
			{
				$errors[] = "Missing newsettings or not an array";
			}

			if (is_array($errors))
			{
				return $errors;
			}

			$conf = CreateObject('phpgwapi.config', $data['appname']);

			$conf->read();
			reset($newsettings);
			while(list($key,$val) = each($newsettings))
			{
				$conf->value($key, $val);
			}
			$conf->save_repository();
			return True;
		}

		public function __construct($session = false)
		{
			$this->so 			= CreateObject('admin.soconfig');

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}

			$start			= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query			= phpgw::get_var('query');
			$sort			= phpgw::get_var('sort');
			$order			= phpgw::get_var('order');
			$filter			= phpgw::get_var('filter', 'int');
			$allrows		= phpgw::get_var('allrows', 'bool');
			$location_id	= phpgw::get_var('location_id', 'int', 'REQUEST', 0);

			$this->start		= $start ? $start : 0;
			$this->location_id	= $location_id ? $location_id : 0;
			$this->so->set_location($this->location_id);

			if(array_key_exists('query',$_POST) || array_key_exists('query',$_GET))
			{
				$this->query = $query;
			}
			if(array_key_exists('filter',$_POST) || array_key_exists('filter',$_GET))
			{
				$this->filter = $filter;
			}
			if(array_key_exists('sort',$_POST) || array_key_exists('sort',$_GET))
			{
				$this->sort = $sort;
			}
			if(array_key_exists('order',$_POST) || array_key_exists('order',$_GET))
			{
				$this->order = $order;
			}
			if ($allrows)
			{
				$this->allrows = $allrows;
			}
		}


		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','admin_config',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','admin_config');

			$this->start	= isset($data['start']) && $data['start'] ? $data['start'] : 0;
			$this->query	= isset($data['query']) ? $data['query'] : '';
			$this->filter	= isset($data['filter']) ? $data['filter'] : '';
			$this->sort		= isset($data['sort']) ? $data['sort'] : '';
			$this->order	= isset($data['order']) ? $data['order'] : '';
		}

		function read_section()
		{
			$config_info = $this->so->read_section(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'allrows'=>$this->allrows));
			$this->total_records = $this->so->total_records;
			return $config_info;
		}

		function read_single_section(int $id)
		{
			$values =$this->so->read_single_section($id);
			return $values;
		}


		function save_section(array $values, $action='')
		{
			if ($action=='edit')
			{
				if ($values['section_id'] != '')
				{

					$receipt = $this->so->edit_section($values);
				}
				else
				{
					$receipt['error'][]=array('msg'=>lang('Error'));
				}
			}
			else
			{
				$receipt = $this->so->add_section($values);
			}

			return $receipt;
		}

		function delete_section(int $id)
		{
			$this->so->delete_section($id);
		}


		function read_attrib(int $section_id)
		{

			$config_info = $this->so->read_attrib(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'allrows'=>$this->allrows, 'section_id'=>$section_id));

			$this->total_records = $this->so->total_records;
			return $config_info;
		}

		function read_single_attrib(int $section_id,int $id)
		{
			$values =$this->so->read_single_attrib($section_id,$id);

			return $values;
		}


		function save_attrib(array $values,$action='')
		{
			if ($action=='edit')
			{
				if ($values['attrib_id'] != '')
				{
					$receipt = $this->so->edit_attrib($values);
				}
				else
				{
					$receipt['error'][]=array('msg'=>lang('Error'));
				}
			}
			else
			{
				$receipt = $this->so->add_attrib($values);
			}

			return $receipt;
		}

		function delete_attrib(int $section_id, int $id)
		{
			$this->so->delete_attrib($section_id,$id);
		}


		function read_value(int $section_id,int $attrib_id)
		{
			$config_info = $this->so->read_value(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'allrows'=>$this->allrows, 'section_id'=>$section_id, 'attrib_id' =>$attrib_id));
			$this->total_records = $this->so->total_records;
			return $config_info;
		}

		function read_single_value(int $section_id,int $attrib_id,int $id)
		{
			$values =$this->so->read_single_value($section_id,$attrib_id,$id);

			return $values;
		}


		function save_value(array $values,$action='')
		{
			if ($action=='edit')
			{
				if ($values['id'] != '')
				{
					$receipt = $this->so->edit_value($values);
				}
				else
				{
					$receipt['error'][]=array('msg'=>lang('Error'));
				}
			}
			else
			{
				$receipt = $this->so->add_value($values);
			}

			return $receipt;
		}

		function delete_value(int $section_id,int $attrib_id,int $id)
		{
			$this->so->delete_value($section_id,$attrib_id,$id);
		}


		function select_choice_list(int $section_id,int $attrib_id,$selected='')
		{
			$list = $this->so->select_choice_list($section_id,$attrib_id);
			return $this->select_list($selected,$list);
		}


		function select_input_type_list($selected='')
		{
			$input_type = array
			(
				array
				(
					'id' => 'text',
					'name' => 'text'
				),
				array
				(
					'id' => 'listbox',
					'name' => 'listbox'
				),
				array
				(
					'id' => 'password',
					'name' => lang('password')
				),
				array
				(
					'id' => 'date',
					'name' => lang('date')
				),
			);
			return $this->select_list($selected,$input_type);

		}

		function select_list($selected='',$input_list='')
		{
			if (isset($input_list) AND is_array($input_list))
			{
				foreach($input_list as $entry)
				{
					$sel_entry = '';
					if ($entry['id']==$selected)
					{
						$sel_entry = 'selected';
					}
					$entry_list[] = array
					(
						'id'		=> $entry['id'],
						'name'		=> $entry['name'],
						'selected'	=> $sel_entry
					);
				}
				for ($i=0;$i<count($entry_list);$i++)
				{
					if ($entry_list[$i]['selected'] != 'selected')
					{
						unset($entry_list[$i]['selected']);
					}
				}
			}
			return $entry_list;
		}
	}
