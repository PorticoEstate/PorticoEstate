<?php
	/**
	* Notes
	* @author Andy Holman
	* @author Bettina Gille [ceb@phpgroupware.org]
	* @author Dave Hall skwashd@phpgroupware.org
	* @copyright Copyright (C) 2000-2003,2005,2007 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package notes
	* @version $Id$
	*/

	/*
		This program is free software; you can redistribute it and/or modify
		it under the terms of the GNU General Public License as published by
		the Free Software Foundation; either version 3 of the License, or
		(at your option) any later version.

		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU General Public License for more details.

		You should have received a copy of the GNU General Public License
		along with this program.  If not, see <http://www.gnu.org/licenses/>.
	*/

	/**
	* Notes business object class
	*
	* @package notes
	*/
	class bonotes
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;
		var $limit;
		var $map;

		var $public_functions = array
		(
			'read'				=> True,
			'read_single'		=> True,
			'save'				=> True,
			'delete'			=> True,
			'check_perms'		=> True
		);

		var $soap_functions = array(
			'list' => array(
				'in'  => array('int','int','struct','string','int'),
				'out' => array('array')
			),
			'read' => array(
				'in'  => array('int','struct'),
				'out' => array('array')
			),
			'save' => array(
				'in'  => array('int','struct'),
				'out' => array()
			),
			'delete' => array(
				'in'  => array('int','struct'),
				'out' => array()
			)
		);

		function bonotes($is_active=False)
		{
			$this->sonotes	= CreateObject('notes.sonotes');
			$this->start	= 0;
			$this->query	= '';
			$this->sort		= 'DESC';
			$this->order	= '';
			$this->cat_id	= 0;
			$this->limit	= True;

			$this->map = array();

			if ($is_active)
			{
				$this->read_sessiondata();
				$this->use_session = True;

				//XXX Caeies : start could use the 'all' value, perhaps think on using -1 ?
				$start		= phpgw::get_var('start');
				$query		= phpgw::get_var('query', 'string');
				$sort		= phpgw::get_var('sort', 'string');
				$order		= phpgw::get_var('order', 'string');
				$filter		= phpgw::get_var('filter', 'string');
				$_cat_id	= phpgw::get_var('cat_id', 'int');

				$this->start = $start;

				if($this->start == 'all')
				{
					$this->limit = False;
					$this->start = 0;
				}

				if(isset($query))
				{
					$this->query = $query;
				}
				if(!empty($filter))
				{
					$this->filter = $filter;
				}
				if(isset($sort))
				{
					$this->sort = $sort;
				}
				if(isset($order))
				{
					$this->order = $order;
				}
				if(isset($_cat_id) && !empty($_cat_id))
				{
					$this->cat_id = $_cat_id;
				}
			}
			else
			{
				// define the map
				$this->map = array
				(
					'application/x-phpgw-notes' => array
					(
						// extern           <> intern
						'note_id'			=> 'id',
						'note_owner'		=> 'owner',
						'note_access'		=> 'access',
						'note_createdate'	=> 'date',
						'note_category'		=> 'category',
						'note_description'	=> 'content'
					),
					'text/plain' => array('content'),
					'text/xml' => array()
				);
			}
		}

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
						'read' => array(
							'function'  => 'read',
							'signature' => array(array(xmlrpcInt,xmlrpcStruct)),
							'docstring' => lang('Read a single entry by passing the id and fieldlist.')
						),
						'save' => array(
							'function'  => 'save',
							'signature' => array(array(xmlrpcStruct,xmlrpcStruct)),
							'docstring' => lang('Update a single entry by passing the fields.')
						),
						'delete' => array(
							'function'  => 'delete',
							'signature' => array(array(xmlrpcBoolean,xmlrpcInt)),
							'docstring' => lang('Delete a single entry by passing the id.')
						),
						'list' => array(
							'function'  => '_list',
							'signature' => array(array(xmlrpcStruct,xmlrpcStruct)),
							'docstring' => lang('Read a list of entries.')
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

		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','notes',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','notes');
			if(!is_array($data))
			{
				return;
			}

			//_debug_array($data);

			$this->start	= $data['start'];
			$this->query	= $data['query'];
			$this->filter	= $data['filter'];
			$this->sort		= $data['sort'];
			$this->order	= $data['order'];
			$this->cat_id	= $data['cat_id'];
		}

		function check_perms($has, $needed)
		{
			return (!!($has & $needed) == True);
		}

		function read($lastmod = -1)
		{
			$notes = $this->sonotes->read(array
			(
				'start'		=> $this->start,
				'query'		=> $this->query,
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'filter'	=> $this->filter,
				'cat_id'	=> $this->cat_id,
				'limit'		=> $this->limit,
				'lastmod'	=> $lastmod
			));
			$this->total_records = $this->sonotes->total_records;

			foreach ( $notes as &$note )
			{
				$note['owner'] = $GLOBALS['phpgw']->accounts->id2name($note['owner']);
			}

			return $notes;
		}

		function read_single($note_id)
		{
			return $this->sonotes->read_single($note_id);
		}

		function save($note)
		{
			if (isset($note['access']) && $note['access'])
			{
				$note['access'] = 'private';
			}
			else
			{
				$note['access'] = 'public';
			}

			if (isset($note['note_id']) && intval($note['note_id']) && $this->sonotes->edit($note))
			{
				$note_id = $note['note_id'];
			}
			else
			{
				$note_id = $this->sonotes->add($note);
			}
			return $note_id;
		}

		function delete($params)
		{
			if (is_array($params))
			{
				return $this->sonotes->delete($params[0]);
			}
			else
			{
				return $this->sonotes->delete($params);
			}
		}

		/**
	 	* Convert data from a certain mime type format to the internal application data structure.
	 	*
	 	* @access  public
	 	* @param   mixed    $dataExtern  data to convert, the datatype depends on the passed mime type
	 	* @param   string   $type        specifies the mime type of the passed data
	 	* @return  array                 data as application internal array
	 	*/
		function importData($dataExtern, $type)
		{
			$dataIntern = array();

			switch ($type)
			{
				case 'application/x-phpgw-notes':
					if (is_array($dataExtern) == false)
					{
						return false;
					}

					foreach($this->map[$type] as $keyExtern => $keyIntern)
					{
						if (isset($dataExtern[$keyExtern]) == true)
						{
					  		$dataIntern[$keyIntern] = $dataExtern[$keyExtern];
						}
						else
						{
							$dataIntern[$keyIntern] = null;
						}
					}
					break;
				case 'text/plain':
					if (is_string($dataExtern) == false)
					{
						return false;
					}
					$keyIntern = $this->map[$type][0];
					$dataIntern[$keyIntern] = $dataExtern;
					break;
				case 'text/xml':
					return false;
					break;
				default:
					return false;
					break;
			}
			return $dataIntern;
		}


		/**
		* Convert data from internal application data structure to a certain mime type format.
	 	*
	 	* @access  public
	 	* @param   array    $dataIntern  data as application internal array
	 	* @param   string   $type        specifies the mime type of the returned data
	 	* @return  mixed                 data in certain mime type format, the datatype depends on the passed mime type
	 	*/
		function exportData($dataIntern, $type)
		{
			if (is_array($dataIntern) == false)
			{
				return false;
			}

			$dataExtern = null;

			switch ($type)
			{
				case 'application/x-phpgw-notes':
					$dataExtern = array();
					foreach($this->map[$type] as $keyExtern => $keyIntern)
					{
						if (isset($dataIntern[$keyIntern]) == true)
						{
					  		$dataExtern[$keyExtern] = $dataIntern[$keyIntern];
						}
						else
						{
							$dataExtern[$keyExtern] = null;
						}
					}

					// extend the internal data with link informtion
					$keyExtern_note_id = $this->getKeyExtern('id', $type);
					$id = $dataExtern[$keyExtern_note_id];
					// info needed to generate a view link
					$dataExtern['link_view'] = array('menuaction'	=> 'notes.uinotes.view',
														'note_id'	=> $id);
					// info needed to generate a edit link
					$dataExtern['link_edit'] = array('menuaction'	=> 'notes.uinotes.edit',
														'note_id'	=> $id);
					break;
				case 'text/plain':
					$keyIntern = $this->map[$type][0];
					$dataExtern = $dataIntern[$keyIntern];
					break;
				case 'text/xml':
					return false;
					break;
				default:
					return false;
					break;
			}
			return $dataExtern;
		}

		function getKeyExtern($keyIntern, $type)
		{
			$keyExtern = false;
			switch ($type)
			{
				case 'application/x-phpgw-notes':
					foreach($this->map[$type] as $keyEx => $keyIn)
					{
						if ($keyIn == $keyIntern)
						{
							$keyExtern = $keyEx;
							break;
						}
					}
					break;
				case 'text/plain':
					$keyIntern = $this->map[$type][0];
					$dataExtern = $dataIntern[$keyIntern];
					break;
				case 'text/xml':
					return false;
					break;
				default:
					return false;
					break;
			}
			return $keyExtern;
		}

		function getKeyIntern($keyExtern, $type)
		{
			$keyIntern = false;
			switch ($type)
			{
				case 'application/x-phpgw-notes':
					if (isset($this->map[$type][$keyExtern]) == true)
					{
						$keyIntern = $this->map[$type][$keyExtern];
					}
					break;
				case 'text/plain':
					$keyIntern = $this->map[$type][0];
					break;
				case 'text/xml':
					return false;
					break;
				default:
					return false;
					break;
			}
			return $keyExtern;
		}
	}
?>
