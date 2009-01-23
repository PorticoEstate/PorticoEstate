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
	phpgw::import_class('phpgwapi.yui');

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
			'addressbook'	=> true,
			'vendor'		=> true,
			'b_account'		=> true,
			'location'		=> true,
			'entity'		=> true,
			'ns3420'		=> true,
			'street'		=> true,
			'tenant'		=> true,
			'phpgw_user'	=> true,
			'project_group'	=> true
		);

		function __construct()
		{
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$GLOBALS['phpgw_info']['flags']['headonly']=true;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$this->nextmatchs			= CreateObject('phpgwapi.nextmatchs');
			$this->bo					= CreateObject('property.bolookup',true);

			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->filter				= $this->bo->filter;
			$this->cat_id				= $this->bo->cat_id;
			$this->part_of_town_id		= $this->bo->part_of_town_id;
			$this->district_id			= $this->bo->district_id;
			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('dragdrop');
		  	phpgwapi_yui::load_widget('datatable');
		  	phpgwapi_yui::load_widget('menu');
		  	phpgwapi_yui::load_widget('connection');
		  	phpgwapi_yui::load_widget('loader');
			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('paginator');
			phpgwapi_yui::load_widget('animation');

	      	if ( !isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css) )
	      	{
	        	$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
	      	}
			// Prepare CSS Style
		  	$GLOBALS['phpgw']->css->validate_file('datatable');
		  	$GLOBALS['phpgw']->css->validate_file('property');
		  	$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');

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
			$bocommon	= CreateObject('property.bocommon');
			$this->cats		= CreateObject('phpgwapi.categories');
			$this->cats->app_name = 'addressbook';
			$this->cats->supress_info	= true;

			$second_display = phpgw::get_var('second_display', 'bool');
			$column = phpgw::get_var('column');

			$default_category = $GLOBALS['phpgw_info']['user']['preferences']['addressbook']['default_category'];

			if ($default_category && !$second_display)
			{
				$this->bo->cat_id	= $default_category;
				$this->cat_id		= $default_category;
			}

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			 {

		    	$datatable['config']['base_url']	= $GLOBALS['phpgw']->link('/index.php', array
	    				(
	    					'menuaction'			=> 'property.uilookup.addressbook',
							'second_display'	=> true,
							'cat_id'			=> $this->cat_id,
							'query'				=> $this->query,
							'filter'			=> $this->filter,
							'column'			=> $column

	    				));

	    		$datatable['config']['allow_allrows'] = true;

				$datatable['config']['base_java_url'] = "menuaction:'property.uilookup.addressbook',"
	    											."second_display:'{true}',"
 	                        						."cat_id:'{$this->cat_id}',"
						 	                        ."query:'{$this->query}',"
 	                        						."filter:'{$this->filter}',"
						 	                        ."column:'{$column}'";
						 	                        
				$values_combo_box[0]	= $this->cats->formatted_xslt_list(array('selected' => $this->cat_id,'globals' => true));;
				$default_value = array ('cat_id'=>'','name'=>lang('no category'));
				array_unshift ($values_combo_box[0]['cat_list'],$default_value);

				$datatable['actions']['form'] = array(
					array(
						'action'	=> $GLOBALS['phpgw']->link('/index.php',
								array(
									'menuaction' 		=> 'property.uilookup.addressbook',
									'second_display'	=> true,
									'cat_id'			=> $this->cat_id,
									'query'				=> $this->query,
									'filter'			=> $this->filter,
									'column'			=> $column
									)
						),
						'fields'	=> array(
                                    'field' => array(
			                                        array( //boton 	CATEGORY
			                                            'id' => 'btn_cat_id',
			                                            'name' => 'cat_id',
			                                            'value'	=> lang('Category'),
			                                            'type' => 'button',
			                                            'style' => 'filter',
			                                            'tab_index' => 1
			                                        ),
			                                        array( //boton  SEARCH
			                                            'id' => 'btn_search',
			                                            'name' => 'search',
			                                            'value'    => lang('search'),
			                                            'type' => 'button',
			                                            'tab_index' => 3
			                                        ),
			   										 array( // TEXT IMPUT
			                                            'name'     => 'query',
			                                            'id'     => 'txt_query',
			                                            'value'    => '',//'',//$query,
			                                            'type' => 'text',
			                                            'size'    => 28,
			                                            'onkeypress' => 'return pulsar(event)',
	                                    				'tab_index' => 2
			                                        )
		                           				),
		                       		'hidden_value' => array(
					                                        array( //div values  combo_box_0
							                                            'id' => 'values_combo_box_0',
							                                            'value'	=> $bocommon->select2String($values_combo_box[0]['cat_list'], 'cat_id') //i.e.  id,value/id,vale/
							                                      )
		                       								)
												)
										  )
				);
			}

			$uicols = array (
				'input_type'	=>	array(text,text),
				'name'			=>	array(contact_id,contact_name),
				'sort_field'	=>	array(person_id,last_name),
				'formatter'		=>	array('',''),
				'descr'			=>	array(lang('ID'),lang('Name'))
			);

			$addressbook_list = array();
			$addressbook_list = $this->bo->read_addressbook();

			$content = array();
			$j=0;
			if (isset($addressbook_list) && is_array($addressbook_list))
			{
				foreach($addressbook_list as $addressbook_entry)
				{
					for ($i=0;$i<count($uicols['name']);$i++)
					{
							if ($uicols['name'][$i] == 'contact_name'){
								$datatable['rows']['row'][$j]['column'][$i]['value'] 	= $addressbook_entry['per_last_name'] . ', ' . $addressbook_entry['per_first_name'];
							}
							else {
								$datatable['rows']['row'][$j]['column'][$i]['value'] 	= $addressbook_entry[$uicols['name'][$i]];
							}
							$datatable['rows']['row'][$j]['column'][$i]['name'] 	= $uicols['name'][$i];
					}
					$j++;
				}
			}

			$uicols_count	= count($uicols['descr']);
			$datatable['rowactions']['action'] = array();
			for ($i=0;$i<$uicols_count;$i++)
			{
				//all colums should be have formatter
				$datatable['headers']['header'][$i]['formatter'] = ($uicols['formatter'][$i]==''?  '""' : $uicols['formatter'][$i]);
				$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
				$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
				$datatable['headers']['header'][$i]['visible'] 			= true;
				$datatable['headers']['header'][$i]['sortable']			= true;
				$datatable['headers']['header'][$i]['sort_field'] 	= $uicols['sort_field'][$i];
			}

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

			$function_exchange_values = '';

			$function_exchange_values .= 'opener.document.getElementsByName("'.$contact_id.'")[0].value = "";' ."\r\n";
			$function_exchange_values .= 'opener.document.getElementsByName("'.$contact_name.'")[0].value = "";' ."\r\n";

			$function_exchange_values .= 'opener.document.getElementsByName("'.$contact_id.'")[0].value = data.getData("contact_id");' ."\r\n";
			$function_exchange_values .= 'opener.document.getElementsByName("'.$contact_name.'")[0].value = data.getData("contact_name");' ."\r\n";
			
			$function_exchange_values .= 'window.close()';

			$datatable['exchange_values'] = $function_exchange_values;
			$datatable['valida'] = '';

			// path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($addressbook_list);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order'] 			= 'contact_id'; // name key Column in myColumnDef
				$datatable['sorting']['sort'] 			= 'asc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['order']			= phpgw::get_var('order', 'string'); // name of column of Database
				$datatable['sorting']['sort'] 			= phpgw::get_var('sort', 'string'); // ASC / DESC
			}

			$appname						= lang('addressbook');
			$function_msg					= lang('list vendors');


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
					'records'			=> array()
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

			//Title of Page
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

	  		// Prepare YUI Library
  			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'lookup.vendor.index', 'property' );

			$this->save_sessiondata();
		}

		function vendor()
		{
			$bocommon	= CreateObject('property.bocommon');

			$this->cats		= CreateObject('phpgwapi.categories');
			$this->cats->app_name = 'fm_vendor';

			$second_display = phpgw::get_var('second_display', 'bool');
			$column = phpgw::get_var('column');

			$default_category = $GLOBALS['phpgw_info']['user']['preferences']['property']['default_vendor_category'];

			if ($default_category && !$second_display)
			{
				$this->bo->cat_id	= $default_category;
				$this->cat_id		= $default_category;
			}

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			 {

		    	$datatable['config']['base_url']	= $GLOBALS['phpgw']->link('/index.php', array
	    				(
	    					'menuaction'			=> 'property.uilookup.vendor',
							'second_display'	=> true,
							'cat_id'			=> $this->cat_id,
							'query'				=> $this->query,
							'filter'			=> $this->filter,
							'column'			=> $column

	    				));

	    		$datatable['config']['allow_allrows'] = true;

				$datatable['config']['base_java_url'] = "menuaction:'property.uilookup.vendor',"
	    											."second_display:'{true}',"
 	                        						."cat_id:'{$this->cat_id}',"
						 	                        ."query:'{$this->query}',"
 	                        						."filter:'{$this->filter}',"
						 	                        ."column:'{$column}'";

				$values_combo_box[0]	= $this->cats->formatted_xslt_list(array('selected' => $this->cat_id,'globals' => true));
				$default_value = array ('cat_id'=>'','name'=>lang('no category'));
				array_unshift ($values_combo_box[0]['cat_list'],$default_value);

				$datatable['actions']['form'] = array(
					array(
						'action'	=> $GLOBALS['phpgw']->link('/index.php',
								array(
									'menuaction' 		=> 'property.uilookup.vendor',
									'second_display'	=> true,
									'cat_id'			=> $this->cat_id,
									'query'				=> $this->query,
									'filter'			=> $this->filter,
									'column'			=> $column
									)
						),
						'fields'	=> array(
                                    'field' => array(
			                                        array( //boton 	CATEGORY
			                                            'id' => 'btn_cat_id',
			                                            'name' => 'cat_id',
			                                            'value'	=> lang('Category'),
			                                            'type' => 'button',
			                                            'style' => 'filter',
			                                            'tab_index' => 1
			                                        ),
			                                        array( //boton  SEARCH
			                                            'id' => 'btn_search',
			                                            'name' => 'search',
			                                            'value'    => lang('search'),
			                                            'type' => 'button',
			                                            'tab_index' => 3
			                                        ),
			   										 array( // TEXT IMPUT
			                                            'name'     => 'query',
			                                            'id'     => 'txt_query',
			                                            'value'    => '',//'',//$query,
			                                            'type' => 'text',
			                                            'size'    => 28,
			                                            'onkeypress' => 'return pulsar(event)',
	                                    				'tab_index' => 2
			                                        )
		                           				),
		                       		'hidden_value' => array(
					                                        array( //div values  combo_box_0
							                                            'id' => 'values_combo_box_0',
							                                            'value'	=> $bocommon->select2String($values_combo_box[0]['cat_list'], 'cat_id') //i.e.  id,value/id,vale/
							                                      )
		                       								)
												)
										  )
				);
			}

			$uicols = array (
				'input_type'	=>	array(text,text),
				'name'			=>	array(id,org_name),
				'formatter'		=>	array('',''),
				'descr'			=>	array(lang('ID'),lang('Name'))
			);

			$vendor_list = array();
			$vendor_list = $this->bo->read_vendor();

			$content = array();
			$j=0;
			if (isset($vendor_list) && is_array($vendor_list))
			{
				foreach($vendor_list as $vendor)
				{
					for ($i=0;$i<count($uicols['name']);$i++)
					{
							$datatable['rows']['row'][$j]['column'][$i]['value'] 	= $vendor[$uicols['name'][$i]];
							$datatable['rows']['row'][$j]['column'][$i]['name'] 	= $uicols['name'][$i];
					}
					$j++;
				}
			}

			$uicols_count	= count($uicols['descr']);
			$datatable['rowactions']['action'] = array();
			for ($i=0;$i<$uicols_count;$i++)
			{
				//all colums should be have formatter
				$datatable['headers']['header'][$i]['formatter'] = ($uicols['formatter'][$i]==''?  '""' : $uicols['formatter'][$i]);
				$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
				$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
				$datatable['headers']['header'][$i]['visible'] 			= true;
				$datatable['headers']['header'][$i]['sortable']			= true;
				$datatable['headers']['header'][$i]['sort_field'] 	= $uicols['name'][$i];
			}

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

			$function_exchange_values = '';

			$function_exchange_values .= 'opener.document.getElementsByName("'.$contact_id.'")[0].value = "";' ."\r\n";
			$function_exchange_values .= 'opener.document.getElementsByName("'.$org_name.'")[0].value = "";' ."\r\n";
			
			$function_exchange_values .= 'opener.document.getElementsByName("'.$contact_id.'")[0].value = data.getData("id");' ."\r\n";
			$function_exchange_values .= 'opener.document.getElementsByName("'.$org_name.'")[0].value = data.getData("org_name");' ."\r\n";
			
			$function_exchange_values .= 'window.close()';

			$datatable['exchange_values'] = $function_exchange_values;
			$datatable['valida'] = '';

			// path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($vendor_list);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order'] 			= 'id'; // name key Column in myColumnDef
				$datatable['sorting']['sort'] 			= 'asc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['order']			= phpgw::get_var('order', 'string'); // name of column of Database
				$datatable['sorting']['sort'] 			= phpgw::get_var('sort', 'string'); // ASC / DESC
			}

			$appname						= lang('vendor');
			$function_msg					= lang('list vendors');


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
					'records'			=> array()
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

			//Title of Page
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

	  		// Prepare YUI Library
  			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'lookup.vendor.index', 'property' );

			$this->save_sessiondata();
		}

		function b_account()
		{
			if( phpgw::get_var('phpgw_return_as') != 'json' )
			 {

		    	$datatable['config']['base_url']	= $GLOBALS['phpgw']->link('/index.php', array
	    				(
	    					'menuaction'			=> 'property.uilookup.b_account',
								'second_display'	=> true,
								'cat_id'			=> $this->cat_id,
								'query'				=> $this->query,
								'filter'			=> $this->filter
	    				));
	    		$datatable['config']['allow_allrows'] = true;

				$datatable['config']['base_java_url'] = "menuaction:'property.uilookup.b_account',"
	    											."second_display:'{true}',"
 	                        						."cat_id:'{$this->cat_id}',"
						 	                        ."query:'{$this->query}',"
 	                        						."filter:'{$this->filter}'";

				$datatable['actions']['form'] = array(
					array(
						'action'	=> $GLOBALS['phpgw']->link('/index.php',
								array(
									'menuaction' 		=> 'property.uilookup.b_account',
									'second_display'	=> true,
									'cat_id'			=> $this->cat_id,
									'query'				=> $this->query,
									'filter'			=> $this->filter
									)
						),
						'fields'	=> array(
                                    'field' => array(
 			                                        array( //boton  SEARCH
			                                            'id' => 'btn_search',
			                                            'name' => 'search',
			                                            'value'    => lang('search'),
			                                            'type' => 'button',
			                                            'tab_index' => 2
			                                        ),
			   										 array( // TEXT IMPUT
			                                            'name'     => 'query',
			                                            'id'     => 'txt_query',
			                                            'value'    => '',//'',//$query,
			                                            'type' => 'text',
			                                            'size'    => 28,
			                                            'onkeypress' => 'return pulsar(event)',
	                                    				'tab_index' => 1
			                                        )
		                           				),
		                       		'hidden_value' => array(
		                       								)
												)
										  )
				);
			}

			$uicols = array (
				'input_type'	=>	array(text,text),
				'name'			=>	array(id,descr),
				'formatter'		=>	array('',''),
				'descr'			=>	array(lang('ID'),lang('Name'))
			);

			$b_account_list = array();
			$b_account_list = $this->bo->read_b_account();

			$content = array();
			$j=0;
			if (isset($b_account_list) && is_array($b_account_list))
			{
				foreach($b_account_list as $b_account_entry)
				{
					for ($i=0;$i<count($uicols['name']);$i++)
					{
							$datatable['rows']['row'][$j]['column'][$i]['value'] 	= $b_account_entry[$uicols['name'][$i]];
							$datatable['rows']['row'][$j]['column'][$i]['name'] 	= $uicols['name'][$i];
					}
					$j++;
				}
			}

			$uicols_count	= count($uicols['descr']);
			$datatable['rowactions']['action'] = array();
			for ($i=0;$i<$uicols_count;$i++)
			{
				//all colums should be have formatter
				$datatable['headers']['header'][$i]['formatter'] = ($uicols['formatter'][$i]==''?  '""' : $uicols['formatter'][$i]);

				$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
				$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
				$datatable['headers']['header'][$i]['visible'] 			= true;
				$datatable['headers']['header'][$i]['sortable']			= true;
				$datatable['headers']['header'][$i]['sort_field'] 	= $uicols['name'][$i];
			}

			$function_exchange_values = '';

			$function_exchange_values .= 'opener.document.getElementsByName("b_account_id")[0].value = "";' ."\r\n";
			$function_exchange_values .= 'opener.document.getElementsByName("b_account_name")[0].value = "";' ."\r\n";

			$function_exchange_values .= 'opener.document.getElementsByName("b_account_id")[0].value = data.getData("id");' ."\r\n";
			$function_exchange_values .= 'opener.document.getElementsByName("b_account_name")[0].value = data.getData("descr");' ."\r\n";

			$function_exchange_values .= 'window.close()';

			$datatable['exchange_values'] = $function_exchange_values;
			$datatable['valida'] = '';

			// path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($b_account_list);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order'] 			= 'id'; // name key Column in myColumnDef
				$datatable['sorting']['sort'] 			= 'desc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['order']			= phpgw::get_var('order', 'string'); // name of column of Database
				$datatable['sorting']['sort'] 			= phpgw::get_var('sort', 'string'); // ASC / DESC
			}

			$appname						= lang('budget account');
			$function_msg					= lang('list budget account');


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
					'records'			=> array()
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

			//Title of Page
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

	  		// Prepare YUI Library
  			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'lookup.tenant.index', 'property' );

			$this->save_sessiondata();
		}


		function street()
		{
			if( phpgw::get_var('phpgw_return_as') != 'json' )
			 {

		    	$datatable['config']['base_url']	= $GLOBALS['phpgw']->link('/index.php', array
	    				(
	    					'menuaction'			=> 'property.uilookup.street',
								'second_display'	=> true,
								'cat_id'			=> $this->cat_id,
								'query'				=> $this->query,
								'filter'			=> $this->filter
	    				));

	    		$datatable['config']['allow_allrows'] = true;

				$datatable['config']['base_java_url'] = "menuaction:'property.uilookup.street',"
	    											."second_display:'{true}',"
 	                        						."cat_id:'{$this->cat_id}',"
						 	                        ."query:'{$this->query}',"
 	                        						."filter:'{$this->filter}'";

				$datatable['actions']['form'] = array(
					array(
						'action'	=> $GLOBALS['phpgw']->link('/index.php',
								array(
									'menuaction' 		=> 'property.uilookup.street',
									'second_display'	=> true,
									'cat_id'			=> $this->cat_id,
									'query'				=> $this->query,
									'filter'			=> $this->filter
									)
						),
						'fields'	=> array(
                                    'field' => array(
 			                                        array( //boton  SEARCH
			                                            'id' => 'btn_search',
			                                            'name' => 'search',
			                                            'value'    => lang('search'),
			                                            'type' => 'button',
			                                            'tab_index' => 2
			                                        ),
			   										 array( // TEXT IMPUT
			                                            'name'     => 'query',
			                                            'id'     => 'txt_query',
			                                            'value'    => '',//'',//$query,
			                                            'type' => 'text',
			                                            'size'    => 28,
			                                            'onkeypress' => 'return pulsar(event)',
	                                    				'tab_index' => 1
			                                        )
		                           				),
		                       		'hidden_value' => array(
		                       								)
												)
										  )
				);
			}

			$uicols = array (
				'input_type'	=>	array(text,text),
				'name'			=>	array(id,street_name),
				'sort_field'	=>	array(id,descr),
				'formatter'		=>	array('',''),
				'descr'			=>	array(lang('ID'),lang('Street name'))
			);

			$street_list = array();
			$street_list = $this->bo->read_street();

			$content = array();
			$j=0;
			if (isset($street_list) && is_array($street_list))
			{
				foreach($street_list as $street_entry)
				{
					for ($i=0;$i<count($uicols['name']);$i++)
					{
							$datatable['rows']['row'][$j]['column'][$i]['value'] 	= $street_entry[$uicols['name'][$i]];
							$datatable['rows']['row'][$j]['column'][$i]['name'] 	= $uicols['name'][$i];
					}
					$j++;
				}
			}

			$uicols_count	= count($uicols['descr']);
			$datatable['rowactions']['action'] = array();
			for ($i=0;$i<$uicols_count;$i++)
			{
				//all colums should be have formatter
				$datatable['headers']['header'][$i]['formatter'] = ($uicols['formatter'][$i]==''?  '""' : $uicols['formatter'][$i]);

				if($uicols['input_type'][$i]!='hidden')
				{
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= true;
					$datatable['headers']['header'][$i]['sortable']		= true;
					$datatable['headers']['header'][$i]['sort_field']	= $uicols['sort_field'][$i];
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

			$function_exchange_values = '';

			$function_exchange_values .= 'opener.document.getElementsByName("street_id")[0].value = "";' ."\r\n";
			$function_exchange_values .= 'opener.document.getElementsByName("street_name")[0].value = "";' ."\r\n";
			
			$function_exchange_values .= 'opener.document.getElementsByName("street_id")[0].value = data.getData("id");' ."\r\n";
			$function_exchange_values .= 'opener.document.getElementsByName("street_name")[0].value = data.getData("street_name");' ."\r\n";

			$function_exchange_values .= 'window.close()';

			$datatable['exchange_values'] = $function_exchange_values;
			$datatable['valida'] = '';

			// path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($street_list);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order'] 			= 'id'; // name key Column in myColumnDef
				$datatable['sorting']['sort'] 			= 'desc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['order']			= phpgw::get_var('order', 'string'); // name of column of Database
				$datatable['sorting']['sort'] 			= phpgw::get_var('sort', 'string'); // ASC / DESC
			}

			$appname						= lang('street');
			$function_msg					= lang('list street');


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
					'records'			=> array()

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

			//Title of Page
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

	  		// Prepare YUI Library
  			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'lookup.tenant.index', 'property' );

			$this->save_sessiondata();
		}

		function tenant()
		{
			if( phpgw::get_var('phpgw_return_as') != 'json' )
			 {

		    	$datatable['config']['base_url']	= $GLOBALS['phpgw']->link('/index.php', array
	    				(
	    					'menuaction'			=> 'property.uilookup.tenant',
								'second_display'	=> true,
								'cat_id'			=> $this->cat_id,
								'query'				=> $this->query,
								'filter'			=> $this->filter
	    				));
	    		$datatable['config']['allow_allrows'] = true;

				$datatable['config']['base_java_url'] = "menuaction:'property.uilookup.tenant',"
	    											."second_display:'{true}',"
 	                        						."cat_id:'{$this->cat_id}',"
						 	                        ."query:'{$this->query}',"
 	                        						."filter:'{$this->filter}'";

				$datatable['actions']['form'] = array(
					array(
						'action'	=> $GLOBALS['phpgw']->link('/index.php',
								array(
									'menuaction' 		=> 'property.uilookup.tenant',
									'second_display'	=> true,
									'cat_id'			=> $this->cat_id,
									'query'				=> $this->query,
									'filter'			=> $this->filter
									)
						),
						'fields'	=> array(
                                    'field' => array(
 			                                        array( //boton  SEARCH
			                                            'id' => 'btn_search',
			                                            'name' => 'search',
			                                            'value'    => lang('search'),
			                                            'type' => 'button',
			                                            'tab_index' => 2
			                                        ),
			   										 array( // TEXT IMPUT
			                                            'name'     => 'query',
			                                            'id'     => 'txt_query',
			                                            'value'    => '',//'',//$query,
			                                            'type' => 'text',
			                                            'size'    => 28,
			                                            'onkeypress' => 'return pulsar(event)',
	                                    				'tab_index' => 1
			                                        )
		                           				),
		                       		'hidden_value' => array(
		                       								)
												)
										  )
				);
			}

			$uicols = array (
				'input_type'	=>	array(text,text,text),
				'name'			=>	array(id,last_name,first_name),
				'formatter'		=>	array('','',''),
				'descr'			=>	array(lang('ID'),lang('last name'),lang('first name'))
			);

			$tenant_list = array();
			$tenant_list = $this->bo->read_tenant();

			$content = array();
			$j=0;
			if (isset($tenant_list) && is_array($tenant_list))
			{
				foreach($tenant_list as $tenant_entry)
				{
					for ($i=0;$i<count($uicols['name']);$i++)
					{
							$datatable['rows']['row'][$j]['column'][$i]['value'] 	= $tenant_entry[$uicols['name'][$i]];
							$datatable['rows']['row'][$j]['column'][$i]['name'] 	= $uicols['name'][$i];
					}
					$j++;
				}
			}

			$uicols_count	= count($uicols['descr']);
			$datatable['rowactions']['action'] = array();
			for ($i=0;$i<$uicols_count;$i++)
			{
				//all colums should be have formatter
				$datatable['headers']['header'][$i]['formatter'] = ($uicols['formatter'][$i]==''?  '""' : $uicols['formatter'][$i]);

				$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
				$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
				$datatable['headers']['header'][$i]['visible'] 			= true;
				$datatable['headers']['header'][$i]['sortable']			= true;
				$datatable['headers']['header'][$i]['sort_field'] 	= $uicols['name'][$i];
			}

			$function_exchange_values = '';

			$function_exchange_values .= 'opener.document.getElementsByName("tenant_id")[0].value = "";' ."\r\n";
			$function_exchange_values .= 'opener.document.getElementsByName("last_name")[0].value = "";' ."\r\n";
			$function_exchange_values .= 'opener.document.getElementsByName("first_name")[0].value = "";' ."\r\n";

			$function_exchange_values .= 'opener.document.getElementsByName("tenant_id")[0].value = data.getData("id");' ."\r\n";
			$function_exchange_values .= 'opener.document.getElementsByName("last_name")[0].value = data.getData("last_name");' ."\r\n";
			$function_exchange_values .= 'opener.document.getElementsByName("first_name")[0].value = data.getData("first_name");' ."\r\n";

			$function_exchange_values .= 'window.close()';

			$datatable['exchange_values'] = $function_exchange_values;
			$datatable['valida'] = '';

			// path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($tenant_list);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order'] 			= 'id'; // name key Column in myColumnDef
				$datatable['sorting']['sort'] 			= 'desc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['order']			= phpgw::get_var('order', 'string'); // name of column of Database
				$datatable['sorting']['sort'] 			= phpgw::get_var('sort', 'string'); // ASC / DESC
			}

			$appname						= lang('tenant');
			$function_msg					= lang('list tenant');


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
					'records'			=> array()
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

			//Title of Page
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

	  		// Prepare YUI Library
  			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'lookup.tenant.index', 'property' );

			$this->save_sessiondata();
		}

		function ns3420()
		{
			if( phpgw::get_var('phpgw_return_as') != 'json' )
			 {

		    	$datatable['config']['base_url']	= $GLOBALS['phpgw']->link('/index.php', array
	    				(
	    					'menuaction'			=> 'property.uilookup.ns3420',
								'second_display'	=> true,
								'cat_id'			=> $this->cat_id,
								'query'				=> $this->query,
								'filter'			=> $this->filter
	    				));

	    		$datatable['config']['allow_allrows'] = true;

				$datatable['config']['base_java_url'] = "menuaction:'property.uilookup.ns3420',"
	    											."second_display:'{true}',"
 	                        						."cat_id:'{$this->cat_id}',"
						 	                        ."query:'{$this->query}',"
 	                        						."filter:'{$this->filter}'";

				$datatable['actions']['form'] = array(
					array(
						'action'	=> $GLOBALS['phpgw']->link('/index.php',
								array(
									'menuaction' 		=> 'property.uilookup.ns3420',
									'second_display'	=> true,
									'cat_id'			=> $this->cat_id,
									'query'				=> $this->query,
									'filter'			=> $this->filter
									)
						),
						'fields'	=> array(
                                    'field' => array(
 			                                        array( //boton  SEARCH
			                                            'id' => 'btn_search',
			                                            'name' => 'search',
			                                            'value'    => lang('search'),
			                                            'type' => 'button',
			                                            'tab_index' => 2
			                                        ),
			   										 array( // TEXT IMPUT
			                                            'name'     => 'query',
			                                            'id'     => 'txt_query',
			                                            'value'    => '',//'',//$query,
			                                            'type' => 'text',
			                                            'size'    => 28,
			                                            'onkeypress' => 'return pulsar(event)',
	                                    				'tab_index' => 1
			                                        )
		                           				),
		                       		'hidden_value' => array(
		                       								)
												)
										  )
				);
			}

			$uicols = array (
				'input_type'	=>	array(text,text),
				'name'			=>	array(id,ns3420_descr),
				'sort_field'	=>	array(id,tekst1),
				'formatter'		=>	array('',''),
				'descr'			=>	array(lang('ID'),lang('ns3420 description'))
			);

			$ns3420_list = array();
			$ns3420_list = $this->bo->read_ns3420();

			$content = array();
			$j=0;
			if (isset($ns3420_list) && is_array($ns3420_list))
			{
				foreach($ns3420_list as $ns3420_entry)
				{
					for ($i=0;$i<count($uicols['name']);$i++)
					{
							$datatable['rows']['row'][$j]['column'][$i]['value'] 	= $ns3420_entry[$uicols['name'][$i]];
							$datatable['rows']['row'][$j]['column'][$i]['name'] 	= $uicols['name'][$i];
					}
					$j++;
				}
			}

			$uicols_count	= count($uicols['descr']);
			$datatable['rowactions']['action'] = array();
			for ($i=0;$i<$uicols_count;$i++)
			{
				//all colums should be have formatter
				$datatable['headers']['header'][$i]['formatter'] = ($uicols['formatter'][$i]==''?  '""' : $uicols['formatter'][$i]);

				$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
				$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
				$datatable['headers']['header'][$i]['visible'] 			= true;
				$datatable['headers']['header'][$i]['sortable']			= true;
				$datatable['headers']['header'][$i]['sort_field'] 	= $uicols['sort_field'][$i];
			}

			$function_exchange_values = '';

			$function_exchange_values .= 'opener.document.getElementsByName("ns3420_id")[0].value = "";' ."\r\n";
			$function_exchange_values .= 'opener.document.getElementsByName("ns3420_descr")[0].value = "";' ."\r\n";


			$function_exchange_values .= 'opener.document.getElementsByName("ns3420_id")[0].value = data.getData("id");' ."\r\n";
			$function_exchange_values .= 'opener.document.getElementsByName("ns3420_descr")[0].value = data.getData("ns3420_descr");' ."\r\n";

			$function_exchange_values .= 'window.close()';

			$datatable['exchange_values'] = $function_exchange_values;
			$datatable['valida'] = '';

			// path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($ns3420_entry);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order'] 			= 'id'; // name key Column in myColumnDef
				$datatable['sorting']['sort'] 			= 'asc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['order']			= phpgw::get_var('order', 'string'); // name of column of Database
				$datatable['sorting']['sort'] 			= phpgw::get_var('sort', 'string'); // ASC / DESC
			}

			$appname						= lang('standard description');
			$function_msg					= lang('list standard description');


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
					'records'			=> array()
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

			//Title of Page
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

	  		// Prepare YUI Library
  			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'lookup.tenant.index', 'property' );

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

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			 {

		    	$datatable['config']['base_url']	= $GLOBALS['phpgw']->link('/index.php', array
	    				(
	    					'menuaction'		=> 'property.uilookup.entity',
							'entity_id'			=> $this->entity_id,
							'cat_id'			=> $this->cat_id,
							'district_id'		=> $this->district_id,
							'query'				=> $this->query,
							'filter'			=> $this->filter

	    				));

	    		$datatable['config']['allow_allrows'] = true;

				$datatable['config']['base_java_url'] = "menuaction:'property.uilookup.entity',"
	    											."entity_id:'{$this->entity_id}',"
 	                        						."cat_id:'{$this->cat_id}',"
						 	                        ."district_id:'{$this->district_id}',"
 	                        						."query:'{$this->query}',"
						 	                        ."filter:'{$this->filter}'";

				$values_combo_box[0] = $boentity->select_category_list('filter',$this->cat_id);
				$default_value = array ('id'=>'','name'=>lang('no category'));
				array_unshift ($values_combo_box[0],$default_value);

				$values_combo_box[1]  = $bocommon->select_district_list('filter',$this->district_id);
				$default_value = array ('id'=>'','name'=>lang('no district'));
				array_unshift ($values_combo_box[1],$default_value);

				$datatable['actions']['form'] = array(
					array(
						'action'	=> $GLOBALS['phpgw']->link('/index.php',
								array(
									'menuaction' 		=> 'property.uilookup.entity',
									'entity_id'			=> $this->entity_id,
									'cat_id'			=> $this->cat_id,
									'district_id'		=> $this->district_id,
									'query'				=> $this->query,
									'filter'			=> $this->filter
									)
						),
						'fields'	=> array(
                                    'field' => array(
			                                        array( //boton 	CATEGORY
			                                            'id' => 'btn_cat_id',
			                                            'name' => 'cat_id',
			                                            'value'	=> lang('Category'),
			                                            'type' => 'button',
			                                            'style' => 'filter',
			                                            'tab_index' => 1
			                                        ),
			                                        array( //boton 	CATEGORY
			                                            'id' => 'btn_district_id',
			                                            'name' => 'district_id',
			                                            'value'	=> lang('District'),
			                                            'type' => 'button',
			                                            'style' => 'filter',
			                                            'tab_index' => 2
			                                        ),
			                                        array( //boton  SEARCH
			                                            'id' => 'btn_search',
			                                            'name' => 'search',
			                                            'value'    => lang('search'),
			                                            'type' => 'button',
			                                            'tab_index' => 4
			                                        ),
			   										array( // TEXT IMPUT
			                                            'name'     => 'query',
			                                            'id'     => 'txt_query',
			                                            'value'    => '',//'',//$query,
			                                            'type' => 'text',
			                                            'size'    => 28,
			                                            'onkeypress' => 'return pulsar(event)',
	                                    				'tab_index' => 3
			                                        )
		                           				),
		                       		'hidden_value' => array(
					                                        array( //div values  combo_box_0
							                                            'id' => 'values_combo_box_0',
							                                            'value'	=> $bocommon->select2String($values_combo_box[0]) //i.e.  id,value/id,vale/
							                                      ),
					                                        array( //div values  combo_box_0
							                                            'id' => 'values_combo_box_1',
							                                            'value'	=> $bocommon->select2String($values_combo_box[1]) //i.e.  id,value/id,vale/
							                                      )
		                       								)
												)
										  )
				);
			}

			$entity_list = $boentity->read(array('lookup'=>true));
			$input_name = $GLOBALS['phpgw']->session->appsession('lookup_fields','property');
			$uicols	= $boentity->uicols;

			if (count($uicols['name']) > 0)
			{
				for ($m = 0; $m<count($input_name); $m++)
				{
					if (!array_search($input_name[$m],$uicols['name']))
					{
						$uicols['name'][] 	= $input_name[$m];
						$uicols['input_type'][] 	= 'hidden';
					}
				}
			} else {

					$uicols['name'][] 	= 'num';
					$uicols['descr'][] 	= 'ID';
					$uicols['input_type'][] 	= 'text';
			}

			$content = array();
			$j=0;
			if (isset($entity_list) && is_array($entity_list))
			{
				foreach($entity_list as $entity_entry)
				{
					for ($i=0;$i<count($uicols['name']);$i++)
					{
						$datatable['rows']['row'][$j]['column'][$i]['value'] 	= $entity_entry[$uicols['name'][$i]];
						$datatable['rows']['row'][$j]['column'][$i]['name'] 	= $uicols['name'][$i];
					}
					/*for ($i=0;$i<count($input_name);$i++)
					{
						$datatable['rows']['row'][$j]['hidden'][$i]['value'] 	= $entity_entry[$input_name[$i]];
						$datatable['rows']['row'][$j]['hidden'][$i]['name'] 	= $input_name[$i];
					}*/
					$j++;
				}
			}

			$uicols_count	= count($uicols['descr']);
			$datatable['rowactions']['action'] = array();
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

					if($uicols['name'][$i]=='loc1' || $uicols['name'][$i]=='num')
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field']	= $uicols['name'][$i];
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

			$function_exchange_values = '';

			for ($i=0;$i<count($input_name);$i++)
			{
				$function_exchange_values .= 'opener.document.getElementsByName("'.$input_name[$i].'")[0].value = "";' ."\r\n";
			}

			for ($i=0;$i<count($input_name);$i++)
			{
				$function_exchange_values .= 'opener.document.getElementsByName("'.$input_name[$i].'")[0].value = data.getData("'.$input_name[$i].'");' ."\r\n";
			}

			$function_exchange_values .= 'window.close()';

			$datatable['exchange_values'] = $function_exchange_values;
			$datatable['valida'] = '';

			// path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($entity_list);
			$datatable['pagination']['records_total'] 	= $boentity->total_records;

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order'] 			= 'num'; // name key Column in myColumnDef
				$datatable['sorting']['sort'] 			= 'desc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['order']			= phpgw::get_var('order', 'string'); // name of column of Database
				$datatable['sorting']['sort'] 			= phpgw::get_var('sort', 'string'); // ASC / DESC
			}


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
					'headers'			=> $uicols

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

			if($this->entity_id)
			{
				$entity 	= $boadmin_entity->read_single($this->entity_id,false);
				$appname	= $entity['name'];
			}
			if($this->cat_id)
			{
				$category = $boadmin_entity->read_single_category($this->entity_id,$this->cat_id);
				$function_msg					= lang('lookup') . ' ' . $category['name'];
			}
			
			//Title of Page
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

	  		// Prepare YUI Library
  			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'lookup.entity.index', 'property' );

			$this->save_sessiondata();
		}

		function phpgw_user()
		{
			$column = phpgw::get_var('column');

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			 {

		    	$datatable['config']['base_url']	= $GLOBALS['phpgw']->link('/index.php', array
	    				(
	    					'menuaction'			=> 'property.uilookup.phpgw_user',
								'second_display'	=> true,
								'cat_id'			=> $this->cat_id,
								'query'				=> $this->query,
								'filter'			=> $this->filter,
								'column'			=> $column
	    				));

	    		$datatable['config']['allow_allrows'] = true;

				$datatable['config']['base_java_url'] = "menuaction:'property.uilookup.phpgw_user',"
	    											."second_display:'{true}',"
 	                        						."cat_id:'{$this->cat_id}',"
						 	                        ."query:'{$this->query}',"
 	                        						."filter:'{$this->filter}',"
 	                        						."column:'{$column}'";

				$datatable['actions']['form'] = array(
					array(
						'action'	=> $GLOBALS['phpgw']->link('/index.php',
								array(
									'menuaction' 		=> 'property.uilookup.phpgw_user',
									'second_display'	=> true,
									'cat_id'			=> $this->cat_id,
									'query'				=> $this->query,
									'filter'			=> $this->filter,
									'column'			=> $column
									)
						),
						'fields'	=> array(
                                    'field' => array(
 			                                        array( //boton  SEARCH
			                                            'id' => 'btn_search',
			                                            'name' => 'search',
			                                            'value'    => lang('search'),
			                                            'type' => 'button',
			                                            'tab_index' => 2
			                                        ),
			   										 array( // TEXT IMPUT
			                                            'name'     => 'query',
			                                            'id'     => 'txt_query',
			                                            'value'    => '',//'',//$query,
			                                            'type' => 'text',
			                                            'size'    => 28,
			                                            'onkeypress' => 'return pulsar(event)',
	                                    				'tab_index' => 1
			                                        )
		                           				),
		                       		'hidden_value' => array(
		                       								)
												)
										  )
				);
			}

			$uicols = array (
				'input_type'	=>	array(text,hidden,text,text),
				'name'			=>	array(id,account_lid,first_name,last_name),
				'sort_field'	=>	array(account_id,account_lid,account_firstname,account_lastname),
				'formatter'		=>	array('','','',''),
				'descr'			=>	array(lang('ID'),'',lang('first name'),lang('last name'))
			);

			$phpgw_user_list = array();
			$phpgw_user_list = $this->bo->read_phpgw_user();

			$content = array();
			$j=0;
			if (isset($phpgw_user_list) && is_array($phpgw_user_list))
			{
				foreach($phpgw_user_list as $phpgw_user_entry)
				{
					for ($i=0;$i<count($uicols['name']);$i++)
					{
							$datatable['rows']['row'][$j]['column'][$i]['value'] 	= $phpgw_user_entry[$uicols['name'][$i]];
							$datatable['rows']['row'][$j]['column'][$i]['name'] 	= $uicols['name'][$i];
					}
					$j++;
				}
			}

			$uicols_count	= count($uicols['descr']);
			$datatable['rowactions']['action'] = array();
			for ($i=0;$i<$uicols_count;$i++)
			{
				//all colums should be have formatter
				$datatable['headers']['header'][$i]['formatter'] = ($uicols['formatter'][$i]==''?  '""' : $uicols['formatter'][$i]);
				if($uicols['input_type'][$i]!='hidden')
				{
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= true;
					$datatable['headers']['header'][$i]['sortable']			= true;
					$datatable['headers']['header'][$i]['sort_field'] 	= $uicols['sort_field'][$i];
				}
				else
				{
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= false;
					$datatable['headers']['header'][$i]['sortable']			= false;
					$datatable['headers']['header'][$i]['format'] 			= 'hidden';
				}
			}

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

			$function_exchange_values = '';

			$function_exchange_values .= 'opener.document.getElementsByName("'.$user_id.'")[0].value = "";' ."\r\n";
			$function_exchange_values .= 'opener.document.getElementsByName("'.$user_name.'")[0].value = "";' ."\r\n";
			
			$function_exchange_values .= 'opener.document.getElementsByName("'.$user_id.'")[0].value = data.getData("id");' ."\r\n";
			$function_exchange_values .= 'opener.document.getElementsByName("'.$user_name.'")[0].value = data.getData("first_name") + " " + data.getData("last_name");' ."\r\n";

			$function_exchange_values .= 'window.close()';

			$datatable['exchange_values'] = $function_exchange_values;
			$datatable['valida'] = '';

			// path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($phpgw_user_list);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order'] 			= 'id'; // name key Column in myColumnDef
				$datatable['sorting']['sort'] 			= 'asc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['order']			= phpgw::get_var('order', 'string'); // name of column of Database
				$datatable['sorting']['sort'] 			= phpgw::get_var('sort', 'string'); // ASC / DESC
			}

			$appname						= lang('standard description');
			$function_msg					= lang('list standard description');


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
					'records'			=> array()
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

			//Title of Page
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

	  		// Prepare YUI Library
  			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'lookup.tenant.index', 'property' );

			$this->save_sessiondata();
		}

		function project_group()
		{
			if( phpgw::get_var('phpgw_return_as') != 'json' )
			 {

		    	$datatable['config']['base_url']	= $GLOBALS['phpgw']->link('/index.php', array
	    				(
	    					'menuaction'			=> 'property.uilookup.project_group',
								'second_display'	=> true,
								'cat_id'			=> $this->cat_id,
								'query'				=> $this->query,
								'filter'			=> $this->filter
	    				));
	    		$datatable['config']['allow_allrows'] = true;

				$datatable['config']['base_java_url'] = "menuaction:'property.uilookup.project_group',"
	    											."second_display:'{true}',"
 	                        						."cat_id:'{$this->cat_id}',"
						 	                        ."query:'{$this->query}',"
 	                        						."filter:'{$this->filter}'";

				$datatable['actions']['form'] = array(
					array(
						'action'	=> $GLOBALS['phpgw']->link('/index.php',
								array(
									'menuaction' 		=> 'property.uilookup.project_group',
									'second_display'	=> true,
									'cat_id'			=> $this->cat_id,
									'query'				=> $this->query,
									'filter'			=> $this->filter
									)
						),
						'fields'	=> array(
                                    'field' => array(
 			                                        array( //boton  SEARCH
			                                            'id' => 'btn_search',
			                                            'name' => 'search',
			                                            'value'    => lang('search'),
			                                            'type' => 'button',
			                                            'tab_index' => 2
			                                        ),
			   										 array( // TEXT IMPUT
			                                            'name'     => 'query',
			                                            'id'     => 'txt_query',
			                                            'value'    => '',//'',//$query,
			                                            'type' => 'text',
			                                            'size'    => 28,
			                                            'onkeypress' => 'return pulsar(event)',
	                                    				'tab_index' => 1
			                                        )
		                           				),
		                       		'hidden_value' => array(
		                       								)
												)
										  )
				);
			}

			$uicols = array (
				'input_type'	=>	array(text,text),
				'name'			=>	array(id,descr),
				'formatter'		=>	array('',''),
				'descr'			=>	array(lang('ID'),lang('Name'))
			);

			$project_group_list = array();
			$project_group_list = $this->bo->read_project_group();

			$content = array();
			$j=0;
			if (isset($project_group_list) && is_array($project_group_list))
			{
				foreach($project_group_list as $project_group_entry)
				{
					for ($i=0;$i<count($uicols['name']);$i++)
					{
							$datatable['rows']['row'][$j]['column'][$i]['value'] 	= $project_group_entry[$uicols['name'][$i]];
							$datatable['rows']['row'][$j]['column'][$i]['name'] 	= $uicols['name'][$i];
					}
					$j++;
				}
			}

			$uicols_count	= count($uicols['descr']);
			$datatable['rowactions']['action'] = array();
			for ($i=0;$i<$uicols_count;$i++)
			{
				//all colums should be have formatter
				$datatable['headers']['header'][$i]['formatter'] = ($uicols['formatter'][$i]==''?  '""' : $uicols['formatter'][$i]);

				$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
				$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
				$datatable['headers']['header'][$i]['visible'] 			= true;
				$datatable['headers']['header'][$i]['sortable']			= true;
				$datatable['headers']['header'][$i]['sort_field'] 	= $uicols['name'][$i];
			}

			$function_exchange_values = '';

			$function_exchange_values .= 'opener.document.getElementsByName("project_group")[0].value = "";' ."\r\n";
			$function_exchange_values .= 'opener.document.getElementsByName("project_group_descr")[0].value = "";' ."\r\n";
			
			$function_exchange_values .= 'opener.document.getElementsByName("project_group")[0].value = data.getData("id");' ."\r\n";
			$function_exchange_values .= 'opener.document.getElementsByName("project_group_descr")[0].value = data.getData("descr");' ."\r\n";

			$function_exchange_values .= 'window.close()';

			$datatable['exchange_values'] = $function_exchange_values;
			$datatable['valida'] = '';

			// path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($project_group_list);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order'] 			= 'id'; // name key Column in myColumnDef
				$datatable['sorting']['sort'] 			= 'desc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['order']			= phpgw::get_var('order', 'string'); // name of column of Database
				$datatable['sorting']['sort'] 			= phpgw::get_var('sort', 'string'); // ASC / DESC
			}

			$appname						= lang('project group');
			$function_msg					= lang('list project group');


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
					'records'			=> array()
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


			//Title of Page
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

	  		// Prepare YUI Library
  			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'lookup.tenant.index', 'property' );

			$this->save_sessiondata();
		}
	}
