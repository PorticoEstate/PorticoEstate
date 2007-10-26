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
	* @subpackage custom
 	* @version $Id: class.uicustom.inc.php,v 1.19 2007/01/26 14:53:47 sigurdne Exp $
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_uicustom
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
			'index'  => True,
			'view'   => True,
			'edit'   => True,
			'excel' => True,
			'delete' => True
		);

		function property_uicustom()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;
			$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->nextmatchs	= CreateObject('phpgwapi.nextmatchs');
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];

			$this->bo		= CreateObject($this->currentapp.'.bocustom',True);
			$this->bocommon		= CreateObject($this->currentapp.'.bocommon');
			$this->menu		= CreateObject($this->currentapp.'.menu');

			$this->start		= $this->bo->start;
			$this->query		= $this->bo->query;
			$this->sort		= $this->bo->sort;
			$this->order		= $this->bo->order;
			$this->filter		= $this->bo->filter;
			$this->cat_id		= $this->bo->cat_id;
			$this->allrows		= $this->bo->allrows;
			$this->menu->sub	='custom';

			$this->acl 		= CreateObject('phpgwapi.acl');
			$this->acl_location	= '.custom';
			$this->acl_read 	= $this->acl->check('.custom',1);
			$this->acl_add 		= $this->acl->check('.custom',2);
			$this->acl_edit 	= $this->acl->check('.custom',4);
			$this->acl_delete 	= $this->acl->check('.custom',8);

		}

		function save_sessiondata()
		{
			$data = array
			(
				'start'			=> $this->start,
				'query'			=> $this->query,
				'sort'			=> $this->sort,
				'order'			=> $this->order,
				'filter'		=> $this->filter,
				'cat_id'		=> $this->cat_id,
				'this->allrows'		=> $this->allrows
			);
			$this->bo->save_sessiondata($data);
		}

		function index()
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('custom',
										'menu',
										'receipt',
										'search_field',
										'nextmatchs'));

			$links = $this->menu->links();

			$receipt = $GLOBALS['phpgw']->session->appsession('session_data','custom_receipt');
			$GLOBALS['phpgw']->session->appsession('session_data','custom_receipt','');

			$list = $this->bo->read();

			$uicols['name'][]	= 'custom_id';
			$uicols['descr'][]	= lang('ID');
			$uicols['name'][]	= 'name';
			$uicols['descr'][]	= lang('Name');
			$uicols['name'][]	= 'entry_date';
			$uicols['descr'][]	= lang('date');
			$uicols['name'][]	= 'user';
			$uicols['descr'][]	= lang('User');

			$j=0;
			if (isset($list) AND is_array($list))
			{
				foreach($list as $entry)
				{
					for ($i=0;$i<count($uicols['name']);$i++)
					{
						if($uicols['input_type'][$i]!='hidden')
						{
							$content[$j]['row'][$i]['value'] 			= $entry[$uicols['name'][$i]];
							$content[$j]['row'][$i]['name'] 			= $uicols['name'][$i];
						}
					}

					if($this->acl_read)
					{
						$content[$j]['row'][$i]['statustext']			= lang('view the entity');
						$content[$j]['row'][$i]['text']				= lang('view');
						$content[$j]['row'][$i++]['link']			= $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $this->currentapp.'.uicustom.view', 'custom_id'=> $entry['custom_id']));
					}
					if($this->acl_edit)
					{
						$content[$j]['row'][$i]['statustext']			= lang('edit the actor');
						$content[$j]['row'][$i]['text']				= lang('edit');
						$content[$j]['row'][$i++]['link']			= $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $this->currentapp.'.uicustom.edit', 'custom_id'=> $entry['custom_id']));
					}
					if($this->acl_delete)
					{
						$content[$j]['row'][$i]['statustext']			= lang('delete the actor');
						$content[$j]['row'][$i]['text']				= lang('delete');
						$content[$j]['row'][$i++]['link']			= $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $this->currentapp.'.uicustom.delete', 'custom_id'=> $entry['custom_id']));
					}

					$j++;
				}
			}

//_debug_array($list);
			$i=0;
			$table_header[$i]['header'] 	= lang('ID');
			$table_header[$i]['width'] 	= '5%';
			$table_header[$i]['align'] 	= 'center';
			$table_header[$i]['sort_link']	= true;
			$table_header[$i]['sort'] 	= $this->nextmatchs->show_sort_order(array
				(
					'sort'	=> $this->sort,
					'var'	=> 'id',
					'order'	=> $this->order,
					'extra'	=> array('menuaction'	=> $this->currentapp.'.uicustom.index',
										'query'	=>$this->query,
										'start_date'	=> $start_date,
										'end_date'=>$end_date)
				));
			$i++;
			$table_header[$i]['header'] 	= lang('name');
			$table_header[$i]['width'] 	= '5%';
			$table_header[$i]['align'] 	= 'center';
			$table_header[$i]['sort_link']	= true;
			$table_header[$i]['sort'] 	= $this->nextmatchs->show_sort_order(array
				(
					'sort'	=> $this->sort,
					'var'	=> 'name',
					'order'	=> $this->order,
					'extra'	=> array('menuaction'	=> $this->currentapp.'.uicustom.index',
										'query'		=> $this->query,
										'start_date'	=> $start_date,
										'end_date'	=> $end_date)
				));
			$i++;

			$table_header[$i]['header'] 	= lang('date');
			$table_header[$i]['width'] 	= '5%';
			$table_header[$i]['align'] 	= 'center';
			$table_header[$i]['sort_link']	= true;
			$table_header[$i]['sort'] 	= $this->nextmatchs->show_sort_order(array
				(
					'sort'	=> $this->sort,
					'var'	=> 'name',
					'order'	=> $this->order,
					'extra'	=> array('menuaction'	=> $this->currentapp.'.uicustom.index',
										'query'		=> $this->query,
										'start_date'	=> $start_date,
										'end_date'	=> $end_date)
				));
			$i++;
			$table_header[$i]['header'] 	= lang('User');
			$table_header[$i]['width'] 	= '5%';
			$table_header[$i]['align'] 	= 'center';
			$table_header[$i]['sort_link']	= true;
			$table_header[$i]['sort'] 	= $this->nextmatchs->show_sort_order(array
				(
					'sort'	=> $this->sort,
					'var'	=> 'user_id',
					'order'	=> $this->order,
					'extra'	=> array('menuaction'	=> $this->currentapp.'.uicustom.index',
										'query'		=> $this->query,
										'start_date'	=> $start_date,
										'end_date'	=> $end_date)
				));
			$i++;


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
				$table_add = array
				(
					'lang_add'		=> lang('add'),
					'lang_add_statustext'	=> lang('add a custom query'),
					'add_action'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $this->currentapp.'.uicustom.edit'))
				);
			}

			$link_data = array
			(
				'menuaction'	=> $this->currentapp.'.uicustom.index',
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'cat_id'	=> $this->cat_id,
				'filter'	=> $this->filter,
				'query'		=> $this->query
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

			$data = array
			(
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'links'						=> $links,
 				'allow_allrows'					=> true,
				'allrows'					=> $this->allrows,
				'start_record'					=> $this->start,
				'record_limit'					=> $record_limit,
				'num_records'					=> count($list),
 				'all_records'					=> $this->bo->total_records,
				'link_url'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'					=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'select_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_searchfield_statustext'			=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'			=> lang('Submit the search string'),
				'query'						=> $this->query,
				'lang_search'					=> lang('search'),
				'table_header'					=> $table_header,
				'values'					=> $content,
				'table_add'					=> $table_add
			);
			$this->save_sessiondata();
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('custom') . ': ' . lang('list custom');

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function edit()
		{
			if(!$this->acl_add && !$this->acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> $this->currentapp.'.uilocation.stop', 'perm'=>2, 'acl_location'=> $this->acl_location));
			}

			$custom_id	= get_var('custom_id',array('POST','GET'));
			$cols_id	= get_var('cols_id',array('POST','GET'));
			$resort		= get_var('resort',array('POST','GET'));
			$values		= get_var('values',array('POST'));

			if($cols_id)
			{
				$this->bo->resort(array('custom_id'=>$custom_id,'id'=>$cols_id,'resort'=>$resort));
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('custom'));

			if ($values['save'] || $values['apply'])
			{
				if(!$values['name'])
				{
					$receipt['error'][]=array('msg'=>lang('Please enter a name !'));
				}

				if(!$values['sql_text'])
				{
					$receipt['error'][]=array('msg'=>lang('Please enter a sql query !'));
				}

				if(!$receipt['error'])
				{
					$values['custom_id']	= $custom_id;
					$receipt = $this->bo->save($values);
					$custom_id = $receipt['custom_id'];
					$this->cat_id = ($values['cat_id']?$values['cat_id']:$this->cat_id);

					if ($values['save'])
					{
						$GLOBALS['phpgw']->session->appsession('session_data','custom_receipt',$receipt);
						$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'=> $this->currentapp.'.uicustom.index'));
					}
				}
			}

			if ($values['cancel'])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'=> $this->currentapp.'.uicustom.index'));
			}


			if ($custom_id)
			{
				$custom = $this->bo->read_single($custom_id);
				$this->cat_id = ($custom['cat_id']?$custom['cat_id']:$this->cat_id);
			}


			$link_data = array
			(
				'menuaction'	=> $this->currentapp.'.uicustom.edit',
				'custom_id'	=> $custom_id
			);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			while (is_array($custom['cols']) && list(,$entry) = each($custom['cols']))
			{
				$cols[] = array(
					'id'		=> $entry['id'],
					'name'		=> $entry['name'],
					'descr'		=> $entry['descr'],
					'sorting'	=> $entry['sorting'],
					'text_up'	=> lang('Up'),
					'text_down'	=> lang('Down'),
					'link_up'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $this->currentapp.'.uicustom.edit', 'resort'=> 'up', 'cols_id'=> $entry['id'], 'custom_id'=> $custom_id)),
					'link_down'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $this->currentapp.'.uicustom.edit', 'resort'=> 'down', 'cols_id'=> $entry['id'], 'custom_id'=> $custom_id)),
					);
			}


			$data = array
			(
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'edit_url'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_custom_id'				=> lang('ID'),
				'value_custom_id'				=> $custom_id,
				'lang_sql_text'					=> lang('sql'),
				'lang_name'					=> lang('name'),
				'lang_save'					=> lang('save'),
				'lang_cancel'					=> lang('cancel'),
				'lang_apply'					=> lang('apply'),
				'value_sql_text'				=> $custom['sql_text'],
				'value_name'					=> $custom['name'],
				'lang_name_statustext'				=> lang('Enter a name for the query'),
				'lang_sql_statustext'				=> lang('Enter a sql query'),
				'lang_apply_statustext'				=> lang('Apply the values'),
				'lang_cancel_statustext'			=> lang('Leave the custom untouched and return back to the list'),
				'lang_save_statustext'				=> lang('Save the custom and return back to the list'),
				'lang_no_cat'					=> lang('no category'),
				'lang_cat_statustext'				=> lang('Select the category the custom belongs to. To do not use a category select NO CATEGORY'),
				'lang_descr'					=> lang('descr'),
				'lang_new_name_statustext'			=> lang('name'),
				'lang_new_descr_statustext'			=> lang('descr'),
				'cols'						=> $cols,
				'lang_col_name'					=> lang('Column name'),
				'lang_col_descr'				=> lang('Column description'),
				'lang_delete_column'				=> lang('Delete column'),
				'lang_delete_cols_statustext'			=> lang('Delete this column from the output'),
				'lang_up_text'					=> lang('Up'),
				'lang_down_text'				=> lang('Down'),
				'lang_sorting'					=> lang('Sorting'),

			);
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('custom') . ': ' . ($custom_id?lang('edit custom'):lang('add custom'));

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}


		function delete()
		{
			$custom_id	= get_var('custom_id',array('POST','GET'));
			$confirm	= get_var('confirm',array('POST'));

			$link_data = array
			(
				'menuaction' => $this->currentapp.'.uicustom.index'
			);

			if (get_var('confirm',array('POST')))
			{
				$this->bo->delete($custom_id);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'		=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $this->currentapp.'.uicustom.delete', 'custom_id'=> $custom_id)),
				'lang_confirm_msg'	=> lang('do you really want to delete this entry'),
				'lang_yes'		=> lang('yes'),
				'lang_yes_statustext'	=> lang('Delete the entry'),
				'lang_no_statustext'	=> lang('Back to the list'),
				'lang_no'		=> lang('no')
			);

			$appname	= lang('custom');
			$function_msg	= lang('delete custom');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->currentapp) . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}


		function view()
		{
			$custom_id	= get_var('custom_id',array('GET'));

			$GLOBALS['phpgw']->xslttpl->add_file(array('custom','nextmatchs'));

			$list= $this->bo->read_custom($custom_id);
			$uicols	= $this->bo->uicols;

//_debug_array($uicols);

			$j=0;
			if (isSet($list) AND is_array($list))
			{
				foreach($list as $entry)
				{
					for ($i=0;$i<count($uicols);$i++)
					{
						$content[$j]['row'][$i]['value'] = $entry[$uicols[$i]['name']];
					}
					$j++;
				}
			}

			for ($i=0;$i<count($uicols);$i++)
			{
				$table_header[$i]['header'] 	= $uicols[$i]['descr'];
				$table_header[$i]['width'] 	= '15%';
				$table_header[$i]['align'] 	= 'left';
			}

//_debug_array($content);

			$custom_name = $this->bo->read_custom_name($custom_id);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('custom') . ': ' . $custom_name;

			$link_data = array
			(
				'menuaction'	=> $this->currentapp.'.uicustom.view',
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'custom_id'	=> $custom_id,
				'filter'	=> $this->filter,
				'query'		=> $this->query
			);


			if(!$this->allrows)
			{
				$record_limit	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$record_limit	= $this->bo->total_records;
			}

			$link_excel = array
			(
				'menuaction'	=> $this->currentapp.'.uicustom.excel',
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'filter'	=> $this->filter,
				'query'		=> $this->query,
				'custom_id'	=> $custom_id,
				'allrows'	=> $this->allrows
			);

			$GLOBALS['phpgw']->js->validate_file('overlib','overlib',$this->currentapp);

			$data = array
			(
				'lang_excel'				=> 'excel',
				'link_excel'				=> $GLOBALS['phpgw']->link('/index.php',$link_excel),
				'lang_excel_help'			=> lang('Download table to MS Excel'),

 				'allow_allrows'				=> true,
				'allrows'				=> $this->allrows,
				'start_record'				=> $this->start,
				'record_limit'				=> $record_limit,
				'num_records'				=> count($list),
 				'all_records'				=> $this->bo->total_records,
				'link_url'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'				=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'select_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_searchfield_statustext'		=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'		=> lang('Submit the search string'),
				'query'					=> $this->query,
				'lang_search'				=> lang('search'),
				'table_header'				=> $table_header,
				'values'				=> $content,

				'done_action'				=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $this->currentapp.'.uicustom.index')),
				'lang_done'				=> lang('done'),
			);

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function excel()
		{
			$custom_id = get_var('custom_id',array('POST','GET'));
			$list= $this->bo->read_custom($custom_id,$allrows=True);
			$uicols	= $this->bo->uicols;
			foreach($uicols as $col)
			{
				$names[] = $col['name'];
				$descr[] = $col['descr'];
			}
			$this->bocommon->excel($list,$names,$descr);
		}
	}
?>
