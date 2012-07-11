<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
	* This file is part of phpGroupWare.
	*
	* phpGroupWare is free software; you can redistribute it and/or modify
	* it under the terms of the GNU General Public License as published by
	* the Free Software Foundation; either version 2 of the License, or
	* (at your option) any later version.
	*
	* phpGroupWare is distributed in the hope that it will be useful,
	* but WITHOUT ANY WARRANTY; without even the implied warranty of
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	* GNU General Public License for more details.
	*
	* You should have received a copy of the GNU General Public License
	* along with phpGroupWare; if not, write to the Free Software
	* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage project
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_botemplate
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

		function property_botemplate($session=false)
		{
			$this->so 		= CreateObject('property.sotemplate');
			$this->bocommon		= CreateObject('property.bocommon');

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}

			$start		= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query		= phpgw::get_var('query');
			$sort		= phpgw::get_var('sort');
			$order		= phpgw::get_var('order');
			$filter		= phpgw::get_var('filter', 'int');
			$cat_id		= phpgw::get_var('cat_id', 'int');
			$allrows	= phpgw::get_var('allrows', 'bool');
			$chapter_id	= phpgw::get_var('chapter_id', 'int');

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
			if(isset($filter))
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
			if(isset($chapter_id))
			{
				$this->chapter_id = $chapter_id;
			}
		}


		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','template',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','template');

			$this->start		= $data['start'];
			$this->query		= $data['query'];
			$this->filter		= $data['filter'];
			$this->sort			= $data['sort'];
			$this->order		= $data['order'];
			$this->cat_id		= $data['cat_id'];
			$this->allrows		= $data['allrows'];
			$this->chapter_id	= $data['chapter_id'];
		}


		function read()
		{
			$template = $this->so->read(array('filter' => $this->filter,'start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
				'chapter_id' => $this->chapter_id,'allrows'=>$this->allrows));
			$this->total_records = $this->so->total_records;

			$dateformat					= $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			for ($i=0; $i<count($template); $i++)
			{
				$template[$i]['owner'] = $GLOBALS['phpgw']->accounts->id2name($template[$i]['owner']);
				$template[$i]['entry_date']		= $GLOBALS['phpgw']->common->show_date($template[$i]['entry_date'],$dateformat);
			}

			return $template;
		}

		function read_template_hour($template_id)
		{
			$template = $this->so->read_template_hour(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
				'chapter_id' => $this->chapter_id,'allrows'=>$this->allrows, 'template_id'=>$template_id));
			$this->total_records = $this->so->total_records;

			$dateformat					= $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			return $template;
		}


		function read_single_template($template_id)
		{
			return $this->so->read_single_template($template_id);
		}

		function read_single_hour($hour_id)
		{
			return  $this->so->read_single_hour($hour_id);
		}

		function get_grouping_list($selected='',$template_id)
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('grouping_select'));
			$groupings= $this->so->get_grouping_list($template_id);
			return $this->bocommon->select_list($selected,$groupings);
		}

		function save_template($values)
		{
			if ($values['template_id'])
			{
				if ($values['template_id'] != 0)
				{
					$receipt = $this->so->edit_template($values);
					$receipt['template_id']=$values['template_id'];
				}
			}
			else
			{
				$receipt = $this->so->add_template($values);
			}
			return $receipt;
		}

		function save_hour($values,$template_id)
		{
			$values['billperae']	= str_replace(",",".",$values['billperae']);
			$values['quantity']		= str_replace(",",".",$values['quantity']);
			$values['cost']			= $values['billperae']*$values['quantity'];
			if($values['ns3420_descr'])
			{
				$values['descr']=$values['ns3420_descr'];
			}

			if ($values['hour_id'])
			{
				if ($values['hour_id'] != 0)
				{
					$receipt = $this->so->edit_hour($values,$template_id);
				}
			}
			else
			{
				$receipt = $this->so->add_custom_hour($values,$template_id);
			}
			return $receipt;
		}

		function delete($params)
		{
			if (is_array($params))
			{
				$this->so->delete($params[0]);
			}
			else
			{
				$this->so->delete($params);
			}
		}

		function delete_hour($hour_id,$template_id)
		{
			return $this->so->delete_hour($hour_id,$template_id);
		}

	}
