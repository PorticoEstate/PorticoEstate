<?php
	/**
	* phpGroupWare - SMS: A SMS Gateway.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package sms
	* @subpackage poll
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package sms
	 */

	class sms_bopoll
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

		function __construct($session=false)
		{
			$this->so 			= CreateObject('sms.sopoll');
			$this->bocommon 	= CreateObject('sms.bocommon');

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

			$this->start = $start ? $start : 0;

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
				$GLOBALS['phpgw']->session->appsession('session_data','sms_poll',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','sms_poll');

			$this->start	= $data['start'];
			$this->query	= $data['query'];
			$this->filter	= $data['filter'];
			$this->sort	= $data['sort'];
			$this->order	= $data['order'];
			$this->cat_id	= $data['cat_id'];
		}

		function read()
		{
			$poll_info = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'allrows'=>$this->allrows));
			$this->total_records = $this->so->total_records;
			return $poll_info;
		}

		function read_single_poll($id)
		{
			$values =$this->so->read_single_poll($id);
			return $values;
		}

		function save_poll($values,$action='')
		{

			if ($action=='edit')
			{
				if ($values['poll_id'] != '')
				{

					$receipt = $this->so->edit_poll($values);
				}
				else
				{
					$receipt['error'][]=array('msg'=>lang('Error'));
				}
			}
			else
			{
				$receipt = $this->so->add_poll($values);
			}

			return $receipt;
		}

		function delete_poll($id)
		{
			$this->so->delete_poll($id);
		}


		function select_type_list($selected='')
		{
			$input_poll[0]['id'] = 'php';
			$input_poll[0]['name'] = 'php code';
			$input_poll[1]['id'] = 'shell';
			$input_poll[1]['name'] = 'poll or shell script';

			return $this->bocommon->select_list($selected,$input_poll);
		}
	}
