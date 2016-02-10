<?php
	/**
	* phpGroupWare - DEMO: a demo aplication.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package demo
	* @subpackage demo
 	* @version $Id$
	*/

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	phpgw::import_class('rental.socontract');
	phpgw::import_class('phpgwapi.yui');
	/**
	 * Description
	 * @package demo
	 */

	class demo_uidemo
	{
		/**
		* @var ??? $grants ???
		*/
		private $grants;

		/**
		* @var ??? $start ???
		*/
		private $start;

		/**
		* @var ??? $query ???
		*/
		private $query;

		/**
		* @var ??? $sort ???
		*/
		private $sort;

		/**
		* @var ??? $order ???
		*/
		private $order;

		/**
		* @var object $cats categories object
		*/
		private $cats;

		/**
		* @var object $nextmatches paging handler
		*/
		private $nextmatches;

		/**
		* @var int $account reference to the current user id
		*/
		private $account;

		/**
		* @var object $bo business logic
		*/
		private $bo;

		/**
		* @var object $acl reference to global access control list manager
		*/
		private $acl;

		/**
		* @var string $acl_location the access control location
		*/
		private $acl_location;

		/**
		* @var bool $acl_read does the current user have read access to the current location
		*/
		private $acl_read;

		/**
		* @var bool $acl_add does the current user have add access to the current location
		*/
		private $acl_add;

		/**
		* @var bool $acl_edit does the current user have edit access to the current location
		*/
		private $acl_edit;

		/**
		* @var bool $allrows display all rows of result set?
		*/
		private $allrows;

		/**
		* @var int $cat_id the currently selected category
		*/
		private $cat_id;

		/**
		* @var bool $filter the current filter
		*/
		private $filter;

		/**
		* @var array $public_functions publicly available methods of the class
		*/
		public $public_functions = array
		(
			'index' 	=> true,
			'index2'	=> true,
			'view'		=> true,
			'edit'		=> true,
			'delete'	=> true,
			'no_access'	=> true
		);

		public function __construct()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$this->cats				= CreateObject('phpgwapi.categories');
			$this->nextmatches		= CreateObject('phpgwapi.nextmatchs');
			$this->account			=& $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo				= CreateObject('demo.bodemo',true);
			$this->acl 				=& $GLOBALS['phpgw']->acl;
			$this->acl_location 	= $this->bo->get_acl_location();
			$this->acl_read 		= $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'demo');
			$this->acl_add 			= $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'demo');
			$this->acl_edit 		= $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'demo');
			$this->acl_delete 		= $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'demo');

			$this->start			= $this->bo->start;
			$this->query			= $this->bo->query;
			$this->sort				= $this->bo->sort;
			$this->order			= $this->bo->order;
			$this->allrows			= $this->bo->allrows;
			$this->cat_id			= $this->bo->cat_id;
			$this->filter			= $this->bo->filter;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'demo';
			$this->bocommon		= CreateObject('property.bocommon');
		}

		private function save_sessiondata()
		{
			$data = array
			(
				'start'		=> $this->start,
				'query'		=> $this->query,
				'sort'		=> $this->sort,
				'order'		=> $this->order,
			);
			$this->bo->save_sessiondata($data);
		}

		public function index()
		{
			$output	= self::get_output();

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= "::{$output}";

			if(!$this->acl_read)
			{
				$this->no_access();
				return;
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('demo','nextmatchs',
										'search_field'));

			$demo_info = $this->bo->read();

			foreach ( $demo_info as $entry )
			{

				$link_view					= '';
				$lang_view_demo_text		= '';
				$text_view					= '';
				if ( demo_bodemo::check_perms($entry['grants'], PHPGW_ACL_READ))
				{
					$link_view					= $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'demo.uidemo.view', 'demo_id'=> $entry['id'],'output'=>$output));
					$lang_view_demo_text		= lang('view the demo');
					$text_view					= lang('view');
				}

				$link_edit					= '';
				$lang_edit_demo_text		= '';
				$text_edit					= '';
				if ( demo_bodemo::check_perms($entry['grants'], PHPGW_ACL_EDIT))
				{
					$link_edit					= $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'demo.uidemo.edit', 'demo_id'=> $entry['id'],'output'=>$output));
					$lang_edit_demo_text		= lang('edit the demo');
					$text_edit					= lang('edit');
				}

				$link_delete				= '';
				$text_delete				= '';
				$lang_delete_demo_text		= '';
				if ( demo_bodemo::check_perms($entry['grants'], PHPGW_ACL_DELETE))
				{
					$link_delete				= $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'demo.uidemo.delete', 'demo_id'=> $entry['id'],'output'=>$output));
					$text_delete				= lang('delete');
					$lang_delete_demo_text		= lang('delete the demo');
				}

				$content[] = array
				(
					'name'						=> $entry['name'],
					'link_edit'					=> $link_edit,
					'link_delete'				=> $link_delete,
					'link_view'					=> $link_view,
					'lang_view_demo_text'		=> $lang_view_demo_text,
					'lang_edit_demo_text'		=> $lang_edit_demo_text,
					'text_view'					=> $text_view,
					'text_edit'					=> $text_edit,
					'text_delete'				=> $text_delete,
					'lang_delete_demo_text'		=> $lang_delete_demo_text,
				);
			}

//_debug_array($content);

			$table_header[] = array
			(
				'sort_name'	=> $this->nextmatches->show_sort_order(array
				(
					'sort'	=> $this->sort,
					'var'	=> 'name',
					'order'	=> $this->order,
					'extra'	=> array
					(
						'menuaction'	=> 'demo.uidemo.index',
						'query'		=> $this->query,
						'cat_id'	=> $this->cat_id,
						'filter'	=> $this->filter,
						'output'	=> $output,
						'allrows'	=> $this->allrows
					)
				)),
				'lang_name'		=> lang('name'),
				'lang_view'		=> lang('view'),
				'lang_edit'		=> (isset($this->acl_edit)?lang('edit'):''),
				'lang_delete'	=> (isset($this->acl_delete)?lang('view'):''),
			);

			if(!$this->allrows)
			{
				$record_limit	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$record_limit	= $this->bo->total_records;
			}

			$link_data = array
			(
				'menuaction'	=> 'demo.uidemo.index',
				'sort'			=> $this->sort,
				'order'			=> $this->order,
				'cat_id'		=> $this->cat_id,
				'filter'		=> $this->filter,
				'query'			=> $this->query,
				'output'		=> $output
			);

			$table_add[] = array
			(
				'lang_add'				=> lang('add'),
				'lang_add_statustext'	=> lang('add a demo'),
				'add_action'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'demo.uidemo.edit','output'=>$output)),
			);

			$msgbox_data = (isset($receipt)?$GLOBALS['phpgw']->common->msgbox_data($receipt):'');

			$data = array
			(
				'msgbox_data'							=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'cat_filter'							=> $this->cats->formatted_xslt_list(array('select_name' => 'cat_id','selected' => $this->cat_id,'globals' => true,'link_data' => $link_data)),
				'filter_data'							=> $this->nextmatches->xslt_filter(array('filter' => $this->filter,'link_data' => $link_data)),
				'allow_allrows'							=> true,
				'allrows'								=> $this->allrows,
				'start_record'							=> $this->start,
				'record_limit'							=> $record_limit,
				'num_records'							=> ($demo_info?count($demo_info):0),
				'all_records'							=> $this->bo->total_records,
				'link_url'								=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'								=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_searchfield_statustext'			=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'			=> lang('Submit the search string'),
				'query'									=> $this->query,
				'lang_search'							=> lang('search'),
				'table_header'							=> $table_header,
				'table_add'								=> $table_add,
				'values'								=> (isset($content)?$content:'')
			);

			$function_msg= lang('list demo values');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('demo') . ": {$function_msg}";

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array("list_{$output}" => $data));
			$this->save_sessiondata();
		}

		public function index2()
		{
			$output	= self::get_output();

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::alternative';
			if(!$this->acl_read)
			{
				$this->no_access();
				return;
			}
			if(!$this->acl_read)
			{
				$this->bocommon->no_access();
				return;
			}

			$receipt = $GLOBALS['phpgw']->session->appsession('session_data', "demo_receipt");
			$this->save_sessiondata();

			$datatable = array();

			$dry_run = false;

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
				$datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
				(
					'menuaction'	=> 'demo.uidemo.index2'
				));

				$datatable['config']['base_java_url'] = "menuaction:'demo.uidemo.index2'";

				$link_data = array
				(
					'menuaction'	=> 'demo.uidemo.index2'
				);


				$datatable['config']['allow_allrows'] = true;

				$values_combo_box = array();
				$values_combo_box[0] = $this->cats->formatted_xslt_list(array('format'=>'filter','selected' => $this->cat_id,'globals' => true,'use_acl' => $this->_category_acl));
				$default_value = array ('cat_id'=>'','name'=> lang('no category'));
				array_unshift ($values_combo_box[0]['cat_list'],$default_value);

				$datatable['actions']['form'] = array
					(
						array
						(
							'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array
							(
								'menuaction'	=> 'demo.uidemo.index2'
							)
						),
						'fields'	=> array
						(
							'field' => array
							(
								array
								( //boton 	CATEGORY
									'id' => 'btn_cat_id',
									'name' => 'cat_id',
									'value'	=> lang('Category'),
									'type' => 'button',
									'style' => 'filter',
									'tab_index' => 1
								),
								array
								(
									'type'	=> 'button',
									'id'	=> 'btn_export',
									'value'	=> lang('download'),
									'tab_index' => 10
								),
								array
								(
									'type'	=> 'button',
									'id'	=> 'btn_done',
									'value'	=> lang('done'),
									'tab_index' => 9
								),
								array
								(
									'type'	=> 'button',
									'id'	=> 'btn_new',
									'value'	=> lang('add'),
									'tab_index' => 8
								),
								array
								( //button     SEARCH
									'id' => 'btn_search',
									'name' => 'search',
									'value'    => lang('search'),
									'type' => 'button',
									'tab_index' => 7
								),
								array
								( // TEXT INPUT
									'name'     => 'query',
									'id'     => 'txt_query',
									'value'    => $this->query,
									'type' => 'text',
									'onkeypress' => 'return pulsar(event)',
									'size'    => 28,
									'tab_index' => 6
								)
							),
							'hidden_value' => array
							(
								array
								( //div values  combo_box_0
									'id' => 'values_combo_box_0',
									'value'	=> $this->bocommon->select2String($values_combo_box[0]['cat_list'], 'cat_id') //i.e.  id,value/id,vale/
								)
							)
						)
					)
				);

//				$dry_run = false;
			}

			$values = $this->bo->read2($dry_run);

			$uicols = $this->bo->uicols;

			$j = 0;
			$count_uicols_name = count($uicols['name']);

			foreach($values as $entry)
			{
				for ($k=0;$k<$count_uicols_name;$k++)
				{
					if($uicols['input_type'][$k]!='hidden')
					{
						$datatable['rows']['row'][$j]['column'][$k]['name'] 		= $uicols['name'][$k];
						$datatable['rows']['row'][$j]['column'][$k]['value']		= $entry[$uicols['name'][$k]];
						$datatable['rows']['row'][$j]['column'][$k]['format']		= $uicols['datatype'][$k];
					}
				}
				$j++;
			}

			$datatable['rowactions']['action'] = array();

			$parameters = array
				(
					'parameter' => array
					(
						array
						(
							'name'		=> 'demo_id',
							'source'	=>  'id'
						),
					)
				);

			if($this->acl_edit)
			{
				$datatable['rowactions']['action'][] = array
					(
						'my_name' 		=> 'edit',
						'statustext' 	=> lang('edit the entry'),
						'text'			=> lang('edit'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'		=> 'demo.uidemo.edit'
						)),
						'parameters'	=> $parameters
					);
				$datatable['rowactions']['action'][] = array
					(
						'my_name'		=> 'edit',
						'text' 			=> lang('open edit in new window'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'		=> 'demo.uidemo.edit',
							'target'			=> '_blank'
						)),
						'parameters'	=> $parameters
					);
			}

			if($this->acl_delete)
			{
				$datatable['rowactions']['action'][] = array
					(
						'my_name' 		=> 'delete',
						'statustext' 	=> lang('delete the entry'),
						'text'			=> lang('delete'),
						'confirm_msg'	=> lang('do you really want to delete this entry'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'demo.uidemo.delete'
						)),
						'parameters'	=> $parameters
					);
			}
			unset($parameters);

			if($this->acl_add)
			{
				$datatable['rowactions']['action'][] = array
					(
						'my_name' 			=> 'add',
						'statustext' 	=> lang('add'),
						'text'			=> lang('add'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'demo.uidemo.edit',
							'type'			=> $this->type,
							'type_id'		=> $this->type_id
						))
					);
			}

			for ($i=0;$i<$count_uicols_name;$i++)
			{
				if($uicols['input_type'][$i]!='hidden')
				{
					$datatable['headers']['header'][$i]['formatter'] 		= ($uicols['formatter'][$i]==''?  '""' : $uicols['formatter'][$i]);
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= true;
					$datatable['headers']['header'][$i]['sortable']			= $uicols['sortable'][$i];
					$datatable['headers']['header'][$i]['sort_field']   	= $uicols['name'][$i];
				}
			}

			//path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($values);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			$appname			=  $this->location_info['name'];
			$function_msg		= lang('list %1', $appname);

			if ( ($this->start == 0) && (!$this->order))
			{
				$datatable['sorting']['order'] 			=  $this->location_info['id']['name']; // name key Column in myColumnDef
				$datatable['sorting']['sort'] 			= 'asc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['order']			= $this->order; // name of column of Database
				$datatable['sorting']['sort'] 			= $this->sort; // ASC / DESC
			}

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

			// values for datatable
			if(isset($datatable['rows']['row']) && is_array($datatable['rows']['row']))
			{
				foreach( $datatable['rows']['row'] as $row )
				{
					$json_row = array();
					foreach( $row['column'] as $column)
					{
						if(isset($column['format']) && $column['format']== 'link' && $column['java_link']==true)
						{
							$json_row[$column['name']] = "<a href='#' id='{$column['link']}' onclick='javascript:filter_data(this.id);'>{$column['value']}</a>";
						}
						else if(isset($column['format']) && $column['format']== 'link')
						{
							$json_row[$column['name']] = "<a href='{$column['value']}' target='_blank'>" .lang('link') . '</a>';
						}
						else if(isset($column['format']) && $column['format']== 'text')
						{
							$json_row[$column['name']] = nl2br($column['value']);
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
				$json['rights'] = $datatable['rowactions']['action'];
			}

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				return $json;
			}

			phpgwapi_yui::load_widget('dragdrop');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('menu');
			phpgwapi_yui::load_widget('connection');
			phpgwapi_yui::load_widget('loader');
			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('paginator');
			phpgwapi_yui::load_widget('animation');

			$datatable['json_data'] = json_encode($json);
			//-------------------- JSON CODE ----------------------

			$template_vars = array();
			$template_vars['datatable'] = $datatable;
			$GLOBALS['phpgw']->xslttpl->add_file(array('datatable'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', $template_vars);

			if ( !isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css) )
			{
				$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
			}

			$GLOBALS['phpgw']->css->validate_file('datatable');
			$GLOBALS['phpgw']->css->validate_file('property');
			$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');

			$GLOBALS['phpgw_info']['flags']['app_header'] = $GLOBALS['phpgw']->translation->translate($this->location_info['acl_app'], array(), false, $this->location_info['acl_app']) . "::{$appname}::{$function_msg}";

			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'demo.index', 'demo' );
		}


		public function edit()
		{
			$acl_location = '.demo_location';
			if(!$this->acl_add)
			{
				$this->no_access();
				return;
			}

			$output	= self::get_output();

			$demo_id	= phpgw::get_var('demo_id', 'int');
			$values		= phpgw::get_var('values', 'string', 'POST');
			$values_attribute  = phpgw::get_var('values_attribute', 'string', 'POST');

			$insert_record_values = $GLOBALS['phpgw']->session->appsession('insert_record_values'. $acl_location,'demo');

			if(isset($insert_record_values) && is_array($insert_record_values))
			{
				for ($j=0;$j<count($insert_record_values);$j++)
				{
					$insert_record['extra'][$insert_record_values[$j]]	= $insert_record_values[$j];
				}
			}

			if (isset($values) && is_array($values))
			{
				if(!$this->acl_edit)
				{
					$this->no_access();
					return;
				}

				if(isset($insert_record['extra']) && is_array($insert_record['extra']))
				{
					while (is_array($insert_record['extra']) && list($key,$column) = each($insert_record['extra']))
					{
						if($_POST[$key])
						{
							$values['extra'][$column]	= $_POST[$key];
						}
					}
				}

				if ((isset($values['save']) && $values['save']) || (isset($values['apply']) && $values['apply']))
				{
					if($GLOBALS['phpgw']->session->is_repost())
					{
						$receipt['error'][]=array('msg'=>lang('Hmm... looks like a repost!'));
					}

					if(!$values['cat_id'] || $values['cat_id'] == 'none')
					{
						$receipt['error'][]=array('msg'=>lang('Please select a category!'));
					}
					if(!$values['name'])
					{
						$receipt['error'][]=array('msg'=>lang('Please enter a name !'));
					}
					if(!$values['address'])
					{
						$receipt['error'][]=array('msg'=>lang('Please enter an address !'));
					}
					if(!$values['zip'])
					{
						$receipt['error'][]=array('msg'=>lang('Please enter a zip code !'));
					}
					else if(!ctype_digit($values['zip']))
					{
						$receipt['error'][]=array('msg'=>lang('Please enter a integer as zip code !'));
					}
					if(!$values['town'])
					{
						$receipt['error'][]=array('msg'=>lang('Please enter a town !'));
					}

					if(isset($values_attribute) && is_array($values_attribute))
					{
						foreach ($values_attribute as $attribute )
						{
							if($attribute['allow_null'] != 'true' && !$attribute['value'])
							{
								$receipt['error'][]=array('msg'=>lang('Please enter value for attribute %1', $attribute['input_text']));
							}
						}
					}

					if($demo_id)
					{
						$values['demo_id']=$demo_id;
					}

					if(!isset($receipt['error']) || !$receipt['error'])
					{
						$receipt = $this->bo->save($values,$values_attribute);
						$demo_id = $receipt['demo_id'];

						if (isset($values['save']) && $values['save'])
						{
							$GLOBALS['phpgw']->session->appsession('session_data','demo_receipt',$receipt);
							$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'=> 'demo.uidemo.index2', 'output'=> $output));
						}
					}
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'=> 'demo.uidemo.index2', 'output'=> $output));
				}
			}

			$values = $this->bo->read_single($demo_id);

			/* Preserve attribute values from post */
			if(isset($receipt['error']) && (isset( $values_attribute) && is_array( $values_attribute)))
			{
				$values = $this->bo->preserve_attribute_values($values,$values_attribute);
			}

			if ($demo_id)
			{
				$function_msg = lang('edit demo');
			}
			else
			{
				$function_msg = lang('add demo');
			}

			$link_data = array
			(
				'menuaction'	=> 'demo.uidemo.edit',
				'demo_id'		=> $demo_id,
				'output'		=> $output
			);

			$generic_list_1 = array();
			for ($i=1;$i<9;$i++)
			{
				$generic_list_1[] = array
				(
					'id'	=> $i,
					'name'	=> "Element A {$i}"
				);
			}
			$generic_list_1[2]['selected'] = 1;

			$generic_list_2 = array();
			for ($i=1;$i<4;$i++)
			{
				$generic_list_2[] = array
				(
					'id'	=> $i,
					'name'	=> "Element B {$i}"
				);
			}

 			// date 1 (jscalendar)
 			execMethod('phpgwapi.jscalendar.add_listener','values_start_date');

			// date 2 (YUI)
			$end_date = $GLOBALS['phpgw']->yuical->add_listener('end_date',date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], time()));




			//inline tables
			$values['consume'][] = array
			(
				'amount'	=> 123456,
				'date'		=> 2012,
				'delete'	=> ''
			);
			$values['consume'][] = array
			(
				'amount'	=> 6789012,
				'date'		=> 2013,
				'delete'	=> ''
			);
			
			
			$datavalues[0] = array
			(
				'name'					=> "0",
				'values' 				=> json_encode($values['consume']),
				'total_records'			=> count($values['consume']),
				'edit_action'			=> "''",
				'is_paginator'			=> 1,
				'footer'				=> 0
			);



			$myColumnDefs[0] = array
			(
				'name'		=> "0",
				'values'	=>	json_encode(array(	array('key' => 'amount','label'=>lang('amount'),'sortable'=>true,'resizeable'=>true, 'formatter' => FormatterRight),
													array('key' => 'date','label'=>lang('date'),'sortable'=>true,'resizeable'=>true),
													array('key' => 'delete','label'=>lang('delete'),'sortable'=>false,'resizeable'=>false)))
			);

			$msgbox_data = isset($receipt)?$GLOBALS['phpgw']->common->msgbox_data($receipt):'';

			$data = array
			(
				'contract'						=> rental_socontract::get_instance()->get_single(19)->toArray(),
				'value_entry_date'				=> isset($values['entry_date'])?$values['entry_date']:'',
				'value_name'					=> isset($values['name'])?$values['name']:'',
				'value_address'					=> isset($values['address'])?$values['address']:'',
				'value_zip'						=> isset($values['zip'])?$values['zip']:'',
				'value_town'					=> isset($values['town'])?$values['town']:'',
				'value_remark'					=> isset($values['remark'])?$values['remark']:'',

				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'value_id'						=> $demo_id,

				'cat_select'					=> $this->cats->formatted_xslt_list(array('select_name' => 'values[cat_id]','selected' => (isset($values['cat_id'])?$values['cat_id']:''))),
				'custom_attributes'				=> array('attributes' => $values['attributes']),
				'value_access'					=> isset($values['access'])?$values['access']:'',
				'generic_list_1'				=> array('options' => $generic_list_1),
				'generic_list_2'				=> array('options' => $generic_list_2),

				'value_start_date'				=> date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], time()),
				'img_cal'						=> $GLOBALS['phpgw']->common->image('phpgwapi','cal'),
				'end_date'						=> $end_date,
				//inline tables
				'property_js'					=> json_encode($GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property2.js"),
				'datatable'						=> $datavalues,
				'myColumnDefs'					=> $myColumnDefs,
				'tabs'							=> self::_generate_tabs(),
				'textareacols'					=> 60,
				'textarearows'					=> 10
			);

			$GLOBALS['phpgw']->richtext->replace_element('remark');
			$GLOBALS['phpgw']->richtext->generate_script();

			$appname		= lang('demo');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('demo') . " - {$appname}: {$function_msg}";
			$GLOBALS['phpgw']->xslttpl->add_file(array('edit','attributes_form'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));

			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'demo.edit', 'demo' );

			phpgwapi_yui::load_widget('dragdrop');
			phpgwapi_yui::load_widget('datatable');
		//	phpgwapi_yui::load_widget('menu');
			phpgwapi_yui::load_widget('connection');
			phpgwapi_yui::load_widget('loader');
			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('paginator');
			phpgwapi_yui::load_widget('animation');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');

		}


		protected function _generate_tabs()
		{
			$tabs = array
			(
				'general'	=> array('label' => lang('general'), 'link' => '#general'),
				'list'		=> array('label' => lang('list'), 'link' => '#list'),
				'tables'	=> array('label' => lang('inline tables'), 'link' => '#tables'),
				'dates'		=> array('label' => lang('dates'), 'link' => '#dates'),
				'custom'	=> array('label' => lang('custom attributes'), 'link' => '#custom'),
			);

			phpgwapi_yui::tabview_setup('demo_tabview');

			return  phpgwapi_yui::tabview_generate($tabs, 'general');
		}


		public function view()
		{
			if(!$this->acl_read)
			{
				$this->no_access();
				return;
			}

			$output	= self::get_output();

			$demo_id	= phpgw::get_var('demo_id', 'int');
			$values		= phpgw::get_var('values', 'string', 'POST');

			$GLOBALS['phpgw']->xslttpl->add_file(array('demo','attributes_view'));

			if ($demo_id)
			{
				$values = $this->bo->read_single($demo_id);
				$function_msg = lang('view demo');
			}
			else
			{
				return;
			}

			$data = array
			(
				'value_entry_date'			=> (isset($values['entry_date'])?$values['entry_date']:''),
				'value_name'				=> (isset($values['name'])?$values['name']:''),
				'value_address'				=> (isset($values['address'])?$values['address']:''),
				'value_zip'					=> (isset($values['zip'])?$values['zip']:''),
				'value_town'				=> (isset($values['town'])?$values['town']:''),
				'value_remark'				=> (isset($values['remark'])?$values['remark']:''),

				'lang_id'					=> lang('demo ID'),
				'lang_entry_date'			=> lang('Entry date'),
				'lang_name'					=> lang('name'),
				'lang_address'				=> lang('address'),
				'lang_zip'					=> lang('zip'),
				'lang_town'					=> lang('town'),
				'lang_remark'				=> lang('remark'),

				'form_action'				=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'demo.uidemo.index','output'=>$output)),
				'lang_cancel'				=> lang('cancel'),
				'value_id'					=> $demo_id,
				'lang_category'				=> lang('category'),
				'value_cat'					=> $this->cats->id2name($values['cat_id']),
				'attributes_values'			=> $values['attributes'],
				'lang_access'				=> lang('private'),
				'value_access'				=> (isset($values['access'])?lang($values['access']):'')
			);

			$appname	= lang('demo');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('demo') . " - {$appname}: {$function_msg}";
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view' => $data));
		}

		public function delete()
		{
			if(!$this->acl_delete)
			{
				$this->no_access();
				return;
			}

			$output	= self::get_output();

			$demo_id	= get_var('demo_id',array('POST','GET'));
			$confirm	= get_var('confirm',array('POST'));

			$link_data = array
			(
				'menuaction' => 'demo.uidemo.index'
			);

			if ( phpgw::get_var('confirm', 'bool', 'POST') )
			{
				$this->bo->delete($demo_id);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'demo.uidemo.delete', 'demo_id'=> $demo_id)),
				'lang_confirm_msg'		=> lang('do you really want to delete this entry'),
				'lang_yes'				=> lang('yes'),
				'lang_yes_statustext'	=> lang('Delete the entry'),
				'lang_no_statustext'	=> lang('Back to the list'),
				'lang_no'				=> lang('no')
			);

			$appname		= lang('demo');
			$function_msg	= lang('delete');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('demo') . " - {$appname}: {$function_msg}";

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		}

		public function no_access($links = '')
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('no_access'));

			$receipt['error'][]=array('msg'=>lang('NO ACCESS'));

			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'	=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'links'			=> $links,
			);

			$appname	= lang('No access');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('demo') . " - {$appname}";
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('no_access' => $data));
		}

		/**
		* Get the output format
		*
		* @return string the output format - html, wml etc
		*/
		private static function get_output()
		{
			$output = phpgw::get_var('output', 'string', 'REQUEST', 'html');
			$GLOBALS['phpgw']->xslttpl->set_output($output);
			return $output;
		}
	}
