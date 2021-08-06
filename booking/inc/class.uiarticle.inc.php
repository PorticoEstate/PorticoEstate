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

	include_class('booking', 'article', 'inc/model/');

	class booking_uiarticle extends phpgwapi_uicommon
	{

		public $public_functions = array(
			'add'			 => true,
			'index'			 => true,
			'query'			 => true,
			'view'			 => true,
			'edit'			 => true,
			'save'			 => true,
			'get'			 => true,
			'get_services'	 => true
		);
		protected
			$fields,
			$permissions,
			$currentapp;

		public function __construct()
		{
			parent::__construct();
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('article');
			$this->bo = createObject('booking.boarticle');
			$this->fields = booking_article::get_fields();
			$this->permissions = booking_article::get_instance()->get_permission_array();
			$this->currentapp = $GLOBALS['phpgw_info']['flags']['currentapp'];
			self::set_active_menu("{$this->currentapp}::article");
		}

		private function get_category_options( $selected = 1 )
		{
			$category_options = array();
			$category_list = execMethod('booking.bogeneric.get_list', array('type' => 'article_category'));


			foreach ($category_list as $category)
			{
				$category_options[] = array(
					'id' => $category['id'],
					'name' => $category['name'],
					'selected' => $category['id'] == $selected ? 1 : 0
				);
			}
			return $category_options;
		}

		private function get_unit_list( $selected = 0 )
		{
			$unit_list = array();
			$unit_list[] = array('id' => 'each', 'name' => lang('each'));
			$unit_list[] = array('id' => 'kg', 'name' => lang('kg'));
			$unit_list[] = array('id' => 'm', 'name' => lang('meter'));
			$unit_list[] = array('id' => 'm2', 'name' => lang('square meter'));

			foreach ($unit_list as &$unit)
			{
				$unit['selected'] = $unit['id'] == $selected ? 1 : 0;
			}
			return $unit_list;
		}

		public function get_services( $selected = 0)
		{
			$services_list = execMethod('booking.bogeneric.get_list', array('type' => 'article_service'));

			$alredy_taken = $this->bo->get_mapped_services();

			foreach ($services_list as $service)
			{
				if($selected != $service['id'] && in_array($service['id'], $alredy_taken) )
				{
					continue;
				}

				$service_options[] = array(
					'id' => $service['id'],
					'name' => $service['name'],
					'selected' => $service['id'] == $selected ? 1 : 0
				);
			}
			return $service_options;
		}

		public function index()
		{
			if (empty($this->permissions[PHPGW_ACL_READ]))
			{
				$message = '';
				if($this->currentapp == 'bookingfrontend')
				{
					$message = lang('you need to log in to access this page.');
				}
				phpgw::no_access(false, $message);
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			phpgwapi_jquery::load_widget('autocomplete');

			$function_msg = lang('article');

			$data = array(
				'datatable_name' => $function_msg,
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array(
								'type' => 'filter',
								'name' => 'filter_article_cat_id',
								'text' => lang('category'),
								'list' =>  $this->get_category_options()
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
				'datatable' => array(
					'source' => self::link(array(
						'menuaction' => "{$this->currentapp}.uiarticle.index",
						'phpgw_return_as' => 'json'
					)),
					'allrows' => true,
					'new_item' => self::link(array('menuaction' => "{$this->currentapp}.uiarticle.add")),
					'editor_action' => '',
					'field' => parent::_get_fields()
				)
			);

			$parameters = array(
				'parameter' => array(
					array(
						'name' => 'id',
						'source' => 'id'
					)
				)
			);

/*			$data['datatable']['actions'][] = array
				(
				'my_name' => 'view',
				'text' => lang('show'),
				'action' => self::link(array
					(
					'menuaction' => "{$this->currentapp}.uiarticle.view"
				)),
				'parameters' => json_encode($parameters)
			);
*/
			$data['datatable']['actions'][] = array
				(
				'my_name' => 'edit',
				'text' => lang('edit'),
				'action' => self::link(array
					(
					'menuaction' => "{$this->currentapp}.uiarticle.edit"
				)),
				'parameters' => json_encode($parameters)
			);

			self::add_javascript($this->currentapp, 'base', 'article.index.js', 'text/javascript', true);
			phpgwapi_jquery::load_widget('numberformat');

			self::render_template_xsl('datatable_jquery', $data);
		}

		/*
		 * Edit the price item with the id given in the http variable 'id'
		 */

		public function edit( $values = array(), $mode = 'edit' )
		{
			$active_tab = !empty($values['active_tab']) ? $values['active_tab'] : phpgw::get_var('active_tab', 'string', 'REQUEST', 'first_tab');
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('edit');
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
				$id = !empty($values['id']) ? $values['id'] : phpgw::get_var('id', 'int');
				$article = $this->bo->read_single($id);
			}

			$tabs = array();
			$tabs['first_tab'] = array(
				'label' => lang('article'),
				'link' => '#first_tab',
			);
			$tabs['prizing'] = array(
				'label' => lang('prizing'),
				'link' => '#prizing',
	//			'disable' => empty($id) ? true : false
			);
			$tabs['files'] = array(
				'label' => lang('files'),
				'link' => '#files',
				'disable' => empty($id) ? true : false
			);

			$bocommon = CreateObject('property.bocommon');

			$comments = (array)$article->comments;
			foreach ($comments as $key => &$comment)
			{
				$comment['value_count'] = $key +1;
				$comment['value_date'] = $GLOBALS['phpgw']->common->show_date($comment['time']);
			}

			$comments_def = array(
				array('key' => 'value_count', 'label' => '#', 'sortable' => true, 'resizeable' => true),
				array('key' => 'value_date', 'label' => lang('Date'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'author', 'label' => lang('User'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'comment', 'label' => lang('Note'), 'sortable' => true, 'resizeable' => true)
			);
 
			$datatable_def[] = array(
				'container' => 'datatable-container_0',
				'requestUrl' => "''",
				'ColumnDefs' => $comments_def,
				'data' => json_encode($comments),
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$dates_def = array(
				array('key' => 'id', 'label' => lang('id'), 'sortable' => true, 'resizeable' => true,'formatter' => 'JqueryPortico.formatLink'),
				array('key' => 'from_', 'label' => lang('From'), 'sortable' => false, 'resizeable' => true),
				array('key' => 'to_', 'label' => lang('To'), 'sortable' => false, 'resizeable' => true),
				array('key' => 'status', 'label' => lang('status'), 'sortable' => false, 'resizeable' => true),
				array('key' => 'vendor_name', 'label' => lang('vendor'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'location', 'label' => lang('location'), 'sortable' => false, 'resizeable' => true),
				array('key' => 'comment', 'label' => lang('Note'), 'sortable' => false, 'resizeable' => true),
				array('key' => 'application_id', 'hidden' => true),
			);

			$datatable_def[] = array(
				'container' => 'datatable-container_1',
				'requestUrl' => json_encode(self::link(array('menuaction' => "{$this->currentapp}.uibooking.query_relaxed",
					'filter_article_id' => $id,
					'filter_active'	=> 1,
					'phpgw_return_as' => 'json'))),
				'ColumnDefs' => $dates_def,
				'data' => json_encode(array()),
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$GLOBALS['phpgw']->jqcal->add_listener('date_from', 'date');
			$data				 = array(
				'datatable_def'		 => $datatable_def,
				'form_action'		 => self::link(array('menuaction' => "{$this->currentapp}.uiarticle.save")),
				'cancel_url'		 => self::link(array('menuaction' => "{$this->currentapp}.uiarticle.index",)),
				'article'			 => $article,
				'article_categories' => array('options' => $this->get_category_options($article->article_cat_id)),
				'unit_list'			 => array('options' => $this->get_unit_list($article->unit)),
				'service_list'		 => ( $id && $article->article_cat_id == 2 )? array('options' => $this->get_services($article->article_id)) : array(),
				'mode'				 => $mode,
				'tabs'				 => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'value_active_tab'	 => $active_tab,
				'multi_upload_parans' => "{menuaction:'{$this->currentapp}.uiarticle.build_multi_upload_file', id:'{$id}'}",
				'multiple_uploader' => true,
				'resources_json' => ( $id && $article->article_cat_id == 1 ) ? json_encode(array($article->article_id)) : '[]'
			);
			self::add_javascript('booking', 'base', 'common');
			phpgwapi_jquery::load_widget('autocomplete');
			phpgwapi_jquery::load_widget('file-upload-minimum');
			phpgwapi_jquery::formvalidator_generate(array());
			self::add_javascript('booking', 'base', 'article.js');
			self::render_template_xsl(array('article', 'datatable_inline','files' ,'multi_upload_file_inline'), array($mode => $data));
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

	}