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
 	* @version $Id: class.uiactor.inc.php 18358 2007-11-27 04:43:37Z skwashd $
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
			'index'  	=> True,
			'view'   	=> True,
			'edit'   	=> True,
			'delete' 	=> True,
			'list_attribute'=> True,
			'edit_attrib'	=> True,
			'columns'	=> True
		);

		function property_uiactor()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;
			$this->currentapp		= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->nextmatchs		= CreateObject('phpgwapi.nextmatchs');
			$this->account			= $GLOBALS['phpgw_info']['user']['account_id'];

			$this->bo				= CreateObject('property.boactor',True);
			$this->bocommon			= CreateObject('property.bocommon');
			$this->menu				= CreateObject('property.menu');

			$this->role				= $this->bo->role;

			$this->cats				= CreateObject('phpgwapi.categories');
			$this->cats->app_name	= 'fm_' . $this->role;

			$this->acl				= CreateObject('phpgwapi.acl');
			$this->acl_location		= '.' . $this->role;

			$this->acl_read 		= $this->acl->check($this->acl_location,1);
			$this->acl_add			= $this->acl->check($this->acl_location,2);
			$this->acl_edit			= $this->acl->check($this->acl_location,4);
			$this->acl_delete		= $this->acl->check($this->acl_location,8);
			$this->acl_manage		= $this->acl->check($this->acl_location,16);
			
			$this->start			= $this->bo->start;
			$this->query			= $this->bo->query;
			$this->sort				= $this->bo->sort;
			$this->order			= $this->bo->order;
			$this->filter			= $this->bo->filter;
			$this->cat_id			= $this->bo->cat_id;
			$this->allrows			= $this->bo->allrows;
			$this->member_id		= $this->bo->member_id;

			$valid_role = array(
				'tenant'=>True,
				'owner'	=>True,
				'vendor'=>True
				);
			if(!$valid_role[$this->role])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> $this->currentapp.'.uilocation.index'));
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


			$GLOBALS['phpgw_info']['flags']['noframework'] = True;

			$values                 = phpgw::get_var('values');

			if ($values['save'])
			{

				$GLOBALS['phpgw']->preferences->account_id=$this->account;
				$GLOBALS['phpgw']->preferences->read_repository();
				$GLOBALS['phpgw']->preferences->add($this->currentapp,'actor_columns_' .$this->role,$values['columns'],'user');
				$GLOBALS['phpgw']->preferences->save_repository();

				$receipt['message'][] = array('msg' => lang('columns is updated'));
			}

			$function_msg   = lang('Select Column');

			$link_data = array
			(
				'menuaction'	=> $this->currentapp.'.uiactor.columns',
				'role'		=> $this->role
			);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data' 	=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'column_list'	=> $this->bo->column_list($values['columns'],$allrows=True),
				'function_msg'	=> $function_msg,
				'form_action'	=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_columns'	=> lang('columns'),
				'lang_none'	=> lang('None'),
				'lang_save'	=> lang('save'),
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

			$this->menu->sub	= $menu_sub[$this->role];

			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> $this->currentapp.'.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('actor',
										'menu',
										'receipt',
										'search_field',
										'nextmatchs',
										'filter_member_of'));

			$links = $this->menu->links($this->role);

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
						$content[$j]['row'][$i++]['link']	= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiactor.view', 'actor_id'=> $entry['id'], 'role'=> $this->role));
					}
					if($this->acl_edit)
					{
						$content[$j]['row'][$i]['statustext']	= lang('edit the actor');
						$content[$j]['row'][$i]['text']		= lang('edit');
						$content[$j]['row'][$i++]['link']	= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiactor.edit', 'actor_id'=> $entry['id'], 'role'=> $this->role));
					}
					if($this->acl_delete)
					{
						$content[$j]['row'][$i]['statustext']	= lang('delete the actor');
						$content[$j]['row'][$i]['text']		= lang('delete');
						$content[$j]['row'][$i++]['link']	= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiactor.delete', 'actor_id'=> $entry['id'], 'role'=> $this->role));
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
								'extra'	=> array('menuaction'	=> $this->currentapp.'.uiactor.index',
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
					'add_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiactor.edit', 'role'=> $this->role))
				);
			}

			$link_data = array
			(
				'menuaction'	=> $this->currentapp.'.uiactor.index',
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
				'menuaction' 	=> $this->currentapp.'.uiactor.columns',
				'role'		=> $this->role
			);

			$member_of_data	= $this->cats->formatted_xslt_list(array('selected' => $this->member_id,'globals' => True, 'link_data' =>$link_data));

			$GLOBALS['phpgw']->js->validate_file('overlib','overlib',$this->currentapp);

			$data = array
			(
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
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> $this->currentapp.'.uilocation.stop', 'perm'=>2, 'acl_location'=> $this->acl_location));
			}

			$actor_id	= phpgw::get_var('actor_id', 'int');
			$values		= phpgw::get_var('values');
			$values_attribute  = phpgw::get_var('values_attribute');

			$insert_record_actor = $GLOBALS['phpgw']->session->appsession('insert_record_actor',$this->currentapp);


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
							$values['extra'][$column]	= $_POST[$key];
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

					if(!isset($receipt['error']) || !$receipt['error'])
					{
						$values['actor_id']	= $actor_id;
						$receipt = $this->bo->save($values,$values_attribute);
						$actor_id = $receipt['actor_id'];
						$this->cat_id = ($values['cat_id']?$values['cat_id']:$this->cat_id);

						if (isset($values['save']) && $values['save'])
						{
							$GLOBALS['phpgw']->session->appsession('session_data','actor_receipt_' . $this->role,$receipt);
							$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> $this->currentapp.'.uiactor.index', 'role'=> $this->role));
						}
					}
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> $this->currentapp.'.uiactor.index', 'role'=> $this->role));
				}
			}


			$actor = $this->bo->read_single(array('actor_id'=>$actor_id));

			/* Preserve attribute values from post */
			if(isset($receipt['error']) && (isset( $values_attribute) && is_array( $values_attribute)))
			{
				$actor = $this->bocommon->preserve_attribute_values($actor,$values_attribute);
			}

			if ($actor_id)
			{
				$this->cat_id = ($actor['cat_id']?$actor['cat_id']:$this->cat_id);
				$this->member_id = ($actor['member_of']?$actor['member_of']:$this->member_id);
			}

			$link_data = array
			(
				'menuaction'	=> $this->currentapp.'.uiactor.edit',
				'actor_id'	=> $actor_id,
				'role'		=> $this->role
			);

			$dateformat = strtolower($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			$sep = '/';
			$dlarr[strpos($dateformat,'y')] = 'yyyy';
			$dlarr[strpos($dateformat,'m')] = 'MM';
			$dlarr[strpos($dateformat,'d')] = 'DD';
			ksort($dlarr);

			$dateformat= (implode($sep,$dlarr));

			switch(substr($dateformat,0,1))
			{
				case 'M':
					$dateformat_validate= "javascript:vDateType='1'";
					$onKeyUp	= "DateFormat(this,this.value,event,false,'1')";
					$onBlur		= "DateFormat(this,this.value,event,true,'1')";
					break;
				case 'y':
					$dateformat_validate="javascript:vDateType='2'";
					$onKeyUp	= "DateFormat(this,this.value,event,false,'2')";
					$onBlur		= "DateFormat(this,this.value,event,true,'2')";
					break;
				case 'D':
					$dateformat_validate="javascript:vDateType='3'";
					$onKeyUp	= "DateFormat(this,this.value,event,false,'3')";
					$onBlur		= "DateFormat(this,this.value,event,true,'3')";
					break;
			}

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$member_of_data	= $this->cats->formatted_xslt_list(array('selected' => $this->member_id,'globals' => True, 'link_data' =>array()));

			$GLOBALS['phpgw']->js->validate_file('dateformat','dateformat',$this->currentapp);

//_debug_array($member_of_data);
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
	//			'value_cat'						=> $actor['cat'],
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

				'lang_dateformat' 				=> lang(strtolower($dateformat)),
				'dateformat_validate'			=> $dateformat_validate,
				'onKeyUp'						=> $onKeyUp,
				'onBlur'						=> $onBlur,
				'lang_attributes'				=> lang('Attributes'),
		//		'attributes_header'				=> $attributes_header,
				'attributes_values'				=> $actor['attributes'],
				'lookup_functions'				=> isset($actor['lookup_functions'])?$actor['lookup_functions']:'',
				'dateformat'					=> $dateformat,
				'lang_edit'						=> lang('edit'),
				'lang_add'						=> lang('add'),
				'textareacols'				=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] : 40,
				'textarearows'				=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] : 6
			);
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('actor') . ': ' . ($actor_id?lang('edit') . ' ' . lang($this->role):lang('add') . ' ' . lang($this->role));

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}


		function delete()
		{
			if(!$this->acl_delete)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> $this->currentapp.'.uilocation.stop', 'perm'=>8, 'acl_location'=> $this->acl_location));
			}

			$attrib		= phpgw::get_var('attrib');
			$id			= phpgw::get_var('id', 'int');
			$actor_id	= phpgw::get_var('actor_id', 'int');
	//		$delete		= phpgw::get_var('delete', 'bool', 'POST');
			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');


			if($attrib)
			{
				$function='list_attribute';
			}
			else
			{
				$function='index';
			}
			$link_data = array
			(
				'menuaction'	=> $this->currentapp.'.uiactor.'.$function,
				'role'		=> $this->role
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete($actor_id,$id,$attrib);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'		=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiactor.delete', 'actor_id'=> $actor_id, 'id'=> $id, 'attrib'=> $attrib, 'role'=> $this->role)),
				'lang_confirm_msg'	=> lang('do you really want to delete this entry'),
				'lang_yes'		=> lang('yes'),
				'lang_yes_statustext'	=> lang('Delete the entry'),
				'lang_no_statustext'	=> lang('Back to the list'),
				'lang_no'		=> lang('no')
			);

			$appname	= lang('actor');
			$function_msg	= lang('delete') . ' ' . lang($this->role);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->currentapp) . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}



		function view()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> $this->currentapp.'.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$actor_id	= phpgw::get_var('actor_id', 'int');
			$action		= phpgw::get_var('action');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('actor') . ': ' . lang('view') . ' ' . lang($this->role);

			$GLOBALS['phpgw']->xslttpl->add_file(array('actor','attributes_view'));

			$actor = $this->bo->read_single(array('actor_id'=>$actor_id));

			$attributes_values=$actor['attributes'];

			$dateformat = strtolower($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			$sep = '/';
			$dlarr[strpos($dateformat,'y')] = 'yyyy';
			$dlarr[strpos($dateformat,'m')] = 'MM';
			$dlarr[strpos($dateformat,'d')] = 'DD';
			ksort($dlarr);

			$dateformat= (implode($sep,$dlarr));

			$member_of_data	= $this->cats->formatted_xslt_list(array('selected' => $actor['member_of'],'globals' => True, 'link_data' =>array()));

			$data = array
			(
				'lang_actor_id'				=> lang($this->role) . ' ID',
				'value_actor_id'			=> $actor_id,
				'done_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiactor.index', 'role'=> $this->role)),
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

		function list_attribute()
		{

			if(!$this->acl_manage)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> $this->currentapp.'.uilocation.stop', 'perm'=>16, 'acl_location'=> $this->acl_location));
			}

			$id	= phpgw::get_var('id', 'int');
			$resort	= phpgw::get_var('resort');

			$GLOBALS['phpgw']->xslttpl->add_file(array(
								'actor',
								'nextmatchs',
								'search_field'));

			if($resort)
			{
				$this->bo->resort_attrib(array('resort'=>$resort,'id'=>$id));
			}

			$attrib_list = $this->bo->read_attrib();

			while (is_array($attrib_list) && list(,$attrib) = each($attrib_list))
			{
				$content[] = array
				(
					'name'						=> $attrib['name'],
					'type_name'					=> $attrib['type_name'],
					'datatype'					=> $attrib['datatype'],
					'column_name'				=> $attrib['column_name'],
					'input_text'				=> $attrib['input_text'],
					'sorting'					=> $attrib['attrib_sort'],
					'search'					=> $attrib['search'],
					'link_up'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiactor.list_attribute', 'resort'=> 'up', 'id'=> $attrib['id'], 'allrows'=> $this->allrows, 'role'=> $this->role)),
					'link_down'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiactor.list_attribute', 'resort'=> 'down', 'id'=> $attrib['id'], 'allrows'=> $this->allrows, 'role'=> $this->role)),
					'link_edit'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiactor.edit_attrib', 'id'=> $attrib['id'], 'role'=> $this->role)),
					'link_delete'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiactor.delete', 'id'=> $attrib['id'], 'attrib'=> true, 'role'=> $this->role)),
					'lang_view_attribtext'		=> lang('view the attrib'),
					'lang_attribute_attribtext'	=> lang('attributes for the attrib'). ' ' . lang('location'),
					'lang_edit_attribtext'		=> lang('edit the attrib'),
					'lang_delete_attribtext'	=> lang('delete the attrib'),
					'text_attribute'			=> lang('Attributes'),
					'text_up'					=> lang('up'),
					'text_down'					=> lang('down'),
					'text_edit'					=> lang('edit'),
					'text_delete'				=> lang('delete')
				);
			}

	//html_print_r($content);

			$table_header[] = array
			(
				'lang_descr'		=> lang('Descr'),
				'lang_datatype'		=> lang('Datatype'),
				'lang_sorting'		=> lang('sorting'),
				'lang_search'		=> lang('search'),
				'lang_edit'			=> lang('edit'),
				'lang_delete'		=> lang('delete'),
				'sort_sorting'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'attrib_sort',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> $this->currentapp.'.uiactor.list_attribute',
														'allrows'=> $this->allrows,
														'role'	=> $this->role)
										)),

				'sort_name'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'column_name',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> $this->currentapp.'.uiactor.list_attribute',
														'allrows'=> $this->allrows,
														'role'	=> $this->role)
										)),
				'lang_name'	=> lang('Name'),
			);

			$table_add[] = array
			(
				'lang_add'		=> lang('add'),
				'lang_add_attribtext'	=> lang('add an attrib'),
				'add_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiactor.edit_attrib', 'role'=> $this->role)),
				'lang_done'		=> lang('done'),
				'lang_done_attribtext'	=> lang('back to admin'),
				'done_action'		=> $GLOBALS['phpgw']->link('/admin/index.php'),
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
				'menuaction'	=> $this->currentapp.'.uiactor.list_attribute',
				'sort'		=>$this->sort,
				'order'		=>$this->order,
				'query'		=>$this->query,
				'role'		=> $this->role

			);

			$data = array
			(
				'allow_allrows'					=> True,
				'allrows'						=> $this->allrows,
				'start_record'					=> $this->start,
				'record_limit'					=> $record_limit,
				'num_records'					=> count($attrib_list),
				'all_records'					=> $this->bo->total_records,
				'link_url'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'						=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_searchfield_attribtext'	=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_attribtext'	=> lang('Submit the search string'),
				'query'							=> $this->query,
				'lang_search'					=> lang('search'),
				'table_header_attrib'			=> $table_header,
				'values_attrib'					=> $content,
				'table_add2'					=> $table_add
			);

			$appname	= lang('actor');
			$function_msg	= lang('list attribute') . ': ' . lang($this->role);
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->currentapp) . ' - ' . $appname . ': ' . $function_msg;
			//$this->save_sessiondata();
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_attribute' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function edit_attrib()
		{
			if(!$this->acl_manage)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> $this->currentapp.'.uilocation.stop', 'perm'=>16, 'acl_location'=> $this->acl_location));
			}

			$id		= phpgw::get_var('id', 'int');
			$values		= phpgw::get_var('values');
	//		$GLOBALS['phpgw']->common->msgbox(lang('Altering ColumnName OR Datatype  - deletes your data in this Column'));
	//html_print_r($values);
			$GLOBALS['phpgw']->xslttpl->add_file(array('actor','choice',));

			if (isset($values['save']) && $values['save'])
			{
				if($id)
				{
					$values['id']=$id;
				}

				if (!$values['column_name'])
				{
					$receipt['error'][] = array('msg'=>lang('Column name not entered!'));
				}

				if (!$values['input_text'])
				{
					$receipt['error'][] = array('msg'=>lang('Input text not entered!'));
				}
				if (!$values['statustext'])
				{
					$receipt['error'][] = array('msg'=>lang('Statustext not entered!'));
				}

				if (!$values['column_info']['type'])
				{
					$receipt['error'][] = array('msg'=>lang('Datatype type not chosen!'));
				}

				if(!ctype_digit($values['column_info']['precision']) && $values['column_info']['precision'])
				{
					$receipt['error'][]=array('msg'=>lang('Please enter precision as integer !'));
					unset($values['column_info']['precision']);
				}

				if($values['column_info']['scale'] && !ctype_digit($values['column_info']['scale']))
				{
					$receipt['error'][]=array('msg'=>lang('Please enter scale as integer !'));
					unset($values['column_info']['scale']);
				}

				if (!$values['column_info']['nullable'])
				{
					$receipt['error'][] = array('msg'=>lang('Nullable not chosen!'));
				}

				if (!isset($receipt['error']) || !$receipt['error'])
				{
					$receipt = $this->bo->save_attrib($values);

					if(!$id)
					{
						$id=$receipt['id'];
					}
				}
				else
				{
					$receipt['error'][] = array('msg'	=> lang('Attribute has NOT been saved'));
				}

			}

			if ($id)
			{
				$values = $this->bo->read_single_attrib($id);
				$function_msg = lang('edit attribute') . ': ' . lang($this->role);
				$action='edit';
			}
			else
			{
				$function_msg = lang('add attribute') . ': ' . lang($this->role);
				$action='add';
			}

			$link_data = array
			(
				'menuaction'	=> $this->currentapp.'.uiactor.edit_attrib',
				'id'		=> $id,
				'role'		=> $this->role

			);
//	_debug_array($values);

			if(isset($values['column_info']) && is_array($values['column_info']))
			{
				if($values['column_info']['type']=='R' || $values['column_info']['type']=='CH' || $values['column_info']['type']=='LB')
				{
					$multiple_choice= True;
				}
				$column_type = $values['column_info']['type'];
				$column_precision = $values['column_info']['precision'];
				$column_scale  = $values['column_info']['scale'];
				$column_default  = $values['column_info']['default'];
				$column_nullable  = $values['column_info']['nullable'];
			}

			$msgbox_data = (isset($receipt)?$this->bocommon->msgbox_data($receipt):'');
			$data = array
			(
				'lang_choice'					=> lang('Choice'),
				'lang_new_value'				=> lang('New value'),
				'lang_new_value_statustext'		=> lang('New value for multiple choice'),
				'multiple_choice'				=> (isset($multiple_choice)?$multiple_choice:''),
				'value_choice'					=> (isset($values['choice'])?$values['choice']:''),
				'lang_delete_value'				=> lang('Delete value'),
				'lang_value'					=> lang('value'),
				'lang_delete_choice_statustext'	=> lang('Delete this value from the list of multiple choice'),
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiactor.list_attribute', 'role'=> $this->role)),
				'lang_id'						=> lang('Attribute ID'),
				'lang_save'						=> lang('save'),
				'lang_done'						=> lang('done'),
				'value_id'						=> $id,

				'lang_column_name'				=> lang('Column name'),
				'value_column_name'				=> (isset($values['column_name'])?$values['column_name']:''),
				'lang_column_name_statustext'	=> lang('enter the name for the column'),

				'lang_input_text'				=> lang('input text'),
				'value_input_text'				=> (isset($values['input_text'])?$values['input_text']:''),
				'lang_input_name_statustext'	=> lang('enter the input text for records'),

				'lang_id_attribtext'			=> lang('Enter the attribute ID'),
				'lang_entity_statustext'		=> lang('Select a actor type'),

				'lang_statustext'				=> lang('Statustext'),
				'lang_statustext_attribtext'	=> lang('Enter a statustext for the inputfield in forms'),
				'value_statustext'				=> (isset($values['statustext'])?$values['statustext']:''),

				'lang_done_attribtext'			=> lang('Back to the list'),
				'lang_save_attribtext'			=> lang('Save the attribute'),

				'lang_datatype'					=> lang('Datatype'),
				'lang_datatype_statustext'		=> lang('Select a datatype'),
				'lang_no_datatype'				=> lang('No datatype'),
				'datatype_list'					=> $this->bocommon->select_datatype((isset($column_type)?$column_type:''),'actor'),

				'lang_precision'				=> lang('Precision'),
				'lang_precision_statustext'		=> lang('enter the record length'),
				'value_precision'				=> (isset($column_precision)?$column_precision:''),

				'lang_scale'					=> lang('scale'),
				'lang_scale_statustext'			=> lang('enter the scale if type is decimal'),
				'value_scale'					=> (isset($column_scale)?$column_scale:''),

				'lang_default'					=> lang('default'),
				'lang_default_statustext'		=> lang('enter the default value'),
				'value_default'					=> (isset($column_default)?$column_default:''),

				'lang_nullable'					=> lang('Nullable'),
				'lang_nullable_statustext'		=> lang('Chose if this column is nullable'),
				'lang_select_nullable'			=> lang('Select nullable'),
				'nullable_list'					=> $this->bocommon->select_nullable((isset($column_nullable)?$column_nullable:'')),

				'value_list'					=> (isset($values['list'])?$values['list']:''),
				'lang_list'						=> lang('show in list'),
				'lang_list_statustext'			=> lang('check to show this attribute in location list'),

				'value_search'					=> (isset($values['search'])?$values['search']:''),
				'lang_include_search'			=> lang('Include in search'),
				'lang_include_search_statustext'=> lang('check to show this attribute in location list'),
			);
	//html_print_r($data);

			$appname = lang('actor');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->currentapp) . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit_attrib' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}
	}
?>
