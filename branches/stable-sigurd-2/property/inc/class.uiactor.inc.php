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
	* @subpackage admin
 	* @version $Id$
	*/

	/**
	 * uiactor class
	 *
	 * uiactor is the ui-class for three set of actors, separarated by their roles:
	 * - Tenant
	 * - Vendor
	 * - Owner
	 * @package property
	 */

	class property_uiactor
	{
		var $grants;
		var $cat_id;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;

		var $public_functions = array
		(
			'index'  	=> true,
			'view'   	=> true,
			'edit'   	=> true,
			'delete' 	=> true,
			'columns'	=> true
		);

		function property_uiactor()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;

			$this->nextmatchs		= CreateObject('phpgwapi.nextmatchs');
			$this->account			= $GLOBALS['phpgw_info']['user']['account_id'];

			$this->bo				= CreateObject('property.boactor',true);
			$this->bocommon			= CreateObject('property.bocommon');

			$this->role				= $this->bo->role;

			$this->cats				= CreateObject('phpgwapi.categories');
			$this->cats->app_name	= 'fm_' . $this->role;

			$this->acl				= & $GLOBALS['phpgw']->acl;
			$this->acl_location		= '.' . $this->role;

			$this->acl_read 		= $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property');
			$this->acl_add			= $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'property');
			$this->acl_edit			= $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'property');
			$this->acl_delete		= $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'property');
			$this->acl_manage		= $this->acl->check($this->acl_location, 16, 'property');

			$this->start			= $this->bo->start;
			$this->query			= $this->bo->query;
			$this->sort				= $this->bo->sort;
			$this->order			= $this->bo->order;
			$this->filter			= $this->bo->filter;
			$this->cat_id			= $this->bo->cat_id;
			$this->allrows			= $this->bo->allrows;
			$this->member_id		= $this->bo->member_id;

			$valid_role = array(
				'tenant'=>true,
				'owner'	=>true,
				'vendor'=>true
				);
			if(!$valid_role[$this->role])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.index'));
			}

			if (phpgw::get_var('admin', 'bool'))
			{
				$GLOBALS['phpgw_info']['flags']['menu_selection'] = "admin::property::{$this->role}";
			}
			else
			{
				$GLOBALS['phpgw_info']['flags']['menu_selection'] = "property::invoice::{$this->role}";
			}
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
				'allrows'	=> $this->allrows,
				'member_id'	=> $this->member_id
			);
			$this->bo->save_sessiondata($data);
		}

		function columns()
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('columns'));
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$values	= phpgw::get_var('values');

			if ($values['save'])
			{
				$GLOBALS['phpgw']->preferences->account_id=$this->account;
				$GLOBALS['phpgw']->preferences->read_repository();
				$GLOBALS['phpgw']->preferences->add('property','actor_columns_' .$this->role,$values['columns'],'user');
				$GLOBALS['phpgw']->preferences->save_repository();

				$receipt['message'][] = array('msg' => lang('columns is updated'));
			}

			$function_msg   = lang('Select Column');

			$link_data = array
			(
				'menuaction'	=> 'property.uiactor.columns',
				'role'		=> $this->role
			);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data' 	=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'column_list'	=> $this->bo->column_list($values['columns'],$allrows=true),
				'function_msg'	=> $function_msg,
				'form_action'	=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_columns'	=> lang('columns'),
				'lang_none'		=> lang('None'),
				'lang_save'		=> lang('save'),
				'select_name'	=> 'period'
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('columns' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}



		function index()
		{
			$menu_sub = array(
				'tenant'=>'invoice',
				'owner'	=>'admin',
				'vendor'=>'invoice'
				);

			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('actor',
										'receipt',
										'search_field',
										'nextmatchs',
										'filter_member_of'));

			$receipt = $GLOBALS['phpgw']->session->appsession('session_data','actor_receipt_' . $this->role);
			$GLOBALS['phpgw']->session->appsession('session_data','actor_receipt_' . $this->role,'');

			$list = $this->bo->read();

			$uicols	= $this->bo->uicols;

			$j=0;

			if (isset($list) AND is_array($list))
			{
				foreach($list as $entry)
				{
					for ($i=0;$i<count($uicols['name']);$i++)
					{
						if($uicols['input_type'][$i]!='hidden')
						{
							$content[$j]['row'][$i]['value'] = $entry[$uicols['name'][$i]];
							$content[$j]['row'][$i]['name'] = $uicols['name'][$i];
						}
					}

					if($this->acl_read)
					{
						$content[$j]['row'][$i]['statustext']	= lang('view the entity');
						$content[$j]['row'][$i]['text']		= lang('view');
						$content[$j]['row'][$i++]['link']	= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiactor.view', 'actor_id'=> $entry['id'], 'role'=> $this->role));
					}
					if($this->acl_edit)
					{
						$content[$j]['row'][$i]['statustext']	= lang('edit the actor');
						$content[$j]['row'][$i]['text']		= lang('edit');
						$content[$j]['row'][$i++]['link']	= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiactor.edit', 'actor_id'=> $entry['id'], 'role'=> $this->role));
					}
					if($this->acl_delete)
					{
						$content[$j]['row'][$i]['statustext']	= lang('delete the actor');
						$content[$j]['row'][$i]['text']		= lang('delete');
						$content[$j]['row'][$i++]['link']	= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiactor.delete', 'actor_id'=> $entry['id'], 'role'=> $this->role));
					}

					$j++;
				}
			}

//html_print_r($content);
			for ($i=0;$i<count($uicols['descr']);$i++)
			{
				if($uicols['input_type'][$i]!='hidden')
				{
					$table_header[$i]['header'] 	= $uicols['descr'][$i];
					$table_header[$i]['width'] 		= '5%';
					$table_header[$i]['align'] 		= 'center';
					if(isset($uicols['datatype'][$i]) && $uicols['datatype'][$i]!='T' && $uicols['datatype'][$i]!='CH')
					{
						$table_header[$i]['sort_link']	=true;
						$table_header[$i]['sort'] 		= $this->nextmatchs->show_sort_order(array
							(
								'sort'	=> $this->sort,
								'var'	=> $uicols['name'][$i],
								'order'	=> $this->order,
								'extra'	=> array('menuaction'	=> 'property.uiactor.index',
													'query'		=> $this->query,
													'role'		=> $this->role,
													'member_id'	=> $this->member_id
												)
							));
					}
				}
			}

			if($this->acl_read)
			{
				$table_header[$i]['width'] 	= '5%';
				$table_header[$i]['align'] 	= 'center';
				$table_header[$i]['header']	= lang('view');
				$i++;
			}
			if($this->acl_edit)
			{
				$table_header[$i]['width'] 	= '5%';
				$table_header[$i]['align'] 	= 'center';
				$table_header[$i]['header']	= lang('edit');
				$i++;
			}
			if($this->acl_delete)
			{
				$table_header[$i]['width'] 	= '5%';
				$table_header[$i]['align'] 	= 'center';
				$table_header[$i]['header']	= lang('delete');
				$i++;
			}

			if($this->acl_add)
			{
				$table_add = array
				(
					'lang_add'		=> lang('add'),
					'lang_add_statustext'	=> lang('add an actor'),
					'add_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiactor.edit', 'role'=> $this->role))
				);
			}

			$link_data = array
			(
				'menuaction'	=> 'property.uiactor.index',
				'sort'		=>$this->sort,
				'order'		=>$this->order,
				'cat_id'	=>$this->cat_id,
				'filter'	=>$this->filter,
				'query'		=>$this->query,
				'role'		=> $this->role,
				'member_id'	=> $this->member_id
			);

			if(!$this->allrows)
			{
				$record_limit	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$record_limit	= $this->bo->total_records;
			}

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$link_columns = array
			(
				'menuaction' 	=> 'property.uiactor.columns',
				'role'		=> $this->role
			);

			$member_of_data	= $this->cats->formatted_xslt_list(array('selected' => $this->member_id,'globals' => true, 'link_data' =>$link_data));

			$GLOBALS['phpgw']->js->validate_file('overlib','overlib','property');

			$data = array
			(
				'menu'							=> $this->bocommon->get_menu(),
				'lang_columns'						=> lang('columns'),
				'link_columns'						=> $GLOBALS['phpgw']->link('/index.php',$link_columns),
				'lang_columns_help'					=> lang('Choose columns'),
				'msgbox_data'						=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'links'								=> $links,
 				'allow_allrows'						=> false,
				'allrows'							=> $this->allrows,
				'start_record'						=> $this->start,
				'record_limit'						=> $record_limit,
				'num_records'						=> count($list),
 				'all_records'						=> $this->bo->total_records,
				'link_url'							=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'							=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_no_cat'						=> lang('no category'),
				'lang_cat_statustext'				=> lang('Select the category the actor belongs to. To do not use a category select NO CATEGORY'),
				'select_name'						=> 'cat_id',
				'cat_list'							=> $this->bocommon->select_category_list(array('format'=>'filter','selected' => $this->cat_id,'type' => $this->role,'order'=>'descr')),

				'select_action'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),

				'lang_no_member'					=> lang('no member'),
				'member_of_name'					=> 'member_id',
				'member_of_list'					=> $member_of_data['cat_list'],

				'filter_list'						=> $this->nextmatchs->xslt_filter(array('filter' => $this->filter)),
				'lang_filter_statustext'			=> lang('Select the filter. To show all entries select SHOW ALL'),
				'lang_searchfield_statustext'		=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'		=> lang('Submit the search string'),
				'query'								=> $this->query,
				'lang_search'						=> lang('search'),
				'table_header'						=> $table_header,
				'values'							=> $content,
				'table_add'							=> $table_add
			);
			$this->save_sessiondata();
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('actor') . ': ' . lang('list ' . $this->role);

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function edit()
		{

			if(!$this->acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>2, 'acl_location'=> $this->acl_location));
			}

			$actor_id	= phpgw::get_var('actor_id', 'int');
			$values		= phpgw::get_var('values');
			$values_attribute  = phpgw::get_var('values_attribute');

			$insert_record_actor = $GLOBALS['phpgw']->session->appsession('insert_record_values.' . $this->role,'property');

//_debug_array($insert_record_actor);
//_debug_array($values_attribute);
			for ($j=0;$j<count($insert_record_actor);$j++)
			{
				$insert_record['extra'][$insert_record_actor[$j]]	= $insert_record_actor[$j];
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('actor','attributes_form'));
			$receipt = array();

			if (is_array($values))
			{
				if(isset($insert_record) && is_array($insert_record))
				{
					foreach ($insert_record['extra'] as $key => $column)
					{
						if($_POST[$key])
						{
							$values['extra'][$column]	= phpgw::get_var($key, 'string', 'POST');
						}
					}
				}

//_debug_array($values);

				if ((isset($values['save']) && $values['save']) || (isset($values['apply']) && $values['apply']))
				{

					if(!isset($values['cat_id']) || !$values['cat_id'])
					{
						$receipt['error'][]=array('msg'=>lang('Please select a category !'));
					}

					if(!$values['last_name'])
					{
//						$receipt['error'][]=array('msg'=>lang('Please enter a name !'));
					}

					if(isset($values_attribute) && is_array($values_attribute))
					{
						foreach ($values_attribute as $attribute )
						{
							if($attribute['nullable'] != 1 && !$attribute['value'])
							{
								$receipt['error'][]=array('msg'=>lang('Please enter value for attribute %1', $attribute['input_text']));
							}
						}
					}

					if(!isset($receipt['error']) || !$receipt['error'])
					{
						$values['actor_id']	= $actor_id;
						$receipt = $this->bo->save($values,$values_attribute);
						$actor_id = $receipt['actor_id'];
						$this->cat_id = ($values['cat_id']?$values['cat_id']:$this->cat_id);

						if (isset($values['save']) && $values['save'])
						{
							$GLOBALS['phpgw']->session->appsession('session_data','actor_receipt_' . $this->role,$receipt);
							$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uiactor.index', 'role'=> $this->role));
						}
					}
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uiactor.index', 'role'=> $this->role));
				}
			}


			$values = $this->bo->read_single(array('actor_id'=>$actor_id));

			/* Preserve attribute values from post */
			if(isset($receipt['error']) && (isset( $values_attribute) && is_array( $values_attribute)))
			{
				$values = $this->bo->preserve_attribute_values($values,$values_attribute);
			}

			if ($actor_id)
			{
				$this->cat_id = ($values['cat_id']?$values['cat_id']:$this->cat_id);
				$this->member_id = ($values['member_of']?$values['member_of']:$this->member_id);
			}

			$link_data = array
			(
				'menuaction'	=> 'property.uiactor.edit',
				'actor_id'	=> $actor_id,
				'role'		=> $this->role
			);


			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$member_of_data	= $this->cats->formatted_xslt_list(array('selected' => $this->member_id,'globals' => true, 'link_data' =>array()));

			$tabs = array();

			if (isset($values['attributes']) && is_array($values['attributes']))
			{
				foreach ($values['attributes'] as & $attribute)
				{
					if($attribute['history'] == true)
					{
						$link_history_data = array
						(
							'menuaction'	=> 'property.uiactor.attrib_history',
							'attrib_id'	=> $attribute['id'],
							'actor_id'	=> $actor_id,
							'role'		=> $this->role,
							'edit'		=> true
						);

						$attribute['link_history'] = $GLOBALS['phpgw']->link('/index.php',$link_history_data);
					}
				}

				phpgwapi_yui::tabview_setup('actor_edit_tabview');
				$tabs['general']	= array('label' => lang('general'), 'link' => '#general');

				$location = $this->acl_location;
				$attributes_groups = $this->bo->get_attribute_groups($location, $values['attributes']);
			
				$attributes = array();
				foreach ($attributes_groups as $group)
				{
					if(isset($group['attributes']))
					{
						$tabs[str_replace(' ', '_', $group['name'])] = array('label' => $group['name'], 'link' => '#' . str_replace(' ', '_', $group['name']));
						$group['link'] = str_replace(' ', '_', $group['name']);
						$attributes[] = $group;			
					}
				}
				unset($attributes_groups);
				unset($values['attributes']);
			}

			$data = array
			(
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'edit_url'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_actor_id'					=> lang($this->role) . ' ID',
				'value_actor_id'				=> $actor_id,
				'lang_category'					=> lang('category'),
				'lang_save'						=> lang('save'),
				'lang_cancel'					=> lang('cancel'),
				'lang_apply'					=> lang('apply'),
	//			'value_cat'						=> $values['cat'],
				'lang_id_statustext'			=> lang('Choose an ID'),
				'lang_apply_statustext'			=> lang('Apply the values'),
				'lang_cancel_statustext'		=> lang('Leave the actor untouched and return back to the list'),
				'lang_save_statustext'			=> lang('Save the actor and return back to the list'),
				'lang_no_cat'					=> lang('no category'),
				'lang_cat_statustext'			=> lang('Select the category the actor belongs to. To do not use a category select NO CATEGORY'),
				'select_name'					=> 'values[cat_id]',
				'cat_list'						=> $this->bocommon->select_category_list(array('format'=>'select','selected' => $this->cat_id,'type' => $this->role,'order'=>'descr')),

				'lang_member_of'				=> lang('member of'),
				'member_of_name'				=> 'member_id',
				'member_of_list'				=> $member_of_data['cat_list'],

				'lang_attributes'				=> lang('Attributes'),
				'attributes_group'				=> $attributes,
				'lookup_functions'				=> isset($values['lookup_functions'])?$values['lookup_functions']:'',
				'dateformat'					=> $dateformat,
				'lang_edit'						=> lang('edit'),
				'lang_add'						=> lang('add'),
				'textareacols'				=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] : 40,
				'textarearows'				=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] : 6,
				'tabs'							=> phpgwapi_yui::tabview_generate($tabs, 'general')
			);
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('actor') . ': ' . ($actor_id?lang('edit') . ' ' . lang($this->role):lang('add') . ' ' . lang($this->role));

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
		}


		function delete()
		{
			if(!$this->acl_delete)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>8, 'acl_location'=> $this->acl_location));
			}

			$actor_id	= phpgw::get_var('actor_id', 'int');
			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
			(
				'menuaction'	=> 'property.uiactor.index',
				'role'		=> $this->role
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete($actor_id);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'		=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiactor.delete', 'actor_id'=> $actor_id, 'role'=> $this->role)),
				'lang_confirm_msg'	=> lang('do you really want to delete this entry'),
				'lang_yes'		=> lang('yes'),
				'lang_yes_statustext'	=> lang('Delete the entry'),
				'lang_no_statustext'	=> lang('Back to the list'),
				'lang_no'		=> lang('no')
			);

			$appname	= lang('actor');
			$function_msg	= lang('delete') . ' ' . lang($this->role);

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

			$actor_id	= phpgw::get_var('actor_id', 'int');
			$action		= phpgw::get_var('action');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('actor') . ': ' . lang('view') . ' ' . lang($this->role);

			$GLOBALS['phpgw']->xslttpl->add_file(array('actor','attributes_view'));

			$actor = $this->bo->read_single(array('actor_id'=>$actor_id, 'view'=>true));

			$attributes_values=$actor['attributes'];

			$dateformat = strtolower($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			$sep = '/';
			$dlarr[strpos($dateformat,'y')] = 'yyyy';
			$dlarr[strpos($dateformat,'m')] = 'MM';
			$dlarr[strpos($dateformat,'d')] = 'DD';
			ksort($dlarr);

			$dateformat= (implode($sep,$dlarr));

			$member_of_data	= $this->cats->formatted_xslt_list(array('selected' => $actor['member_of'],'globals' => true, 'link_data' =>array()));

			$data = array
			(
				'lang_actor_id'				=> lang($this->role) . ' ID',
				'value_actor_id'			=> $actor_id,
				'done_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiactor.index', 'role'=> $this->role)),
				'lang_category'				=> lang('category'),
				'lang_time_created'			=> lang('time created'),
				'lang_done'				=> lang('done'),
				'cat_list'				=> $this->bocommon->select_category_list(array('format'=>'select','selected' => $actor['cat_id'],'type' => $this->role,'order'=>'descr')),

				'lang_member_of'			=> lang('member of'),
				'member_of_list'			=> $member_of_data['cat_list'],

				'value_date'				=> $GLOBALS['phpgw']->common->show_date($actor['entry_date']),
				'lang_dateformat' 			=> lang(strtolower($dateformat)),
				'lang_attributes'			=> lang('Attributes'),
				'attributes_view'			=> $attributes_values,
				'dateformat'				=> $dateformat,
				'textareacols'				=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] : 40,
				'textarearows'				=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] : 6
			);

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}
	}

