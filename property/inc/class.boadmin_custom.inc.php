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
	* @subpackage admin
 	* @version $Id: class.boadmin_custom.inc.php,v 1.4 2007/01/26 14:53:46 sigurdne Exp $
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_boadmin_custom
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;

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

		function property_boadmin_custom($session=False)
		{
			$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->so 		= CreateObject('property.soadmin_custom');
			$this->bocommon = CreateObject('property.bocommon');

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
			$allrows	= phpgw::get_var('allrows', 'bool');

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
				$GLOBALS['phpgw']->session->appsession('session_data','standard_e',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','standard_e');


			$this->start	= $data['start'];
			$this->query	= $data['query'];
			$this->filter	= $data['filter'];
			$this->sort	= $data['sort'];
			$this->order	= $data['order'];
			$this->cat_id	= $data['cat_id'];
			$this->allrows	= $data['allrows'];
		}


		function read($allrows='')
		{
			if($allrows)
			{
				$this->allrows = $allrows;
			}

			$custom_function = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'acl_location' => $this->cat_id,'allrows'=>$this->allrows));

			$this->total_records = $this->so->total_records;

			return $custom_function;
		}

		function resort_custom_function($id,$resort)
		{
			$this->so->resort_custom_function(array('resort'=>$resort,'acl_location' => $this->cat_id,'id'=>$id));
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

		function select_custom_function($selected='')
		{

			$dir_handle = @opendir(PHPGW_APP_INC . SEP . 'custom');
			$i=0; $myfilearray = '';
			while ($file = readdir($dir_handle))
			{
				if ((substr($file, 0, 1) != '.') && is_file(PHPGW_APP_INC . SEP . 'custom' . SEP . $file) )
				{
					$myfilearray[$i] = $file;
					$i++;
				}
			}
			closedir($dir_handle);
			sort($myfilearray);

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

			for ($i=0;$i<count($file_list);$i++)
			{
				if ($file_list[$i]['selected'] != 'selected')
				{
					unset($conv_list[$i]['selected']);
				}
			}

			return $file_list;
		}

		function read_single_custom_function($id)
		{
			return $this->so->read_single_custom_function($id,$this->cat_id);
		}

		function delete($id)
		{
			$this->so->delete_custom_function($id,$this->cat_id);
		}
	}
?>
