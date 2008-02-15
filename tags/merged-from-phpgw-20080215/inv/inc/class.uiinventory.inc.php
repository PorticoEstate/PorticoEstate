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

	class uiinventory
	{
		var $boinv;
		var $nextmatchs;
		var $sbox;
		var $t;
		var $grants;

		var $start;
		var $filter;
		var $query;
		var $sort;
		var $order;
		var $cat_id;

		var $taxpercent;
		var $number;
		var $action;
		var $product_id;
		var $status;
		var $dist;
		var $selection;

		var $public_functions = array
		(
			'list_products'		=> True,
			'edit_product'		=> True,
			'view_product'		=> True,
			'delete_product'	=> True,
			'list_status'		=> True,
			'add_status'		=> True,
			'edit_status'		=> True,
			'delete_status'		=> True
		);

		function uiinventory() 
		{
			$this->boinv							= CreateObject('inv.boinventory',True);
			$this->bo								= CreateObject('inv.boinvoice');
			$this->nextmatchs						= CreateObject('phpgwapi.nextmatchs');
			$this->sbox								= CreateObject('phpgwapi.sbox');
			$this->t								= $GLOBALS['phpgw']->template;
			$this->grants							= $GLOBALS['phpgw']->acl->get_grants('inv');
			$this->grants[$this->boinv->account]	= PHPGW_ACL_READ + PHPGW_ACL_ADD + PHPGW_ACL_EDIT + PHPGW_ACL_DELETE;

			$this->start							= $this->boinv->start;
			$this->query							= $this->boinv->query;
			$this->filter							= $this->boinv->filter;
			$this->order							= $this->boinv->order;
			$this->sort								= $this->boinv->sort;
			$this->cat_id							= $this->boinv->cat_id;
			$this->taxpercent						= $this->boinv->taxpercent;
			$this->number							= $this->boinv->number;
			$this->action							= $this->boinv->action;
			$this->product_id						= $this->boinv->product_id;
			$this->status							= $this->boinv->status;
			$this->dist								= $this->boinv->dist;
			$this->selection						= $this->boinv->selection;
		}

		function save_sessiondata()
		{
			$data = array
			(
				'start'			=> $this->start,
				'query'			=> $this->query,
				'filter'		=> $this->filter,
				'order'			=> $this->order,
				'sort'			=> $this->sort,
				'cat_id'		=> $this->cat_id,
				'action'		=> $this->action,
				'product_id'	=> $this->product_id,
				'status'		=> $this->status,
				'dist'			=> $this->dist,
				'selection'		=> $this->selection
			);
			$this->boinv->save_sessiondata($data);
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

		function list_products()
		{
			$link_data = array
			(
				'menuaction'	=> 'inv.uiinventory.list_products',
				'cat_id'		=> $this->cat_id,
				'action'		=> $this->action,
				'status'		=> $this->status,
				'dist'			=> $this->dist,
				'selection'		=> $this->selection
			);

			$this->t->set_file(array('listproducts_t' => 'listproducts_full.tpl',
										'listproducts' => 'listproducts_full.tpl'));
    		$this->t->set_block('listproducts_t','listproducts','list');

			$this->display_app_header();

			$nopref = $this->boinv->check_prefs();
			if (is_array($nopref))
			{
				$this->t->set_var('pref_message',$GLOBALS['phpgw']->common->error_list($nopref));
			}
			else
			{
				$prefs = $this->boinv->get_prefs();
			}

//			$archive_id = $this->boinv->get_status_id('archive');

			if ($this->selection == 'category')
			{
				$selection_list= '<form method="POST" action="' . $GLOBALS['phpgw']->link('/index.php',$link_data) . '" name="form">' . "\n"
							. '<select name="cat_id" onChange="this.form.submit();"><option value="">' . lang('Select category') . '</option>' . "\n"
							. $this->boinv->formatted_cat_list() . '</select>';
			}
			elseif ($this->selection == 'dist')
			{
				$selection_list= '<form method="POST" action="' . $GLOBALS['phpgw']->link('/index.php',$link_data) .'" name="form">' . "\n"
							. '<select name="dist" onChange="this.form.submit();"><option value="">' . lang('Select distributor')
							. $this->boinv->select_dist_list() . '</select>';
			}

			$this->t->set_var('selection_list',$selection_list);
			$this->t->set_var('selection_action',$GLOBALS['phpgw']->link('/index.php',$link_data));

			if (!$start)
			{
				$start = '0';
			}

			switch ($this->status)
			{
				case 'active':		$lang_action = lang('Product list'); break;
				case 'minstock':	$lang_action = lang('Out of stock product list'); break;
				case 'receipt':		$lang_action = lang('Stock receipt list'); break;
			}

			$this->t->set_var('lang_action',$lang_action);
			$category = $this->boinv->return_single_cat();
			$products = $this->boinv->read_products();

//--------------------------------- nextmatch --------------------------------------------

			$left = $this->nextmatchs->left('/index.php',$this->start,$this->boinv->total_records,$link_data);
			$right = $this->nextmatchs->right('/index.php',$this->start,$this->boinv->total_records,$link_data);
			$this->t->set_var('left',$left);
			$this->t->set_var('right',$right);

			$this->t->set_var('search_message',$this->nextmatchs->show_hits($this->boinv->total_records,$this->start));

// ------------------------------ end nextmatch ------------------------------------------

//---------------------------- list variable template-declarations -------------------------

			$this->t->set_var('sort_num',$this->nextmatchs->show_sort_order($this->sort,'id',$this->order,'/index.php',lang('Product ID'),$link_data));
			$this->t->set_var('sort_serial',$this->nextmatchs->show_sort_order($this->sort,'serial',$this->order,'/index.php',lang('Serial number'),$link_data));
			$this->t->set_var('sort_name',$this->nextmatchs->show_sort_order($this->sort,'name',$this->order,'/index.php',lang('Name'),$link_data));

			if ($this->selection == 'category')
			{
				$this->t->set_var('sort_selection',$this->nextmatchs->show_sort_order($this->sort,'dist',$this->order,'/index.php',lang('Distributor'),$link_data));
			}
			elseif ($this->selection == 'dist')
			{
				$this->t->set_var('sort_selection',$this->nextmatchs->show_sort_order($this->sort,'category',$this->order,'/index.php',lang('Category'),$link_data));
			}

			$this->t->set_var('sort_status',$this->nextmatchs->show_sort_order($this->sort,'status',$this->order,'/index.php',lang('Status'),$link_data));
			$this->t->set_var('sort_cost',$this->nextmatchs->show_sort_order($this->sort,'cost',$this->order,'/index.php',lang('Cost'),$link_data));
			$this->t->set_var('sort_price',$this->nextmatchs->show_sort_order($this->sort,'price',$this->order,'/index.php',lang('Price'),$link_data));
			$this->t->set_var('sort_retail',$this->nextmatchs->show_sort_order($this->sort,'retail',$this->order,'/index.php',lang('Retail'),$link_data));
			$this->t->set_var('sort_stock',$this->nextmatchs->show_sort_order($this->sort,'stock',$this->order,'/index.php',lang('Stock'),$link_data));
			$this->t->set_var('currency',$prefs['currency']);
			$this->t->set_var('search_action',$GLOBALS['phpgw']->link('/index.php',$link_data));

// -------------------------------- end declaration -----------------------------------------

			for ($i=0;$i<count($products);$i++)
			{
				$this->nextmatchs->template_alternate_row_color($this->t);
				$serial = $GLOBALS['phpgw']->strip_html($products[$i]['serial']);
				if (! $serial) $serial = '&nbsp;';

				$name = $GLOBALS['phpgw']->strip_html($products[$i]['name']);
				if (! $name) $name = '&nbsp;';

				if ($this->selection == 'category')
				{
					$abid = $products[$i]['dist'];
					if (!$abid) { $selection = '&nbsp;'; }
					else
					{
						$dist = $this->bo->read_single_contact($abid);
						$selection = $dist[0]['org_name'];
					}
				}
				elseif ($this->selection == 'dist')
				{
					$selection = $this->boinv->cats->id2name($products[$i]['category']);
				}

				if ($products[$i]['mstock'] == $products[$i]['stock']) { $stock = '<b>' . $products[$i]['stock'] . '</b>'; }
				if ($products[$i]['mstock'] < $products[$i]['stock']) { $stock = $products[$i]['stock']; }
				if ($products[$i]['mstock'] > $products[$i]['stock']) { $stock = '<font color="FF0000"><b>' . $products[$i]['stock'] . '</b></font>'; }

//---------------------------------- list records -------------------------------------

				$this->t->set_var(array('num' => $GLOBALS['phpgw']->strip_html($products[$i]['num']),
										'name' => $name,
										'selection' => $selection,
										'status' => lang($this->boinv->return_value($products[$i]['status'])),
										'cost' => $products[$i]['cost'],
										'price' => $products[$i]['price'],
										'retail' => sprintf("%01.2f",round($products[$i]['price']*(1+$this->taxpercent),2)),
										'stock' => $products[$i]['stock'],
										'serial' => $serial));

				$link_data['product_id'] = $products[$i]['con'];
				if ($this->boinv->check_perms($grants[$category[0]['owner']],PHPGW_ACL_EDIT) || $category[0]['owner'] == $this->boinv->account)
				{
					$link_data['menuaction'] = 'inv.uiinventory.edit_product';
					$this->t->set_var('edit',$GLOBALS['phpgw']->link('/index.php',$link_data));
					$this->t->set_var('lang_edit_entry',lang('Edit'));
				}

				$link_data['menuaction'] = 'inv.uiinventory.view_product';
				$this->t->set_var('view',$GLOBALS['phpgw']->link('/index.php',$link_data));

				$this->t->fp('list','listproducts',True);
			}

			if ($this->boinv->check_perms($grants[$category[0]['owner']],PHPGW_ACL_ADD) || $category[0]['owner'] == $this->boinv->account)
			{
				$link_data['menuaction'] = 'inv.uiinventory.edit_product';
				$link_data['product_id'] = '';
				$this->t->set_var('action','<form method="POST" action="' . $GLOBALS['phpgw']->link('/index.php',$link_data)
							. '"><input type="submit" value="' . lang('Add') .'"></form>');
			}
			else { $this->t->set_var('action',''); }

// ---------------------------- end list records -----------------------------------------

			$this->save_sessiondata();
			$this->t->pfp('out','listproducts_t',True);
		}

		function edit_product()
		{
			$values		= get_var('values',Array('POST'));
			$submit		= get_var('submit',Array('POST'));
			$referer	= get_var('referer',Array('POST'));

			if (!$submit)
			{
				$referer = get_var('HTTP_REFERER',Array('GLOBAL','SERVER'));
				$cat_id  = $this->cat_id;
			}

			if ($submit)
			{
				$error = $this->boinv->check_values($values);
				if (is_array($error))
				{
					$this->t->set_var('message',$GLOBALS['phpgw']->common->error_list($error));
				}
				else
				{
					$postval	= $this->boinv->save_product($values);
					$cat_id		= $values['cat_id'];
					$num		= $postval['num'];
					$retail		= $postval['retail'];

					if ($this->product_id)
					{
						$this->t->set_var('message',lang('Product %1 %2 has been updated !', $values['num'],$values['name']));
					}
					else
					{
						$this->t->set_var('message',lang('Product %1 %2 has been added !', $values['num'],$values['name']));
					}
				}
			}

			$this->display_app_header();

			$this->t->set_file(array('form' => 'product_form.tpl'));

			if ($this->product_id)
			{
				$this->t->set_block('form','add','addhandle');
				$this->t->set_block('form','edit','edithandle');
				$this->t->set_var('lang_action',lang('Edit product'));
				$hidden_vars = '<input type="hidden" name="product_id" value="' . $this->product_id . '">' . "\n";
				$values = $this->boinv->read_single_product();
				$retail = $this->boinv->get_retail($values['cat_id'],$values['price']);
				$num = $values['num'];
			}
			else
			{
				$this->t->set_block('form','add','addhandle');
				$this->t->set_block('form','edit','edithandle');
				$this->t->set_var('lang_action',lang('Add product'));
				$this->t->set_var('lang_choose',lang('Generate Product ID ?'));
				$this->t->set_var('choose','<input type="checkbox" name="values[choose]" value="True"' . ($values['choose'] == True?' checked':'') . '>');
			}

			$link_data = array
			(
				'menuaction'	=> 'inv.uiinventory.edit_product',
				'action'		=> $this->action,
				'product_id'	=> $this->product_id
			);

			if (isset($GLOBALS['phpgw_info']['user']['preferences']['common']['currency']))
			{
				$currency = $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'];
			}
			else
			{
				$this->t->set_var('error',lang('Please set your preferences for this application !'));
			}

			$owner = $this->boinv->cats->id2name($cat_id,'owner');
			$this->t->set_var('actionurl',$GLOBALS['phpgw']->link('/index.php',$link_data));

			$hidden_vars .= '<input type="hidden" name="referer" value="' . $referer . '">' . "\n";
			$this->t->set_var('hidden_vars',$hidden_vars);
			$this->t->set_var('num',$GLOBALS['phpgw']->strip_html($num));
			$this->t->set_var('short_name',$GLOBALS['phpgw']->strip_html($values['name']));
			$this->t->set_var('product_note',$GLOBALS['phpgw']->strip_html($values['note']));
			$this->t->set_var('serial',$GLOBALS['phpgw']->strip_html($values['serial']));
			$this->t->set_var('descr',$GLOBALS['phpgw']->strip_html($values['descr']));

			if (! ereg('http://',$values['url']))
			{
				$url = 'http://'. $values['url'];
			}
			else
			{
				$url = $GLOBALS['phpgw']->strip_html($values['url']);
			}

			$this->t->set_var('url',$url);

			if (! ereg('ftp://',$values['ftp']))
			{
				$ftp = 'ftp://'. $values['ftp'];
			}
			else
			{
				$ftp = $GLOBALS['phpgw']->strip_html($values['ftp']);
			}

			$this->t->set_var('ftp',$ftp);

			$this->t->set_var('cost',$values['cost']);
			$this->t->set_var('price',$values['price']);

			$this->t->set_var('retail',sprintf("%01.2f",$retail));

			$this->t->set_var('stock',$values['stock']);
			$this->t->set_var('mstock',$values['mstock']);

			$this->t->set_var('category_list',$this->boinv->cats->formatted_list(array('format' => 'select','type' => 'all','selected' => $cat_id)));
			$this->t->set_var('status_list',$this->boinv->select_status_list($values['status']));

			$this->t->set_var('dist_list',$this->boinv->select_dist_list($values['dist']));

			$this->t->set_var('room_list',$this->boinv->select_room_list($values['bin']));

			$this->t->set_var('currency',$currency);

			if (!$values['pdate'])
			{
				$pmonth	= date('m',time());
				$pday	= date('d',time());
				$pyear	= date('Y',time());
			}
			else
			{
				$pmonth = date('m',$values['pdate']);
				$pday	= date('d',$values['pdate']);
				$pyear	= date('Y',$values['pdate']);
			}

			$this->t->set_var('purchase_date_select',$GLOBALS['phpgw']->common->dateformatorder($this->sbox->getYears('values[pyear]',$pyear),
																						$this->sbox->getMonthText('values[pmonth]',$pmonth),
																						$this->sbox->getDays('values[pday]',$pday)));

			if (!$values['sdate'])
			{
				$smonth = 0;
				$sday = 0;
				$syear = 0;
			}
			else
			{
				$smonth = date('m',$values['sdate']);
				$sday = date('d',$values['sdate']);
				$syear = date('Y',$values['sdate']);
			}

			$this->t->set_var('selling_date_select',$GLOBALS['phpgw']->common->dateformatorder($this->sbox->getYears('values[syear]',$syear),
																						$this->sbox->getMonthText('values[smonth]',$smonth),
																						$this->sbox->getDays('values[sday]',$sday)));

			$this->t->set_var('done_action',$referer);

			$this->t->set_var('edithandle','');
			$this->t->set_var('addhandle','');

			$this->t->pfp('out','form');

			if ($this->product_id)
			{
				if ($this->boinv->check_perms($grants[$owner],PHPGW_ACL_DELETE) || $owner == $this->boinv->account)
				{
					$link_data['menuaction'] = 'inv.uiinventory.delete_product';
					$this->t->set_var('delete','<form method="POST" action="' . $GLOBALS['phpgw']->link('/index.php',$link_data)
										. '"><input type="submit" value="' . lang('Delete') .'"></form>');
				}
				$this->t->pfp('edithandle','edit');
			}
			else
			{
				$this->t->pfp('addhandle','add');
			}
		}

		function view_product()
		{
			$submit  = get_var('submit',Array('POST'));
			$referer = get_var('referer',Array('POST'));

			if(!$submit)
			{
				$referer = get_var('HTTP_REFERER',Array('GLOBAL','SERVER'));
				$cat_id  = $this->cat_id;
			}

			if($submit)
			{
				Header('Location: ' . $referer);
			}

			if(!$this->product_id)
			{
				Header('Location: ' . $referer);
			}

			$this->display_app_header();

			$this->t->set_file(array('view' => 'view_product.tpl'));

			$nopref = $this->boinv->check_prefs();
			if (is_array($nopref))
			{
				$this->t->set_var('pref_message',$GLOBALS['phpgw']->common->error_list($nopref));
			}
			else
			{
				$prefs = $this->boinv->get_prefs();
			}
	
			$this->t->set_var('lang_action',lang('View product'));
			$this->t->set_var('hidden_vars','<input type="hidden" name="referer" value="' . $referer . '">');
			$this->t->set_var('currency',$prefs['currency']);  

			$values = $this->boinv->read_single_product();

			$this->t->set_var('cat_name',$GLOBALS['phpgw']->strip_html($this->boinv->cats->id2name($values['cat_id'],'name')));

			$abid = $values['dist'];
			if (!$abid) { $dist = '&nbsp;'; }
			else
			{
				$dist = $this->boinv->read_single_contact($abid);
			}
			$this->t->set_var('dist',$GLOBALS['phpgw']->strip_html($dist[0]['org_name']));

			$this->t->set_var('status',lang($this->boinv->return_value($values['status'])));

			$this->t->set_var('num',$GLOBALS['phpgw']->strip_html($values['num']));
			$this->t->set_var('name',$GLOBALS['phpgw']->strip_html($values['name']));
			$this->t->set_var('descr',$GLOBALS['phpgw']->strip_html($values['descr']));
			$this->t->set_var('serial',$GLOBALS['phpgw']->strip_html($values['serial']));

			$url = $values['url'];
			if (!$url || ($url == 'http://')) { $url = ''; }
			else
			{ 
				$url = $GLOBALS['phpgw']->strip_html($values['url']);
				if (! ereg('http://',$url)) { $url = 'http://'. $url; }
			}
			$this->t->set_var('url',$url);

			$ftp = $values['ftp'];
			if (!$ftp || ($ftp == 'ftp://')) { $ftp = ''; }
			else
			{ 
				$ftp = $GLOBALS['phpgw']->strip_html($values['ftp']);
				if (! ereg('ftp://',$ftp)) { $ftp = 'ftp://'. $ftp; }
			}
			$this->t->set_var('url',$url);

			$this->t->set_var('cost',$values['cost']);
			$this->t->set_var('price',$values['price']);
			$this->t->set_var('stock',$values['stock']);
			$this->t->set_var('mstock',$values['mstock']);

			$this->t->set_var('retail',sprintf("%01.2f",$this->boinv->get_retail($values['cat_id'],$values['price'])));

			if ($values['pdate'] != 0)
			{
				$pdate = $values['pdate'] + (60*60) * $GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'];
				$pdateout = $GLOBALS['phpgw']->common->show_date($pdate,$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			}

			$this->t->set_var('pdate',$pdateout);

			if ($values['sdate'] != 0)
			{
				$sdate = $values['sdate'] + (60*60) * $GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'];
				$sdateout = $GLOBALS['phpgw']->common->show_date($sdate,$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			}

			$this->t->set_var('sdate',$sdateout);

			$this->t->set_var('done_action',$referer);
			$this->t->pfp('out','view');
		}

		function delete_product()
		{
//			$product_id = get_var('product_id',Array('POST'));
			$confirm    = get_var('confirm',Array('POST'));

			if (!$confirm)
			{
				$referer = get_var('HTTP_REFERER',Array('GLOBAL','SERVER'));
			}

			$link_data = array
			(
				'menuaction'		=> 'inv.uiinventory.list_products',
				'action'			=> 'subproducts'
			);

			if ($confirm)
			{
				$this->boinv->delete(array('action' => 'pro','product_id' => $this->product_id));
				Header('Location: ' . $GLOBALS['phpgw']->link('/index.php',$link_data));
			}

			if (!$this->product_id)
			{
				Header('Location: ' . $referer);
			}

			$this->display_app_header();

			$this->t->set_file(array('delete' => 'delete.tpl'));

			$hidden_vars = '<input type="hidden" name="product_id" value="' . $this->product_id . '">' . "\n"
							. '<input type="hidden" name="referer" value="' . $referer . '">' . "\n";

			$this->t->set_var('deleteheader',lang('Are you sure you want to delete this entry ?'));
			$this->t->set_var('nolink',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$this->t->set_var('lang_no',lang('No'));
        	$this->t->set_var('hidden_vars',$hidden_vars);
        	$this->t->set_var('action_url',$GLOBALS['phpgw']->link('/index.php','menuaction=inv.uiinventory.delete_product&product_id=' . $this->product_id));
        	$this->t->set_var('lang_yes',lang('Yes'));
			$this->t->pfp('out','delete');
		}

		function list_status()
		{
			$lm = get_var('lm',Array('POST'));

			$link_data = array
			(
				'menuaction'	=> 'inv.uiinventory.list_status'
			);

			$this->t->set_file(array('status_list_t' => 'liststatus.tpl'));
			$this->t->set_block('status_list_t','status_list','list');  

			$this->display_app_header();

			$this->t->set_var('lang_action',lang('Product status list'));

			if ($lm == 'statusupdated')
			{
				$this->t->set_var('message',lang('Product status has been updated !'));
			}
			if ($lm == 'statusadded')
			{
				$this->t->set_var('message',lang('Product status has been added !'));
			}

			$sta = $this->boinv->read_status();

			for ($i=0;$i<count($sta);$i++)
			{
				$this->nextmatchs->template_alternate_row_color($this->t);
				$status_name = $GLOBALS['phpgw']->strip_html($sta[$i]['status_name']);
				$this->t->set_var('name',lang($status_name));

				if ($status_name != 'archive')
				{
					$link_data['menuaction']	= 'inv.uiinventory.edit_status';
					$link_data['status_id']		= $sta[$i]['status_id'];
					$this->t->set_var('edit',$GLOBALS['phpgw']->link('/index.php',$link_data));
					$this->t->set_var('lang_edit_entry',lang('Edit'));

					$link_data['menuaction']	= 'inv.uiinventory.delete_status';
					$this->t->set_var('delete',$GLOBALS['phpgw']->link('/index.php',$link_data));
					$this->t->set_var('lang_delete_entry',lang('Delete'));
				}
				else
				{
					$this->t->set_var('edit','&nbsp;');
					$this->t->set_var('delete','&nbsp;');
					$this->t->set_var('lang_edit_entry','');
					$this->t->set_var('lang_delete_entry','');
				}

				$this->t->fp('list','status_list',True);
			}

			$this->t->set_var('add_action',$GLOBALS['phpgw']->link('/index.php','menuaction=inv.uiinventory.add_status'));
			$this->t->pfp('out','status_list_t',True);                                                                                                                                              
		}

		function add_status()
		{
			$status_name = get_var('status_name',Array('POST'));
			$this->t->set_file(array('form' => 'status_form.tpl'));

			$this->display_app_header();

			if(get_var('submit',Array('POST')))
			{
				$error = $this->boinv->check_values(array('action' => 'status','status_name' => $status_name));
				if (is_array($error))
				{
					$this->t->set_var('message',$GLOBALS['phpgw']->common->error_list($error));
				}
				else
				{
					$this->boinv->save_status(array('status_name' => $status_name));
					Header('Location: ' . $GLOBALS['phpgw']->link('/index.php','menuaction=inv.uiinventory.list_status&lm=statusadded'));
					$GLOBALS['phpgw']->common->phpgw_exit();
				}
			}

			$this->t->set_var('status_name',$status_name);
			$this->t->set_var('lang_action',lang('Add product status'));
			$this->t->set_var('form_action',$GLOBALS['phpgw']->link('/index.php','menuaction=inv.uiinventory.add_status'));
			$this->t->pfp('out','form');
		}

		function edit_status()
		{
			$status_id   = get_var('status_id',Array('GET','POST'));
			$status_name = $get_var('status_name',Array('POST'));

			$this->t->set_file(array('form' => 'status_form.tpl'));
			$this->display_app_header();

			if(get_var('submit',Array('POST')))
			{
				$error = $this->boinv->check_values(array('action' => 'status','status_name' => $status_name,'status_id' => $status_id));
				if (is_array($error))
				{
					$this->t->set_var('message',$GLOBALS['phpgw']->common->error_list($error));
				}
				else
				{
					$this->boinv->save_status(array('status_name' => $status_name,'status_id' => $status_id));
					Header('Location: ' . $GLOBALS['phpgw']->link('/index.php','menuaction=inv.uiinventory.list_status&lm=statusupdated'));
					$GLOBALS['phpgw']->common->phpgw_exit();
				}
			}

			$this->t->set_var('status_name',$GLOBALS['phpgw']->strip_html($this->boinv->return_value($status_id)));
			$this->t->set_var('status_id',$status_id);
			$this->t->set_var('lang_action','Edit product status');
			$this->t->set_var('form_action',$GLOBALS['phpgw']->link('index.php','menuaction=inv.uiinventory.edit_status&status_id=' . $status_id));
			$this->t->pfp('out','form');
		}

		function delete_status()
		{
			$status_id = get_var('status_id',Array('GET','POST'));

			$link_data = array
			(
				'menuaction'	=> 'inv.uiinventory.list_status',
				'status_id'		=> $status_id
			);

			if(get_var('confirm',Array('POST')))
			{
				$this->boinv->delete(array('action' => 'status', 'status_id' => $status_id));
				Header('Location: ' . $GLOBALS['phpgw']->link('/index.php',$link_data));
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			$this->t->set_file(array('status_delete' => 'delete.tpl'));

			$this->display_app_header();

			$this->t->set_var('deleteheader',lang('Are you sure you want to delete this entry ?'));
			$this->t->set_var('hidden_vars','<input type="hidden" name="status_id" value="' . $status_id . '">');
			$this->t->set_var('nolink',$GLOBALS['phpgw']->link('/index.php',$link_data));
        	$this->t->set_var('lang_no',lang('No'));

			$link_data['menuaction'] = 'inv.uiinventory.delete_status';
			$this->t->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$this->t->set_var('lang_yes',lang('Yes'));
			$this->t->pfp('out','status_delete');
		}
	}
?>
