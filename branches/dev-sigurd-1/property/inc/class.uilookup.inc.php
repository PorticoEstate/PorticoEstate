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
	* @subpackage core
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_uilookup
	{
		var $grants;
		var $cat_id;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;
		var $part_of_town_id;
		var $district_id;
		var $sub;
		var $currentapp;

		var $public_functions = array
		(
			'addressbook'	=> True,
			'vendor'		=> True,
			'b_account'		=> True,
			'location'		=> True,
			'entity'		=> True,
			'ns3420'		=> True,
			'street'		=> True,
			'tenant'		=> True,
			'phpgw_user'	=> True
		);

		function property_uilookup()
		{
			$GLOBALS['phpgw_info']['flags']['noframework'] = True;
			$GLOBALS['phpgw_info']['flags']['headonly']=true;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;
		//	$this->currentapp			= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->nextmatchs			= CreateObject('phpgwapi.nextmatchs');
			$this->bo					= CreateObject('property.bolookup',True);

			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->filter				= $this->bo->filter;
			$this->cat_id				= $this->bo->cat_id;
			$this->part_of_town_id		= $this->bo->part_of_town_id;
			$this->district_id			= $this->bo->district_id;
			$GLOBALS['phpgw']->js->set_onload('document.search.query.focus();');
		}

		function save_sessiondata()
		{
			$data = array
			(
				'start'				=> $this->start,
				'query'				=> $this->query,
				'sort'				=> $this->sort,
				'order'				=> $this->order,
				'filter'			=> $this->filter,
				'cat_id'			=> $this->cat_id,
				'part_of_town_id'	=> $this->part_of_town_id,
				'district_id'		=> $this->district_id
			);
			$this->bo->save_sessiondata($data);
		}

		function addressbook()
		{

			$this->cats		= CreateObject('phpgwapi.categories');
			$this->cats->app_name = 'addressbook';

			$GLOBALS['phpgw']->xslttpl->add_file(array('lookup',
										'nextmatchs',
										'search_field',
										'cat_filter'));

			$second_display = phpgw::get_var('second_display', 'bool');
			$column = phpgw::get_var('column');


			$default_category = $GLOBALS['phpgw_info']['user']['preferences']['addressbook']['default_category'];

			if ($default_category && !$second_display)
			{
				$this->bo->cat_id	= $default_category;
				$this->cat_id		= $default_category;
			}

			$addressbook_list = $this->bo->read_addressbook();

			while (is_array($addressbook_list) && list(,$addressbook_entry) = each($addressbook_list))
			{
				$content[] = array
				(
					'id'				=> $addressbook_entry['contact_id'],
					'contact_name'		=> $addressbook_entry['per_last_name'] . ', ' . $addressbook_entry['per_first_name'],
					'lang_select'		=> lang('Select'),
					'lang_select_statustext' => lang('Select this contact')
				);
			}

			$table_header[] = array
			(
				'sort_name'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=>	'last_name',
											'order'	=>	$this->order,
											'extra'		=> array('menuaction'	=> 'property.uilookup.addressbook',
																	'cat_id'	=> $this->cat_id,
																	'column'	=> $column)
										)),
				'lang_name'		=> lang('Name'),
				'sort_id'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=>	'person_id',
											'order'	=>	$this->order,
											'extra'		=> array('menuaction'	=> 'property.uilookup.addressbook',
																	'cat_id'	=>$this->cat_id,
																	'column'	=> $column)
										)),
				'lang_id'		=> lang('ID'),
				'lang_select'		=> lang('Select')
			);

			$table_done[] = array
			(
				'lang_done'				=> lang('Done'),
				'lang_done_statustext'	=> lang('Close this window')
			);

			$link_select = array
			(
				'menuaction'		=> 'property.uilookup.addressbook',
				'second_display'	=> true,
				'order'				=> $this->order,
				'sort'				=> $this->sort,
				'cat_id'			=> $this->cat_id,
				'query'				=> $this->query,
				'filter'			=> $this->filter,
				'column'			=> $column
			);

			if($column)
			{
				$contact_id	=$column;
				$contact_name	=$column . '_name';
			}
			else
			{
				$contact_id	='contact_id';
				$contact_name	='contact_name';
			}

			$cat_data	= $this->cats->formatted_xslt_list(array('selected' => $this->cat_id,'globals' => True, 'link_data' =>$link_select));
			$data = array
			(
				'allow_allrows'					=> false,
				'start_record'					=> $this->start,
				'record_limit'					=> $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'],
				'num_records'					=> count($addressbook_list),
				'all_records'					=> $this->bo->total_records,
				'link_url'						=> $GLOBALS['phpgw']->link('/index.php',$link_select),
				'img_path'						=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_no_cat'					=> lang('no category'),
				'lang_cat_statustext'			=> lang('Select the category the building belongs to. To do not use a category select NO CATEGORY'),
				'select_name'					=> 'cat_id',
				'cat_list'						=> $cat_data['cat_list'],
				'select_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_select),
				'filter_list'					=> $this->nextmatchs->xslt_filter(array('filter' => $this->filter,'yours' => 'yes')),
				'lang_filter_statustext'		=> lang('Select the filter. To show all entries select SHOW ALL'),
				'lang_searchfield_statustext'	=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'	=> lang('Submit the search string'),
				'query'							=> $this->query,
				'lang_search'					=> lang('search'),
				'table_header_contact'			=> $table_header,
				'values_contact'				=> $content,
				'table_done'					=> $table_done,
				'contact_id'					=> $contact_id,
				'contact_name'					=> $contact_name
			);

			$appname						= lang('addressbook');
			$function_msg					= lang('list vendors');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_contact' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
			$this->save_sessiondata();
		}

		function vendor()
		{

			$this->cats		= CreateObject('phpgwapi.categories');
			$this->cats->app_name = 'fm_vendor';

			$GLOBALS['phpgw']->xslttpl->add_file(array('lookup',
										'nextmatchs',
										'search_field',
										'cat_filter'));

			$second_display = phpgw::get_var('second_display', 'bool');
			$column = phpgw::get_var('column');


			$default_category = $GLOBALS['phpgw_info']['user']['preferences']['property']['default_vendor_category'];

			if ($default_category && !$second_display)
			{
				$this->bo->cat_id	= $default_category;
				$this->cat_id		= $default_category;
			}

			$vendor_list = $this->bo->read_vendor();

			while (is_array($vendor_list) && list(,$vendor_entry) = each($vendor_list))
			{
				$content[] = array
				(
					'id'				=> $vendor_entry['id'],
					'vendor_name'		=> $vendor_entry['org_name'],
					'lang_select'		=> lang('Select'),
					'lang_select_statustext' => lang('Select this vendor')
				);
			}

			$table_header[] = array
			(
				'sort_name'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=>	'org_name',
											'order'	=>	$this->order,
											'extra'		=> array('menuaction'	=> 'property.uilookup.vendor',
																	'cat_id'	=> $this->cat_id,
																	'column'	=> $column)
										)),
				'lang_name'		=> lang('Name'),
				'sort_id'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=>	'id',
											'order'	=>	$this->order,
											'extra'		=> array('menuaction'	=> 'property.uilookup.vendor',
																	'cat_id'	=>$this->cat_id,
																	'column'	=> $column)
										)),
				'lang_id'		=> lang('ID'),
				'lang_select'		=> lang('Select')
			);

			$table_done[] = array
			(
				'lang_done'				=> lang('Done'),
				'lang_done_statustext'	=> lang('Close this window')
			);

			$link_select = array
			(
				'menuaction'		=> 'property.uilookup.vendor',
				'second_display'	=> true,
				'order'				=> $this->order,
				'sort'				=> $this->sort,
				'cat_id'			=> $this->cat_id,
				'query'				=> $this->query,
				'filter'			=> $this->filter,
				'column'			=> $column
			);

			if($column)
			{
				$contact_id	=$column;
				$org_name	=$column . '_org_name';
			}
			else
			{
				$contact_id	='vendor_id';
				$org_name	='vendor_name';
			}

			$cat_data	= $this->cats->formatted_xslt_list(array('selected' => $this->cat_id,'globals' => True, 'link_data' =>$link_select));
			$data = array
			(
				'allow_allrows'					=> false,
				'start_record'					=> $this->start,
				'record_limit'					=> $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'],
				'num_records'					=> count($vendor_list),
				'all_records'					=> $this->bo->total_records,
				'link_url'						=> $GLOBALS['phpgw']->link('/index.php',$link_select),
				'img_path'						=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_no_cat'					=> lang('no category'),
				'lang_cat_statustext'			=> lang('Select the category the building belongs to. To do not use a category select NO CATEGORY'),
				'select_name'					=> 'cat_id',
				'cat_list'						=> $cat_data['cat_list'],
				'select_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_select),
				'filter_list'					=> $this->nextmatchs->xslt_filter(array('filter' => $this->filter,'yours' => 'yes')),
				'lang_filter_statustext'		=> lang('Select the filter. To show all entries select SHOW ALL'),
				'lang_searchfield_statustext'	=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'	=> lang('Submit the search string'),
				'query'							=> $this->query,
				'lang_search'					=> lang('search'),
				'table_header_vendor'			=> $table_header,
				'values_vendor'					=> $content,
				'table_done'					=> $table_done,
				'contact_id'					=> $contact_id,
				'org_name'						=> $org_name
			);

			$appname						= lang('vendor');
			$function_msg					= lang('list vendors');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_vendor' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
			$this->save_sessiondata();
		}

		function b_account()
		{

			$GLOBALS['phpgw']->xslttpl->add_file(array('lookup',
										'nextmatchs',
										'search_field'));

			$b_account_list = $this->bo->read_b_account();

			while (is_array($b_account_list) && list(,$b_account_entry) = each($b_account_list))
			{
				$content[] = array
				(
					'id'				=> $b_account_entry['id'],
					'b_account_name'		=> $b_account_entry['descr'],
					'lang_select'		=> lang('Select'),
					'lang_select_statustext' => lang('Select this budget account')
				);
			}

			$table_header[] = array
			(
				'sort_name'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=>	'descr',
											'order'	=>	$this->order,
											'extra'		=> array('menuaction'	=> 'property.uilookup.b_account',
																	'cat_id'	=>$this->cat_id)
										)),
				'lang_name'		=> lang('Name'),
				'sort_id'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=>	'id',
											'order'	=>	$this->order,
											'extra'		=> array('menuaction'	=> 'property.uilookup.b_account',
																	'cat_id'	=>$this->cat_id)
										)),
				'lang_id'		=> lang('ID'),
				'lang_select'		=> lang('Select')
			);

			$table_done[] = array
			(
				'lang_done'				=> lang('Done'),
				'lang_done_statustext'	=> lang('Close this window')
			);

			$link_select = array
			(
				'menuaction'		=> 'property.uilookup.b_account',
				'second_display'	=> true,
				'order'				=> $this->order,
				'sort'				=> $this->sort,
				'cat_id'			=> $this->cat_id,
				'query'				=> $this->query,
				'filter'			=> $this->filter
			);


			$data = array
			(
				'allow_allrows'					=> false,
				'start_record'					=> $this->start,
				'record_limit'					=> $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'],
				'num_records'					=> count($b_account_list),
				'all_records'					=> $this->bo->total_records,
				'link_url'						=> $GLOBALS['phpgw']->link('/index.php',$link_select),
				'img_path'						=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'select_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_select),
				'filter_list'					=> $this->nextmatchs->xslt_filter(array('filter' => $this->filter,'yours' => 'yes')),
				'lang_filter_statustext'		=> lang('Select the filter. To show all entries select SHOW ALL'),
				'lang_searchfield_statustext'	=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'	=> lang('Submit the search string'),
				'query'							=> $this->query,
				'lang_search'					=> lang('search'),
				'table_header_b_account'		=> $table_header,
				'values_b_account'			=> $content,
				'table_done'					=> $table_done
			);

			$appname						= lang('budget account');
			$function_msg					= lang('list budget account');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_b_account' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
			$this->save_sessiondata();
		}


		function street()
		{

			$GLOBALS['phpgw']->xslttpl->add_file(array('lookup',
										'nextmatchs',
										'search_field'));

			$street_list = $this->bo->read_street();

			while (is_array($street_list) && list(,$street_entry) = each($street_list))
			{
				$content[] = array
				(
					'id'				=> $street_entry['id'],
					'street_name'		=> $street_entry['street_name'],
					'lang_select'		=> lang('Select'),
					'lang_select_statustext' => lang('Select this street')
				);
			}

			$table_header[] = array
			(
				'sort_name'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=>	'descr',
											'order'	=>	$this->order,
											'extra'		=> array('menuaction'	=> 'property.uilookup.street',
																	'cat_id'	=>$this->cat_id)
										)),
				'lang_name'		=> lang('Street name'),
				'sort_id'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=>	'id',
											'order'	=>	$this->order,
											'extra'		=> array('menuaction'	=> 'property.uilookup.street',
																	'cat_id'	=>$this->cat_id)
										)),
				'lang_id'		=> lang('ID'),
				'lang_select'		=> lang('Select')
			);

			$table_done[] = array
			(
				'lang_done'				=> lang('Done'),
				'lang_done_statustext'	=> lang('Close this window')
			);

			$link_select = array
			(
				'menuaction'		=> 'property.uilookup.street',
				'second_display'	=> true,
				'order'				=> $this->order,
				'sort'				=> $this->sort,
				'cat_id'			=> $this->cat_id,
				'query'				=> $this->query,
				'filter'			=> $this->filter
			);


			$data = array
			(
				'allow_allrows'					=> false,
				'start_record'					=> $this->start,
				'record_limit'					=> $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'],
				'num_records'					=> count($street_list),
				'all_records'					=> $this->bo->total_records,
				'link_url'						=> $GLOBALS['phpgw']->link('/index.php',$link_select),
				'img_path'						=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_no_cat'					=> lang('no category'),
				'lang_cat_statustext'			=> lang('Select the category the building belongs to. To do not use a category select NO CATEGORY'),
				'select_name'					=> 'cat_id',
				'select_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_select),
				'filter_list'					=> $this->nextmatchs->xslt_filter(array('filter' => $this->filter,'yours' => 'yes')),
				'lang_filter_statustext'		=> lang('Select the filter. To show all entries select SHOW ALL'),
				'lang_searchfield_statustext'	=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'	=> lang('Submit the search string'),
				'query'							=> $this->query,
				'lang_search'					=> lang('search'),
				'table_header_street'		=> $table_header,
				'values_street'			=> $content,
				'table_done'					=> $table_done
			);

			$appname						= lang('street');
			$function_msg					= lang('list street');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_street' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
			$this->save_sessiondata();
		}

		function tenant()
		{

			$GLOBALS['phpgw']->xslttpl->add_file(array('lookup',
										'nextmatchs',
										'search_field'));

			$tenant_list = $this->bo->read_tenant();

			while (is_array($tenant_list) && list(,$tenant_entry) = each($tenant_list))
			{
				$content[] = array
				(
					'id'				=> $tenant_entry['id'],
					'last_name'			=> $tenant_entry['last_name'],
					'first_name'		=> $tenant_entry['first_name'],
					'lang_select'		=> lang('Select'),
					'lang_select_statustext' => lang('Select this tenant')
				);
			}

			$table_header[] = array
			(
				'sort_last_name'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=>	'last_name',
											'order'	=>	$this->order,
											'extra'		=> array('menuaction'	=> 'property.uilookup.tenant',
																	'cat_id'	=>$this->cat_id)
										)),
				'sort_first_name'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=>	'first_name',
											'order'	=>	$this->order,
											'extra'		=> array('menuaction'	=> 'property.uilookup.tenant',
																	'cat_id'	=>$this->cat_id)
										)),
				'lang_last_name'		=> lang('last name'),
				'lang_first_name'		=> lang('first name'),
				'sort_id'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=>	'id',
											'order'	=>	$this->order,
											'extra'		=> array('menuaction'	=> 'property.uilookup.tenant',
																	'cat_id'	=>$this->cat_id)
										)),
				'lang_id'		=> lang('ID'),
				'lang_select'		=> lang('Select')
			);

			$table_done[] = array
			(
				'lang_done'				=> lang('Done'),
				'lang_done_statustext'	=> lang('Close this window')
			);

			$link_select = array
			(
				'menuaction'		=> 'property.uilookup.tenant',
				'second_display'	=> true,
				'order'				=> $this->order,
				'sort'				=> $this->sort,
				'cat_id'			=> $this->cat_id,
				'query'				=> $this->query,
				'filter'			=> $this->filter
			);


			$data = array
			(
				'allow_allrows'					=> false,
				'start_record'					=> $this->start,
				'record_limit'					=> $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'],
				'num_records'					=> count($tenant_list),
				'all_records'					=> $this->bo->total_records,
				'link_url'						=> $GLOBALS['phpgw']->link('/index.php',$link_select),
				'img_path'						=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_no_cat'					=> lang('no category'),
				'lang_cat_statustext'			=> lang('Select the category the building belongs to. To do not use a category select NO CATEGORY'),
				'select_name'					=> 'cat_id',
				'select_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_select),
				'lang_searchfield_statustext'	=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'	=> lang('Submit the search string'),
				'query'							=> $this->query,
				'lang_search'					=> lang('search'),
				'table_header_tenant_new'		=> $table_header,
				'values_tenant_new'				=> $content,
				'table_done'					=> $table_done
			);

			$appname						= lang('tenant');
			$function_msg					= lang('list tenant');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_tenant' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
			$this->save_sessiondata();
		}

		function ns3420()
		{

			$GLOBALS['phpgw']->xslttpl->add_file(array('lookup',
										'nextmatchs',
										'search_field'));

			$ns3420_list = $this->bo->read_ns3420();

			while (is_array($ns3420_list) && list(,$ns3420_entry) = each($ns3420_list))
			{
				$content[] = array
				(
					'id'				=> $ns3420_entry['id'],
					'ns3420_descr'		=> $ns3420_entry['ns3420_descr'],
					'lang_select'		=> lang('Select'),
					'lang_select_statustext' => lang('Select this ns3420 - code')
				);
			}

			$table_header[] = array
			(
				'sort_descr'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=>	'tekst1',
											'order'	=>	$this->order,
											'extra'		=> array('menuaction'	=> 'property.uilookup.ns3420',
																	'query'	=>$this->query)
										)),
				'lang_descr'		=> lang('ns3420 description'),
				'sort_id'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=>	'id',
											'order'	=>	$this->order,
											'extra'		=> array('menuaction'	=> 'property.uilookup.ns3420',
																	'query'	=>$this->query)
										)),
				'lang_id'		=> lang('ID'),
				'lang_select'		=> lang('Select')
			);

			$table_done[] = array
			(
				'lang_done'				=> lang('Done'),
				'lang_done_statustext'	=> lang('Close this window')
			);

			$link_select = array
			(
				'menuaction'		=> 'property.uilookup.ns3420',
				'second_display'	=> true,
				'order'				=> $this->order,
				'sort'				=> $this->sort,
				'cat_id'			=> $this->cat_id,
				'query'				=> $this->query,
				'filter'			=> $this->filter
			);


			$data = array
			(
				'allow_allrows'					=> false,
				'start_record'					=> $this->start,
				'record_limit'					=> $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'],
				'num_records'					=> count($ns3420_list),
				'all_records'					=> $this->bo->total_records,
				'link_url'						=> $GLOBALS['phpgw']->link('/index.php',$link_select),
				'img_path'						=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'select_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_select),
				'lang_searchfield_statustext'	=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'	=> lang('Submit the search string'),
				'lang_search'					=> lang('search'),
				'query'							=> $this->query,
				'table_header_ns3420'		=> $table_header,
				'values_ns3420'			=> $content,
				'table_done'					=> $table_done
			);

			$appname						= lang('standard description');
			$function_msg					= lang('list standard description');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_ns3420' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
			$this->save_sessiondata();
		}


		function entity()
		{
			$bocommon					= CreateObject('property.bocommon');
			$boentity					= CreateObject('property.boentity');
			$boadmin_entity				= CreateObject('property.boadmin_entity');
			$this->start				= $boentity->start;
			$this->query				= $boentity->query;
			$this->sort					= $boentity->sort;
			$this->order				= $boentity->order;
			$this->filter				= $boentity->filter;
			$this->cat_id				= $boentity->cat_id;
			$this->part_of_town_id		= $boentity->part_of_town_id;
			$this->district_id			= $boentity->district_id;
			$this->entity_id			= $boentity->entity_id;

			$GLOBALS['phpgw']->xslttpl->add_file(array('lookup',
										'nextmatchs',
										'search_field'));

			$entity_list = $boentity->read(array('lookup'=>True));

			$input_name = $GLOBALS['phpgw']->session->appsession('lookup_fields','property');
//_debug_array($input_name);

			$uicols	= $boentity->uicols;

//_debug_array($uicols);

			$j=0;

			if (isset($entity_list) AND is_array($entity_list))
			{
				foreach($entity_list as $entity_entry)
				{

					for ($i=0;$i<count($uicols['name']);$i++)
					{
						if($uicols['input_type'][$i]!='hidden')
						{
							$content[$j]['row'][$i]['value'] 	= $entity_entry[$uicols['name'][$i]];
							$content[$j]['row'][$i]['name'] 	= $uicols['name'][$i];
						}
					}

					for ($i=0;$i<count($input_name);$i++)
					{
						$content[$j]['hidden'][$i]['value'] 	= $entity_entry[$input_name[$i]];
						$content[$j]['hidden'][$i]['name'] 		= $input_name[$i];
					}

					$j++;
				}
			}

//_debug_array($content);
			for ($i=0;$i<count($uicols['descr']);$i++)
			{
				if($uicols['input_type'][$i]!='hidden')
				{
					$table_header[$i]['header'] 	= $uicols['descr'][$i];
					$table_header[$i]['width'] 		= '5%';
					$table_header[$i]['align'] 		= 'center';
					if($uicols['name'][$i]=='loc1')
					{
						$table_header[$i]['sort_link']	=true;
						$table_header[$i]['sort'] 		= $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=>	'loc1',
											'order'	=>	$this->order,
											'extra'		=> array('menuaction'	=> 'property.uilookup.entity',
																	'type_id'	=>$type_id,
																	'query'		=>$this->query,
																	'district_id'	=> $this->district_id,
																	'lookup'	=>$lookup,
																	'entity_id'		=>$this->entity_id,
																	'cat_id'	=>$this->cat_id)
										));
					}
					if($uicols['name'][$i]=='num')
					{
						$table_header[$i]['sort_link']	=true;
						$table_header[$i]['sort'] 		= $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=>	'num',
											'order'	=>	$this->order,
											'extra'		=> array('menuaction'	=> 'property.uilookup.entity',
																	'type_id'	=>$type_id,
																	'query'		=>$this->query,
																	'lookup'	=>$lookup,
																	'district_id'	=> $this->district_id,
																	'entity_id'		=>$this->entity_id,
																	'cat_id'	=>$this->cat_id)
										));
					}
				}
			}

			$table_header[$i]['width'] 			= '5%';
			$table_header[$i]['align'] 			= 'center';
			$table_header[$i]['header']		= lang('select');


//_debug_array($table_header);
//_debug_array($uicols);

			$table_done[] = array
			(
				'lang_done'				=> lang('Done'),
				'lang_done_statustext'	=> lang('Close this window')
			);

			$link_select = array
			(
				'menuaction'		=> 'property.uilookup.entity',
				'order'				=> $this->order,
				'sort'				=> $this->sort,
				'cat_id'			=> $this->cat_id,
				'entity_id'			=> $this->entity_id,
				'district_id'		=> $this->district_id,
				'query'				=> $this->query,
				'filter'			=> $this->filter
			);


			for ($i=0;$i<count($input_name);$i++)
			{
				$function_exchange_values .= 'opener.document.form.' . $input_name[$i] .'.value = thisform.elements[' . $i . '].value;' ."\r\n";
			}

			$function_exchange_values .='window.close()';


			$data = array
			(
				'exchange_values'				=> 'Exchange_values(this.form);',
				'function_exchange_values'		=> $function_exchange_values,
				'lang_select'					=> lang('select'),
				'lookup'						=> 1,//$lookup,
				'allow_allrows'					=> false,
				'start_record'					=> $this->start,
				'record_limit'					=> $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'],
				'num_records'					=> count($entity_list),
				'all_records'					=> $boentity->total_records,
				'link_url'						=> $GLOBALS['phpgw']->link('/index.php',$link_select),
				'img_path'						=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_no_cat'					=> lang('no category'),
				'lang_cat_statustext'			=> lang('Select the category the building belongs to. To do not use a category select NO CATEGORY'),
				'select_name'					=> 'cat_id',
				'cat_list'						=> $boentity->select_category_list('filter',$this->cat_id),
				'district_list'					=> $bocommon->select_district_list('filter',$this->district_id),
				'select_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_select),
				'lang_no_district'				=> lang('no district'),
				'lang_district_statustext'		=> lang('Select the district the selection belongs to. To do not use a district select NO DISTRICT'),
				'select_district_name'			=> 'district_id',
				'lang_select'					=> lang('Select'),

				'filter_list'					=> $this->nextmatchs->xslt_filter(array('filter' => $this->filter,'yours' => 'yes')),
				'lang_filter_statustext'		=> lang('Select the filter. To show all entries select SHOW ALL'),
				'lang_searchfield_statustext'	=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'	=> lang('Submit the search string'),
				'query'							=> $this->query,
				'lang_search'					=> lang('search'),
				'table_header_entity'			=> $table_header,
				'values_entity'					=> $content,
				'table_done'					=> $table_done
			);

//_debug_array($content);
			if($this->entity_id)
			{
				$entity 	= $boadmin_entity->read_single($this->entity_id,false);
				$appname	= $entity['name'];
			}
			if($this->cat_id)
			{
				$category = $boadmin_entity->read_single_category($this->entity_id,$this->cat_id);
				$function_msg					= lang('lookup') . ' ' . $category['name'];
				$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_entity' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
			$this->save_sessiondata();
		}

		function phpgw_user()
		{

			$GLOBALS['phpgw']->xslttpl->add_file(array('lookup',
										'nextmatchs',
										'search_field'));

			$phpgw_user_list = $this->bo->read_phpgw_user();

			$column = phpgw::get_var('column');
			
			while (is_array($phpgw_user_list) && list(,$phpgw_user_entry) = each($phpgw_user_list))
			{
				$content[] = array
				(
					'id'				=> $phpgw_user_entry['id'],
					'account_lid'		=> $phpgw_user_entry['account_lid'],
					'first_name'			=> $phpgw_user_entry['first_name'],
					'last_name'			=> $phpgw_user_entry['last_name'],
					'lang_select'		=> lang('Select'),
					'lang_select_statustext' => lang('Select this user')
				);
			}

			$table_header[] = array
			(
				'sort_account_lid'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=>	'account_lid',
											'order'	=>	$this->order,
											'extra'		=> array('menuaction'	=> 'property.uilookup.phpgw_user',
																	'cat_id'	=>$this->cat_id,
																	'column'	=> $column)
										)),
				'sort_last_name'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=>	'account_lastname',
											'order'	=>	$this->order,
											'extra'		=> array('menuaction'	=> 'property.uilookup.phpgw_user',
																	'cat_id'	=>$this->cat_id,
																	'column'	=> $column)
										)),
				'sort_first_name'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=>	'account_firstname',
											'order'	=>	$this->order,
											'extra'		=> array('menuaction'	=> 'property.uilookup.phpgw_user',
																	'cat_id'	=>$this->cat_id,
																	'column'	=> $column)
										)),
				'lang_last_name'		=> lang('last name'),
				'lang_first_name'		=> lang('first name'),
				'sort_id'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=>	'account_id',
											'order'	=>	$this->order,
											'extra'		=> array('menuaction'	=> 'property.uilookup.phpgw_user',
																	'cat_id'	=>$this->cat_id,
																	'column'	=> $column)
										)),
				'lang_id'		=> lang('ID'),
				'lang_select'		=> lang('Select')
			);

			$table_done[] = array
			(
				'lang_done'				=> lang('Done'),
				'lang_done_statustext'	=> lang('Close this window')
			);

			$link_select = array
			(
				'menuaction'		=> 'property.uilookup.phpgw_user',
				'order'				=> $this->order,
				'sort'				=> $this->sort,
				'cat_id'			=> $this->cat_id,
				'query'				=> $this->query,
				'filter'			=> $this->filter,
				'column'			=> $column
			);

			if($column)
			{
				$user_id	=$column;
				$user_name	=$column . '_user_name';
			}
			else
			{
				$user_id	='user_id';
				$user_name	='user_name';
			}


			$data = array
			(
				'allow_allrows'					=> false,
				'start_record'					=> $this->start,
				'record_limit'					=> $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'],
				'num_records'					=> count($phpgw_user_list),
				'all_records'					=> $this->bo->total_records,
				'link_url'						=> $GLOBALS['phpgw']->link('/index.php',$link_select),
				'img_path'						=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_no_cat'					=> lang('no category'),
				'lang_cat_statustext'			=> lang('Select the category the building belongs to. To do not use a category select NO CATEGORY'),
				'select_name'					=> 'cat_id',
				'select_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_select),
				'lang_searchfield_statustext'	=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'	=> lang('Submit the search string'),
				'query'							=> $this->query,
				'lang_search'					=> lang('search'),
				'table_header_phpgw_user'		=> $table_header,
				'values_phpgw_user'				=> $content,
				'table_done'					=> $table_done,
				'user_id'						=> $user_id,
				'user_name'						=> $user_name

			);

			$appname						= lang('phpgw_user');
			$function_msg					= lang('list phpgw_user');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_phpgw_user' => $data));
			$this->save_sessiondata();
		}
	}
?>
