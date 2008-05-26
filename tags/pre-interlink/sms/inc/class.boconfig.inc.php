<?php
	/**
	* phpGroupWare - SMS: A SMS Gateway.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package sms
	* @subpackage config
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package sms
	 */

	class sms_boconfig
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;

		var $public_functions = array
		(
			'read'			=> True,
			'read_single'		=> True,
			'save'			=> True,
			'delete'		=> True,
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

		function sms_boconfig($session=False)
		{
		//	$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->so 		= CreateObject('sms.soconfig');
			$this->bocommon 	= CreateObject('sms.bocommon');

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = True;
			}

			$start	= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query	= phpgw::get_var('query');
			$sort	= phpgw::get_var('sort');
			$order	= phpgw::get_var('order');
			$filter	= phpgw::get_var('filter', 'int');
			$cat_id	= phpgw::get_var('cat_id', 'int');
			$allrows= phpgw::get_var('allrows', 'bool');

			if ($start)
			{
				$this->start=$start;
			}
			else
			{
				$this->start=0;
			}

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
			if(array_key_exists('cat_id',$_POST) || array_key_exists('cat_id',$_GET))
			{
				$this->cat_id = $cat_id;
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
				$GLOBALS['phpgw']->session->appsession('session_data','sms_config',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','sms_config');

			$this->start	= $data['start'];
			$this->query	= $data['query'];
			$this->filter	= $data['filter'];
			$this->sort	= $data['sort'];
			$this->order	= $data['order'];
			$this->cat_id	= $data['cat_id'];
		}


		function read_type()
		{
			$config_info = $this->so->read_type(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'allrows'=>$this->allrows));
			$this->total_records = $this->so->total_records;
			return $config_info;
		}

		function read_single_type($id)
		{
			$values =$this->so->read_single_type($id);
			return $values;
		}


		function save_type($values,$action='')
		{

			if ($action=='edit')
			{
				if ($values['type_id'] != '')
				{

					$receipt = $this->so->edit_type($values);
				}
				else
				{
					$receipt['error'][]=array('msg'=>lang('Error'));
				}
			}
			else
			{
				$receipt = $this->so->add_type($values);
			}

			return $receipt;
		}

		function delete_type($id)
		{
			$this->so->delete_type($id);
		}


		function read_attrib($type_id)
		{
			$config_info = $this->so->read_attrib(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'allrows'=>$this->allrows, 'type_id'=>$type_id));
			$this->total_records = $this->so->total_records;
			return $config_info;
		}

		function read_single_attrib($type_id,$id)
		{
			$values =$this->so->read_single_attrib($type_id,$id);

			return $values;
		}


		function save_attrib($values,$action='')
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

		function delete_attrib($type_id,$id)
		{
			$this->so->delete_attrib($type_id,$id);
		}


		function read_value($type_id,$attrib_id)
		{
			$config_info = $this->so->read_value(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'allrows'=>$this->allrows, 'type_id'=>$type_id, 'attrib_id' =>$attrib_id));
			$this->total_records = $this->so->total_records;
			return $config_info;
		}

		function read_single_value($type_id,$attrib_id,$id)
		{
			$values =$this->so->read_single_value($type_id,$attrib_id,$id);

			return $values;
		}


		function save_value($values,$action='')
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

		function delete_value($type_id,$attrib_id,$id)
		{
			$this->so->delete_value($type_id,$attrib_id,$id);
		}


		function select_choice_list($type_id,$attrib_id,$selected='')
		{
			$list = $this->so->select_choice_list($type_id,$attrib_id);
			return $this->bocommon->select_list($selected,$list);
		}


		function select_input_type_list($selected='')
		{
			$input_type[0]['id'] = 'text';
			$input_type[0]['name'] = 'text';
			$input_type[1]['id'] = 'listbox';
			$input_type[1]['name'] = 'listbox';

			return $this->bocommon->select_list($selected,$input_type);

		}

		function select_category_list($format='',$selected='')
		{

			switch($format)
			{
				case 'select':
					$GLOBALS['phpgw']->xslttpl->add_file(array('cat_select'));
					break;
				case 'filter':
					$GLOBALS['phpgw']->xslttpl->add_file(array('cat_filter'));
					break;
			}

			$categories= $this->so->select_category_list();

			while (is_array($categories) && list(,$category) = each($categories))
			{
				$sel_category = '';
				if ($category['id']==$selected)
				{
					$sel_category = 'selected';
				}

				$category_list[] = array
				(
					'cat_id'	=> $category['id'],
					'name'		=> $category['name'],
					'selected'	=> $sel_category
				);
			}

			for ($i=0;$i<count($category_list);$i++)
			{
				if ($category_list[$i]['selected'] != 'selected')
				{
					unset($category_list[$i]['selected']);
				}
			}

			return $category_list;
		}


/*		function select_conf_list($selected='')
		{
			$places= $this->so->select_place_list();
			$place_list = $this->bocommon->select_list($selected,$places);
			return $place_list;
		}
*/
	}
