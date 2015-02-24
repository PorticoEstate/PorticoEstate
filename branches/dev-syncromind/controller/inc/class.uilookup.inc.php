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
 	* @version $Id: class.uilookup.inc.php 11909 2014-04-13 16:49:53Z sigurdne $
	*/

	/**
	 * Description
	 * @package property
	 */
	phpgw::import_class('phpgwapi.yui');

	class controller_uilookup
	{
		var $cat_id;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;

		var $public_functions = array
			(
				'control'		=> true
			);

		function __construct()
		{
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$GLOBALS['phpgw_info']['flags']['headonly']=true;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;

			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->filter				= $this->bo->filter;
			$this->cat_id				= $this->bo->cat_id;
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

		function control()
		{
			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{

				$datatable['config']['base_url']	= $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'			=> 'controller.uilookup.control',
						'second_display'	=> true,
						'cat_id'			=> $this->cat_id,
						'query'				=> $this->query,
						'filter'			=> $this->filter,
					));

				$datatable['config']['allow_allrows'] = true;

				$datatable['config']['base_java_url'] = "menuaction:'controller.uilookup.control',"
					."second_display:true,"
					."cat_id:'{$this->cat_id}',"
					."query:'{$this->query}',"
					."filter:'{$this->filter}'";

				$datatable['actions']['form'] = array
					(
						array
						(
							'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array
							(
								'menuaction' 		=> 'controller.uilookup.control',
								'second_display'	=> true,
								'cat_id'			=> $this->cat_id,
								'query'				=> $this->query,
								'filter'			=> $this->filter
							)
						),
						'fields'	=> array
						(
							'field' => array
							(
								array
								( //boton  SEARCH
									'id' => 'btn_search',
									'name' => 'search',
									'value'    => lang('search'),
									'type' => 'button',
									'tab_index' => 2
								),
								array
								( // TEXT IMPUT
									'name'     => 'query',
									'id'     => 'txt_query',
									'value'    => '',//'',//$query,
									'type' => 'text',
									'size'    => 28,
									'onkeypress' => 'return pulsar(event)',
									'tab_index' => 1
								)
							),
							'hidden_value' => array
							(
								)
							)
						)
					);
			}

			$uicols = array (
				'input_type'	=>	array('text','text',),
				'name'			=>	array('id','title'),
				'sort_field'	=>	array('id','title'),
				'formatter'		=>	array('',''),
				'descr'			=>	array(lang('ID'),lang('title'))
			);

			$values = array();
			$uicontrol	= CreateObject('controller.uicontrol');

			$_GET['startIndex'] = phpgw::get_var('start');
			$_REQUEST['startIndex'] = phpgw::get_var('start');
			$_GET['dir'] = phpgw::get_var('sort', 'string', 'GET', 'ASC');
			$_REQUEST['dir'] = phpgw::get_var('sort', 'string', 'GET', 'ASC');
			$_GET['sort'] = phpgw::get_var('order', 'string', 'GET', 'id');
			$_REQUEST['sort'] = phpgw::get_var('order', 'string', 'GET', 'id');


			$values = $uicontrol->query();

			$json = array
				(
					'recordsReturned' 	=> $values['ResultSet']['recordsReturned'],
					'totalRecords' 		=> $values['ResultSet']['totalRecords'],
					'startIndex' 		=> $values['ResultSet']['startIndex'],
				//	'pageSize' 		=> $values['ResultSet']['pageSize'],
					'sort'				=> $values['ResultSet']['sortKey'],
					'dir'				=> $values['ResultSet']['sortDir'],
					'records'			=> $values['ResultSet']['Result'],
				);

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				return $json;
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

			$function_exchange_values = '';

			$function_exchange_values .= 'opener.document.getElementById("control_id").value = "";' ."\r\n";
			$function_exchange_values .= 'opener.document.getElementById("control_name").value = "";' ."\r\n";

			$function_exchange_values .= 'opener.document.getElementById("control_id").value = data.getData("id");' ."\r\n";
			$function_exchange_values .= 'opener.document.getElementById("control_name").value = data.getData("title");'  ."\r\n";

			$function_exchange_values .= 'window.close()';

			$datatable['exchange_values'] = $function_exchange_values;
			$datatable['valida'] = '';

			// path for property.js
			$property_js = "/property/js/yahoo/property.js";

			if (!isset($GLOBALS['phpgw_info']['server']['no_jscombine']) || !$GLOBALS['phpgw_info']['server']['no_jscombine'])
			{
				$cachedir = urlencode($GLOBALS['phpgw_info']['server']['temp_dir']);
				$property_js = "/phpgwapi/inc/combine.php?cachedir={$cachedir}&type=javascript&files=" . str_replace('/', '--', ltrim($property_js,'/'));
			}

			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url'] . $property_js;

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)phpgw::get_var('start');
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= $values['ResultSet']['recordsReturned'];
			$datatable['pagination']['records_total'] 	= $values['ResultSet']['totalRecords'];

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order'] 			= 'id'; // name key Column in myColumnDef
				$datatable['sorting']['sort'] 			= 'asc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['order']			= phpgw::get_var('sort', 'string'); // name of column of Database
				$datatable['sorting']['sort'] 			= phpgw::get_var('dir', 'string'); // ASC / DESC
			}

			$appname						= lang('controller');
			$function_msg					= '';


			//-- BEGIN----------------------------- JSON CODE ------------------------------

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

			//Title of Page
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('controller') . ' - ' . $appname . ': ' . $function_msg;

			// Prepare generic YUI Library for old style lookup
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'lookup.tenant.index', 'property' );
		}
	}
