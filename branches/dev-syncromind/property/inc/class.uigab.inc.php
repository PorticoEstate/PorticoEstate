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
	phpgw::import_class('phpgwapi.uicommon_jquery');
	phpgw::import_class('phpgwapi.jquery');

	class property_uigab extends phpgwapi_uicommon_jquery
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
				'index'  		=> true,
				'list_detail'  	=> true,
				'query'			=> true,
				'view' 			=> true,
				'edit'   		=> true,
				'delete' 		=> true,
				'download'  	=> true
			);

		function __construct()
		{
			parent::__construct();
			
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::location::gabnr';

			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo					= CreateObject('property.bogab',true);
			$this->bocommon				= CreateObject('property.bocommon');
			$this->bolocation			= CreateObject('property.bolocation');

			$this->config				= CreateObject('phpgwapi.config','property');
			$this->acl 					= & $GLOBALS['phpgw']->acl;
			$this->acl_location			= '.location';
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
			$this->gab_insert_level		= $this->bo->gab_insert_level;

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
			$check_payments = phpgw::get_var('check_payments', 'bool');
			$location_code 	= phpgw::get_var('location_code');
			$gaards_nr 		= phpgw::get_var('gaards_nr', 'int');
			$bruksnr 		= phpgw::get_var('bruksnr', 'int');
			$feste_nr 		= phpgw::get_var('feste_nr', 'int');
			$seksjons_nr 	= phpgw::get_var('seksjons_nr', 'int');


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

            if( phpgw::get_var('phpgw_return_as') == 'json' )
            {
				return $this->query();
            }
            
            self::add_javascript('phpgwapi','jquery','editable/jquery.jeditable.js');
            self::add_javascript('phpgwapi','jquery','editable/jquery.dataTables.editable.js');
			
			$appname		= lang('gab');
			$function_msg	= lang('list gab');
			
            $data   = array(
                'datatable_name'    => $appname,
                'form'  => array(
                               'toolbar'    => array(
                                   'item'   => array(								   
                                        array(
                                           'type'   => 'link',
                                           'value'  => lang('new'),
                                           'href'   => self::link(array(
                                               'menuaction'	=> 'property.uigab.add'
                                           )),
                                           'class'  => 'new_item'
                                        ),
                                        array(
                                           'type'   => 'link',
                                           'value'  => lang('columns'),
                                           'href'   => '#',
                                           'class'  => '',
                                           'onclick'    => "JqueryPortico.openPopup({menuaction:'property.uigab.columns'},{closeAction:'reload'})"
                                       )
                                   )
                               )
                            ),
                'datatable' =>  array(
                    'source'    => self::link(array(
                        'menuaction'		=> 'property.uigab.index',
                        'phpgw_return_as'   => 'json'
                    )),
					'download'	=> self::link(array(
							'menuaction'	=> 'property.uigab.download',
							'export'		=> true,
							'allrows'		=> true
					)),
                    'allrows'   => true,
                    'editor_action' => '',
                    'field' =>  array()
                )
            );

			$uicols = array (
				'input_type'	=>	array('hidden','text','text','text','text','hidden','text','text','text','link','link'),
				'name'			=>	array('gab_id','gaards_nr','bruksnr','feste_nr','seksjons_nr','hits','owner','location_code','address','map','gab'),
				'formatter'		=>	array('','','','','','','','','','linktToMap','linktToGab'),
				'sortable'		=>	array('','true','','','','','','true','','',''),
				'descr'			=>	array('dummy',lang('Gaards nr'),lang('Bruks nr'),lang('Feste nr'),lang('Seksjons nr'),lang('hits'),lang('Owner'),lang('Location'),lang('Address'),lang('Map'),lang('Gab')),
				'className'		=> 	array('','','','','','','','center','','center','center')
			);
			
			$count_uicols_name = count($uicols['name']);

            for ($k = 0; $k < $count_uicols_name; $k++) 
			{
                $params = array
                            (
                                'key'		=>  $uicols['name'][$k],
                                'label'		=>  $uicols['descr'][$k],
                                'sortable'  =>  ($uicols['sortable'][$k])?true:false,
                                'hidden'    =>  ($uicols['input_type'][$k] == 'hidden')?true:false,
								'className' =>  ($uicols['className'][$k])?$uicols['className'][$k]:''
                            );               
                
                if($uicols['formatter'][$k])
				{
					$params['formatter'] = $uicols['formatter'][$k];
				}
					
                array_push($data['datatable']['field'], $params);
            }
			
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
				$data['datatable']['actions'][] = array
					(
						'my_name'		=> 'view',
						'text' 			=> lang('view'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uigab.list_detail'
						)),
						'parameters'	=> json_encode($parameters)
					);
			}

			if($this->acl_add)
			{
				$data['datatable']['actions'][] = array
					(
						'my_name'		=> 'add',
						'text' 			=> lang('add'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uigab.add',
							'from'			=> 'index'
						))
					);
			}
			unset($parameters);

			$code =	<<<JS

				var columns = ["gaards_nr","bruksnr"];				

				function initCompleteDatatable(oSettings, json, oTable) 
				{
					$('#datatable-container_filter').empty();
					columns.forEach(function(col)
					{
						$('#datatable-container_filter').append('<input type="text" placeholder="Search '+col+'" id="'+col+'" />');
					});
					
					// Apply the search
					var api = oTable.api();
					columns.forEach(function(col)
					{
						$( '#' + col).on( 'keyup change', function () 
						{
							oTable.dataTableSettings[0]['ajax']['data'][col] = this.value;
							oTable.fnDraw();
						});
					});
				};
					
JS;

			$GLOBALS['phpgw']->js->add_code('', $code, true);
					
			//Title of Page
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
					
			self::add_javascript('property', 'portico', 'gab.index.js');
			self::render_template_xsl('datatable_jquery',$data);

		}

        public function query()
        {
			$address 			= phpgw::get_var('address');
			$location_code 		= phpgw::get_var('location_code');
			$gaards_nr 			= phpgw::get_var('gaards_nr', 'int');
			$bruksnr 			= phpgw::get_var('bruksnr', 'int');
			$feste_nr 			= phpgw::get_var('feste_nr', 'int');
			$seksjons_nr 		= phpgw::get_var('seksjons_nr', 'int');
			
            $search			= phpgw::get_var('search');
			$order			= phpgw::get_var('order');
			$draw			= phpgw::get_var('draw', 'int');
			$columns		= phpgw::get_var('columns');
			
            $params = array(
                'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query' => $search['value'],
				'order' => $columns[$order[0]['column']]['data'],
				'sort' => $order[0]['dir'],
				'allrows' => phpgw::get_var('length', 'int') == -1,
				'location_code' => $location_code,
				'gaards_nr' => $gaards_nr,
				'bruksnr' => $bruksnr,
				'feste_nr' => $feste_nr,
				'seksjons_nr' => $seksjons_nr,
				'address' => $address
            );
			
			$gab_list = $this->bo->read($params);

			$config		= CreateObject('phpgwapi.config','property');

			$config->read_repository();

			$link_to_map = (isset($config->config_data['map_url'])?$config->config_data['map_url']:'');
			if($link_to_map)
			{
				$text_map=lang('Map');
			}

			$link_to_gab = isset($config->config_data['gab_url'])?$config->config_data['gab_url']:'';
			$gab_url_paramtres = isset($config->config_data['gab_url_paramtres']) ? $config->config_data['gab_url_paramtres']:'type=eiendom&Gnr=__gaards_nr__&Bnr=__bruks_nr__&Fnr=__feste_nr__&Snr=__seksjons_nr__';

			if($link_to_gab)
			{
				$text_gab=lang('GAB');
			}

			//$payment_date = $this->bo->payment_date;

			$uicols = array (
				'input_type'	=>	array('hidden','text','text','text','text','hidden','text','text','text','link','link'),
				'name'			=>	array('gab_id','gaards_nr','bruksnr','feste_nr','seksjons_nr','hits','owner','location_code','address','map','gab'),
				'formatter'		=>	array('','','','','','','','','','',''),
				'descr'			=>	array('dummy',lang('Gaards nr'),lang('Bruks nr'),lang('Feste nr'),lang('Seksjons nr'),lang('hits'),lang('Owner'),lang('Location'),lang('Address'),lang('Map'),lang('Gab')),
				'className'		=> 	array('','','','','','','','','','','')
			);

			/*while (is_array($payment_date) && list(,$date) = each($payment_date))
			{
				$uicols['input_type'][] = 'date';
				$uicols['name'][] = str_replace('/','_',$date);
				$uicols['formatter'][] = '';
				$uicols['descr'][] = $date;
				$uicols['className'][] = 'rightClass';
			}*/

			$values = array();
			$j=0;
			if (isset($gab_list) && is_array($gab_list))
			{
				foreach($gab_list as $gab)
				{
					for ($i=0;$i<count($uicols['name']);$i++)
					{
							if ($uicols['name'][$i] == 'gaards_nr')
							{
								$value_gaards_nr	= substr($gab['gab_id'],4,5);
								$value	= $value_gaards_nr;

							}
							else if ($uicols['name'][$i] == 'bruksnr')
							{
								$value_bruks_nr		= substr($gab['gab_id'],9,4);
								$value	= $value_bruks_nr;

							}
							else if ($uicols['name'][$i] == 'feste_nr')
							{
								$value_feste_nr		= substr($gab['gab_id'],13,4);
								$value	= $value_feste_nr;

							}
							else if ($uicols['name'][$i] == 'seksjons_nr')
							{
								$value_seksjons_nr	= substr($gab['gab_id'],17,3);
								$value	= $value_seksjons_nr;

							}
							else
							{
								$value	= isset($gab[$uicols['name'][$i]]) ? $gab[$uicols['name'][$i]] : '';
							}

							//$values[$j][$uicols['name'][$i]] 			= $value;
							//$values['rows']['row'][$j]['column'][$i]['name'] 			= $uicols['name'][$i];

							if(isset($uicols['input_type']) && isset($uicols['input_type'][$i]) && $uicols['input_type'][$i]=='link' && $uicols['name'][$i] == 'map' )
							{
								$value_gaards_nr	= substr($gab['gab_id'],4,5);
								$value_bruks_nr		= substr($gab['gab_id'],9,4);
								$value_feste_nr		= substr($gab['gab_id'],13,4);
								$link = phpgw::safe_redirect($link_to_map . '?maptype=Eiendomskart&gnr=' . (int)$value_gaards_nr . '&bnr=' . (int)$value_bruks_nr . '&fnr=' . (int)$value_feste_nr);

								/*$values['rows']['row'][$j]['column'][$i]['format'] 	= 'link';
								$values['rows']['row'][$j]['column'][$i]['value']	= $text_map;
								$values['rows']['row'][$j]['column'][$i]['link']		= $link;
								$values['rows']['row'][$j]['column'][$i]['target']	= '_blank';*/
								$values[$j]['link_map'] 			= $link;
								$value = $text_map;
							}
							if(isset($uicols['input_type']) && isset($uicols['input_type'][$i]) && $uicols['input_type'][$i]=='link' && $uicols['name'][$i] == 'gab' )
							{
								$value_kommune_nr	= substr($gab['gab_id'],0,4);
								$value_gaards_nr	= substr($gab['gab_id'],4,5);
								$value_bruks_nr		= substr($gab['gab_id'],9,4);
								$value_feste_nr		= substr($gab['gab_id'],13,4);
								$value_seksjons_nr	= substr($gab['gab_id'],17,3);

								$_param = str_replace(array
									(
										'__kommune_nr__',
										'__gaards_nr__',
										'__bruks_nr__',
										'__feste_nr__',
										'__seksjons_nr__'
									),array
									(
										$value_kommune_nr,
										(int)$value_gaards_nr,
										(int)$value_bruks_nr,
										(int)$value_feste_nr,
										(int)$value_seksjons_nr
									), $gab_url_paramtres);

								$link = phpgw::safe_redirect("$link_to_gab?{$_param}");

								/*$values['rows']['row'][$j]['column'][$i]['format'] 	= 'link';
								$values['rows']['row'][$j]['column'][$i]['value']	= $text_gab;
								$values['rows']['row'][$j]['column'][$i]['link']		= $link;
								$values['rows']['row'][$j]['column'][$i]['target']	= '_blank';*/
								$values[$j]['link_gab'] 			= $link;
								$value = $text_gab;
							}
							
							$values[$j][$uicols['name'][$i]] 			= $value;

					}

					$j++;
				}
			}
			
            
            if( phpgw::get_var('export','bool'))
            {
                return $values;
            }
            
            $result_data = array('results'  => $values);
            $result_data['total_records'] = $this->bo->total_records;
            $result_data['draw'] = $draw;
            
            return $this->jquery_results($result_data);
        }
		
		
		function list_detail()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$gab_id 		= phpgw::get_var('gab_id');

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{

				$datatable['config']['base_url']	= $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'	=> 'property.uigab.list_detail',
						'gab_id'		=> $gab_id
					));

				$datatable['config']['allow_allrows'] = true;

				$datatable['config']['base_java_url'] = "menuaction:'property.uigab.list_detail',"
					."gab_id: '{$gab_id}'";

				$datatable['actions']['form'] = array
					(
						array
						(
							'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array
							(
								'menuaction' 		=> 'property.uigab.list_detail'

							)
						),
						'fields'	=> array
						(
							'field' => array
							(
								array
								( // mensaje
									'type'	=> 'label',
									'id'	=> 'msg_header',
									'value'	=> '',
									'style' => 'filter'
								),
								array
								( // boton done
									'type'	=> 'button',
									'id'	=> 'btn_done',
									'tab_index' => 1,
									'value'	=> lang('done')
								),												
								array
								(
									'type'	=> 'button',
									'id'	=> 'btn_new',
									'value'	=> lang('add'),
									'tab_index' => 2
								)													
							),
							'hidden_value' => array
							(
								)
							)
						)
					);

			}

			$gab_list = $this->bo->read_detail($gab_id, true);

			$uicols	= $this->bo->uicols;

			$content = array();
			$j=0;
			if (isset($gab_list) && is_array($gab_list))
			{
				foreach($gab_list as $gab_entry)
				{
					for ($i=0;$i<count($uicols['name']);$i++)
					{
						if($uicols['input_type'][$i]!='hidden')
						{
							$datatable['rows']['row'][$j]['column'][$i]['name'] 	= $uicols['name'][$i];
							$datatable['rows']['row'][$j]['column'][$i]['value']	= $gab_entry[$uicols['name'][$i]];
						}
					}
					$j++;
				}
			}

			$datatable['rowactions']['action'] = array();
			if(!$lookup)
			{
				$parameters = array
					(
						'parameter' => array
						(
							array
							(
								'name'		=> 'location_code',
								'source'	=> 'location_code'
							),
						)
					);

				if($this->acl_read)
				{
					$datatable['rowactions']['action'][] = array
						(
							'my_name'			=> 'view',
							'text' 			=> lang('view'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'property.uigab.view',
								'gab_id'		=>	$gab_id
							)),
							'parameters'	=> $parameters
						);
				}

				if($this->acl_edit)
				{
					$datatable['rowactions']['action'][] = array
						(
							'my_name'			=> 'edit',
							'text' 			=> lang('edit'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'property.uigab.edit',
								'from'			=> 'list_detail',
								'gab_id'		=>	$gab_id
							)),
							'parameters'	=> $parameters
						);
				}

				if($this->acl_delete)
				{
					$datatable['rowactions']['action'][] = array
						(
							'my_name'			=> 'delete',
							'text' 			=> lang('delete'),
							'confirm_msg'	=> lang('do you really want to delete this entry'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'property.uigab.delete',
								'gab_id'		=>	$gab_id
							)),
							'parameters'	=> $parameters
						);
				}			

				if($this->acl_add)
				{
					$datatable['rowactions']['action'][] = array
						(
							'my_name'			=> 'add',
							'text' 			=> lang('add'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'property.uigab.edit',
								'gab_id'		=>	$gab_id,
								'from' 			=> 'list_detail',
								'new'			=>	true									
							))
						);
				}					
				unset($parameters);
			}		

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

					if($uicols['name'][$i]=='gab_id')
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field']	= 'gab_id';
					}

					if($uicols['name'][$i]=='address')
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field'] 	= 'address';
					}

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

			$gaards_nr	= substr($gab_id,4,5);
			$bruks_nr = substr($gab_id,9,4);
			$feste_nr	= substr($gab_id,13,4);
			$seksjons_nr = substr($gab_id,17,3);

			$info = array ();
			$info[0]['name'] = lang('gaards nr');		
			$info[0]['value'] = $gaards_nr;
			$info[1]['name'] = lang('bruks nr');		
			$info[1]['value'] = $bruks_nr;																												
			$info[2]['name'] = lang('Feste nr');		
			$info[2]['value'] = $feste_nr;		
			$info[3]['name'] = lang('Seksjons nr');		
			$info[3]['value'] = $seksjons_nr;		
			$info[4]['name'] = lang('owner');		
			$info[4]['value'] = lang($gab_list[0]['owner']);		

			// path for property.js
			$property_js = "/property/js/yahoo/property.js";

			if (!isset($GLOBALS['phpgw_info']['server']['no_jscombine']) || !$GLOBALS['phpgw_info']['server']['no_jscombine'])
			{
				$cachedir = urlencode($GLOBALS['phpgw_info']['server']['temp_dir']);
				$property_js = "/phpgwapi/inc/combine.php?cachedir={$cachedir}&type=javascript&files=" . str_replace('/', '--', ltrim($property_js,'/'));
			}

			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url'] . $property_js;

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($gab_list);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;			

			$datatable['sorting']['order'] 	= phpgw::get_var('order', 'string'); // Column

			$appname		= lang('gab');
			$function_msg	= lang('list gab detail');

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order']		= 'address'; // name key Column in myColumnDef
				$datatable['sorting']['sort']		= 'asc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['order']   	= phpgw::get_var('order', 'string'); // name of column of Database
				$datatable['sorting']['sort'] 		= phpgw::get_var('sort', 'string'); // ASC / DESC
			}

			phpgwapi_yui::load_widget('dragdrop');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('menu');
			phpgwapi_yui::load_widget('connection');
			phpgwapi_yui::load_widget('loader');
			phpgwapi_yui::load_widget('paginator');
			phpgwapi_yui::load_widget('tabview');


			//-- BEGIN----------------------------- JSON CODE ------------------------------

			//values for Pagination
			$json = array
				(
					'recordsReturned' 	=> $datatable['pagination']['records_returned'],
					'totalRecords' 		=> (int)$datatable['pagination']['records_total'],
					'startIndex' 		=> $datatable['pagination']['records_start'],
					'sort'				=> $datatable['sorting']['order'],
					'dir'				=> $datatable['sorting']['sort'],
					'records'			=> array(),
					'info'				=> $info
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

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				return $json;
			}


			$datatable['json_data'] = json_encode($json);
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
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'gab.list_detail', 'property' );

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

					'action'						=> $action,
					'lookup_type'					=> $lookup_type,
					'location_data'					=> $location_data,
					'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'done_action'					=> $GLOBALS['phpgw']->link('/index.php',$done_data),
					'lang_save'						=> lang('save'),
					'lang_done'						=> lang('done'),

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

			//cramirez add JsonCod for Delete
			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				$this->bo->delete($gab_id,$location_code);
				return "gab_id ".$gab_id." ".lang("has been deleted");
			}

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
					'lang_save'						=> lang('save'),
					'lang_done'						=> lang('done'),

					'lang_remark'					=> lang('remark'),
					'value_remark'					=> $values['remark'],
					'lang_done_statustext'			=> lang('Back to the list'),

					'edit_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uigab.edit', 'from'=>'list_detail', 'gab_id'=> $gab_id, 'location_code'=> $location_code)),
					'lang_edit_statustext'			=> lang('Edit this entry'),
					'lang_edit'						=> lang('Edit')
				);

			$appname		= lang('gab');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view' => $data));
		}
	}
