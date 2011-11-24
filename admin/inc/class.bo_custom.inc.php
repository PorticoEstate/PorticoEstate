<?php
	/**
	* phpGroupWare - admin
	*
	* @author Dave Hall <dave.hall@skwashd.com>
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage admin
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	class admin_bo_custom
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $location;
		var $appname;

		var $public_functions = array
		(
			'read'			=> True,
			'read_single'	=> True,
			'save'			=> True,
			'delete'		=> True,
			'check_perms'	=> True
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

		public function __construct($session=False)
		{
			$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->so			= createObject('phpgwapi.custom_fields');

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = True;
			}

			$start		= phpgw::get_var('start', 'int');
			$query		= phpgw::get_var('query');
			$sort		= phpgw::get_var('sort');
			$order		= phpgw::get_var('order');
			$filter		= phpgw::get_var('filter');
			$location	= phpgw::get_var('location');
			$allrows	= phpgw::get_var('allrows', 'bool');
			$appname	= phpgw::get_var('appname');

			if ($start)
			{
				$this->start=$start;
			}
			else
			{
				$this->start = 0;
			}

			$this->query = $query;
			$this->filter = $filter;
			$this->sort = $sort;
			$this->order = $order;
			$this->location = $location;
			$this->appname = $appname;
			$this->allrows = $allrows;
		}

		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','cust_attrib',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','cust_attrib');

			$this->start	= (isset($data['start'])?$data['start']:'');
			$this->query	= (isset($data['query'])?$data['query']:'');
			//$this->filter	= $data['filter'];
			$this->sort		= (isset($data['sort'])?$data['sort']:'');
			$this->order	= (isset($data['order'])?$data['order']:'');
			$this->location	= (isset($data['location'])?$data['location']:'');
			$this->appname	= (isset($data['appname'])?$data['appname']:'');
			$this->allrows	= (isset($data['allrows'])?$data['allrows']:'');
		}

		/**
		 * Delete what ever data you feel like, we can just keep on adding conditions to the if block here for you
		 *
		 * @param string $location the location
		 * @param string $appname the application name
		 * @param int $attrib_id the db pk for the attribute to delete
		 * @param int $attrib_id the db pk for the attribute to delete
		 *
		 * @return void
		 */
		public function delete($location = '', $appname = '', $attrib_id = 0, $custom_function_id = 0)
		{
			if ( !$attrib_id || !$location )
			{
				return;
			}
				
			if ( $attrib_id && !$custom_function_id )
			{
				$this->so->delete($appname, $location, $attrib_id);
			}
			else if ( $custom_function_id )
			{
				$GLOBALS['phpgw']->custom_functions->delete($appname, $location, $custom_function_id);
			}
		}

		function get_attribs($appname = '',$location = '', $allrows = null)
		{
			if ( !is_null($allrows) )
			{
				$this->allrows = $allrows;
			}

			$inc_choices = false;
		 	$attribs = $this->so->find($appname, $location, $this->start, $this->query, $this->sort, $this->order, $this->allrows, $inc_choices);

			foreach( $attribs as &$attrib )
			{
				$attrib['datatype'] = $this->so->translate_datatype($attrib['datatype']);
			}

			$this->total_records = $this->so->total_records;

			return $attribs;
		}

		function get_attrib_single($appname,$location,$id)
		{
			return $this->so->get($appname,$location,$id);
		}

		function resort_attrib($id,$resort)
		{
			$this->so->resort($id, $resort, $this->appname, $this->location);
		}

		function save_attrib($attrib)
		{
			if (isset($attrib['id']) && $attrib['id'])
			{
				if ( $this->so->edit($attrib) )
				{
					return array
					(
						'msg'	=> array('msg' => lang('Custom field has been created'))
					);
				}

				return array('error' => lang('Unable to edit custom field'));
			}
			else
			{
				$id = $this->so->add($attrib);

				if ( $id == 0  )
				{
					return array('error' => lang('Unable to add field'));
				}
				else if ( $id == -1 )
				{
					return array
					(
						'id'	=> '',
						'error'	=> array
						(
							array('msg' => lang('Table is not defined')),
							array('msg' => lang('Attribute has NOT been saved'))
						)
					);
				}
				else if ( $id == -2 )
				{
					return array
					(
						'id'	=> '',
						'error'	=> array
						(
							array('msg' => lang('field already exists, please choose another name')),
							array('msg' => lang('Attribute has NOT been saved'))
						)
					);
				}

				return array
				(
					'id'	=> $id,
					'msg'	=> array('msg' => lang('Custom field has been created'))
				);
			}
		}

		function read_custom_function($appname, $location, $allrows = false)
		{
			if ( $allrows )
			{
				$this->allrows = $allrows;
			}

			$criteria = array
			(
				'start'		=> $this->start,
				'query'		=> $this->query,
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'appname'	=> $appname,
				'location'	=> $location,
				'allrows'	=> $this->allrows
			);

			$custom_function = $GLOBALS['phpgw']->custom_functions->find($criteria);

			$this->total_records = $GLOBALS['phpgw']->custom_functions->total_records;

			return $custom_function;
		}

		/**
		 * Resort a list of custom functions
		 *
		 * @param integer $id the db key of the function
		 * @param string  $resort the direction to resort the item (up/down)
		 *
		 * @return void
		 */
		public function resort_custom_function($id, $resort)
		{
			$GLOBALS['phpgw']->custom_functions->resort($id, $resort, $this->appname, $this->location);
		}

		/**
		 * Sigurd knows what I do
		 *
		 * @param string $custom_function ????
		 * @param string $action          ????
		 */
		public function save_custom_function($custom_function, $action = '')
		{
			$cfuncs =& $GLOBALS['phpgw']->custom_functions;

			if ( $action == 'edit' )
			{
				if ( $custom_function['id'] != '' )
				{
					if ( $cfuncs->edit($custom_function) )
					{
						return array('msg' => lang('Custom function has been updated'));
					}
				}
				return array('error' => lang('Unable to edit custom function'));
			}
			else
			{
				$id = $cfuncs->add($custom_function);
				if ( $id )
				{
					return array('id' => $id);
				}
				return array('error' => lang('Unable to add custom function'));
			}
		}

		/**
		 * Create an XSLT select widget compatiable array containing custom functions
		 *
		 * @param string $selected the name of the currently selected file
		 * @param string $appname  the name of the module requesting the list
		 *
		 * @return array list of custom functions
		 */
		public static function select_custom_function($selected, $appname)
		{
			$dirname = PHPGW_SERVER_ROOT . "/{$appname}/inc/custom/{$GLOBALS['phpgw_info']['user']['domain']}";
			// prevent path traversal
			if ( preg_match('/\./', $appname) 
			 || !is_dir($dirname) )
			{
				return array();
			}

			$find = array('/_/', '/\.php$/');
			$replace = array(' ', '');

			$file_list = array();
			$dir = new DirectoryIterator(PHPGW_SERVER_ROOT . "/{$appname}/inc/custom/{$GLOBALS['phpgw_info']['user']['domain']}"); 
			if ( is_object($dir) )
			{
				foreach ( $dir as $file )
				{
					if ( $file->isDot() || !$file->isFile() || !$file->isReadable() )
					{
						continue;
					}

					$file_list[] = array
					(
						'id'		=> (string) $file,
						'name'		=> preg_replace($find, $replace, $file),
						'selected'	=> (int) ($file == $selected)
					);
				}
			}

			return $file_list;
		}

		/**
		 * Fetch a single custom function
		 *
		 * @param string  $appname  the module the function belongs to
		 * @param string  $location the location the function is used
		 * @param integer $id       the ID for the function
		 *
		 * @return array the function values - null if not found
		 */
		function read_single_custom_function($appname, $location, $id)
		{
			return $GLOBALS['phpgw']->custom_functions->get($appname, $location, $id);
		}

		function select_datatype($selected='')
		{
			foreach( $this->so->datatype_text as $key => $name)
			{
				$datatypes[] = array
				(
					'id'	=> $key,
					'name'	=> $name,
				);
			}
			return $this->select_list($selected,$datatypes);
		}

		function select_nullable($selected='')
		{
			$nullable[0]['id']= 'True';
			$nullable[0]['name']= lang('True');
			$nullable[1]['id']= 'False';
			$nullable[1]['name']= lang('False');

			return $this->select_list($selected,$nullable);
		}

		function select_list($selected='',$input_list='')
		{
			if (isset($input_list) AND is_array($input_list))
			{
				foreach($input_list as $entry)
				{
					if ($entry['id']==$selected)
					{
						$entry_list[] = array
						(
							'id'		=> $entry['id'],
							'name'		=> $entry['name'],
							'selected'	=> 'selected'
						);
					}
					else
					{
						$entry_list[] = array
						(
							'id'		=> $entry['id'],
							'name'		=> $entry['name']
						);
					}
				}
			}
			return $entry_list;
		}

		function get_attrib_group_list($app,$location, $selected)
		{
			$group_list = $this->read_attrib_group($app, $location, true);

			foreach($group_list as &$group)
			{
				if( $group['id'] == $selected )
				{
					$group['selected'] = true;
				}
			}
			//_debug_array($group_list);die();
			return $group_list;
		}

		function read_attrib_group($app, $location, $allrows = false)
		{
			if($allrows)
			{
				$this->allrows = $allrows;
			}

			$attrib = $this->so->find_group($app, $location, $this->start, $this->query, $this->sort, $this->order, $this->allrows);

			$this->total_records = $this->so->total_records;

			return $attrib;
		}


	}
