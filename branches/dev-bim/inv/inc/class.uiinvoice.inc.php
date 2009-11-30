<?php
	/*******************************************************************\
	* phpGroupWare - Inventory                                          *
	* http://www.phpgroupware.org                                       *
	*                                                                   *
	* Inventar Manager                                                  *
	* Written by Bettina Gille [ceb@phpgroupware.org]                   *
	*        and Joseph Engo <jengo@phpgroupware.org>                   *
	* -----------------------------------------------                   *
	* Copyright (C) 2000,2001,2002 Bettina Gille                        *
	*                                                                   *
	* This program is free software; you can redistribute it and/or     *
	* modify it under the terms of the GNU General Public License as    *
	* published by the Free Software Foundation; either version 2 of    *
	* the License, or (at your option) any later version.               *
	*                                                                   *
	* This program is distributed in the hope that it will be useful,   *
	* but WITHOUT ANY WARRANTY; without even the implied warranty of    *
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU  *
	* General Public License for more details.                          *
	*                                                                   *
	* You should have received a copy of the GNU General Public License *
	* along with this program; if not, write to the Free Software       *
	* Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.         *
	\*******************************************************************/
	/* $Id$ */

	class uiinvoice
	{
		var $bo;
		var $boinv;
		var $nextmatchs;
		var $action;
		var $t;

		var $public_functions = array
		(
			'abook'			=> True,
			'preferences'	=> True,
			'list_dist'		=> True,
			'edit_dist'		=> True,
			'view_dist'		=> True,
			'delete_dist'	=> True
		);

		function uiinvoice() 
		{
			$action = get_var('action',Array('GET','POST'));

			$this->bo								= CreateObject('inv.boinvoice',True,$action);
			$this->boinv							= CreateObject('inv.boinventory');
			$this->nextmatchs						= CreateObject('phpgwapi.nextmatchs');
			$this->sbox								= CreateObject('phpgwapi.sbox');
			$this->t								= $GLOBALS['phpgw']->template;
			$this->grants							= $GLOBALS['phpgw']->acl->get_grants('inv');
			$this->grants[$this->boinv->account]	= PHPGW_ACL_READ + PHPGW_ACL_ADD + PHPGW_ACL_EDIT + PHPGW_ACL_DELETE;

			$this->start							= $this->bo->start;
			$this->query							= $this->bo->query;
			$this->filter							= $this->bo->filter;
			$this->order							= $this->bo->order;
			$this->sort								= $this->bo->sort;
			$this->cat_id							= $this->bo->cat_id;
			$this->action							= $this->bo->action;
		}

		function save_sessiondata($action)
		{
			$data = array
			(
				'start'		=> $this->start,
				'query'		=> $this->query,
				'filter'	=> $this->filter,
				'order'		=> $this->order,
				'sort'		=> $this->sort,
				'cat_id'	=> $this->cat_id,
				'action'	=> $this->action 
			);
			$this->bo->save_sessiondata($data,$action);
		}

		function set_app_langs()
		{
			$this->t->set_var('bg_color',$GLOBALS['phpgw_info']['theme']['th_bg']);
			$this->t->set_var('tr_color1',$GLOBALS['phpgw_info']['theme']['row_on']);
			$this->t->set_var('tr_color3',$GLOBALS['phpgw_info']['theme']['row_off']);

			$this->t->set_var('lang_edit',lang('Edit'));
			$this->t->set_var('lang_view',lang('View'));
			$this->t->set_var('lang_delete',lang('Delete'));

			$this->t->set_var('lang_select_cat',lang('Select category'));
			$this->t->set_var('lang_search',lang('Search'));
			$this->t->set_var('lang_submit',lang('Submit'));
			$this->t->set_var('lang_products',lang('Products'));
			$this->t->set_var('lang_categorys',lang('Categories'));
			$this->t->set_var('lang_product_status',lang('Product status'));
			$this->t->set_var('lang_orders',lang('Orders'));
			$this->t->set_var('lang_dists',lang('Distributors'));
    		$this->t->set_var('lang_archive',lang('Archive'));
    		$this->t->set_var('lang_rooms',lang('Stock rooms'));
			$this->t->set_var('lang_num',lang('Product ID'));
			$this->t->set_var('lang_name',lang('Short Name'));
			$this->t->set_var('lang_status_name',lang('Status Name'));
			$this->t->set_var('lang_url',lang('WWW'));
			$this->t->set_var('lang_ftp',lang('FTP'));
			$this->t->set_var('lang_purchase_date',lang('Purchase date'));
			$this->t->set_var('lang_selling_date',lang('Selling date'));
			$this->t->set_var('lang_description',lang('Description'));
			$this->t->set_var('lang_category',lang('Category'));
			$this->t->set_var('lang_in_stock',lang('in Stock'));
			$this->t->set_var('lang_min_stock',lang('min Stock'));
			$this->t->set_var('lang_cost',lang('Purchase price'));
			$this->t->set_var('lang_price',lang('Selling price'));
			$this->t->set_var('lang_retail',lang('Retail'));
			$this->t->set_var('lang_status',lang('Status'));
			$this->t->set_var('lang_serial',lang('Serial number'));
			$this->t->set_var('lang_select_dist',lang('Select distributor'));
			$this->t->set_var('lang_select_room',lang('Select Stock room'));
			$this->t->set_var('lang_select_cat',lang('Select category'));
			$this->t->set_var('lang_distributor',lang('Distributor'));
			$this->t->set_var('lang_note',lang('Note'));
			$this->t->set_var('lang_room',lang('Stock room'));
			$this->t->set_var('lang_add',lang('Add'));
			$this->t->set_var('lang_reset',lang('Clear Form'));
			$this->t->set_var('lang_done',lang('Done'));
			$this->t->set_var('lang_save',lang('Save'));
			$this->t->set_var('lang_company',lang('Company name'));
			$this->t->set_var('lang_department',lang('Department'));
			$this->t->set_var('lang_software',lang('Software'));
			$this->t->set_var('lang_url',lang('WWW'));
			$this->t->set_var('lang_url_mirror',lang('WWW mirror'));
			$this->t->set_var('lang_industry_type',lang('Industry type'));
			$this->t->set_var('lang_firstname',lang('Firstname'));
			$this->t->set_var('lang_lastname',lang('Lastname'));
			$this->t->set_var('lang_notes',lang('Notes'));
			$this->t->set_var('lang_cell',lang('Mobile phone'));
			$this->t->set_var('lang_ftp',lang('FTP'));
			$this->t->set_var('lang_ftp_mirror',lang('FTP mirror'));
			$this->t->set_var('lang_email',lang('Email'));
			$this->t->set_var('lang_phone',lang('Business phone'));
			$this->t->set_var('lang_fax',lang('Fax'));
			$this->t->set_var('lang_contact',lang('Contact'));
			$this->t->set_var('lang_access',lang('Private'));
			$this->t->set_var('lang_reset',lang('Clear Form'));
		}

		function display_app_header()
		{
			$this->t->set_file(array('header' => 'header.tpl'));
			$this->t->set_block('header','inv_header');

			$this->t->set_var('info',lang('Inventory'));
			$this->set_app_langs();

			$this->t->set_var('link_products',$GLOBALS['phpgw']->link('/index.php','menuaction=inv.uiinventory.list_products&action=subproduct'));
			$this->t->set_var('link_categorys',$GLOBALS['phpgw']->link('/index.php','menuaction=preferences.uicategories.index&cats_app=inv&cats_level=True&extra=tax,number'));
			$this->t->set_var('link_status',$GLOBALS['phpgw']->link('/index.php','menuaction=inv.uiinventory.list_status')); 
			$this->t->set_var('link_orders',$GLOBALS['phpgw']->link('/inv/listorders.php'));
			$this->t->set_var('link_dists',$GLOBALS['phpgw']->link('/index.php','menuaction=inv.uiinvoice.list_dist&action=inv_dist'));
			$this->t->set_var('link_archive',$GLOBALS['phpgw']->link('/inv/archiv.php','subarchive=True'));
			$this->t->set_var('link_rooms',$GLOBALS['phpgw']->link('/inv/rooms.php','subroom=True'));

			if ($subarchive == True)
			{
				$this->t->set_var('sub_productarchive',$GLOBALS['phpgw']->link('/inv/archiv.php','subarchive=True')); 
				$this->t->set_var('sub_orderarchive',$GLOBALS['phpgw']->link('/inv/order_archiv.php','subarchive=True')); 
				$this->t->set_var('sublang_productarchive',lang('Product archive'));
				$this->t->set_var('sublang_orderarchive',lang('Order archive'));
				$this->t->set_var('tr_color2',$GLOBALS['phpgw_info']['theme']['row_off']);
			}

			if ($subroom == True)
			{
				$this->t->set_var('sub_rooms',$GLOBALS['phpgw']->link('/inv/rooms.php','subroom=True')); 
				$this->t->set_var('sub_stockprd',$GLOBALS['phpgw']->link('/inv/liststockprd.php','subroom=True')); 
				$this->t->set_var('sublang_rooms',lang('Stock rooms'));
				$this->t->set_var('sublang_stockprd',lang('Product location'));
				$this->t->set_var('tr_color2',$GLOBALS['phpgw_info']['theme']['row_off']);
			}

			if ($this->action == 'subproduct')
			{
				$this->t->set_var('sub_products',$GLOBALS['phpgw']->link('/index.php','menuaction=inv.uiinventory.list_products&action=subproduct&status=active&selection=category')); 
				$this->t->set_var('sub_minstock',$GLOBALS['phpgw']->link('/index.php','menuaction=inv.uiinventory.list_products&action=subproduct&status=minstock&selection=category')); 
				$this->t->set_var('sub_receipt',$GLOBALS['phpgw']->link('/index.php','menuaction=inv.uiinventory.list_products&action=subproduct&status=receipt&selection=dist'));
				$this->t->set_var('sublang_products',lang('Products'));
				$this->t->set_var('sublang_minstock',lang('Out of stock products'));
				$this->t->set_var('sublang_receipt',lang('Stock receipt'));
				$this->t->set_var('tr_color2',$GLOBALS['phpgw_info']['theme']['row_off']);
			}

			$this->t->fp('app_header','inv_header');

			$GLOBALS['phpgw']->common->phpgw_header();
		}

		function abook()
		{
			$this->t->set_file(array('abook_list_t' => 'addressbook.tpl'));
			$this->t->set_block('abook_list_t','abook_list','list');

			$this->set_app_langs();

			$this->t->set_var('title',$GLOBALS['phpgw_info']['site_title']);
			$this->t->set_var('lang_action',lang('Address book'));
			$this->t->set_var('charset',$GLOBALS['phpgw']->translation->translate('charset'));
			$this->t->set_var('font',$GLOBALS['phpgw_info']['theme']['font']);

			$link_data = array
			(
				'menuaction'	=> 'inv.uiinvoice.abook',
				'action'		=> 'inv_abook'
			);
 
			$entries = $this->bo->read_abook();

// --------------------------------- nextmatch ---------------------------

			$left = $this->nextmatchs->left('/index.php',$this->start,$this->bo->total_records,$link_data);
			$right = $this->nextmatchs->right('/index.php',$this->start,$this->bo->total_records,$link_data);
			$this->t->set_var('left',$left);
			$this->t->set_var('right',$right);

			$this->t->set_var('lang_showing',$this->nextmatchs->show_hits($this->bo->total_records,$this->start));

// -------------------------- end nextmatch ------------------------------------

			$this->t->set_var('cats_action',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$this->boinv->cats->app_name = 'addressbook';
			$this->t->set_var('cats_list',$this->boinv->cats->formatted_list('select','all',$this->cat_id,True));
			$this->t->set_var('filter_action',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$this->t->set_var('filter_list',$this->nextmatchs->filter(1,1));
			$this->t->set_var('search_action',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$this->t->set_var('search_list',$this->nextmatchs->search(1));

// ---------------- list header variable template-declarations --------------------------

// -------------- list header variable template-declaration ------------------------

			$this->t->set_var('sort_company',$this->nextmatchs->show_sort_order($this->sort,'org_name',$this->order,'/index.php',lang('Company'),$link_data));
			$this->t->set_var('sort_firstname',$this->nextmatchs->show_sort_order($this->sort,'n_given',$this->order,'/index.php',lang('Firstname'),$link_data));
			$this->t->set_var('sort_lastname',$this->nextmatchs->show_sort_order($this->sort,'n_family',$this->order,'/index.php',lang('Lastname'),$link_data));
			$this->t->set_var('lang_select',lang('Select'));

// ------------------------- end header declaration --------------------------------

			for ($i=0;$i<count($entries);$i++)
			{
				$this->nextmatchs->template_alternate_row_color($this->t);
				$firstname = $entries[$i]['n_given'];
				if (!$firstname) { $firstname = '&nbsp;'; }
				$lastname = $entries[$i]['n_family'];
				if (!$lastname) { $lastname = '&nbsp;'; }
				$company = $entries[$i]['org_name'];
				if (!$company) { $company = '&nbsp;'; }

// ---------------- template declaration for list records -------------------------- 

				$this->t->set_var(array('company' 	=> $company,
									'firstname' 	=> $firstname,
									'lastname'		=> $lastname,
									'abid'			=> $entries[$i]['id']));

				$this->t->pf('list','abook_list',True);
			}

			$this->t->pfp('out','abook_list_t',True);
			$this->save_sessiondata('inv_abook');
			$GLOBALS['phpgw']->common->phpgw_exit();
		}

		function preferences()
		{
			$submit	= get_var('submit',Array('POST'));
			$prefs	= get_var('prefs',Array('POST'));
			$abid	= get_var('abid',Array('POST'));

			if ($submit)
			{
				$prefs['abid']		= $abid;
				$this->boinv->save_prefs($prefs);

				Header('Location: ' . $GLOBALS['phpgw']->link('/preferences/index.php'));
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();

			$this->t->set_file(array('prefs' => 'preferences.tpl'));

			$this->t->set_var('actionurl',$GLOBALS['phpgw']->link('/index.php','menuaction=inv.uiinvoice.preferences'));
			$this->t->set_var('addressbook_link',$GLOBALS['phpgw']->link('/index.php','menuaction=inv.uiinvoice.abook&action=inv_abook'));
			$this->t->set_var('lang_action',lang('Inventory preferences'));
			$this->t->set_var('lang_address',lang('Select your address'));
			$this->t->set_var('lang_select',lang('Select per button !'));
			$this->t->set_var('lang_print_format',lang('Select print format'));
			$this->t->set_var('lang_def_cat',lang('Default category'));
			$this->t->set_var('lang_select_def_cat',lang('Select default category'));

			$prefs = $this->boinv->read_prefs();

			if (isset($prefs['abid']))
			{
				$abid = $prefs['abid'];
				$entry = $this->bo->read_single_contact($abid);

				if ($entry[0]['org_name'] == '') { $this->t->set_var('name',$entry[0]['n_given'] . ' ' . $entry[0]['n_family']); }
				else { $this->t->set_var('name',$entry[0]['org_name'] . ' [ ' . $entry[0]['n_given'] . ' ' . $entry[0]['n_family'] . ' ]'); }
			}
			else
			{
				$this->t->set_var('name',$name);
			}

			$this->t->set_var('abid',$abid);

			if ($prefs['print_format']=='html'):
				$format_sel[0]=' selected';
			elseif ($prefs['print_format']=='pdf'):
				$format_sel[1]=' selected';
			endif;

			$print_format = '<option value="html"' . $format_sel[0] .'>' . lang('HTML') . '</option>' . "\n"
						. '<option value="pdf"' . $format_sel[1] . '>' . lang('PDF') . '</option>' . "\n";

			$this->t->set_var('print_format',$print_format);

			$this->t->set_var('category_list',$this->boinv->cats->formatted_list(array('format' => 'select','type' => 'all','selected' => $prefs['cat_id'])));

			$this->t->set_var('lang_save',lang('Save'));
			$this->t->pfp('out','prefs');
		}

		function list_dist()
		{
			$this->display_app_header();

			$link_data = array
			(
				'menuaction'	=> 'inv.uiinvoice.list_dist',
				'action'		=> 'inv_dist'
			);

			$this->t->set_file(array('distlist_list_t' => 'listdist.tpl',
						'distlist_list' => 'listdist.tpl'));
			$this->t->set_block('distlist_list_t','distlist_list','list');

			$this->t->set_var('lang_action',lang('Distributor list'));

			$this->t->set_var('search_action',$GLOBALS['phpgw']->link('/index.php',$link_data));

			$entries = $this->bo->read_dist();

//--------------------------------- nextmatch --------------------------------------------

			$left = $this->nextmatchs->left('/index.php',$this->start,$this->bo->total_records,$link_data);
			$right = $this->nextmatchs->right('/index.php',$this->start,$this->bo->total_records,$link_data);
			$this->t->set_var('left',$left);
			$this->t->set_var('right',$right);

		    $this->t->set_var('lang_showing',$this->nextmatchs->show_hits($this->bo->total_records,$this->start));

// ------------------------------ end nextmatch ------------------------------------------

// -------------- list header variable template-declaration ------------------------

			$this->t->set_var('sort_company',$this->nextmatchs->show_sort_order($this->sort,'org_name',$this->order,'/index.php',lang('Company name'),$link_data));
			$this->t->set_var('sort_department',$this->nextmatchs->show_sort_order($this->sort,'org_unit',$this->order,'/index.php',lang('Department'),$link_data));
			$this->t->set_var('sort_industry_type',$this->nextmatchs->show_sort_order($this->sort,'industry_type',$this->order,'/index.php',lang('Industry type'),$link_data));
			$this->t->set_var('sort_url',$this->nextmatchs->show_sort_order($this->sort,'url',$this->order,'/index.php',lang('WWW'),$link_data));
			$this->t->set_var('sort_ftp',$this->nextmatchs->show_sort_order($this->sort,'ftp',$this->order,'/index.php',lang('FTP'),$link_data));

			$this->t->set_var('cats_action',$GLOBALS['phpgw']->link('/index.php',$link_data));

			$this->boinv->cats->app_name = 'addressbook';
			$this->t->set_var('cats_list',$this->boinv->cats->formatted_list('select','all',$this->cat_id,'True'));

// ------------------------- end header declaration --------------------------------

			for ($i=0;$i<count($entries);$i++)
			{
				$this->nextmatchs->template_alternate_row_color($this->t);
				$company = $GLOBALS['phpgw']->strip_html($entries[$i]['org_name']);
				if (! $company) $company = '&nbsp;';
				$department = $GLOBALS['phpgw']->strip_html($entries[$i]['org_unit']);
				if (! $department) $department = '&nbsp;';
				$industry_type = $GLOBALS['phpgw']->strip_html($entries[$i]['industry_type']);
				if (! $industry_type) $industry_type = '&nbsp;';

				$url = $entries[$i]['url'];
				if (!$url || ($url == 'http://'))
				{
					$url = ''; 
					$this->t->set_var('lang_url','&nbsp;');
				}
				else
				{
					$url = $GLOBALS['phpgw']->strip_html($url);
					if (! ereg('http://',$url)) { $url = 'http://'. $url; }
					$this->t->set_var('lang_url',lang('Click me'));
				}
				$this->t->set_var('url',$url);

				$ftp = $entries[$i]['ftp'];
				if (!$ftp || ($ftp == 'ftp://'))
				{ 
					$ftp = ''; 
					$this->t->set_var('lang_ftp','&nbsp;');
				}
				else
				{
					$ftp = $GLOBALS['phpgw']->strip_html($ftp);
					if (! ereg('ftp://',$ftp)) { $ftp = 'ftp://'. $ftp; }
					$this->t->set_var('lang_ftp',lang('Click me'));
				}
				$this->t->set_var('ftp',$ftp);

// --------------- template declaration for list records ---------------------------- 

				$this->t->set_var(array('company'	=> $company,
									'department'	=> $department,
								'industry_type'		=> $industry_type,
											'url'	=> $url,
											'ftp'	=> $ftp));

				$this->t->set_var('products',$GLOBALS['phpgw']->link('/index.php','menuaction=inv.uiinventory.list_products&action=subproduct&status=receipt&selection=dist'));

				$link_data['dist_id']		= $entries[$i]['id'];

				$link_data['menuaction']	= 'inv.uiinvoice.view_dist';
				$this->t->set_var('view',$GLOBALS['phpgw']->link('/index.php',$link_data));

				$link_data['menuaction']	= 'inv.uiinvoice.edit_dist';
				$this->t->set_var('edit',$GLOBALS['phpgw']->link('/index.php',$link_data));

				$this->t->fp('list','distlist_list',True);
			}

			$link_data['dist_id']	= '';
			$link_data['cat_id']	= $this->cat_id;
			$this->t->set_var('add_action',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$this->t->pfp('out','distlist_list_t',True);
			$this->save_sessiondata('inv_dist');
		}

		function edit_dist()
		{
			$dist_id = get_var('dist_id',Array('GET','POST'));
			$values  = get_var('values',Array('POST'));

			$link_data = array
			(
				'menuaction'	=> 'inv.uiinvoice.list_dist',
				'action'		=> 'inv_dist'
			);

			if(!$dist_id)
			{
				Header('Location: ' . $GLOBALS['phpgw']->link('/index.php',$link_data));
			}

			if(get_var('submit',Array('POST')))
			{
				$this->bo->save_dist($values);
				Header('Location: ' . $phpgw->link('/index.php',$link_data));
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			$this->t->set_file(array('form' => 'dist_form.tpl'));
			$this->t->set_block('form','add','addhandle');
			$this->t->set_block('form','edit','edithandle');

			$this->display_app_header();

			$link_data['menuaction']	= 'inv.uiinvoice.edit_dist';
			$link_data['dist_id']		= $dist_id;
			$this->t->set_var('actionurl',$GLOBALS['phpgw']->link('/index.php',$link_data));

			if ($dist_id)
			{
				$values = $this->bo->read_single_contact($dist_id);
				$this->t->set_var('lang_action',lang('Edit distributor'));
				$cat_id = $values[0]['cat_id'];
			}
			else
			{
				$this->t->set_var('lang_action',lang('Add distributor'));
				$cat_id = $this->cat_id;
			}

			$this->bo->cats->app_name = 'addressbook';
			$this->t->set_var('cats_list',$this->bo->cats->formatted_list('select','all',$cat_id,'True'));

			$this->t->set_var('dist_id',$dist_id);
			$this->t->set_var('company',$GLOBALS['phpgw']->strip_html($values[0]['org_name']));
			$this->t->set_var('department',$GLOBALS['phpgw']->strip_html($values[0]['org_unit']));
			$this->t->set_var('firstname',$GLOBALS['phpgw']->strip_html($values[0]['n_given']));
			$this->t->set_var('lastname',$GLOBALS['phpgw']->strip_html($values[0]['n_family']));
			$this->t->set_var('industry_type',$GLOBALS['phpgw']->strip_html($values[0]['industry_type']));
			$this->t->set_var('software',$GLOBALS['phpgw']->strip_html($values[0]['software']));
			$this->t->set_var('email',$GLOBALS['phpgw']->strip_html($values[0]['email']));
			$this->t->set_var('wphone',$GLOBALS['phpgw']->strip_html($values[0]['tel_work']));
			$this->t->set_var('fax',$GLOBALS['phpgw']->strip_html($values[0]['tel_fax']));
			$this->t->set_var('cell',$GLOBALS['phpgw']->strip_html($values[0]['tel_cell']));

			if (! ereg('http://',$values[0]['url']))
			{
				$values[0]['url'] = 'http://'. $values['url'];
			}
			$this->t->set_var('url',$GLOBALS['phpgw']->strip_html($values[0]['url']));

			if (! ereg('http://',$values[0]['url_mirror']))
			{
				$values[0]['url_mirror'] = 'http://'. $values[0]['url_mirror'];
			}
			$this->t->set_var('url_mirror',$GLOBALS['phpgw']->strip_html($values[0]['url_mirror']));

			if (! ereg('ftp://',$values[0]['ftp']))
			{
				$values[0]['ftp'] = 'ftp://'. $values[0]['ftp'];
			}
			$this->t->set_var('ftp',$GLOBALS['phpgw']->strip_html($values[0]['ftp']));

			if (! ereg('ftp://',$values[0]['ftp_mirror']))
			{
				$values[0]['ftp_mirror'] = 'ftp://'. $values[0]['ftp_mirror'];
			}
			$this->t->set_var('ftp_mirror',$GLOBALS['phpgw']->strip_html($values[0]['ftp_mirror']));

			$this->t->set_var('notes',$GLOBALS['phpgw']->strip_html(nl2br($values[0]['note'])));

			$this->t->set_var('access','<input type="checkbox" name="values[access]" value="True"' . ($values[0]['access'] == 'private'?' checked':'') . '>');

			$this->t->set_var('edithandle','');
			$this->t->set_var('addhandle','');

			$this->t->pfp('out','form');

			if ($dist_id)
			{
				$this->t->pfp('addhandle','add');
			}
			else
			{
				$this->t->pfp('edithandle','edit');
			}
		}

		function delete_dist()
		{
			$dist_id = get_var('dist_id',Array('GET','POST'));

			$link_data = array
			(
				'menuaction'	=> 'inv.uiinvoice.list_dist',
				'action'		=> 'inv_dist'
			);

			if(!$dist_id)
			{
				Header('Location: ' . $GLOBALS['phpgw']->link('/index.php',$link_data));
			}

			if(get_var('confirm',Array('POST')))
			{
				$this->bo->delete_dist($dist_id);

				Header('Location: ' . $GLOBALS['phpgw']->link('/index.php',$link_data));
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			$this->t->set_file(array('dist_delete' => 'delete.tpl'));

			$this->display_app_header();

			$this->t->set_var('deleteheader',lang('Are you sure you want to delete this entry ?'));

			$hidden_vars = '<input type="hidden" name="dist_id" value="' . $dist_id . '">' . "\n";
			$this->t->set_var('hidden_vars',$hidden_vars);
			$this->t->set_var('nolink',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$this->t->set_var('lang_no',lang('No'));

			$link_data['menuaction']	= 'inv.uiinvoice.delete_dist';
			$link_data['dist_id']		= $dist_id;

			$this->t->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$this->t->set_var('lang_yes',lang('Yes'));
			$this->t->pfp('out','dist_delete');
		}

		function view_dist()
		{
			$dist_id = get_var('dist_id',Array('GET','POST'));

			$link_data = array
			(
				'menuaction'	=> 'inv.uiinvoice.list_dist',
				'action'		=> 'inv_dist'
			);

			if(!$dist_id)
			{
				Header('Location: ' . $GLOBALS['phpgw']->link('/index.php',$link_data));
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			$this->t->set_file(array('view' => 'view_dist.tpl'));

			$this->display_app_header();

			$this->t->set_var('lang_action',lang('View distributor'));

			$fields = $this->bo->read_single_contact($dist_id);

			$this->t->set_var('company',$GLOBALS['phpgw']->strip_html($fields[0]['org_name']));
			$this->t->set_var('department',$GLOBALS['phpgw']->strip_html($fields[0]['org_unit']));
			$this->t->set_var('firstname',$GLOBALS['phpgw']->strip_html($fields[0]['n_given']));
			$this->t->set_var('lastname',$GLOBALS['phpgw']->strip_html($fields[0]['n_family']));
			$this->t->set_var('industry_type',$GLOBALS['phpgw']->strip_html($fields[0]['industry_type']));
			$this->t->set_var('software',$GLOBALS['phpgw']->strip_html($fields[0]['software']));
			$this->t->set_var('email',$GLOBALS['phpgw']->strip_html($fields[0]['email']));
			$this->t->set_var('wphone',$GLOBALS['phpgw']->strip_html($fields[0]['tel_work']));
			$this->t->set_var('fax',$GLOBALS['phpgw']->strip_html($fields[0]['tel_fax']));
			$this->t->set_var('cell',$GLOBALS['phpgw']->strip_html($fields[0]['tel_cell']));
			$this->t->set_var('access',lang($fields[0]['access']));

			$url = $fields[0]['url'];
			$url_mirror = $fields[0]['url_mirror'];
			$ftp = $fields[0]['ftp'];
			$ftp_mirror = $fields[0]['ftp_mirror'];

			if (!$url || ($url == 'http://'))
			{
				$url = '';
			}
			else
			{
				if (! ereg('http://',$url))
				{
					$url = 'http://'. $url;
				}
			}
    		$this->t->set_var('url',$GLOBALS['phpgw']->strip_html($url));

		    if (!$url_mirror || ($url_mirror == 'http://'))
			{
				$url_mirror = '';
			}
			else
			{
				if (! ereg('http://',$url_mirror))
				{
					$url_mirror = 'http://'. $url_mirror;
				}
			}
 			$this->t->set_var('url_mirror',$GLOBALS['phpgw']->strip_html($url_mirror));

			if (!$ftp || ($ftp == 'ftp://'))
			{
				$ftp = '';
			} 
			else
			{
				if (! ereg('ftp://',$ftp))
				{
					$ftp = 'ftp://'. $ftp;
				}
			}
			$this->t->set_var('ftp',$GLOBALS['phpgw']->strip_html($ftp));

			if (!$ftp_mirror || ($ftp_mirror == 'ftp://'))
			{
				$ftp_mirror = '';
			}
			else
			{
				if (! ereg('ftp://',$ftp_mirror))
				{
					$ftp_mirror = 'ftp://'. $ftp_mirror;
				}
			}
			$this->t->set_var('ftp_mirror',$GLOBALS['phpgw']->strip_html($ftp_mirror));

			$this->t->set_var('notes',nl2br($GLOBALS['phpgw']->strip_html($fields[0]['notes'])));
			$this->t->set_var('cat',$this->bo->cats->id2name($fields[0]['cat_id']),'name');

			$this->t->set_var('done_action',$GLOBALS['phpgw']->link('/index.php',$link_data));

			$this->t->pfp('out','view');
		}
	}
?>
