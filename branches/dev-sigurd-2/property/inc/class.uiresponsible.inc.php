<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package phpgroupware
	* @subpackage property
	* @category core
 	* @version $Id: class.uiresponsible.inc.php 732 2008-02-10 16:21:14Z sigurd $
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
	 * ResponsibleMatrix - handles automated assigning of tasks based on (physical)location and category.
	 *
	 * @package phpgroupware
	 * @subpackage property
	 * @category core
	 */

	class property_uiresponsible
	{

		/**
		* @var integer $start for pagination
		*/
		protected $start = 0;

		/**
		* @var string $sort how to sort the queries - ASC/DESC
		*/
		protected $sort;

		/**
		* @var string $order field to order by in queries
		*/
		protected $order;

		/**
		* @var object $nextmatchs paging handler
		*/
		private $nextmatchs;

		/**
		* @var object $bo business logic
		*/
		protected $bo;

		/**
		* @var object $acl reference to global access control list manager
		*/
		protected $acl;

		/**
		* @var string $acl_location the access control location
		*/
		protected $acl_location;

		/**
		* @var bool $acl_read does the current user have read access to the current location
		*/
		protected $acl_read;

		/**
		* @var bool $acl_add does the current user have add access to the current location
		*/
		protected $acl_add;

		/**
		* @var bool $acl_edit does the current user have edit access to the current location
		*/
		protected $acl_edit;

		/**
		* @var bool $allrows display all rows of result set?
		*/
		protected $allrows;

		/**
		* @var array $public_functions publicly available methods of the class
		*/
		public $public_functions = array
		(
			'index' 		=> true,
			'contact' 		=> true,
			'edit_type' 	=> true,
			'edit_contact' 	=> true,
			'no_access'		=> true,
			'delete_type'	=> true
		);

		/**
		* Constructor
		*/

		public function __construct()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'admin::property::responsible_matrix';
			$this->bo					= CreateObject('property.boresponsible', true);
			$this->nextmatchs			= CreateObject('phpgwapi.nextmatchs');
			$this->acl 					= & $GLOBALS['phpgw']->acl;
			$this->acl_location 		= $this->bo->acl_location;
			$this->acl_read 			= $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property');
			$this->acl_add 				= $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'property');
			$this->acl_edit 			= $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'property');
			$this->acl_delete 			= $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'property');
			$this->bolocation			= CreateObject('preferences.boadmin_acl');
			$this->bolocation->acl_app 	= 'property';
			$this->location				= $this->bo->location;
			$this->cats					= & $this->bo->cats;
			$this->query				= $this->bo->query;
			$this->allrows				= $this->bo->allrows;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->cat_id				= $this->bo->cat_id;
		}

		/**
		* Save sessiondata
		*
		* @return void
		*/

		private function _save_sessiondata()
		{
			$data = array
			(
				'start'		=> $this->start,
				'query'		=> $this->query,
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'location'	=> $this->location,
				'allrows'	=> $this->allrows,
				'cat_id'	=> $this->cat_id
			);
			$this->bo->save_sessiondata($data);
		}

		/**
		* list available responsible types
		*
		* @return void
		*/

		public function index()
		{
			if(!$this->acl_read)
			{
				$this->no_access();
				return;
			}

			$lookup = phpgw::get_var('lookup', 'bool');

			if($lookup)
			{
				$GLOBALS['phpgw_info']['flags']['noframework']	= true;
				$GLOBALS['phpgw_info']['flags']['headonly']		= true;
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('responsible', 'nextmatchs','search_field'));

			$responsible_info = $this->bo->read_type();

			$content = array();
			foreach ( $responsible_info as $entry )
			{
  				$link_edit					= '';
				$lang_edit_text		= '';
				$text_edit					= '';
				if ($this->acl_edit && !$lookup)
				{
					$link_edit				= $GLOBALS['phpgw']->link('/index.php', array
												(
													'menuaction'	=> 'property.uiresponsible.edit_type',
													'id'			=> $entry['id'],
													'location'		=> str_replace('property', '', $entry['app_name'])
												));
					$lang_edit_text			= lang('edit type');
					$text_edit				= lang('edit');
				}

				$link_delete				= '';
				$text_delete				= '';
				$lang_delete_text		= '';
				if ($this->acl_delete && !$lookup)
				{
					$link_delete			= $GLOBALS['phpgw']->link('/index.php', array
												(
													'menuaction'	=> 'property.uiresponsible.delete_type',
													'id'			=> $entry['id']
												));
					$text_delete			= lang('delete');
					$lang_delete_text		= lang('delete type');
				}

				$link_contacts				= '';
				$text_contacts				= '';
				$lang_contacts_text			= '';
				$lang_select				= '';
				$lang_select_text			= '';
				if (!$lookup)
				{
					$link_contacts			= $GLOBALS['phpgw']->link('/index.php', array
												(
													'menuaction'	=> 'property.uiresponsible.contact',
													'type_id'=> $entry['id']
												));
					$text_contacts			= lang('contacts');
					$lang_contacts_text		= lang('list of contacts for this responsibility type');
				}
				else
				{
					$lang_select			= lang('select');
					$lang_select_text		= lang('select responsibility type for this contact');
				}

				$content[] = array
				(
					'id'					=> $entry['id'],
					'name'					=> $entry['name'],
					'descr'					=> $entry['descr'],
					'active'				=> $entry['active'] == 1 ? 'X' : '',
					'created_by'			=> $entry['created_by'],
					'created_on'			=> $entry['created_on'],
					'category'				=> $entry['category'],
					'app_name'				=> $entry['app_name'],
					'link_contacts'			=> $link_contacts,
					'text_contacts'			=> $text_contacts,
					'lang_contacts_text'	=> $lang_contacts_text,
					'link_edit'				=> $link_edit,
					'text_edit'				=> $text_edit,
					'lang_edit_text'		=> $lang_edit_text,
					'link_delete'			=> $link_delete,
					'text_delete'			=> $text_delete,
					'lang_delete_text'		=> $lang_delete_text,
					'lang_select'			=> $lang_select,
					'lang_select_text'		=> $lang_select_text
				);
			}

			$table_header[] = array
			(
				'sort_name'	=> $this->nextmatchs->show_sort_order(array
				(
					'sort'	=> $this->sort,
					'var'	=> 'name',
					'order'	=> $this->order,
					'extra'	=> array
					(
						'menuaction'	=> 'property.uiresponsible.index',
						'allrows'		=> $this->allrows,
						'location'		=> $this->location
					)
				)),
				'lang_name'			=> lang('name'),
				'lang_descr'		=> lang('descr'),
				'lang_category'		=> lang('category'),
				'lang_created_by'	=> lang('supervisor'),
				'lang_app_name'		=> lang('location'),
				'lang_active'		=> lang('active'),
				'lang_contacts'		=> !$lookup ? lang('contacts') : '',
				'lang_edit'			=> $this->acl_edit && !$lookup ? lang('edit') : '',
				'lang_delete'		=> $this->acl_delete && !$lookup ? lang('delete') : '',
				'lang_select'		=> $lookup ? lang('select') : ''
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
				'menuaction'	=> 'property.uiresponsible.index',
				'sort'			=> $this->sort,
				'order'			=> $this->order,
				'query'			=> $this->query,
				'location'		=> $this->location,
				'lookup'		=> $lookup

			);

			$link_add_action = array
			(
				'menuaction'	=> 'property.uiresponsible.edit_type',
				'location'		=> $this->location
			);

			$table_add = array();
			if(!$lookup)
			{
				$table_add[] = array
				(
					'lang_add'				=> lang('add'),
					'lang_add_statustext'	=> lang('add type'),
					'add_action'			=> $GLOBALS['phpgw']->link('/index.php', $link_add_action)
				);
			}
			else
			{
				if(!isset($GLOBALS['phpgw_info']['flags']['java_script']))
				{
					$GLOBALS['phpgw_info']['flags']['java_script'] = '';
				}

				$GLOBALS['phpgw_info']['flags']['java_script'] .= "\n"
					. '<script type="text/javascript">' ."\n"
					. "//<[CDATA[\n"
					. 'function Exchange_values(thisform)' ."\r\n"
					. "{\r\n"
					. "opener.document.form.responsibility_id.value = thisform.elements[0].value;\r\n"
					. "opener.document.form.responsibility_name.value = thisform.elements[1].value;\r\n"
					. "window.close()\r\n"
					. "}\r\n"
					. "//]]\n"
					. "</script>\n";
			}

			$receipt = $GLOBALS['phpgw']->session->appsession('session_data', 'responsible_receipt');
			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);
			$GLOBALS['phpgw']->session->appsession('session_data', 'responsible_receipt', '');

			$data = array
			(
				'msgbox_data'						=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'allow_allrows'						=> true,
				'allrows'							=> $this->allrows,
				'start_record'						=> $this->start,
				'record_limit'						=> $record_limit,
				'num_records'						=> $responsible_info ? count($responsible_info) : 0,
				'all_records'						=> $this->bo->total_records,
				'select_action'						=> $GLOBALS['phpgw']->link('/index.php', $link_data),
				'link_url'							=> $GLOBALS['phpgw']->link('/index.php', $link_data),
				'img_path'							=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi', 'default'),
				'lang_searchfield_statustext'		=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'		=> lang('Submit the search string'),
				'query'								=> $this->query,
				'lang_search'						=> lang('search'),
				'table_header_type'					=> $table_header,
				'table_add'							=> $table_add,
				'values_type'						=> $content,
				'lang_no_location'					=> lang('No location'),
				'lang_location_statustext'			=> lang('Select submodule'),
				'select_name_location'				=> 'location',
				'location_list'						=> $this->bolocation->select_location('filter', $this->location),
			);

			$function_msg= lang('list available responsible types');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('responsible matrix') . ":: {$function_msg}";

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('list_type' => $data));
			$this->_save_sessiondata();
		}

		/**
		* Add or Edit available responsible types
		*
		* @return void
		*/

		public function edit_type()
		{
			if(!$this->acl_add)
			{
				$this->no_access();
				return;
			}

			$id		= phpgw::get_var('id', 'int');
			$values	= phpgw::get_var('values', 'string', 'POST');

			$GLOBALS['phpgw']->xslttpl->add_file(array('responsible'));

			if (isset($values) && is_array($values))
			{
				if(!$this->acl_edit)
				{
					$this->no_access();
					return;
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

					if($id)
					{
						$values['id']=$id;
					}

					if(!isset($receipt['error']) || !$receipt['error'])
					{
						$receipt = $this->bo->save_type($values);
						$id = $receipt['id'];

						if (isset($values['save']) && $values['save'])
						{
							$GLOBALS['phpgw']->session->appsession('session_data', 'responsible_receipt', $receipt);
							$GLOBALS['phpgw']->redirect_link('/index.php', array
													(
														'menuaction'=> 'property.uiresponsible.index',
														'location' => $this->location
													));
						}
					}
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/index.php', array
													(
														'menuaction'=> 'property.uiresponsible.index',
														'location' => $this->location
													));
				}
			}


			if ($id)
			{
				$function_msg = lang('edit responsible type');
				$values = $this->bo->read_single_type($id);
			}
			else
			{
				$function_msg = lang('add responsible type');
			}

			$link_data = array
			(
				'menuaction'	=> 'property.uiresponsible.edit_type',
				'id'			=> $id,
				'location'		=> $this->location
			);

			$msgbox_data = (isset($receipt)?$GLOBALS['phpgw']->common->msgbox_data($receipt):'');

			$data = array
			(
				'value_entry_date'				=> isset($values['entry_date']) ? $values['entry_date'] : '',
				'value_name'					=> isset($values['name']) ? $values['name'] : '',
				'value_descr'					=> isset($values['descr']) ? $values['descr'] : '',
				'value_active'					=> isset($values['active']) ? $values['active'] : '',

				'lang_entry_date'				=> lang('Entry date'),
				'lang_name'						=> lang('name'),
				'lang_descr'					=> lang('descr'),

				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'					=> $GLOBALS['phpgw']->link('/index.php', $link_data),
				'lang_id'						=> lang('ID'),
				'lang_save'						=> lang('save'),
				'lang_cancel'					=> lang('cancel'),
				'value_id'						=> $id,
				'lang_active'					=> lang('active'),
				'lang_active_on_statustext'		=> lang('set this item inactive'),
				'lang_active_off_statustext'	=> lang('set this item active'),
				'lang_cancel_status_text'		=> lang('Back to the list'),
				'lang_save_status_text'			=> lang('Save the responsible type'),
				'lang_apply'					=> lang('apply'),
				'lang_apply_status_text'		=> lang('Apply the values'),

				'lang_category'					=> lang('category'),
				'lang_no_cat'					=> lang('no category'),
				'cat_select'					=> $this->cats->formatted_xslt_list(array
														(
															'select_name' => 'values[cat_id]',
															'selected' => isset($values['cat_id'])?$values['cat_id']:''
														)),
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('responsible matrix') . "::{$function_msg}";
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('edit_type' => $data));
		}

		/**
		* List of contacts given responsibilities within locations
		*
		* @return void
		*/

		public function contact()
		{
			if(!$this->acl_read)
			{
				$this->no_access();
				return;
			}

			$type_id		= phpgw::get_var('type_id', 'int');

			$GLOBALS['phpgw']->xslttpl->add_file(array('responsible', 'nextmatchs','search_field'));

			$responsible_info = $this->bo->read_contact($type_id);

			$content = array();
			foreach ( $responsible_info as $entry )
			{
  				$link_edit					= '';
				$lang_edit_demo_text		= '';
				$text_edit					= '';
				if ($this->acl_edit)
				{
					$link_edit				= $GLOBALS['phpgw']->link('/index.php', array
																		(
																			'menuaction'	=> 'property.uiresponsible.edit_contact',
																			'id'			=> $entry['id'],
																			'location'		=> str_replace('property', '', $entry['app_name']),
																			'type_id'		=> $type_id
																		));
					$lang_edit_text			= lang('edit type');
					$text_edit				= lang('edit');
				}

				$link_delete				= '';
				$text_delete				= '';
				$lang_delete_demo_text		= '';
			/*	if ($this->acl_delete)
				{
					$link_delete			= $GLOBALS['phpgw']->link('/index.php', array
																(
																	'menuaction'=> 'property.uiresponsible.delete_contact',
																	'id'=> $entry['id']
																));
					$text_delete			= lang('delete');
					$lang_delete_text		= lang('delete type');
				}
			*/

				$content[] = array
				(
					'location_code'			=> $entry['location_code'],
					'item'					=> $entry['item'],
					'active_from'			=> $entry['active_from'],
					'active_to'				=> $entry['active_to'],
					'created_by'			=> $entry['created_by'],
					'created_on'			=> $entry['created_on'],
					'contact_name'			=> $entry['contact_name'],
					'remark'				=> $entry['remark'],
					'link_edit'				=> $link_edit,
					'text_edit'				=> $text_edit,
					'lang_edit_text'		=> $lang_edit_text,
					'link_delete'			=> $link_delete,
					'text_delete'			=> $text_delete,
					'lang_delete_text'		=> $lang_delete_text
				);
			}

			$table_header[] = array
			(
				'sort_location'	=> $this->nextmatchs->show_sort_order(array
				(
					'sort'	=> $this->sort,
					'var'	=> 'location_code',
					'order'	=> $this->order,
					'extra'	=> array
					(
						'menuaction'	=> 'property.uiresponsible.contact',
						'allrows'		=> $this->allrows,
						'location'		=> $this->location,
						'type_id'		=> $type_id
					)
				)),
				'sort_active_from'	=> $this->nextmatchs->show_sort_order(array
				(
					'sort'	=> $this->sort,
					'var'	=> 'active_from',
					'order'	=> $this->order,
					'extra'	=> array
					(
						'menuaction'	=> 'property.uiresponsible.contact',
						'allrows'		=> $this->allrows,
						'location'		=> $this->location,
						'type_id'		=> $type_id
					)
				)),
				'sort_active_to'	=> $this->nextmatchs->show_sort_order(array
				(
					'sort'	=> $this->sort,
					'var'	=> 'active_to',
					'order'	=> $this->order,
					'extra'	=> array
					(
						'menuaction'	=> 'property.uiresponsible.contact',
						'allrows'		=> $this->allrows,
						'location'		=> $this->location,
						'type_id'		=> $type_id
					)
				)),
				'lang_contact'		=> lang('contact'),
				'lang_location'		=> lang('location'),
				'lang_item'			=> lang('item'),
				'lang_active_from'	=> lang('active from'),
				'lang_active_to'	=> lang('active to'),
				'lang_created_on'	=> lang('created'),
				'lang_created_by'	=> lang('supervisor'),
				'lang_remark'		=> lang('remark'),
				'lang_edit'			=> $this->acl_edit ? lang('edit') : '',
		//		'lang_delete'		=> $this->acl_delete ? lang('delete') : '',
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
				'menuaction'	=> 'property.uiresponsible.contact',
				'sort'			=> $this->sort,
				'order'			=> $this->order,
				'query'			=> $this->query,
				'location'		=> $this->location,
				'type_id'		=> $type_id

			);

			$link_add_action = array
			(
				'menuaction'	=> 'property.uiresponsible.edit_contact',
				'location'		=> $this->location,
				'type_id'		=> $type_id
			);

			$table_add[] = array
			(
				'lang_add'					=> lang('add'),
				'lang_add_statustext'		=> lang('add contact'),
				'add_action'				=> $GLOBALS['phpgw']->link('/index.php', $link_add_action),
				'lang_cancel'				=> lang('cancel'),
				'lang_cancel_statustext'	=> lang('back to list type'),
				'cancel_action'				=> $GLOBALS['phpgw']->link('/index.php', array
																	('menuaction'	=> 'property.uiresponsible.index'
																	))
			);

			$receipt = $GLOBALS['phpgw']->session->appsession('session_data', 'responsible_contact_receipt');
			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);
			$GLOBALS['phpgw']->session->appsession('session_data', 'responsible_contact_receipt', '');

			$type_info = $this->bo->read_single_type($type_id);
			$category = $this->cats->return_single($type_info['cat_id']);
			$data = array
			(
				'msgbox_data'							=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'allow_allrows'							=> true,
				'allrows'								=> $this->allrows,
				'start_record'							=> $this->start,
				'record_limit'							=> $record_limit,
				'num_records'							=> $responsible_info ? count($responsible_info) : 0,
				'all_records'							=> $this->bo->total_records,
				'select_action'							=> $GLOBALS['phpgw']->link('/index.php', $link_data),
				'link_url'								=> $GLOBALS['phpgw']->link('/index.php', $link_data),
				'img_path'								=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi', 'default'),
				'lang_searchfield_statustext'			=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'			=> lang('Submit the search string'),
				'query'									=> $this->query,
				'lang_search'							=> lang('search'),
				'table_header_contact'					=> $table_header,
				'table_add'								=> $table_add,
				'values_contact'						=> $content,
				'lang_no_location'						=> lang('No location'),
				'lang_location_statustext'				=> lang('Select submodule'),
				'select_name_location'					=> 'location',
				'location_name'							=> "property{$this->location}", //FIXME once interlink is settled
				'lang_no_cat'							=> lang('no category'),
				'type_name'								=> $type_info['name'],
				'category_name'							=> $category[0]['name']
			);

			$function_msg= lang('list available responsible contacts');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('responsible matrix') . ":: {$function_msg}";

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('list_contact' => $data));
			$this->_save_sessiondata();
		}


		/**
		* Add or Edit available contact related to responsible types and (physical) locations
		*
		* @return void
		*/

		public function edit_contact()
		{
			if(!$this->acl_add)
			{
				$this->no_access();
				return;
			}

			$id						= phpgw::get_var('id', 'int');
			$type_id				= phpgw::get_var('type_id', 'int');
			$values					= phpgw::get_var('values', 'string', 'POST');
			$contact_id				= phpgw::get_var('contact', 'int');
			$contact_name			= phpgw::get_var('contact_name', 'string');
			$responsibility_id		= phpgw::get_var('responsibility_id', 'int');
			$responsibility_name	= phpgw::get_var('responsibility_name', 'string');
			$bolocation				= CreateObject('property.bolocation');
			$bocommon				= CreateObject('property.bocommon');

			$GLOBALS['phpgw']->xslttpl->add_file(array('responsible'));

			if (isset($values) && is_array($values))
			{
				if(!$this->acl_edit)
				{
					$this->no_access();
					return;
				}

				if ((isset($values['save']) && $values['save']) || (isset($values['apply']) && $values['apply']))
				{
					$insert_record = $GLOBALS['phpgw']->session->appsession('insert_record', 'property');
					$insert_record_entity = $GLOBALS['phpgw']->session->appsession('insert_record_entity', 'property');

					if(isset($insert_record_entity) && is_array($insert_record_entity))
					{
						foreach ($insert_record_entity as $insert_record_entry)
						{
							$insert_record['extra'][$insert_record_entry]	= $insert_record_entry;
						}
					}

					$values = $bocommon->collect_locationdata($values, $insert_record);

					if($id)
					{
						$values['id']=$id;
					}
					if($contact_id)
					{
						$values['contact_id']=$contact_id;
					}

					if($contact_name)
					{
						$values['contact_name']=$contact_name;
					}

					if($responsibility_id)
					{
						$values['responsibility_id']=$responsibility_id;
					}

					if($contact_name)
					{
						$values['responsibility_name']=$responsibility_name;
					}

					if(!isset($values['responsibility_id']))
					{
						$receipt['error'][]=array('msg'=>lang('Please select a responsibility!'));
					}

					if(!isset($values['contact_id']))
					{
						$receipt['error'][]=array('msg'=>lang('Please select a contact!'));
					}

					if(!isset($values['location']['loc1']))
					{
						$receipt['error'][]=array('msg'=>lang('Please select a location!'));
					}

					if($GLOBALS['phpgw']->session->is_repost())
					{
						$receipt['error'][]=array('msg'=>lang('Hmm... looks like a repost!'));
					}

					if(!isset($receipt['error']) || !$receipt['error'])
					{
						$receipt = $this->bo->save_contact($values);
						$id = $receipt['id'];

						if (isset($values['save']) && $values['save'])
						{
							$GLOBALS['phpgw']->session->appsession('session_data', 'responsible_contact_receipt', $receipt);
							$GLOBALS['phpgw']->redirect_link('/index.php', array
															(
																'menuaction'=> 'property.uiresponsible.contact',
																'location'	=> $this->location,
																'type_id'	=> $type_id
															));
						}
						else if (isset($values['apply']) && $values['apply'])
						{
							$GLOBALS['phpgw']->redirect_link('/index.php', array
															(
																'menuaction'=> 'property.uiresponsible.edit_contact',
																'location'	=> $this->location,
																'type_id'	=> $type_id,
																'id'		=> $id
															));
						}
					}
					else
					{
						if(isset($values['location']) && $values['location'])
						{
							$location_code=implode("-", $values['location']);
							$values['location_data'] = $bolocation->read_single($location_code, isset($values['extra']) ? $values['extra'] : false);
						}
						if(isset($values['extra']['p_num']) && $values['extra']['p_num'])
						{
							$values['p'][$values['extra']['p_entity_id']]['p_num']=$values['extra']['p_num'];
							$values['p'][$values['extra']['p_entity_id']]['p_entity_id']=$values['extra']['p_entity_id'];
							$values['p'][$values['extra']['p_entity_id']]['p_cat_id']=$values['extra']['p_cat_id'];
							$values['p'][$values['extra']['p_entity_id']]['p_cat_name']=phpgw::get_var('entity_cat_name_'.$values['extra']['p_entity_id'], 'string', 'POST');
						}
					}
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/index.php', array
															(
																'menuaction'=> 'property.uiresponsible.contact',
																'location' => $this->location,
																'type_id' => $type_id
															));
				}
			}


			if ($id)
			{
				$function_msg = lang('edit responsible type');
				$values = $this->bo->read_single_contact($id);
			}
			else
			{
				$function_msg = lang('add responsible type');
			}

			$location_data = $bolocation->initiate_ui_location(array(
						'values'	=> $values['location_data'],
						'type_id'	=> -1, // calculated from location_types
						'no_link'	=> false, // disable lookup links for location type less than type_id
						'tenant'	=> false,
						'lookup_type'	=> 'form',
						'lookup_entity'	=> $bocommon->get_lookup_entity('project'),
						'entity_data'	=> isset($values['p']) ? $values['p'] : ''
						));

			$link_data = array
			(
				'menuaction'	=> 'property.uiresponsible.edit_contact',
				'id'			=> $id,
				'location'		=> $this->location,
				'type_id'		=> $type_id
			);

			$msgbox_data = (isset($receipt)?$GLOBALS['phpgw']->common->msgbox_data($receipt):'');

			$lookup_link_contact		= "menuaction:'property.uilookup.addressbook', column:'contact'";
			$lookup_link_responsibility	= "menuaction:'property.uiresponsible.index', location:'{$this->location}', lookup:1";

			$lookup_function = "\n"
				. '<script type="text/javascript">' ."\n"
				. '//<[CDATA[' ."\n"
				. 'function lookup_contact()' ."\r\n"
				. "{\r\n"
				. ' var oArgs = {' . $lookup_link_contact . "};\n"
				. " var strURL = phpGWLink('index.php', oArgs);\n"
				. ' Window1=window.open(strURL,"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");' . "\r\n"
				. '}'."\r\n"
				. 'function lookup_responsibility()' ."\r\n"
				. "{\r\n"
				. ' var oArgs = {' . $lookup_link_responsibility . "};\n"
				. " var strURL = phpGWLink('index.php', oArgs);\n"
				. ' Window1=window.open(strURL,"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");' . "\r\n"
				. '}'."\r\n"
				. '//]]' ."\n"
				. "</script>\n";

			if(!isset($GLOBALS['phpgw_info']['flags']['java_script']))
			{
				$GLOBALS['phpgw_info']['flags']['java_script'] = '';
			}

			$GLOBALS['phpgw_info']['flags']['java_script'] .= $lookup_function;

			$jscal = CreateObject('phpgwapi.jscalendar');
			$jscal->add_listener('values_active_from');
			$jscal->add_listener('values_active_to');

			$data = array
			(
				'value_entry_date'				=> isset($values['entry_date']) ? $values['entry_date'] : '',
				'value_name'					=> isset($values['name']) ? $values['name'] : '',
				'value_remark'					=> isset($values['remark']) ? $values['remark'] : '',
				'lang_entry_date'				=> lang('Entry date'),
				'lang_remark'					=> lang('remark'),

				'lang_responsibility'			=> lang('responsibility'),
				'lang_responsibility_status_text'=> lang('click to select responsibility'),
				'value_responsibility_id'		=> isset($values['responsibility_id']) ? $values['responsibility_id'] : '',
				'value_responsibility_name'		=> isset($values['responsibility_name']) ? $values['responsibility_name'] : '',

				'lang_contact'					=> lang('contact'),
				'lang_contact_status_text'		=> lang('click to select contact'),
				'value_contact_id'				=> isset($values['contact_id']) ? $values['contact_id'] : '',
				'value_contact_name'			=> isset($values['contact_name']) ? $values['contact_name'] : '',

				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'					=> $GLOBALS['phpgw']->link('/index.php', $link_data),
				'lang_id'						=> lang('ID'),
				'lang_save'						=> lang('save'),
				'lang_cancel'					=> lang('cancel'),
				'value_id'						=> $id,
				'lang_cancel_status_text'		=> lang('Back to the list'),
				'lang_save_status_text'			=> lang('Save the responsible type'),
				'lang_apply'					=> lang('apply'),
				'lang_apply_status_text'		=> lang('Apply the values'),

				'lang_location'					=> lang('location'),
				'value_location_name'			=> "property{$this->location}", //FIXME once interlink is settled
				'location_data'					=> $location_data,

				'lang_active_from'				=> lang('active from'),
				'lang_active_to'				=> lang('active to'),
				'value_active_from'				=> isset($values['active_from']) ? $values['active_from'] : '',
				'value_active_to'				=> isset($values['active_to']) ? $values['active_to'] : '',
				'img_cal'						=> $GLOBALS['phpgw']->common->image('phpgwapi', 'cal'),
				'lang_datetitle'				=> lang('Select date'),
				'lang_active_from_statustext'	=> lang('Select the start date for this responsibility'),
				'lang_active_to_statustext'		=> lang('Select the closing date for this responsibility'),

			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('responsible matrix') . "::{$function_msg}";
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('edit_contact' => $data));
		}

		/**
		* Display an error in case of missing rights
		*
		* @return void
		*/

		public function no_access()
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('no_access'));

			$receipt['error'][]=array('msg'=>lang('NO ACCESS'));

			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'	=> $GLOBALS['phpgw']->common->msgbox($msgbox_data)
			);

			$function_msg	= lang('No access');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('responsible matrix') . ":: {$function_msg}";
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('no_access' => $data));
		}

		/**
		* Delete a responsibility type
		*
		* @return void
		*/

		public function delete_type()
		{
			if(!$this->acl_delete)
			{
				$this->no_access();
				return;
			}

			$id				= phpgw::get_var('id', 'int');

			$link_data = array
			(
				'menuaction' => 'property.uiresponsible.index'
			);

			if ( phpgw::get_var('confirm', 'bool', 'POST') )
			{
				$this->bo->delete_type($id);
				$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'			=> $GLOBALS['phpgw']->link('/index.php', $link_data),
				'delete_action'			=> $GLOBALS['phpgw']->link('/index.php', array
															(
																'menuaction'=> 'property.uiresponsible.delete_type',
																'id'=> $id
															)),
				'lang_confirm_msg'		=> lang('do you really want to delete this entry'),
				'lang_yes'				=> lang('yes'),
				'lang_yes_statustext'	=> lang('Delete the entry'),
				'lang_no_statustext'	=> lang('Back to the list'),
				'lang_no'				=> lang('no')
			);

			$function_msg	= lang('delete');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('responsible matrix') . "::{$function_msg}";

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('delete' => $data));
		}
	}
