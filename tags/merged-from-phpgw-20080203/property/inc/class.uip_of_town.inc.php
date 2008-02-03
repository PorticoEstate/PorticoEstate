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
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_uip_of_town
	{
		var $grants;
		var $district_id;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;

		var $public_functions = array
		(
			'index'  => True,
			'view'   => True,
			'edit'   => True,
			'delete' => True
		);

		function property_uip_of_town()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'admin::property::location::town';
		//	$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->nextmatchs	= CreateObject('phpgwapi.nextmatchs');
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];

			$this->bo		= CreateObject('property.bop_of_town',True);
			$this->bocommon		= CreateObject('property.bocommon');

			$this->acl 		= CreateObject('phpgwapi.acl');
			$this->acl_location	= '.admin';
			$this->acl_read 	= $this->acl->check($this->acl_location,1);
			$this->acl_add 		= $this->acl->check($this->acl_location,2);
			$this->acl_edit 	= $this->acl->check($this->acl_location,4);
			$this->acl_delete 	= $this->acl->check($this->acl_location,8);
			$this->acl_manage 	= $this->acl->check($this->acl_location,16);

			$this->start		= $this->bo->start;
			$this->query		= $this->bo->query;
			$this->sort		= $this->bo->sort;
			$this->order		= $this->bo->order;
			$this->filter		= $this->bo->filter;
			$this->district_id	= $this->bo->district_id;
			$this->allrows		= $this->bo->allrows;
		}

		function save_sessiondata()
		{
			$data = array
			(
				'start'			=> $this->start,
				'query'			=> $this->query,
				'sort'			=> $this->sort,
				'order'			=> $this->order,
				'filter'		=> $this->filter,
				'district_id'		=> $this->district_id,
				'this->allrows'	=> $this->allrows
			);
			$this->bo->save_sessiondata($data);
		}

		function index()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('p_of_town',
										'receipt',
										'search_field',
										'nextmatchs'));

			$receipt = $GLOBALS['phpgw']->session->appsession('session_data','p_of_town_receipt');
			$GLOBALS['phpgw']->session->appsession('session_data','p_of_town_receipt','');

			$p_of_towns_list = $this->bo->read();

			if (isSet($p_of_towns_list) AND is_array($p_of_towns_list))
			{
				foreach($p_of_towns_list as $p_of_town)
				{
					$content[] = array
					(
						'part_of_town_id'		=> $p_of_town['part_of_town_id'],
						'name'				=> $p_of_town['name'],
						'category'			=> $p_of_town['category'],
						'link_view'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uip_of_town.view', 'part_of_town_id'=> $p_of_town['part_of_town_id'])),
						'link_edit'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uip_of_town.edit', 'part_of_town_id'=> $p_of_town['part_of_town_id'])),
						'link_delete'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uip_of_town.delete', 'part_of_town_id'=> $p_of_town['part_of_town_id'])),
						'lang_view_statustext'		=> lang('view the part of town'),
						'lang_edit_statustext'		=> lang('edit the part of town'),
						'lang_delete_statustext'	=> lang('delete the part of town'),
						'text_view'			=> lang('view'),
						'text_edit'			=> lang('edit'),
						'text_delete'			=> lang('delete')
					);
				}
			}

			$table_header = array
			(
				'lang_name'		=> lang('name'),
				'lang_time_created'	=> lang('time created'),
				'lang_view'		=> lang('view'),
				'lang_edit'		=> lang('edit'),
				'lang_delete'		=> lang('delete'),
				'lang_part_of_town_id'	=> lang('Part of town id'),
				'sort_name'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'name',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uip_of_town.index',
																	'district_id'	=> $this->district_id,
																	'query'		=> $this->query,
																	'allrows'	=> $this->allrows)
										)),
				'sort_part_of_town_id'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'part_of_town_id',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uip_of_town.index',
																	'district_id'	=> $this->district_id,
																	'query'		=> $this->query,
																	'allrows'	=> $this->allrows)
										)),
				'sort_category'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'descr',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uip_of_town.index',
																	'district_id'	=> $this->district_id,
																	'query'		=> $this->query,
																	'allrows'	=> $this->allrows)
										)),
				'lang_category'		=> lang('category')
			);

			$table_add = array
			(
				'lang_add'		=> lang('add'),
				'lang_add_statustext'	=> lang('add a part of town'),
				'add_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uip_of_town.edit'))
			);

			$link_data = array
			(
				'menuaction'	=> 'property.uip_of_town.index',
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'district_id'	=> $this->district_id,
				'filter'	=> $this->filter,
				'query'		=> $this->query
			);

			if(!$this->allrows)
			{
				$record_limit	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$record_limit	= $this->bo->total_records;
			}

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
 				'allow_allrows'					=> true,
				'allrows'					=> $this->allrows,
				'start_record'					=> $this->start,
				'record_limit'					=> $record_limit,
				'num_records'					=> count($p_of_towns_list),
 				'all_records'					=> $this->bo->total_records,
				'link_url'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'					=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'select_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),

				'district_list'					=> $this->bocommon->select_district_list('filter',$this->district_id),
				'lang_no_district'				=> lang('no district'),
				'lang_district_statustext'			=> lang('Select the district the selection belongs to. To do not use a district select NO DISTRICT'),
				'select_district_name'				=> 'district_id',

				'lang_searchfield_statustext'			=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'			=> lang('Submit the search string'),
				'query'						=> $this->query,
				'lang_search'					=> lang('search'),
				'table_header'					=> $table_header,
				'values'					=> $content,
				'table_add'					=> $table_add
			);
			$this->save_sessiondata();
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('part of town') . ': ' . lang('list part of town');

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list' => $data));
		}


		function edit()
		{
			if(!$this->acl_add)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 2, 'acl_location'=> $this->acl_location));
			}

			$part_of_town_id	= phpgw::get_var('part_of_town_id', 'int');
			$values			= phpgw::get_var('values');

			if($GLOBALS['phpgw']->is_repost())
			{
//				$receipt['error'][]=array('msg'=>lang('Repost !'));
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('p_of_town'));

			if ($values['save'] || $values['apply'])
			{
				if(!$values['district_id'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a district !'));
				}

				if(!$values['name'])
				{
					$receipt['error'][]=array('msg'=>lang('Please enter a name !'));
				}

				if(!$receipt['error'])
				{
					$values['part_of_town_id']	= $part_of_town_id;
					$receipt = $this->bo->save($values);
					$part_of_town_id = $receipt['part_of_town_id'];
					$this->district_id = ($values['district_id']?$values['district_id']:$this->district_id);

					if ($values['save'])
					{
						$GLOBALS['phpgw']->session->appsession('session_data','p_of_town_receipt',$receipt);
						$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uip_of_town.index'));
					}
				}
			}

			if ($values['cancel'])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uip_of_town.index'));
			}


			if ($part_of_town_id)
			{
				$values = $this->bo->read_single($part_of_town_id);
				$this->district_id = ($values['district_id']?$values['district_id']:$this->district_id);
			}

			$link_data = array
			(
				'menuaction'		=> 'property.uip_of_town.edit',
				'part_of_town_id'	=> $part_of_town_id
			);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'abook_data'					=> $abook_data,
				'edit_url'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_part_of_town_id'				=> lang('ID'),
				'value_part_of_town_id'				=> $part_of_town_id,
				'lang_name'					=> lang('name'),
				'lang_district'					=> lang('District'),
				'lang_save'					=> lang('save'),
				'lang_cancel'					=> lang('cancel'),
				'lang_apply'					=> lang('apply'),
				'value_name'					=> $values['name'],
				'lang_name_statustext'				=> lang('Enter a name for this part of town'),
				'lang_apply_statustext'				=> lang('Apply the values'),
				'lang_cancel_statustext'			=> lang('Leave the part of town untouched and return back to the list'),
				'lang_save_statustext'				=> lang('Save the part of town and return back to the list'),
				'lang_no_district'				=> lang('no district'),
				'lang_district_statustext'			=> lang('Select the district the part of town belongs to.'),
				'select_district_name'				=> 'values[district_id]',
				'district_list'					=> $this->bocommon->select_district_list('select',$this->district_id)
			);
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('part of town') . ': ' . ($part_of_town_id?lang('edit part og town'):lang('add part of town'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
		}


		function delete()
		{
			if(!$this->acl_delete)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 8, 'acl_location'=> $this->acl_location));
			}

			$part_of_town_id	= phpgw::get_var('part_of_town_id', 'int');

			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
			(
				'menuaction' => 'property.uip_of_town.index'
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete($part_of_town_id);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'		=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uip_of_town.delete', 'part_of_town_id'=> $part_of_town_id)),
				'lang_confirm_msg'	=> lang('do you really want to delete this entry'),
				'lang_yes'		=> lang('yes'),
				'lang_yes_statustext'	=> lang('Delete the entry'),
				'lang_no_statustext'	=> lang('Back to the list'),
				'lang_no'		=> lang('no')
			);

			$appname	= lang('part of town');
			$function_msg	= lang('delete part of town');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function view()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$part_of_town_id	= phpgw::get_var('part_of_town_id', 'int', 'GET');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('part of town') . ': ' . lang('view part of town');

			$GLOBALS['phpgw']->xslttpl->add_file('p_of_town');

			$p_of_town = $this->bo->read_single($part_of_town_id);
			$data = array
			(
				'done_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uip_of_town.index')),
				'lang_id'		=> lang('ID'),
				'lang_name'		=> lang('name'),
				'lang_district'		=> lang('District'),
				'lang_done'		=> lang('done'),
				'value_id'		=> $p_of_town['id'],
				'value_name'		=> $p_of_town['name'],
				'value_district'	=> $this->bo->read_district_name($p_of_town['district_id']),
				'value_date'		=> $GLOBALS['phpgw']->common->show_date($p_of_town['entry_date'])
			);

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view' => $data));
		}
	}
?>
