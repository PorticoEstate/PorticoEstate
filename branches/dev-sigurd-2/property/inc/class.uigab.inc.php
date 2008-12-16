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
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */
	phpgw::import_class('phpgwapi.yui');
	
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
			'index'  	=> true,
			'list_detail'  	=> true,
			'view' 		=> true,
			'edit'   	=> true,
			'delete' 	=> true,
			'download'  	=> true
		);

		function property_uigab()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::location::gabnr';

		//	$this->currentapp			= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->nextmatchs			= CreateObject('phpgwapi.nextmatchs');
			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo					= CreateObject('property.bogab',true);
			$this->bocommon				= CreateObject('property.bocommon');
			$this->bolocation				= CreateObject('property.bolocation');

			$this->config				= CreateObject('phpgwapi.config');
			$this->acl 					= & $GLOBALS['phpgw']->acl;
			$this->acl_location		= '.location';
			$this->acl_read 			= $this->acl->check('.location', PHPGW_ACL_READ, 'property');
			$this->acl_add 				= $this->acl->check('.location', PHPGW_ACL_ADD, 'property');
			$this->acl_edit 			= $this->acl->check('.location', PHPGW_ACL_EDIT, 'property');
			$this->acl_delete 			= $this->acl->check('.location', PHPGW_ACL_DELETE, 'property');

			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->filter				= $this->bo->filter;
			$this->cat_id				= $this->bo->cat_id;
			$this->allrows				= $this->bo->allrows;
			$this->gab_insert_level			= $this->bo->gab_insert_level;

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

		function download()
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

			$gab_list = $this->bo->read($location_code,$gaards_nr,$bruksnr,$feste_nr,$seksjons_nr,$address,$check_payments,$allrows=true);

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

			$this->bocommon->download($content,$table_header['name'],$table_header['descr'],array());
		}


		function index()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$address 		= phpgw::get_var('address');
			$check_payments 	= phpgw::get_var('check_payments', 'bool');
			$location_code 		= phpgw::get_var('location_code');
			$gaards_nr 		= phpgw::get_var('gaards_nr', 'int');
			$bruksnr 		= phpgw::get_var('bruksnr', 'int');
			$feste_nr 		= phpgw::get_var('feste_nr', 'int');
			$seksjons_nr 		= phpgw::get_var('seksjons_nr', 'int');

			$datatable = array();
			$values_combo_box = array();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			 {														
				$datatable['menu']				= $this->bocommon->get_menu();

		    	$datatable['config']['base_url']	= $GLOBALS['phpgw']->link('/index.php', array
	    				(
	    					'menuaction'			=> 'property.uigab.index',
							'location_code'		=> $location_code,
							'gaards_nr'		=> $gaards_nr,
							'bruksnr'		=> $bruksnr,
							'feste_nr'		=> $feste_nr,
							'seksjons_nr'	=> $seksjons_nr,
							'address'		=> $address,
							'check_payments'	=> $check_payments

	    				));

				$datatable['config']['base_java_url'] = "menuaction:'property.uigab.index',"
	    											."location_code:'{$location_code}',"
	    											."gaards_nr:'{$gaards_nr}',"
 	                        						."bruksnr: '{$bruksnr}',"
 	                        						."feste_nr:'{$feste_nr}',"
						 	                        ."seksjons_nr:'{$seksjons_nr}',"
 	                        						."address:'{$address}',"
						 	                        ."check_payments:'{$check_payments}'";

				$datatable['actions']['form'] = array(
					array(
						'action'	=> $GLOBALS['phpgw']->link('/index.php',
								array(
									'menuaction' 		=> 'property.uigab.index',
									'location_code'		=> $location_code,
									'gaards_nr'		=> $gaards_nr,
									'bruksnr'		=> $bruksnr,
									'feste_nr'		=> $feste_nr,
									'seksjons_nr'	=> $seksjons_nr,
									'address'		=> $address,
									'check_payments'	=> $check_payments
									)
						),
						'fields'	=> array(
                                    'field' => array(
													array(
						                                'type'	=> 'button',
						                            	'id'	=> 'btn_export',
						                                'value'	=> lang('download')						                                
						                            ),														
													array(
						                                'type'	=> 'submit',
						                            	'id'	=> 'btn_new',
						                                'value'	=> lang('add')						                        
						                            ),							                            											
					                                array( // address link
						                                'type' => 'link',
						                                'id' => 'lnk_address',
						                                'url' => "",
														'value' => lang('Address'),
														'style' => 'filter'														
													),												
			   										 array( // TEXT IMPUT
			                                            'name'     => 'address',
			                                            'id'     => 'txt_address',
			                                            'value'    => '',//$query,
			                                            'type' => 'text',
			                                            'size'    => 28,
			                                            'style' => 'filter'
			                                        ),
					                                array( // check link
						                                'type' => 'link',
						                                'id' => 'lnk_check',
						                                'url' => "",
														'value' => lang('check payments'),
														'style' => 'filter'														
													),				                                        
			   										 array( // TEXT IMPUT
			                                            'name'     => 'check',
			                                            'id'     => 'txt_check',
			                                            'value'    => 0,//$query,
			                                            'type' => 'checkbox',
			                                            'style' => 'filter'
			                                        ),
													array( //hidden check_payments
						                                'type'	=> 'hidden',
						                                'name'     => 'check_payments',
						                            	'id'	=> 'txt_check_payments',
						                                'value'	=> 0,
						                                'style' => 'filter'
						                            ),		
					                                array( // location_code link
						                                'type' => 'link',
						                                'id' => 'lnk_property_id',
						                                'url' => "",
														'value' => lang('property id'),
														'style' => 'filter'													
													),							                            	                                        	 			                                        														
			   										 array( // TEXT IMPUT
			                                            'name'     => 'location_code',
			                                            'id'     => 'txt_location_code',
			                                            'value'    => '',//$query,
			                                            'type' => 'text',
			                                            'size'    => 6,
			                                            'style' => 'filter'
			                                        ),	
					                                array( // gaards_nr link
						                                'type' => 'link',
						                                'id' => 'lnk_gaards_nr',
						                                'url' => "",
														'value' => lang('gaards nr'),
														'style' => 'filter'													
													),															                                        			                                        
			   										 array( // TEXT IMPUT
			                                            'name'     => 'gaards_nr',
			                                            'id'     => 'txt_gaards_nr',
			                                            'value'    => '',//$query,
			                                            'type' => 'text',
			                                            'size'    => 6,
			                                            'style' => 'filter'
			                                        ),		
					                                array( // bruksnr link
						                                'type' => 'link',
						                                'id' => 'lnk_bruksnr',
						                                'url' => "",
														'value' => lang('bruks nr'),
														'style' => 'filter'												
													),																                                        
			   										 array( // TEXT IMPUT
			                                            'name'     => 'bruksnr',
			                                            'id'     => 'txt_bruksnr',
			                                            'value'    => '',//$query,
			                                            'type' => 'text',
			                                            'size'    => 6,
			                                            'style' => 'filter'
			                                        ),	
					                                array( // feste_nr link
						                                'type' => 'link',
						                                'id' => 'lnk_feste_nr',
						                                'url' => "",
														'value' => lang('Feste nr'),
														'style' => 'filter'													
													),																	                                        
			   										 array( // TEXT IMPUT
			                                            'name'     => 'feste_nr',
			                                            'id'     => 'txt_feste_nr',
			                                            'value'    => '',//$query,
			                                            'type' => 'text',
			                                            'size'    => 6,
			                                            'style' => 'filter'
			                                        ),	
					                                array( // seksjons_nr link
						                                'type' => 'link',
						                                'id' => 'lnk_seksjons_nr',
						                                'url' => "",
														'value' => lang('Seksjons nr'),
														'style' => 'filter'													
													),																	                                        	
			   										 array( // TEXT IMPUT
			                                            'name'     => 'seksjons_nr',
			                                            'id'     => 'txt_seksjons_nr',
			                                            'value'    => '',//$query,
			                                            'type' => 'text',
			                                            'size'    => 6,
			                                            'style' => 'filter'
			                                        ),	                                       
			                                        array( //boton     SEARCH
			                                            'id' => 'btn_search',
			                                            'name' => 'search',
			                                            'value'    => lang('search'),
			                                            'type' => 'button',
			                                            'style' => 'filter'
			                                        ),							                            		                                        	                                         						                            
			                                        array( //boton     reset
			                                            'id' => 'btn_reset',
			                                            'name' => 'reset',
			                                            'value'    => lang('reset'),
			                                            'type' => 'reset',
			                                            'style' => 'filter'
			                                        )							                            				                            				                                        				                                        			                                        			                                        			                         
		                           				),
		                       		'hidden_value' => array(		                                        
		                       								)
												)
										  )
				);

			}
			
			$gab_list = $this->bo->read($location_code,$gaards_nr,$bruksnr,$feste_nr,$seksjons_nr,$address,$check_payments);

			$config		= CreateObject('phpgwapi.config','property');

			$config->read_repository();

			$link_to_map = (isset($config->config_data['map_url'])?$config->config_data['map_url']:'');
			if($link_to_map)
			{
				$text_map=lang('Map');
			}
			
			$link_to_gab = (isset($config->config_data['gab_url'])?$config->config_data['gab_url']:'');
			if($link_to_gab)
			{
				$text_gab=lang('GAB');
			}

			$payment_date = $this->bo->payment_date;
				
			$uicols = array (
				'input_type'	=>	array(hidden,text,text,text,text,hidden,text,text,text,link,link),
				'name'			=>	array(gab_id,gaards_nr,bruksnr,feste_nr,seksjons_nr,hits,owner,location_code,address,map,gab),
				'formatter'		=>	array('','','','','','','','','','',''),
				'descr'			=>	array(dummy,lang('Gaards nr'),lang('Bruks nr'),lang('Feste nr'),lang('Seksjons nr'),lang('hits'),lang('Owner'),lang('Location'),lang('Address'),lang('Map'),lang('Gab')),
				'className'		=> 	array('','','','','','','','','','','')
				);

			while (is_array($payment_date) && list(,$date) = each($payment_date))
			{
				$uicols['input_type'][] = 'date';
				$uicols['name'][] = str_replace('/','_',$date);
				$uicols['formatter'][] = '';
				$uicols['descr'][] = $date;
				$uicols['className'][] = 'rightClasss';
				
				$uicols_add['input_type'][] = 'date';
				$uicols_add['name'][] = str_replace('/','_',$date);
				$uicols_add['formatter'][] = '';
				$uicols_add['descr'][] = $date;
				$uicols_add['className'][] = 'rightClasss';				
			}
		
			$content = array();
			$j=0;
			if (isset($gab_list) && is_array($gab_list))
			{
				foreach($gab_list as $gab)
				{
					for ($i=0;$i<count($uicols['name']);$i++)
					{
						if($uicols['input_type'][$i]!='hidden')
						{
							if ($uicols['name'][$i] == 'gaards_nr') 
							{
								$value_gaards_nr	= substr($gab['gab_id'],4,5);
								$value	= $value_gaards_nr;							
								
							} elseif ($uicols['name'][$i] == 'bruksnr') {
							
								$value_bruks_nr		= substr($gab['gab_id'],9,4);
								$value	= $value_bruks_nr;							
								
							} elseif ($uicols['name'][$i] == 'feste_nr') {
							
								$value_feste_nr		= substr($gab['gab_id'],13,4);
								$value	= $value_feste_nr;						
								
							} elseif ($uicols['name'][$i] == 'seksjons_nr') {
						
								$value_seksjons_nr	= substr($gab['gab_id'],17,3);
								$value	= $value_seksjons_nr;						
								
							} else {														
								$value	= $gab[$uicols['name'][$i]];
						    }
						    
						    $datatable['rows']['row'][$j]['column'][$i]['value'] 			= $value;
							$datatable['rows']['row'][$j]['column'][$i]['name'] 			= $uicols['name'][$i];
							$datatable['rows']['row'][$j]['column'][$i]['align'] 			= 'center';
							
							if(isset($uicols['input_type']) && isset($uicols['input_type'][$i]) && $uicols['input_type'][$i]=='link' && $uicols['name'][$i] == 'map' )
							{
								$value_gaards_nr	= substr($gab['gab_id'],4,5);
								$value_bruks_nr		= substr($gab['gab_id'],9,4);
								$value_feste_nr		= substr($gab['gab_id'],13,4);
								$link = phpgw::safe_redirect($link_to_map . '?maptype=Eiendomskart&gnr=' . (int)$value_gaards_nr . '&bnr=' . (int)$value_bruks_nr . '&fnr=' . (int)$value_feste_nr);

								$datatable['rows']['row'][$j]['column'][$i]['format'] 	= 'link';
								$datatable['rows']['row'][$j]['column'][$i]['value']	= $text_map;
								$datatable['rows']['row'][$j]['column'][$i]['link']		= $link;
								$datatable['rows']['row'][$j]['column'][$i]['target']	= '_blank';
							}							
							if(isset($uicols['input_type']) && isset($uicols['input_type'][$i]) && $uicols['input_type'][$i]=='link' && $uicols['name'][$i] == 'gab' )
							{
								$value_gaards_nr	= substr($gab['gab_id'],4,5);
								$value_bruks_nr		= substr($gab['gab_id'],9,4);
								$value_feste_nr		= substr($gab['gab_id'],13,4);
								$value_seksjons_nr	= substr($gab['gab_id'],17,3);
								$link = phpgw::safe_redirect($link_to_gab . '?type=eiendom&Gnr=' . (int)$value_gaards_nr . '&Bnr=' . (int)$value_bruks_nr . '&Fnr=' . (int)$value_feste_nr . '&Snr=' . (int)$value_seksjons_nr);
								
								$datatable['rows']['row'][$j]['column'][$i]['format'] 	= 'link';
								$datatable['rows']['row'][$j]['column'][$i]['value']	= $text_gab;
								$datatable['rows']['row'][$j]['column'][$i]['link']		= $link;
								$datatable['rows']['row'][$j]['column'][$i]['target']	= '_blank';
							}	
							
							if (isset($uicols['input_type'][$i]) && $uicols['input_type'][$i]=='date')
							{									
							    $datatable['rows']['row'][$j]['column'][$i]['value'] 			= $gab['payment'][str_replace('_','/',$uicols['name'][$i])];
								$datatable['rows']['row'][$j]['column'][$i]['name'] 			= $uicols['name'][$i];
								$datatable['rows']['row'][$j]['column'][$i]['align'] 			= 'right';								
							}
						}
						else
						{
								$datatable['rows']['row'][$j]['column'][$i]['name'] 			= $uicols['name'][$i];
								$datatable['rows']['row'][$j]['column'][$i]['value']			= $gab[$uicols['name'][$i]];
						}

						$datatable['rows']['row'][$j]['hidden'][$i]['value'] 			= $gab[$uicols['name'][$i]];
						$datatable['rows']['row'][$j]['hidden'][$i]['name'] 			= $uicols['name'][$i];
					}
									
					$j++;
				}
			}
	
			// NO pop-up
			$datatable['rowactions']['action'] = array();
			if(!$lookup)
			{
				$parameters = array
				(
					'parameter' => array
					(
						array
						(
							'name'		=> 'gab_id',
							'source'	=> 'gab_id'
						),
					)
				);

				if($this->acl_read)
				{
					$datatable['rowactions']['action'][] = array(
						'text' 			=> lang('view'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'	=> 'property.uigab.list_detail'
										)),
						'parameters'	=> $parameters
					);
				}

				if($this->acl_add) 
				{
					$datatable['rowactions']['action'][] = array(
							'text' 			=> lang('add'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
											(
												'menuaction'	=> 'property.uigab.edit',
												'from'			=> 'index'
											))
					);
				}
				unset($parameters);
			}
			
			//$uicols_count indicates the number of columns to display in actuall option-menu. this variable was set in $this->bo->read()
			$uicols_count	= count($uicols['descr']);

			for ($i=0;$i<$uicols_count;$i++)
			{
				//all colums should be have formatter
				$datatable['headers']['header'][$i]['formatter'] = ($uicols['formatter'][$i]==''?  '""' : $uicols['formatter'][$i]);

				if($uicols['input_type'][$i]!='hidden')
				{
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= true;
					$datatable['headers']['header'][$i]['sortable']			= false;

					if($uicols['name'][$i]=='gaards_nr'):
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field']	= 'gab_id';
					}
					elseif($uicols['name'][$i]=='location_code'):
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field'] 	= 'location_code';
					}
					endif;
				}
				else
				{
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= false;
					$datatable['headers']['header'][$i]['sortable']		= false;
					$datatable['headers']['header'][$i]['format'] 			= 'hidden';
				}
			}

			if(!$this->allrows)
			{
				$record_limit	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$record_limit	= $this->bo->total_records;
			}


			// path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($gab_list);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			$datatable['sorting']['order'] 	= phpgw::get_var('order', 'string'); // Column
			$datatable['sorting']['sort'] 	= phpgw::get_var('sort', 'string'); // ASC / DESC

			$appname		= lang('gab');
			$function_msg	= lang('list gab');

			phpgwapi_yui::load_widget('dragdrop');
		  	phpgwapi_yui::load_widget('datatable');
		  	phpgwapi_yui::load_widget('menu');
		  	phpgwapi_yui::load_widget('connection');
		  	//// cramirez: necesary for include a partucular js
		  	phpgwapi_yui::load_widget('loader');
		  	phpgwapi_yui::load_widget('paginator');
		  	//cramirez: necesary for use opener . Avoid error JS
			phpgwapi_yui::load_widget('tabview');


//-- BEGIN----------------------------- JSON CODE ------------------------------

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
    			//values for Pagination
	    		$json = array
	    		(
	    			'recordsReturned' 	=> $datatable['pagination']['records_returned'],
    				'totalRecords' 		=> (int)$datatable['pagination']['records_total'],
	    			'startIndex' 		=> $datatable['pagination']['records_start'],
					'sort'				=> $datatable['sorting']['order'],
	    			'dir'				=> $datatable['sorting']['sort'],
					'records'			=> array(),
				    'headers'			=> $uicols_add['name'],
				    'headers_all'		=> $uicols['name']
	    		);

				// values for datatable
	    		if(isset($datatable['rows']['row']) && is_array($datatable['rows']['row'])){
	    			foreach( $datatable['rows']['row'] as $row )
	    			{
		    			$json_row = array();
		    			foreach( $row['column'] as $column)
		    			{
		    				if(isset($column['format']) && $column['format']== "link" && $column['java_link']==true)
		    				{
		    					$json_row[$column['name']] = "<a href='#' id='".$column['link']."' onclick='javascript:filter_data(this.id);'>" .$column['value']."</a>";
		    				}
		    				elseif(isset($column['format']) && $column['format']== "link")
		    				{
		    				  $json_row[$column['name']] = "<a href='".$column['link']."' target='_blank'>" .$column['value']."</a>";
		    				}
		    				else
		    				{
		    				  $json_row[$column['name']] = $column['value'];
		    				}
		    			}
		    			$json['records'][] = $json_row;
	    			}
	    		}

				// right in datatable
				if(isset($datatable['rowactions']['action']) && is_array($datatable['rowactions']['action']))
				{
					$json ['rights'] = $datatable['rowactions']['action'];
				}

	    		return $json;
			}
//-------------------- JSON CODE ----------------------

			// Prepare template variables and process XSLT
			$template_vars = array();
			$template_vars['datatable'] = $datatable;
			$GLOBALS['phpgw']->xslttpl->add_file(array('datatable'));
	      	$GLOBALS['phpgw']->xslttpl->set_var('phpgw', $template_vars);

	      	if ( !isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css) )
	      	{
	        	$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
	      	}
			// Prepare CSS Style
		  	$GLOBALS['phpgw']->css->validate_file('datatable');
		  	$GLOBALS['phpgw']->css->validate_file('property');
		  	$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');

			//Title of Page
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

	  		// Prepare YUI Library
  			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'gab.index', 'property' );
  			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'json-min', 'phpgwapi' );

			$this->save_sessiondata();

		}

		function list_detail()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('gab', 'values', 'table_header', 'nextmatchs'));

			$gab_id 		= phpgw::get_var('gab_id');

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
						'no_link'		=> false, // disable lookup links for location type less than type_id
						'tenant'		=> false,
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
						'no_link'		=> false, // disable lookup links for location type less than type_id
						'tenant'		=> false,
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

