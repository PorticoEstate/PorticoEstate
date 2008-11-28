<?php
  /**************************************************************************\
  * phpGroupWare - Calendar - Custom fields and sorting                      *
  * http://www.phpgroupware.org                                              *
  * Written by Ralf Becker <RalfBecker@outdoor-training.de>                  *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

	/* $Id$ */

	class calendar_bocustom_fields
	{
		var $stock_fields = array
		(
			'title' => array
			(
				'label' => 'Title',
				'title' => True
			),
			'description' => 'Description',
			'category'    => 'Category',
			'location'    => 'Location',
			'startdate'   => 'Start Date/Time',
			'enddate'     => 'End Date/Time',
			'priority'    => 'Priority',
			'access'      => 'Access',
			'participants'=> 'Participants',
			'owner'       => 'Created By',
			'updated'     => 'Updated',
			'alarm'       => 'Alarm',
			'recure_type' => 'Repetition'
		);

		public function __construct()
		{
			$this->config = CreateObject('phpgwapi.config','calendar');
			$this->config->read();

			$this->fields = &$this->config->config_data['fields'];

			if (!is_array($this->fields)) {
				$this->fields = array();
			}

			foreach ($this->fields as $field => $data)	// this can be removed after a while
			{
				if (!isset($this->stock_fields[$field]) && $field[0] != '#')
				{
					unset($this->fields[$field]);
					$this->fields['#'.$field] = $data;
				}
			}

			foreach($this->stock_fields as $field => $data)
			{
				if (!is_array($data))
				{
					$data = array('label' => $data);
				}
				if (!isset($this->fields[$field]))
				{
					$this->fields[$field] = array(
						'name'     => $field,
						'title'    => (isset($data['title'])?$data['title']:''),
						'disabled' => (isset($data['disabled'])?$data['disabled']:'')
					);
				}
				$this->fields[$field]['label']  = (isset($data['label'])?$data['label']:'');
				$this->fields[$field]['length'] = (isset($data['length'])?$data['length']:'');
				$this->fields[$field]['shown']  = (isset($data['shown'])?$data['shown']:'');
			}
		}

		function set($data)
		{
			if (is_array($data) && strlen($data['name']) > 0)
			{
				if (!isset($this->stock_fields[$name = $data['name']]))
				{
					$name = '#'.$name;
				}
				$this->fields[$name] = $data;
			}
		}

		function save($fields=False)
		{
			if ($fields)
			{
				$this->fields = $fields;
			}
			//echo "<pre>"; print_r($this->config->config_data); echo "</pre>\n";
			$this->config->save_repository();
		}
	}
