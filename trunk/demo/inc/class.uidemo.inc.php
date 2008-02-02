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
	   the Free Software Foundation, either version 3 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

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
		* @var object $menu application menu handler
		*/
		private $menu;
		
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
			$this->menu				= CreateObject('demo.menu');
			$this->menu->set_sub('demo');
			$this->acl 				=& $GLOBALS['phpgw']->acl;
			$this->acl_location 	= $this->bo->get_acl_location();
			$this->acl_read 		= $this->acl->check($this->acl_location, PHPGW_ACL_READ);
			$this->acl_add 			= $this->acl->check($this->acl_location, PHPGW_ACL_ADD);
			$this->acl_edit 		= $this->acl->check($this->acl_location, PHPGW_ACL_EDIT);
			$this->acl_delete 		= $this->acl->check($this->acl_location, PHPGW_ACL_DELETE);

			$this->start			= $this->bo->start;
			$this->query			= $this->bo->query;
			$this->sort				= $this->bo->sort;
			$this->order			= $this->bo->order;
			$this->allrows			= $this->bo->allrows;
			$this->cat_id			= $this->bo->cat_id;
			$this->filter			= $this->bo->filter;
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
		
			$this->menu->set_sub($output);
			$links = $this->menu->links();

			if(!$this->acl_read)
			{
				$this->no_access($links);
				return;
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('demo','nextmatchs','menu',
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
				'links'									=> $links,
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
			
			$this->menu->set_sub('alternative');
			$links = $this->menu->links();
			if(!$this->acl_read)
			{
				$this->no_access($links);
				return;
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('demo','nextmatchs','menu',
										'search_field'));

			$demo_info = $this->bo->read2();

			$uicols	= $this->bo->uicols;
//_debug_array($uicols);
			$j=0;

			if (isset($demo_info) && is_array($demo_info))
			{
				foreach($demo_info as $entry)
				{
					for ($i=0;$i<count($uicols['name']);$i++)
					{
						if($uicols['input_type'][$i]!='hidden')
						{
							$content[$j]['row'][$i]['value'] 		= $entry[$uicols['name'][$i]];
							$content[$j]['row'][$i]['name'] 		= $uicols['name'][$i];
							if($uicols['input_type'][$i]=='link')
							{
								$content[$j]['row'][$i]['text']		= lang('link');
								$content[$j]['row'][$i]['link']		= $entry[$uicols['name'][$i]];
								$content[$j]['row'][$i]['target']	= '_blank';
							}
						}
					}

					if($this->acl_read)
					{
						$content[$j]['row'][$i]['statustext']			= lang('view the record');
						$content[$j]['row'][$i]['text']					= lang('view');
						$content[$j]['row'][$i++]['link']				= $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'demo.uidemo.view','demo_id'=> $entry['id']));
					}
					if($this->acl_edit)
					{
						$content[$j]['row'][$i]['statustext']			= lang('edit the record');
						$content[$j]['row'][$i]['text']					= lang('edit');
						$content[$j]['row'][$i++]['link']				= $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'demo.uidemo.edit', 'demo_id'=> $entry['id']));
					}
					if($this->acl_delete)
					{
						$content[$j]['row'][$i]['statustext']			= lang('delete the record');
						$content[$j]['row'][$i]['text']					= lang('delete');
						$content[$j]['row'][$i++]['link']				= $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'demo.uidemo.delete', 'demo_id'=> $entry['id']));
					}

					$j++;
				}
			}

			for ($i=0;$i<count($uicols['descr']);$i++)
			{
				if($uicols['input_type'][$i]!='hidden')
				{
					$table_header[$i]['header'] 	= $uicols['descr'][$i];
					$table_header[$i]['width'] 		= '5%';
					$table_header[$i]['align'] 		= 'center';
					if($uicols['datatype'][$i]!='T' && $uicols['datatype'][$i]!='CH')
					{
						$table_header[$i]['sort_link']	=true;
						$table_header[$i]['sort'] 		= $this->nextmatches->show_sort_order(array
						(
							'sort'	=> $this->sort,
							'var'	=> $uicols['name'][$i],
							'order'	=> $this->order,
							'extra'	=> array
							(
								 'menuaction'	=> 'demo.uidemo.index2',
								 'query'			=> $this->query,
								 'cat_id'		=> $this->cat_id,
								 'filter'		=> $this->filter,
								 'output'		=> $output,
								 'allrows'		=> $this->allrows
							)
						));
					}
				}
			}

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


			if($this->acl_add)
			{
				$table_add[] = array
				(
					'lang_add'				=> lang('add'),
					'lang_add_statustext'	=> lang('add a demo'),
					'add_action'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'demo.uidemo.edit','output'=>$output)),
				);
			}

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
				'menuaction'	=> 'demo.uidemo.index2',
				'sort'			=> $this->sort,
				'order'			=> $this->order,
				'cat_id'		=> $this->cat_id,
				'filter'		=> $this->filter,
				'query'			=> $this->query,
				'output'		=> $output
			);

			$msgbox_data = (isset($receipt)?$GLOBALS['phpgw']->common->msgbox_data($receipt):'');

			$data = array
			(
				'msgbox_data'							=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'links'									=> $links,
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

//_debug_array($data);
			$function_msg= lang('list demo values');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('demo'). ": {$function_msg}";
			
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array("list2_{$output}" => $data));
			$this->save_sessiondata();
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

			$GLOBALS['phpgw']->xslttpl->add_file(array('demo','attributes_form'));

			if (isset($values) && is_array($values))
			{
				if(!$this->acl_edit)
				{
					$this->no_access($links);
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
							$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'=> 'demo.uidemo.index', 'output'=> $output));
						}
					}
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'=> 'demo.uidemo.index', 'output'=> $output));
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

			$msgbox_data = (isset($receipt)?$GLOBALS['phpgw']->common->msgbox_data($receipt):'');

			$data = array
			(
				'value_entry_date'				=> (isset($values['entry_date'])?$values['entry_date']:''),
				'value_name'					=> (isset($values['name'])?$values['name']:''),
				'value_address'					=> (isset($values['address'])?$values['address']:''),
				'value_zip'						=> (isset($values['zip'])?$values['zip']:''),
				'value_town'					=> (isset($values['town'])?$values['town']:''),
				'value_remark'					=> (isset($values['remark'])?$values['remark']:''),

				'lang_entry_date'				=> lang('Entry date'),
				'lang_name'						=> lang('name'),
				'lang_address'					=> lang('address'),
				'lang_zip'						=> lang('zip'),
				'lang_town'						=> lang('town'),
				'lang_remark'					=> lang('remark'),

				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_id'						=> lang('training ID'),
				'lang_save'						=> lang('save'),
				'lang_cancel'					=> lang('cancel'),
				'value_id'						=> $demo_id,
				'lang_done_status_text'			=> lang('Back to the list'),
				'lang_save_status_text'			=> lang('Save the training'),
				'lang_apply'					=> lang('apply'),
				'lang_apply_status_text'		=> lang('Apply the values'),

				'lang_category'					=> lang('category'),
				'lang_no_cat'					=> lang('no category'),
				'cat_select'					=> $this->cats->formatted_xslt_list(array('select_name' => 'values[cat_id]','selected' => (isset($values['cat_id'])?$values['cat_id']:''))),
				'attributes_values'				=> $values['attributes'],

				'lang_access'					=> lang('private'),
				'value_access'					=> (isset($values['access'])?$values['access']:''),
				'lang_access_off_statustext'	=> lang('The note is public. If the note should be private, check this box'),
				'lang_access_on_statustext'		=> lang('The note is private. If the note should be public, uncheck this box')
			);

			$appname		= lang('demo');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('demo') . " - {$appname}: {$function_msg}";
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
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
			$GLOBALS['phpgw']->xslttpl->add_file(array('no_access','menu'));

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
