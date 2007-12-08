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
	* @subpackage location
 	* @version $Id: class.uigab.inc.php 18358 2007-11-27 04:43:37Z skwashd $
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_uigab
	{
		var $grants;
		var $cat_id;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;
		var $part_of_town_id;
		var $sub;
		var $currentapp;

		var $public_functions = array
		(
			'index'  	=> True,
			'list_detail'  	=> True,
			'view' 		=> True,
			'edit'   	=> True,
			'delete' 	=> True,
			'excel'  	=> True
		);

		function property_uigab()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;
		//	$this->currentapp			= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->nextmatchs			= CreateObject('phpgwapi.nextmatchs');
			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo					= CreateObject('property.bogab',True);
			$this->bocommon				= CreateObject('property.bocommon');
			$this->menu					= CreateObject('property.menu');
			$this->bolocation				= CreateObject('property.bolocation');

			$this->config				= CreateObject('phpgwapi.config');
			$this->acl 				= CreateObject('phpgwapi.acl');
			$this->acl_location		= '.location';
			$this->acl_read 			= $this->acl->check('.location',1);
			$this->acl_add 				= $this->acl->check('.location',2);
			$this->acl_edit 			= $this->acl->check('.location',4);
			$this->acl_delete 			= $this->acl->check('.location',8);

			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->filter				= $this->bo->filter;
			$this->cat_id				= $this->bo->cat_id;
			$this->allrows				= $this->bo->allrows;
			$this->gab_insert_level			= $this->bo->gab_insert_level;

			$this->menu->sub			='location';
		}

		function save_sessiondata()
		{
			$data = array
			(
				'start'		=> $this->start,
				'query'		=> $this->query,
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'filter'	=> $this->filter,
				'cat_id'	=> $this->cat_id,
				'allrows'	=> $this->allrows
			);
			$this->bo->save_sessiondata($data);
		}

		function excel()
		{
			$address 		= phpgw::get_var('address');
			$check_payments 	= phpgw::get_var('check_payments', 'bool');
			$location_code 		= phpgw::get_var('location_code');
			$gaards_nr 		= phpgw::get_var('gaards_nr', 'int');
			$bruksnr 		= phpgw::get_var('bruksnr', 'int');
			$feste_nr 		= phpgw::get_var('feste_nr', 'int');
			$seksjons_nr 		= phpgw::get_var('seksjons_nr', 'int');


			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop','perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$gab_list = $this->bo->read($location_code,$gaards_nr,$bruksnr,$feste_nr,$seksjons_nr,$address,$check_payments,$allrows=True);

			$payment_date = $this->bo->payment_date;

			$i=0;
			
			while (is_array($gab_list) && list(,$gab) = each($gab_list))
			{
				if(is_array($payment_date))
				{
					reset($payment_date);
				}
				$value_gaards_nr	= substr($gab['gab_id'],4,5);
				$value_bruks_nr		= substr($gab['gab_id'],9,4);
				$value_feste_nr		= substr($gab['gab_id'],13,4);
				$value_seksjons_nr	= substr($gab['gab_id'],17,3);

				$content[] = array
				(
					'owner'				=> lang($gab['owner']),
					'hits'				=> $gab['hits'],
					'address'			=> $gab['address'],
					'gaards_nr'			=> $value_gaards_nr,
					'bruks_nr'			=> $value_bruks_nr,
					'feste_nr'			=> $value_feste_nr,
					'seksjons_nr'			=> $value_seksjons_nr,
					'location_code'			=> $gab['location_code'],
				);

				while (is_array($payment_date) && list(,$date) = each($payment_date))
				{
					$content[$i][$date] = $gab['payment'][$date];
				}
				
				$i++;
			}

			//_debug_array($content);
			$table_header['name'] = array('owner','hits','address','gaards_nr','bruks_nr','feste_nr','seksjons_nr','location_code');
			$table_header['descr'] = array(lang('owner'),lang('hits'),lang('address'),'gaards_nr','bruks_nr','feste_nr','seksjons_nr','location_code');			

			if(is_array($payment_date))
			{
				reset($payment_date);
			}
			
			while (is_array($payment_date) && list(,$date) = each($payment_date))
			{
				$table_header['name'][] = $date;
				$table_header['descr'][] = $date;
			}

			$this->bocommon->excel($content,$table_header['name'],$table_header['descr'],array());
		}


		function index()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('gab',
										'menu',
										'nextmatchs'));

			$address 		= phpgw::get_var('address');
			$check_payments 	= phpgw::get_var('check_payments', 'bool');
			$location_code 		= phpgw::get_var('location_code');
			$gaards_nr 		= phpgw::get_var('gaards_nr', 'int');
			$bruksnr 		= phpgw::get_var('bruksnr', 'int');
			$feste_nr 		= phpgw::get_var('feste_nr', 'int');
			$seksjons_nr 		= phpgw::get_var('seksjons_nr', 'int');
			$reset_query 		= phpgw::get_var('reset_query', 'bool');

			if($reset_query)
			{
				unset($address);
				unset($check_payments);
				unset($location_code);
				unset($gaards_nr);
				unset($bruksnr);
				unset($feste_nr);
				unset($seksjons_nr);
			}

			$links = $this->menu->links('gab');

			$gab_list = $this->bo->read($location_code,$gaards_nr,$bruksnr,$feste_nr,$seksjons_nr,$address,$check_payments);

			if($this->acl_read)
			{
				$text_view	= lang('view');
				$lang_view_statustext	= lang('view gab detail');
			}

			$config		= CreateObject('phpgwapi.config','property');

			$config->read_repository();
			
			$link_to_map = (isset($config->config_data['map_url'])?$config->config_data['map_url']:'');

			if($link_to_map)
			{
				$text_map=lang('Map');
				$lang_map_statustext	= lang('View map');
			}
			$link_to_gab = (isset($config->config_data['gab_url'])?$config->config_data['gab_url']:'');
			if($link_to_gab)
			{
				$text_gab=lang('GAB');
				$lang_gab_statustext	= lang('View gab-info');
			}

			$payment_date = $this->bo->payment_date;
			
			$i=0;
			
			$content=array();
			while (is_array($gab_list) && list(,$gab) = each($gab_list))
			{
				if(is_array($payment_date))
				{
					reset($payment_date);
				}
				$value_gaards_nr	= substr($gab['gab_id'],4,5);
				$value_bruks_nr		= substr($gab['gab_id'],9,4);
				$value_feste_nr		= substr($gab['gab_id'],13,4);
				$value_seksjons_nr	= substr($gab['gab_id'],17,3);

				$content[] = array
				(
					'owner'				=> lang($gab['owner']),
					'hits'				=> $gab['hits'],
					'address'			=> $gab['address'],
					'gaards_nr'			=> $value_gaards_nr,
					'bruks_nr'			=> $value_bruks_nr,
					'feste_nr'			=> $value_feste_nr,
					'seksjons_nr'			=> $value_seksjons_nr,
					'location_code'			=> $gab['location_code'],
					'link_view'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'property.uigab.list_detail','gab_id'=>$gab['gab_id'])),
					'lang_view_statustext'		=> $lang_view_statustext,
					'text_view'			=> $text_view,
					'link_map'			=> $link_to_map . '?maptype=Eiendomskart&gnr=' . (int)$value_gaards_nr . '&bnr=' . (int)$value_bruks_nr . '&fnr=' . (int)$value_feste_nr,
					'lang_map_statustext'		=> $lang_map_statustext,
					'text_map'			=> $text_map,
					'link_gab'			=> $link_to_gab . '?type=eiendom&Gnr=' . (int)$value_gaards_nr . '&Bnr=' . (int)$value_bruks_nr . '&Fnr=' . (int)$value_feste_nr . '&Snr=' . (int)$value_seksjons_nr,
					'lang_gab_statustext'		=> $lang_gab_statustext,
					'text_gab'			=> $text_gab
				);
				while (is_array($payment_date) && list(,$date) = each($payment_date))
				{
					$content[$i]['payment'][] = array('amount' => $gab['payment'][$date]);
				}
				
				$i++;
			}

			$table_header[] = array
			(
				'sort_gab_id'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=>	'gab_id',
											'order'	=>	$this->order,
											'extra'	=> array('menuaction'=> 'property.uigab.index',
														'cat_id'	=>$this->cat_id,
													//	'district_id'	=> $this->district_id,
														'filter'	=>$this->filter,
														'allrows'	=> $this->allrows,
														'query'		=>$this->query,
														'location_code'	=>$location_code,
														'gaards_nr'	=>$gaards_nr,
														'bruksnr'	=>$bruksnr,
														'feste_nr'	=>$feste_nr,
														'seksjons_nr'	=>$seksjons_nr,
														'address'	=>$address,
														'check_payments'	=>$check_payments)
										)),
				'lang_gab'	=> lang('gab'),
				'sort_hits'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=>	'hits',
											'order'	=>	$this->order,
											'extra'	=> array('menuaction'=> 'property.uigab.index',
														'cat_id'	=>$this->cat_id,
													//	'district_id'	=> $this->district_id,
														'filter'	=>$this->filter,
														'allrows'	=> $this->allrows,
														'query'		=>$this->query,
														'location_code'	=>$location_code,
														'gaards_nr'	=>$gaards_nr,
														'bruksnr'	=>$bruksnr,
														'feste_nr'	=>$feste_nr,
														'seksjons_nr'	=>$seksjons_nr,
														'address'	=>$address,
														'check_payments'	=>$check_payments)
										)),
				'sort_location_code'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=>	'location_code',
											'order'	=>	$this->order,
											'extra'	=> array('menuaction'=> 'property.uigab.index',
														'cat_id'	=>$this->cat_id,
													//	'district_id'	=> $this->district_id,
														'filter'	=>$this->filter,
														'allrows'	=> $this->allrows,
														'query'		=>$this->query,
														'location_code'	=>$location_code,
														'gaards_nr'	=>$gaards_nr,
														'bruksnr'	=>$bruksnr,
														'feste_nr'	=>$feste_nr,
														'seksjons_nr'	=>$seksjons_nr,
														'address'	=>$address,
														'check_payments'	=>$check_payments)
										)),
				'lang_owner'		=> lang('owner'),
				'lang_hits'		=> lang('hits'),
				'lang_address'		=> lang('Address'),
				'lang_gaards_nr'	=> lang('gaards nr'),
				'lang_bruksnr'		=> lang('bruks nr'),
				'lang_feste_nr'		=> lang('Feste nr'),
				'lang_seksjons_nr'	=> lang('Seksjons nr'),
				'lang_location_code'=> lang('Location'),
				'lang_view'			=> lang('view'),
				'lang_map'			=> lang('map'),
				);

			$colspan = count($table_header[0]);
			
			if(is_array($payment_date))
			{
				reset($payment_date);
			}
			
			while (is_array($payment_date) && list(,$date) = each($payment_date))
			{
				$table_header[0]['payment_header'][] = array('header'=>$date);
				$colspan++;
			}

			$search_field_header[] = array
			(
				'lang_property'		=> lang('Property ID'),
				'lang_gaards_nr'	=> lang('gaards nr'),
				'lang_bruksnr'		=> lang('bruks nr'),
				'lang_feste_nr'		=> lang('Feste nr'),
				'lang_seksjons_nr'	=> lang('Seksjons nr')
				);

			if($this->acl_add)
			{
				$table_add[] = array
				(
					'lang_add'		=> lang('add'),
					'lang_add_statustext'	=> lang('add a gab'),
					'add_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'property.uigab.edit', 'from'=>'index'))
				);
			}

			$link_data = array
			(
				'menuaction'	=> 'property.uigab.index',
					'sort'		=>$this->sort,
					'order'		=>$this->order,
					'cat_id'	=>$this->cat_id,
					'filter'	=>$this->filter,
					'location_code'	=>$location_code,
					'gaards_nr'	=>$gaards_nr,
					'bruksnr'	=>$bruksnr,
					'feste_nr'	=>$feste_nr,
					'seksjons_nr'	=>$seksjons_nr,
					'address'	=>$address,
					'check_payments'	=>$check_payments
			);


			$link_excel = array
			(
				'menuaction'	=> 'property.uigab.excel',
					'sort'		=>$this->sort,
					'order'		=>$this->order,
					'cat_id'	=>$this->cat_id,
					'filter'	=>$this->filter,
					'location_code'	=>$location_code,
					'gaards_nr'	=>$gaards_nr,
					'bruksnr'	=>$bruksnr,
					'feste_nr'	=>$feste_nr,
					'seksjons_nr'	=>$seksjons_nr,
					'address'	=>$address,
					'check_payments'	=>$check_payments
			);


			if(!$this->allrows)
			{
				$record_limit	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$record_limit	= $this->bo->total_records;
			}

			$GLOBALS['phpgw']->js->validate_file('overlib','overlib','property');

			$data = array
			(
				'lang_excel'				=> 'excel',
				'link_excel'				=> $GLOBALS['phpgw']->link('/index.php',$link_excel),
				'lang_excel_help'			=> lang('Download table to MS Excel'),
				
				'search_field_header'			=> $search_field_header,
				'links'					=> $links,
				'allrows'				=> $this->allrows,
				'allow_allrows'				=> true,
				'start_record'				=> $this->start,
				'record_limit'				=> $record_limit,
				'num_records'				=> count($gab_list),
				'all_records'				=> $this->bo->total_records,
				'link_url'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'				=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'select_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'address'				=> $address,
				'location_code'				=> $location_code,
				'gaards_nr'				=> $gaards_nr,
				'bruksnr'				=> $bruksnr,
				'feste_nr'				=> $feste_nr,
				'seksjons_nr'				=> $seksjons_nr,
				'lang_search_location_statustext'	=> lang('search by location_code. To show all entries, empty all fields and press the SUBMIT button again'),
				'lang_search_gaard_statustext'		=> lang('search by gaards nr. To show all entries, empty all fields and press the SUBMIT button again'),
				'lang_search_bruk_statustext'		=> lang('search by bruk. To show all entries, empty all fields and press the SUBMIT button again'),
				'lang_search_feste_statustext'		=> lang('search by feste. To show all entries, empty all fields and press the SUBMIT button again'),
				'lang_search_seksjon_statustext'	=> lang('search by seksjon. To show all entries, empty all fields and press the SUBMIT button again'),
				'lang_searchbutton_statustext'		=> lang('Submit the search string'),
				'lang_reset_query_statustext'		=> lang('check to reset the query'),
				'lang_search'				=> lang('search'),
				'table_header_gab'			=> $table_header,
				'values_gab'				=> $content,
				'table_add'				=> $table_add,
				'lang_check_payments'			=> lang('check payments'),
				'lang_check_payments_statustext'	=> lang('List payments history'),
				'value_check_payments'			=> $check_payments,
				'colspan'				=> $colspan
			);

			$appname		= lang('gab');
			$function_msg	= lang('list gab');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_gab' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
			$this->save_sessiondata();
		}

		function list_detail()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('gab','values','table_header',
										'menu',
										'nextmatchs'));

			$gab_id 		= phpgw::get_var('gab_id');

			$links = $this->menu->links('gab');

			$gab_list = $this->bo->read_detail($gab_id);

			$uicols	= $this->bo->uicols;

			$j=0;
			while (is_array($gab_list) && list(,$gab_entry) = each($gab_list))
			{
				for ($k=0;$k<count($uicols['name']);$k++)
				{
					if($uicols['input_type'][$k]!='hidden')
					{
						$content[$j]['row'][$k]['value'] 			= $gab_entry[$uicols['name'][$k]];
						$content[$j]['row'][$k]['name'] 			= $uicols['name'][$k];
					}
				}

				if(!$lookup)
				{
					if($this->acl_read)
					{
						$content[$j]['row'][$k]['statustext']			= lang('view the gab');
						$content[$j]['row'][$k]['text']					= lang('view');
						$content[$j]['row'][$k]['link']					= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uigab.view', 'gab_id' => $gab_entry['gab_id'], 'location_code'=>$gab_entry['location_code']));
						$k++;
					}

					if($this->acl_edit)
					{
						$content[$j]['row'][$k]['statustext']			= lang('edit the gab');
						$content[$j]['row'][$k]['text']					= lang('edit');
						$content[$j]['row'][$k]['link']					= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uigab.edit', 'gab_id'=> $gab_entry['gab_id'], 'location_code'=>$gab_entry['location_code'], 'from'=>'list_detail'));
						$k++;
					}

					if($this->acl_delete)
					{
						$content[$j]['row'][$k]['statustext']			= lang('delete the gab');
						$content[$j]['row'][$k]['text']					= lang('delete');
						$content[$j]['row'][$k]['link']					= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uigab.delete', 'gab_id'=> $gab_entry['gab_id'], 'location_code'=> $gab_entry['location_code']));
						$k++;
					}
				}

				$j++;
			}

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
											'var'	=>	'location_code',
											'order'	=>	$this->order,
											'extra'		=> array('menuaction'	=> 'property.uigab.index',
																	'type_id'	=>$type_id,
																	'query'		=>$this->query,
																	'lookup'	=>$lookup,
																	'district_id'	=> $this->district_id,
																	'cat_id'	=>$this->cat_id)
										));
					}
					if($uicols['name'][$i]=='gab_id')
					{
						$table_header[$i]['sort_link']	=true;
						$table_header[$i]['sort'] 		= $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=>	'gab_id',
											'order'	=>	$this->order,
											'extra'		=> array('menuaction'	=> 'property.uigab.index',
																	'type_id'	=>$type_id,
																	'query'		=>$this->query,
																	'lookup'	=>$lookup,
																	'district_id'	=> $this->district_id,
																	'cat_id'	=>$this->cat_id)
										));
					}
					if($uicols['name'][$i]=='address')
					{
						$table_header[$i]['sort_link']	=true;
						$table_header[$i]['sort'] 		= $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=>	'address',
											'order'	=>	$this->order,
											'extra'		=> array('menuaction'	=> 'property.uigab.index',
																	'type_id'	=>$type_id,
																	'query'		=>$this->query,
																	'lookup'	=>$lookup,
																	'district_id'	=> $this->district_id,
																	'cat_id'	=>$this->cat_id)
										));
					}
				}
			}

			if(!$lookup)
			{
				if($this->acl_read)
				{
					$table_header[$i]['width'] 			= '5%';
					$table_header[$i]['align'] 			= 'center';
					$table_header[$i]['header']			= lang('view');
					$i++;
				}
				if($this->acl_edit)
				{
					$table_header[$i]['width'] 			= '5%';
					$table_header[$i]['align'] 			= 'center';
					$table_header[$i]['header']			= lang('edit');
					$i++;
				}
				if($this->acl_delete)
				{
					$table_header[$i]['width'] 			= '5%';
					$table_header[$i]['align'] 			= 'center';
					$table_header[$i]['header']			= lang('delete');
					$i++;
				}
			}
			else
			{
				$table_header[$i]['width'] 			= '5%';
				$table_header[$i]['align'] 			= 'center';
				$table_header[$i]['header']		= lang('select');
			}

//_debug_array($content);
			if($this->acl_add)
			{
				$table_add[] = array
				(
					'lang_add'		=> lang('add'),
					'lang_add_statustext'	=> lang('add a gab'),
					'add_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uigab.edit', 'from' => 'list_detail', 'gab_id'=> $gab_id, 'new'=>true))

				);
			}


			$table_done[] = array
			(
				'lang_done'		=> lang('done'),
				'lang_done_statustext'	=> lang('back to list'),
				'done_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uigab.index'))
			);

			$link_data = array
			(
				'menuaction'	=> 'property.uigab.list_detail',
						'sort'			=>$this->sort,
						'order'			=>$this->order,
						'cat_id'		=>$this->cat_id,
						'filter'		=>$this->filter,
						'gab_id'		=>$gab_id
			);

			if(!$this->allrows)
			{
				$record_limit	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$record_limit	= $this->bo->total_records;
			}

			$data = array
			(
				'gaards_nr'						=> substr($gab_id,4,5),
				'bruks_nr'						=> substr($gab_id,9,4),
				'feste_nr'						=> substr($gab_id,13,4),
				'seksjons_nr'					=> substr($gab_id,17,3),

				'value_owner'					=> lang($gab_list[0]['owner']),
				'lang_owner'					=> lang('owner'),
				'lang_gaards_nr'				=> lang('gaards nr'),
				'lang_bruksnr'					=> lang('bruks nr'),
				'lang_feste_nr'					=> lang('Feste nr'),
				'lang_seksjons_nr'				=> lang('Seksjons nr'),

				'links'							=> $links,
				'allrows'						=> $this->allrows,
				'allow_allrows'					=> true,
				'start_record'					=> $this->start,
				'record_limit'					=> $record_limit,
				'num_records'					=> count($gab_list),
				'all_records'					=> $this->bo->total_records,
				'link_url'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'						=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'table_header'					=> $table_header,
				'values'						=> $content,
				'table_add'						=> $table_add,
				'table_done'					=> $table_done
			);

			$appname		= lang('gab');
			$function_msg	= lang('list gab detail');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_gab_detail' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
			$this->save_sessiondata();
		}


		function edit()
		{
			if(!$this->acl_add && !$this->acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop','perm'=>2, 'acl_location'=> $this->acl_location));
			}

			$from 			= phpgw::get_var('from');
			$new 			= phpgw::get_var('new', 'bool');
			$gab_id 		= phpgw::get_var('gab_id');
			$location_code 	= phpgw::get_var('location_code');
			$values			= phpgw::get_var('values');

			$GLOBALS['phpgw']->xslttpl->add_file(array('gab'));

			if(!$values && $location_code)
			{
				$values['location_data'] = $this->bolocation->read_single($location_code,$values['extra']);
			}

//_debug_array($values);

			if ($values['save'])
			{
				$insert_record 		= $GLOBALS['phpgw']->session->appsession('insert_record','property');
				$values = $this->bocommon->collect_locationdata($values,$insert_record);

				$values['gab_id'] = $gab_id;

				$values['location_code'] = $location_code;

				if(!$values['location_code'] && !$values['location'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a location !'));
				}

				if((count($values['location']) < $this->gab_insert_level) && !$values['propagate'] && !$values['location_code'])
				{
					$receipt['error'][] = array('msg'=>lang('Either select propagate - or choose location level %1 !',$this->gab_insert_level));
				}

//_debug_array($values);
				if(!$receipt['error'])
				{
					$receipt 		= $this->bo->save($values);
					$location_code	= $receipt['location_code'];
					$gab_id 		= $receipt['gab_id'];
//_debug_array($receipt);
				}
			}

			if ($gab_id && !$new)
			{
				$values = $this->bo->read_single($gab_id,$location_code);
			}
			if ($values['location_code'])
			{
				$function_msg = lang('Edit gab');
				$action='edit';
				$lookup_type ='view';

			}
			else
			{
				$function_msg = lang('Add gab');
				$action='add';
				$lookup_type ='form';
			}

			if ($values['cat_id'])
			{
				$this->cat_id = $values['cat_id'];
			}


			if($values['location_data'])
			{
				$type_id	= count(explode('-',$values['location_code']));
			}
			else
			{
				$type_id	= $this->gab_insert_level;
			}
			$location_data=$this->bolocation->initiate_ui_location(array(
						'values'		=> $values['location_data'],
						'type_id'		=> $type_id,
						'no_link'		=> False, // disable lookup links for location type less than type_id
						'tenant'		=> False,
						'lookup_type'	=> $lookup_type
						));

			$link_data = array
			(
				'menuaction'	=> 'property.uigab.edit',
				'gab_id'			=> $gab_id,
				'location_code'		=> $location_code,
				'from'				=> $from
			);


			
			$done_data = array('menuaction'=> 'property.uigab.'.$from);
			if($from=='list_detail')
			{
				$done_data['gab_id'] = $gab_id;
			}

			$kommune_nr		= substr($gab_id,0,4);
			if(!$kommune_nr > 0)
			{
				$this->config->read_repository();
				$kommune_nr= $this->config->config_data['default_municipal'];
			}

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'value_owner'					=> $values['owner'],
				'lang_owner'					=> lang('owner'),
				'kommune_nr'					=> $kommune_nr,
				'gaards_nr'						=> substr($gab_id,4,5),
				'bruks_nr'						=> substr($gab_id,9,4),
				'feste_nr'						=> substr($gab_id,13,4),
				'seksjons_nr'					=> substr($gab_id,17,3),

				'lang_kommune_nr'				=> lang('kommune nr'),
				'lang_gaards_nr'				=> lang('gaards nr'),
				'lang_bruksnr'					=> lang('bruks nr'),
				'lang_feste_nr'					=> lang('Feste nr'),
				'lang_seksjons_nr'				=> lang('Seksjons nr'),

				'action'					=> $action,
				'lookup_type'					=> $lookup_type,
				'location_data'					=> $location_data,
				'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'					=> $GLOBALS['phpgw']->link('/index.php',$done_data),
				'lang_save'					=> lang('save'),
				'lang_done'					=> lang('done'),

				'lang_propagate'				=> lang('propagate'),
				'lang_propagate_statustext'		=> lang('check to inherit from this location'),

				'lang_remark_statustext'		=> lang('Enter a remark for this entity'),
				'lang_remark'					=> lang('remark'),
				'value_remark'					=> $values['remark'],
				'lang_done_statustext'			=> lang('Back to the list'),
				'lang_save_statustext'			=> lang('Save the gab')
			);

			$appname		= lang('gab');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function delete()
		{
			if(!$this->acl_delete)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 8, 'acl_location'=> $this->acl_location));
			}

			$gab_id = phpgw::get_var('gab_id');
			$location_code = phpgw::get_var('location_code');
			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
			(
				'menuaction' => 'property.uigab.list_detail',
					'gab_id' => $gab_id
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete($gab_id,$location_code);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'		=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uigab.delete', 'gab_id'=> $gab_id, 'location_code'=>$location_code)),
				'lang_confirm_msg'	=> lang('do you really want to delete this entry'),
				'lang_yes'		=> lang('yes'),
				'lang_yes_statustext'	=> lang('Delete the entry'),
				'lang_no_statustext'	=> lang('Back to the list'),
				'lang_no'		=> lang('no')
			);

			$appname			= lang('gab');
			$function_msg			= lang('delete gab at:') . ' ' . $location_code;

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

			$gab_id 		= phpgw::get_var('gab_id');
			$location_code 	= phpgw::get_var('location_code');

			$GLOBALS['phpgw']->xslttpl->add_file(array('gab'));

//_debug_array($values);


			if ($gab_id && !$new)
			{
				$values = $this->bo->read_single($gab_id,$location_code);
			}

			$function_msg = lang('View gab');
			$location_type ='view';


			$location_data=$this->bolocation->initiate_ui_location(array(
						'values'		=> $values['location_data'],
						'type_id'		=> count(explode('-',$values['location_code'])),
						'no_link'		=> False, // disable lookup links for location type less than type_id
						'tenant'		=> False,
						'lookup_type'	=> 'view'
						));


			$data = array
			(
				'kommune_nr'					=> substr($gab_id,0,4),
				'gaards_nr'						=> substr($gab_id,4,5),
				'bruks_nr'						=> substr($gab_id,9,4),
				'feste_nr'						=> substr($gab_id,13,4),
				'seksjons_nr'					=> substr($gab_id,17,3),

				'value_owner'					=> lang($values['owner']),
				'lang_owner'					=> lang('owner'),

				'lang_kommune_nr'				=> lang('kommune nr'),
				'lang_gaards_nr'				=> lang('gaards nr'),
				'lang_bruksnr'					=> lang('bruks nr'),
				'lang_feste_nr'					=> lang('Feste nr'),
				'lang_seksjons_nr'				=> lang('Seksjons nr'),

				'location_type'					=> $location_type,
				'location_data'					=> $location_data,
				'done_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uigab.list_detail','gab_id' => $gab_id)),
				'lang_save'					=> lang('save'),
				'lang_done'					=> lang('done'),

				'lang_remark'					=> lang('remark'),
				'value_remark'					=> $values['remark'],
				'lang_done_statustext'				=> lang('Back to the list'),

				'edit_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uigab.edit', 'from'=>'list_detail', 'gab_id'=> $gab_id, 'location_code'=> $location_code)),
				'lang_edit_statustext'				=> lang('Edit this entry'),
				'lang_edit'					=> lang('Edit')
			);

			$appname		= lang('gab');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}
	}
?>
