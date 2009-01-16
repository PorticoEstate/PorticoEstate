<?php
	/**
	* phpGroupWare - HRM: a  human resource competence management system.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package hrm
	* @subpackage admin
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package hrm
	 */

	class hrm_bocategory
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;

		var $public_functions = array
		(
			'read'				=> true,
			'read_single'		=> true,
			'save'				=> true,
			'delete'			=> true,
			'check_perms'		=> true
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

		function hrm_bocategory($session=false)
		{
		//	$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->so 		= CreateObject('hrm.socategory');
			$this->bocommon = CreateObject('hrm.bocommon');

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = true;
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
			if(isset($cat_id))
			{
				$this->cat_id = $cat_id;
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
				$GLOBALS['phpgw']->session->appsession('session_data','category',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','category');

			//_debug_array($data);

			$this->start	= $data['start'];
			$this->query	= $data['query'];
			$this->filter	= $data['filter'];
			$this->sort		= $data['sort'];
			$this->order	= $data['order'];
			$this->cat_id	= $data['cat_id'];
			$this->allrows	= $data['allrows'];
		}


		function read($type='',$type_id='')
		{
			$category = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'type' => $type,type_id=>$type_id,'allrows'=>$this->allrows));

			$this->total_records = $this->so->total_records;

			return $category;
		}

		function read_single($id,$type,$type_id)
		{
			return $this->so->read_single($id,$type,$type_id);
		}

		function select_category_list($type,$selected)
		{
			$category_list = $this->so->select_category_list($type);
			$category_list = $this->bocommon->select_list($selected,$category_list);
			return $category_list;
		}

		function save($category,$action='',$type ='',$type_id)
		{
			if ($action=='edit')
			{
				if ($category['id'] != '')
				{

					$receipt = $this->so->edit($category,$type,$type_id);
				}
			}
			else
			{
				$receipt = $this->so->add($category,$type,$type_id);
			}

			return $receipt;
		}

		function delete($id,$type,$type_id)
		{
			$this->so->delete($id,$type,$type_id);
		}
	}
