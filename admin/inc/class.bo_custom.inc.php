<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage admin
 	* @version $Id: class.bo_custom.inc.php,v 1.6 2006/11/19 20:47:45 sigurdne Exp $
	*/

	/**
	 * Description
	 * @package property
	 */

	class bo_custom
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

		function bo_custom($session=False)
		{
			$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->so 		= CreateObject('phpgwapi.custom_fields');

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
			if(isset($location))
			{
				$this->location = $location;
			}
			if(isset($appname))
			{
				$this->appname = $appname;
			}
			if(isset($allrows))
			{
				$this->allrows = $allrows;
			}
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
		//	$this->filter	= $data['filter'];
			$this->sort		= (isset($data['sort'])?$data['sort']:'');
			$this->order	= (isset($data['order'])?$data['order']:'');
			$this->location	= (isset($data['location'])?$data['location']:'');
			$this->appname	= (isset($data['appname'])?$data['appname']:'');
			$this->allrows	= (isset($data['allrows'])?$data['allrows']:'');
		}

		function delete($location='',$appname='',$attrib_id='',$custom_function_id='')
		{
			if($attrib_id && $location && $appname && !$custom_function_id):
			{
				$this->so->_delete_attrib($location,$appname,$attrib_id);
			}
			elseif($custom_function_id && $appname && $location):
			{
				$this->so->_delete_custom_function($appname,$location,$custom_function_id);
			}
			endif;
		}

		function get_attribs($appname='',$location='',$allrows='')
		{
			if($allrows)
			{
				$this->allrows = $allrows;
			}

			//$attrib = $this->so->get_attribs(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
			//								'location' => $location,'appname' => $appname,'allrows'=>$this->allrows));


		 	$attrib = $this->so->get_attribs($appname, $location, $this->start, $this->query, $this->sort, $this->order, $this->allrows, $inc_choices = false);

			for ($i=0; $i<count($attrib); $i++)
			{
				$attrib[$i]['datatype'] = $this->so->translate_datatype($attrib[$i]['datatype']);
			}

			$this->total_records = $this->so->total_records;

			return $attrib;
		}

		function get_attrib_single($appname,$location,$id)
		{
			return $this->so->get_attrib_single($appname,$location,$id);
		}

		function resort_attrib($id,$resort)
		{
			$this->so->resort_attrib($id, $resort, $this->appname, $this->location);
		}

		function save_attrib($attrib)
		{
			if (isset($attrib['id']) && $attrib['id'])
			{
					$receipt = $this->so->edit_attrib($attrib);
			}
			else
			{
				$receipt = $this->so->add_attrib($attrib);
			}
			return $receipt;
		}


		function read_custom_function($appname='',$location='',$allrows='')
		{
			if($allrows)
			{
				$this->allrows = $allrows;
			}

			$custom_function = $this->so->read_custom_function(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'appname' => $appname,'location' => $location,'allrows'=>$this->allrows));

			$this->total_records = $this->so->total_records;

			return $custom_function;
		}

		function resort_custom_function($id,$resort)
		{
			$this->so->resort_custom_function(array('resort'=>$resort,'appname' => $this->appname,'location' => $this->location,'id'=>$id));
		}

		function save_custom_function($custom_function,$action='')
		{
			if ($action=='edit')
			{
				if ($custom_function['id'] != '')
				{

					$receipt = $this->so->edit_custom_function($custom_function);
				}
			}
			else
			{
				$receipt = $this->so->add_custom_function($custom_function);
			}
			return $receipt;
		}

		function select_custom_function($selected='', $appname)
		{
			$file_list = array();
			$dir_handle = @opendir(PHPGW_SERVER_ROOT . SEP . $appname . SEP . 'inc' . SEP . 'custom');
			$i=0; $myfilearray = '';
			if ($dir_handle)
			{
				while ($file = readdir($dir_handle))
				{
					if ((substr($file, 0, 1) != '.') && is_file(PHPGW_SERVER_ROOT . SEP . $appname . SEP . 'inc' . SEP . 'custom' . SEP . $file) )
					{
						$myfilearray[$i] = $file;
						$i++;
					}
				}
				closedir($dir_handle);
				sort($myfilearray);
			}

			if(isset($myfilearray)&&is_array($myfilearray))
			{
				for ($i=0;$i<count($myfilearray);$i++)
				{
					$fname = ereg_replace('_',' ',$myfilearray[$i]);
					$sel_file = '';
					if ($myfilearray[$i]==$selected)
					{
						$sel_file = 'selected';
					}

					$file_list[] = array
					(
						'id'		=> $myfilearray[$i],
						'name'		=> $fname,
						'selected'	=> $sel_file
					);
				}
			}

			for ($i=0;$i<count($file_list);$i++)
			{
				if ($file_list[$i]['selected'] != 'selected')
				{
					unset($file_list[$i]['selected']);
				}
			}

			return $file_list;
		}

		function read_single_custom_function($appname='',$location='',$id)
		{
			return $this->so->read_single_custom_function($appname,$location,$id);
		}

		function select_datatype($selected='')
		{
			$datatypes[0]['id']= 'V';
			$datatypes[0]['name']= lang('varchar');
			$datatypes[1]['id']= 'C';
			$datatypes[1]['name']= lang('Character');
			$datatypes[2]['id']= 'I';
			$datatypes[2]['name']= lang('Integer');
			$datatypes[3]['id']= 'N';
			$datatypes[3]['name']= lang('Decimal');
			$datatypes[4]['id']= 'D';
			$datatypes[4]['name']= lang('Date');
			$datatypes[5]['id']= 'T';
			$datatypes[5]['name']= lang('Memo');
			$datatypes[6]['id']= 'R';
			$datatypes[6]['name']= lang('Multiple radio');
			$datatypes[7]['id']= 'CH';
			$datatypes[7]['name']= lang('Multiple Checkbox');
			$datatypes[8]['id']= 'LB';
			$datatypes[8]['name']= lang('ListBox');
			$datatypes[9]['id']= 'AB';
			$datatypes[9]['name']= lang('Contact');
			$datatypes[10]['id']= 'VENDOR';
			$datatypes[10]['name']= lang('Vendor');
			$datatypes[11]['id']= 'email';
			$datatypes[11]['name']= lang('Email');
			$datatypes[12]['id']= 'link';
			$datatypes[12]['name']= lang('Link');

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
	}
?>
