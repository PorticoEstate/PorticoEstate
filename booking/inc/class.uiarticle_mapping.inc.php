<?php
	/**
	 * phpGroupWare - booking: a part of a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2016 Free Software Foundation, Inc. http://www.fsf.org/
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
	 * @internal Development of this article was funded by http://www.bergen.kommune.no/
	 * @package booking
	 * @subpackage article
	 * @version $Id: $
	 */
	phpgw::import_class('phpgwapi.uicommon');
	phpgw::import_class('phpgwapi.datetime');

	include_class('booking', 'article_mapping', 'inc/model/');

	class booking_uiarticle_mapping extends phpgwapi_uicommon
	{
		const STATUS_ACTIVE = 1;

		public $public_functions = array(
			'add'						 => true,
			'index'						 => true,
			'query'						 => true,
			'view'						 => true,
			'edit'						 => true,
			'save'						 => true,
			'get'						 => true,
			'get_services'				 => true,
			'get_reserved_resources'	 => true,
			'handle_multi_upload_file'	 => true,
			'_get_files'				 => true,
			'view_file'					 => true,
			'update_file_data'			 => true,
			'get_articles'				 => true,
			'get_pricing'				 => true
		);
		protected
			$fields,
			$permissions,
			$currentapp;

		public function __construct()
		{
			parent::__construct();
			$GLOBALS['phpgw_info']['flags']['app_header']	 .= '::' . lang('article mapping');
			$this->bo										 = createObject('booking.boarticle_mapping');
			$this->fields									 = booking_article_mapping::get_fields();
			$this->permissions								 = booking_article_mapping::get_instance()->get_permission_array();
			$this->currentapp								 = $GLOBALS['phpgw_info']['flags']['currentapp'];
			self::set_active_menu("{$this->currentapp}::commerce::article");
		}

		private function get_category_options( $selected = 1 )
		{
			$category_options	 = array();
			$category_list		 = execMethod('booking.bogeneric.get_list', array('type' => 'article_category'));

			foreach ($category_list as $category)
			{
				$category_options[] = array(
					'id'		 => $category['id'],
					'name'		 => $category['name'],
					'selected'	 => $category['id'] == $selected ? 1 : 0
				);
			}
			return $category_options;
		}

		private function get_status_options( $selected = 1 )
		{
			$status_options	 = array();
			$status_list	 = array(
				0					 => lang('all'),
				self::STATUS_ACTIVE	 => lang('active'),
			);

			foreach ($status_list as $status_id => $status_name)
			{
				$status_options[] = array(
					'id'		 => $status_id,
					'name'		 => $status_name,
					'selected'	 => $status_id == $selected ? 1 : 0
				);
			}
			return $status_options;
		}

		private function get_unit_list( $selected = 0 )
		{
			$unit_list	 = array();
			$unit_list[] = array('id' => 'each', 'name' => lang('each'));
			$unit_list[] = array('id' => 'kg', 'name' => lang('kg'));
			$unit_list[] = array('id' => 'm', 'name' => lang('meter'));
			$unit_list[] = array('id' => 'm2', 'name' => lang('square meter'));
			$unit_list[] = array('id' => 'minute', 'name' => lang('minute'));
			$unit_list[] = array('id' => 'hour', 'name' => lang('hour'));
			$unit_list[] = array('id' => 'day', 'name' => lang('day'));

			foreach ($unit_list as &$unit)
			{
				$unit['selected'] = $unit['id'] == $selected ? 1 : 0;
			}
			return $unit_list;
		}

		public function get_reserved_resources()
		{
			$building_id = phpgw::get_var('building_id', 'int');
			return $this->bo->get_reserved_resources($building_id);
		}

		public function get_services( $selected = 0 )
		{
			$services_list = execMethod('booking.bogeneric.get_list', array('type' => 'article_service'));

			$alredy_taken = $this->bo->get_mapped_services();

			foreach ($services_list as $service)
			{
				if ($selected != $service['id'] && in_array($service['id'], $alredy_taken))
				{
					continue;
				}

				$service_options[] = array(
					'id'		 => $service['id'],
					'name'		 => $service['name'],
					'selected'	 => $service['id'] == $selected ? 1 : 0
				);
			}
			return $service_options;
		}

		public function get_articles()
		{
			$resources	 = phpgw::get_var('resources', 'int', 'GET');
			$application_id	 = phpgw::get_var('application_id', 'int', 'GET');
			$reservation_type	 = phpgw::get_var('reservation_type', 'string', 'GET');
			$reservation_id	 = phpgw::get_var('reservation_id', 'int', 'GET');
			$alloc_template_id	 = phpgw::get_var('alloc_template_id', 'int', 'GET');

			if($alloc_template_id)
			{
				$alloc = CreateObject('booking.boseason')->wtemplate_alloc_read_single($alloc_template_id);
				if(!empty($alloc['articles']))
				{
					$selected_alloc_articles = array();
					$alloc_articles = $alloc['articles'];
					if($alloc_articles && is_array($alloc_articles))
					{
						foreach ($alloc_articles as $alloc_article)
						{
							$_article_info = explode('_', $alloc_article);

							if(empty($_article_info[0]))
							{
								continue;
							}

							/**
							 * the value selected_articles[]
							 * <mapping_id>_<quantity>_<tax_code>_<ex_tax_price>_<parent_mapping_id>
							 */

							$article_mapping_id = $_article_info[0];
							$parent_mapping_id		= !empty($_article_info[4]) ? $_article_info[4] : null;

							$identificator = "{$article_mapping_id}_{$parent_mapping_id}";

							$selected_alloc_articles[$identificator] = array(
								'quantity'				=> $_article_info[1],
								'tax_code'				=> $_article_info[2],
								'ex_tax_price'			=> $_article_info[3],
								'parent_mapping_id'		=> $parent_mapping_id
							);
						}

					}
				}
			}


			$purchase_order = createObject('booking.sopurchase_order')->get_purchase_order($application_id, $reservation_type, $reservation_id);
			$articles	 = $this->bo->get_articles($resources);

			foreach ($articles as &$article)
			{
				if(!empty($purchase_order['lines']))
				{
					foreach ($purchase_order['lines'] as $line)
					{
						if($line['article_mapping_id'] == $article['id']  && (int)$line['parent_mapping_id'] == (int)$article['parent_mapping_id'])
						{
							$article['unit_price']					 = $line['unit_price'];
							$article['ex_tax_price']				 = $line['unit_price'];
							$article['price']						 = $line['price'];
							$article['selected_quantity']			 = $line['quantity'];
							$article['selected_sum']				 = (float)($line['amount'] + $line['tax']);
							$article['selected_article_quantity']	 = "{$article['id']}_{$line['quantity']}_{$line['tax_code']}_{$line['unit_price']}_{$article['parent_mapping_id']}";
							$article['tax_code']					 = $line['tax_code'];
							$article['tax_percent']					 = $line['tax_percent'];
							$article['tax']							 = $line['unit_price'] * ($line['tax_percent']/100);
						}
					}
				}

				if(!empty($selected_alloc_articles["{$article['id']}_{$article['parent_mapping_id']}"]))
				{
					$article['selected_quantity']			= $selected_alloc_articles["{$article['id']}_{$article['parent_mapping_id']}"]['quantity'];
					$article['tax_code']					= $selected_alloc_articles["{$article['id']}_{$article['parent_mapping_id']}"]['tax_code'];
					$article['ex_tax_price']				= number_format((float)$selected_alloc_articles["{$article['id']}_{$article['parent_mapping_id']}"]['ex_tax_price'], 2, '.', '');
					$article['selected_article_quantity']	 = "{$article['id']}_{$article['selected_quantity']}_{$article['tax_code']}_{$article['ex_tax_price']}_{$article['parent_mapping_id']}";
				}

				$article['ex_tax_price'] = number_format((float)$article['ex_tax_price'], 2, '.', '');
				$article['unit_price']	 = number_format((float)$article['unit_price'], 2, '.', '');
				$article['price']		 = number_format((float)$article['price'], 2, '.', '');
				$article['tax']			 = number_format((float)$article['tax'], 2, '.', '');
				$article['mandatory']	 = isset($article['resource_id']) ? 1 : '';

				if(empty($article['selected_quantity']))
				{
					$article['selected_quantity']	 = isset($article['resource_id']) ? 1 : '';
				}

				if(empty($article['selected_article_quantity']))
				{
					$article['selected_article_quantity']	 = isset($article['resource_id']) ? "{$article['id']}_1" : '';
				}

				if(empty($article['selected_sum']))
				{
					$article['selected_sum']		 = isset($article['resource_id']) ? $article['price'] : '';
				}
			}

			return array('data' => $articles);
		}

		public function index()
		{
			if (empty($this->permissions[PHPGW_ACL_READ]))
			{
				$message = '';
				if ($this->currentapp == 'bookingfrontend')
				{
					$message = lang('you need to log in to access this page.');
				}
				phpgw::no_access(false, $message);
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				$relaxe_acl = true;
				return $this->query($relaxe_acl);
			}

			phpgwapi_jquery::load_widget('autocomplete');

			$function_msg = lang('article');

			$data = array(
				'datatable_name' => $function_msg,
				'form'			 => array(
					'toolbar' => array(
						'item' => array(
							array(
								'type'	 => 'filter',
								'name'	 => 'filter_article_cat_id',
								'text'	 => lang('category'),
								'list'	 => $this->get_category_options()
							),
							array(
								'type'	 => 'filter',
								'name'	 => 'filter_active',
								'text'	 => lang('status'),
								'list'	 => $this->get_status_options()
							),
//							array(
//								'type' =>  $this->currentapp == 'booking' ? 'checkbox' : 'hidden',
//								'name' => 'filter_active',
//								'text' => lang('showall'),
//								'value' =>  1,
//								'checked'=> 1,
//							)
						)
					)
				),
				'datatable'		 => array(
					'source'		 => self::link(array(
						'menuaction'		 => "{$this->currentapp}.uiarticle_mapping.index",
						'phpgw_return_as'	 => 'json'
					)),
					'allrows'		 => true,
					'new_item'		 => self::link(array('menuaction' => "{$this->currentapp}.uiarticle_mapping.add")),
					'editor_action'	 => '',
					'field'			 => parent::_get_fields()
				)
			);

			$parameters = array(
				'parameter' => array(
					array(
						'name'	 => 'id',
						'source' => 'id'
					)
				)
			);

			/* 			$data['datatable']['actions'][] = array
			  (
			  'my_name' => 'view',
			  'text' => lang('show'),
			  'action' => self::link(array
			  (
			  'menuaction' => "{$this->currentapp}.uiarticle_mapping.view"
			  )),
			  'parameters' => json_encode($parameters)
			  );
			 */
			$data['datatable']['actions'][] = array
				(
				'my_name'	 => 'edit',
				'text'		 => lang('edit'),
				'action'	 => self::link(array
					(
					'menuaction' => "{$this->currentapp}.uiarticle_mapping.edit"
				)),
				'parameters' => json_encode($parameters)
			);

			self::add_javascript($this->currentapp, 'base', 'article_mapping.index.js', 'text/javascript', true);
			phpgwapi_jquery::load_widget('numberformat');

			self::render_template_xsl('datatable_jquery', $data);
		}

		public function get_pricing()
		{
			$id	 = phpgw::get_var('id', 'int');
			$filter_active	 = phpgw::get_var('filter_active', 'bool');

			$pricing	 = $this->bo->get_pricing($id, $filter_active);

			return $pricing;
		}
		/*
		 * Edit the price item with the id given in the http variable 'id'
		 */
		public function edit( $values = array(), $mode = 'edit' )
		{
			$active_tab										 = !empty($values['active_tab']) ? $values['active_tab'] : phpgw::get_var('active_tab', 'string', 'REQUEST', 'first_tab');
			if (empty($this->permissions[PHPGW_ACL_ADD]))
			{
				phpgw::no_access();
			}

			if (!empty($values['object']))
			{
				$article = $values['object'];
			}
			else
			{
				$id		 = !empty($values['id']) ? $values['id'] : phpgw::get_var('id', 'int');
				$article = $this->bo->read_single($id, true, true);
			}

			$id = (int)$id;

			$tabs				 = array();
			$tabs['first_tab']	 = array(
				'label'	 => lang('article'),
				'link'	 => '#first_tab',
			);
			$tabs['prizing']	 = array(
				'label'		 => lang('prizing'),
				'link'		 => '#prizing',
				'disable'	 => empty($id) ? true : false
			);
			$tabs['files']		 = array(
				'label'		 => lang('files'),
				'link'		 => '#files',
				'disable'	 => empty($id) ? true : false
			);

			$pricing	 = $this->bo->get_pricing($id);
			$dateformat	 = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			foreach ($pricing as $key => &$price)
			{
				$price['value_date']		 = $GLOBALS['phpgw']->common->show_date(strtotime($price['from_']), 'Y-m-d');
				$active_checked				 = $price['active'] ? 'checked' : '';
				$price['active_checkbox']	 = "<input type='checkbox' {$active_checked}  name='price_table[active][{$price['id']}]' value='1'>";
				$default_checked			 = $price['default_'] ? 'checked' : '';
				$price['default_radio']		 = "<input type='radio' {$default_checked}  name='price_table[default_]' value='{$price['id']}'>";
				$price['delete_checkbox']	 = "<input type='checkbox' name='price_table[delete][{$price['id']}]' value='{$price['id']}'>";
			}

			$pricing_def = array(
				array('key' => 'id', 'label' => '#', 'sortable' => true, 'resizeable' => true),
				array('key' => 'article_mapping_id', 'label' => lang('article id'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'price', 'label' => lang('price'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'value_date', 'label' => lang('from'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'remark', 'label' => lang('remark'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'active_checkbox', 'label' => lang('active'), 'sortable' => false, 'resizeable' => true),
				array('key' => 'default_radio', 'label' => lang('default'), 'sortable' => false, 'resizeable' => true),
				array('key' => 'delete_checkbox', 'label' => lang('delete'), 'sortable' => false, 'resizeable' => true),
			);

			$datatable_def	 = array();
			$datatable_def[] = array(
				'container'	 => 'datatable-container_0',
				'requestUrl' => "''",
				'ColumnDefs' => $pricing_def,
				'data'		 => json_encode($pricing),
				'config'	 => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$file_def = array
				(
				array('key' => 'file_name', 'label' => lang('Filename'), 'sortable' => false, 'resizeable' => true),
				array('key' => 'picture', 'label' => '', 'sortable' => false, 'resizeable' => false, 'formatter' => 'JqueryPortico.showPicture'),
			);

			$requestUrl	 = json_encode(self::link(array(
					'menuaction'		 => "{$this->currentapp}.uiarticle_mapping.update_file_data",
					'location_id'		 => $GLOBALS['phpgw']->locations->get_id('booking', '.article'),
					'location_item_id'	 => $id,
					'phpgw_return_as'	 => 'json')
			));
			$requestUrl	 = str_replace('&amp;', '&', $requestUrl);

			$buttons = array(
				array(
					'action'		 => 'delete_file',
					'type'			 => 'buttons',
					'name'			 => 'delete',
					'label'			 => lang('Delete file'),
					'funct'			 => 'onActionsClick_files',
					'classname'		 => '',
					'value_hidden'	 => "",
					'confirm_msg'	 => "Vil du slette fil(er)"
				),
			);

			$tabletools = array
				(
				array('my_name' => 'select_all'),
				array('my_name' => 'select_none')
			);

			foreach ($buttons as $entry)
			{
				$tabletools[] = array
					(
					'my_name'		 => $entry['name'],
					'text'			 => $entry['label'],
					'className'		 => $entry['classname'],
					'confirm_msg'	 => $entry['confirm_msg'],
					'type'			 => 'custom',
					'custom_code'	 => "
						var api = oTable1.api();
						var selected = api.rows( { selected: true } ).data();
						var ids = [];
						for ( var n = 0; n < selected.length; ++n )
						{
							var aData = selected[n];
							ids.push(aData['file_id']);
						}
						{$entry['funct']}('{$entry['action']}', ids);
						"
				);
			}

			$code = <<<JS

	this.onActionsClick_filter_files=function(action, ids)
	{
		var tags = $('select#tags').val();
		var oArgs = {menuaction: '{$this->currentapp}.uiarticle_mapping.update_data',action:'get_files', id: {$id}};
		var requestUrl = phpGWLink('index.php', oArgs, true);
		$.each(tags, function (k, v)
		{
			requestUrl += '&tags[]=' + v;
		});
		JqueryPortico.updateinlineTableHelper('datatable-container_1', requestUrl);
	}

	this.onActionsClick_files=function(action, ids)
	{
		var numSelected = 	ids.length;
console.log(ids);
		if (numSelected ==0)
		{
			alert('None selected');
			return false;
		}
		var tags = $('select#tags').val();

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: {$requestUrl},
			data:{ids:ids, tags:tags, action:action},
			success: function(data) {
				if( data != null)
				{

				}
	//			var oArgs = {menuaction: '{$this->currentapp}.uiarticle_mapping.update_data',action:'get_files', id: {$id}};
	//			var strURL = phpGWLink('index.php', oArgs, true);

				JqueryPortico.updateinlineTableHelper('datatable-container_1');

				if(action=='delete_file')
				{
		//			refresh_glider(strURL);
				}
			},
			error: function(data) {
				alert('feil');
			}
		});
	}
JS;
			$GLOBALS['phpgw']->js->add_code('', $code);

			$datatable_def[] = array
				(
				'container'	 => 'datatable-container_1',
				'requestUrl' => json_encode(self::link(array('menuaction'		 => "{$this->currentapp}.uiarticle_mapping._get_files",
						'id'				 => $id,
						'section'			 => 'documents',
						'phpgw_return_as'	 => 'json'))),
				'ColumnDefs' => $file_def,
				'data'		 => json_encode(array()),
				'tabletools' => $tabletools,
				'config'	 => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$GLOBALS['phpgw']->jqcal2->add_listener('date_from', 'date');
			$data = array(
				'datatable_def'			 => $datatable_def,
				'form_action'			 => self::link(array('menuaction' => "{$this->currentapp}.uiarticle_mapping.save")),
				'cancel_url'			 => self::link(array('menuaction' => "{$this->currentapp}.uiarticle_mapping.index",)),
				'article'				 => $article,
				'article_categories'	 => array('options' => $this->get_category_options($article->article_cat_id)),
				'unit_list'				 => array('options' => $this->get_unit_list($article->unit)),
				'tax_code_list'			 => array('options' => execMethod('booking.bogeneric.get_list', array('type' => 'tax', 'order' => 'id', 'selected' => $article->tax_code))),
				'service_list'			 => ( $id && $article->article_cat_id == 2 ) ? array('options' => $this->get_services($article->article_id)) : array(),
				'mode'					 => $mode,
				'tabs'					 => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'value_active_tab'		 => $active_tab,
				'fileupload'			 => true,
				'multi_upload_action'	 => self::link(array('menuaction' => "{$this->currentapp}.uiarticle_mapping.handle_multi_upload_file", 'id' => $id, 'section' => $section)),
				'multiple_uploader'		 => true,
				'resources_json'		 => ( $id && $article->article_cat_id == 1 ) ? json_encode(array($article->article_id)) : '[]'
			);

			$GLOBALS['phpgw_info']['flags']['app_header']	 .= '::' . lang('edit');

			if($article->article_name)
			{
				$GLOBALS['phpgw_info']['flags']['app_header'] = lang('edit') . '::' . $article->article_name;
			}

			self::add_javascript('booking', 'base', 'common');
			phpgwapi_jquery::load_widget('autocomplete');
			phpgwapi_jquery::load_widget('file-upload-minimum');
			phpgwapi_jquery::formvalidator_generate(array());
			self::add_javascript('booking', 'base', 'article_mapping.js');
			self::render_template_xsl(array('article_mapping', 'datatable_inline', 'files', 'multi_upload_file_inline'), array($mode => $data));
		}
		/*
		 * Get the article with the id given in the http variable 'id'
		 */

		public function get( $id = 0 )
		{
			if (empty($this->permissions[PHPGW_ACL_ADD]))
			{
				phpgw::no_access();
			}

			$id = !empty($id) ? $id : phpgw::get_var('id', 'int');

			$article = $this->bo->read_single($id)->toArray();

			unset($article['secret']);

			return $article;
		}

		public function update_file_data()
		{
			if (empty($this->permissions[PHPGW_ACL_ADD]))
			{
				return array();
			}

			$section			 = phpgw::get_var('section', 'string', 'REQUEST', 'documents');
			$location_item_id	 = phpgw::get_var('location_item_id', 'int');
			$ids				 = phpgw::get_var('ids', 'int');
			$action				 = phpgw::get_var('action', 'string');
			$tags				 = phpgw::get_var('tags', 'string');

			$fakebase	 = '/booking';
			$bofiles	 = CreateObject('property.bofiles', $fakebase);

			if ($action == 'delete_file' && $ids && $location_item_id)
			{
				$bofiles->delete_file("/article/{$location_item_id}/{$section}/", array('file_action' => $ids));
			}
			else if ($action == 'set_tag' && $ids)
			{
				$bofiles->set_tags($ids, $tags);
			}
			else if ($action == 'remove_tag' && $ids)
			{
				$bofiles->remove_tags($ids, $tags);
			}

			return $action;
		}

		function _get_files()
		{
			$id		 = phpgw::get_var('id', 'int');
			$section = phpgw::get_var('section', 'string', 'REQUEST', 'documents');

			if (empty($this->permissions[PHPGW_ACL_READ]))
			{
				return array();
			}


			$vfs				 = CreateObject('phpgwapi.vfs');
			$vfs->override_acl	 = 1;

			$files = $vfs->ls(array(
				'string'	 => "/booking/article/{$id}/$section",
				'relatives'	 => array(RELATIVE_NONE)));

			$vfs->override_acl = 0;

			$img_types = array(
				'image/jpeg',
				'image/png',
				'image/gif'
			);

			$content_files	 = array();
			$lang_view		 = lang('click to view file');
			$lang_delete	 = lang('Check to delete file');

			$z = 0;
			foreach ($files as $_entry)
			{
				$link_file_data = array
					(
					'menuaction' => "{$this->currentapp}.uiarticle_mapping.view_file",
					'file_id'	 => $_entry['file_id']
				);

				$link_view_file = $GLOBALS['phpgw']->link('/index.php', $link_file_data);

				$content_files[] = array(
					'file_id'		 => $_entry['file_id'],
					'file_name'		 => "<a href='{$link_view_file}' target='_blank' title='{$lang_view}'>{$_entry['name']}</a>",
					'delete_file'	 => "<input type='checkbox' name='values[file_action][]' value='{$_entry['file_id']}' title='{$lang_delete}'>",
				);
				if (in_array($_entry['mime_type'], $img_types))
				{
					$content_files[$z]['file_name']		 = $_entry['name'];
					$content_files[$z]['img_id']		 = $_entry['file_id'];
					$content_files[$z]['img_url']		 = self::link(array(
							'menuaction' => "{$this->currentapp}.uiarticle_mapping.view_file",
							'file_id'	 => $_entry['file_id'],
							'file'		 => $_entry['directory'] . '/' . urlencode($_entry['name'])
					));
					$content_files[$z]['thumbnail_flag'] = 'thumb=1';
				}
				$z++;
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				$total_records = count($content_files);

				return array
					(
					'data'				 => $content_files,
					'draw'				 => phpgw::get_var('draw', 'int'),
					'recordsTotal'		 => $total_records,
					'recordsFiltered'	 => $total_records
				);
			}
			return $content_files;
		}

		public function handle_multi_upload_file()
		{
			if (empty($this->permissions[PHPGW_ACL_ADD]))
			{
				phpgw::no_access();
			}

			$section = phpgw::get_var('section', 'string', 'REQUEST', 'documents');
			$id		 = phpgw::get_var('id', 'int', 'GET');

			phpgw::import_class('property.multiuploader');

			$options['fakebase']	 = "/booking";
			$options['base_dir']	 = "article/{$id}/{$section}";
			$options['upload_dir']	 = $GLOBALS['phpgw_info']['server']['files_dir'] . '/booking/' . $options['base_dir'] . '/';
			$options['script_url']	 = html_entity_decode(self::link(array('menuaction' => "{$this->currentapp}.uiarticle_mapping.handle_multi_upload_file", 'id' => $id, 'section' => $section)));
			$upload_handler			 = new property_multiuploader($options, false);

			switch ($_SERVER['REQUEST_METHOD'])
			{
				case 'OPTIONS':
				case 'HEAD':
					$upload_handler->head();
					break;
				case 'GET':
					$upload_handler->get();
					break;
				case 'PATCH':
				case 'PUT':
				case 'POST':
					$upload_handler->add_file();
					break;
				case 'DELETE':
					$upload_handler->delete_file();
					break;
				default:
					$upload_handler->header('HTTP/1.1 405 Method Not Allowed');
			}

			$GLOBALS['phpgw']->common->phpgw_exit();
		}
	}