<?php
	/**
	* phpGroupWare - HRM: a  human resource competence management system.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package hrm
	* @subpackage place
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package hrm
	 */

	class hrm_uiplace
	{
		var $grants;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $sub;
		var $currentapp;

		var $public_functions = array
		(
			'index'  => true,
			'view'   => true,
			'training'=> true,
			'edit'   => true,
			'delete' => true
		);

		function hrm_uiplace()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
		//	$this->currentapp		= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->nextmatchs		= CreateObject('phpgwapi.nextmatchs');
			$this->account			= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo			= CreateObject('hrm.boplace',true);
			$this->bocommon			= CreateObject('hrm.bocommon');

			$this->start			= $this->bo->start;
			$this->query			= $this->bo->query;
			$this->sort			= $this->bo->sort;
			$this->order			= $this->bo->order;
			$this->allrows			= $this->bo->allrows;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'hrm::place';
		}

		function save_sessiondata()
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

		function index()
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('place','nextmatchs','menu',
										'search_field'));

			$place_info = $this->bo->read();

			while (is_array($place_info) && list(,$entry) = each($place_info))
			{

				$content[] = array
				(
					'name'					=> $entry['name'],
					'link_edit'				=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uiplace.edit', 'place_id'=> $entry['id'])),
					'link_delete'				=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uiplace.delete', 'place_id'=> $entry['id'])),
					'link_view'				=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uiplace.view', 'place_id'=> $entry['id'])),
					'lang_view_place_text'			=> lang('view the place'),
					'lang_edit_place_text'			=> lang('edit the place'),
					'text_view'				=> lang('view'),
					'text_edit'				=> lang('edit'),
					'text_delete'				=> lang('delete'),
					'lang_delete_place_text'		=> lang('delete the place'),
				);
			}

//_debug_array($content);

			$table_header[] = array
			(

				'sort_name'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'name',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'hrm.uiplace.index',
														'query'		=> $this->query,
														'cat_id'	=> $this->cat_id,
														'allrows'	=> $this->allrows)
										)),
				'lang_delete'	=> lang('delete'),
				'lang_edit'	=> lang('edit'),
				'lang_view'	=> lang('view'),
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
				'menuaction'	=> 'hrm.uiplace.index',
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'cat_id'	=> $this->cat_id,
				'filter'	=> $this->filter,
				'query'		=> $this->query
			);

			$table_add[] = array
			(
				'lang_add'		=> lang('add'),
				'lang_add_statustext'	=> lang('add a place'),
				'add_action'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uiplace.edit')),
			);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'menu'							=> execMethod('hrm.menu.links'),
				'allow_allrows'					=> true,
				'allrows'					=> $this->allrows,
				'start_record'					=> $this->start,
				'record_limit'					=> $record_limit,
				'num_records'					=> count($place_info),
				'all_records'					=> $this->bo->total_records,
				'link_url'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'					=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_searchfield_statustext'			=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'			=> lang('Submit the search string'),
				'query'						=> $this->query,
				'lang_search'					=> lang('search'),
				'table_header'					=> $table_header,
				'table_add'					=> $table_add,
				'values'					=> $content
			);

			$appname	= lang('place');
			$function_msg= lang('list place');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('hrm') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list' => $data));
			$this->save_sessiondata();
		}


		function edit()
		{
			$place_id	= phpgw::get_var('place_id', 'int');
			$values		= phpgw::get_var('values');

			$GLOBALS['phpgw']->xslttpl->add_file(array('place'));

			if (is_array($values))
			{
				if ($values['save'] || $values['apply'])
				{

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

					if($place_id)
					{
						$values['place_id']=$place_id;
						$action='edit';
					}

					if(!$receipt['error'])
					{
						$receipt = $this->bo->save($values,$action);
						$place_id = $receipt['place_id'];

						if ($values['save'])
						{
							$GLOBALS['phpgw']->session->appsession('session_data','hrm_training_receipt',$receipt);
							$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'=> 'hrm.uiplace.index', 'place_id'=> $place_id));
						}
					}
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'=> 'hrm.uiplace.index', 'place_id'=> $place_id));
				}
			}


			if ($place_id)
			{
				if(!$receipt['error'])
				{
					$values = $this->bo->read_single($place_id);
				}
				$function_msg = lang('edit place');
				$action='edit';
			}
			else
			{
				$function_msg = lang('add place');
				$action='add';
			}

			$link_data = array
			(
				'menuaction'	=> 'hrm.uiplace.edit',
				'place_id'	=> $place_id
			);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);


			$data = array
			(
				'value_title'			=> $values['title'],
				'value_entry_date'		=> $values['entry_date'],
				'value_name'			=> $values['name'],
				'value_address'			=> $values['address'],
				'value_zip'			=> $values['zip'],
				'value_town'			=> $values['town'],
				'value_remark'			=> $values['remark'],

				'lang_entry_date'		=> lang('Entry date'),
				'lang_name'			=> lang('name'),
				'lang_address'			=> lang('address'),
				'lang_zip'			=> lang('zip'),
				'lang_town'			=> lang('town'),
				'lang_remark'			=> lang('remark'),

				'msgbox_data'			=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_id'			=> lang('training ID'),
				'lang_save'			=> lang('save'),
				'lang_cancel'			=> lang('cancel'),
				'value_id'			=> $place_id,
				'lang_done_status_text'		=> lang('Back to the list'),
				'lang_save_status_text'		=> lang('Save the training'),
				'lang_apply'			=> lang('apply'),
				'lang_apply_status_text'	=> lang('Apply the values'),
			);

			$appname					= lang('Place');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('hrm') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
		}

		function view()
		{
			$place_id	= phpgw::get_var('place_id', 'int');
			$values		= phpgw::get_var('values');

			$GLOBALS['phpgw']->xslttpl->add_file(array('place'));

			if ($place_id)
			{
				$values = $this->bo->read_single($place_id);
				$function_msg = lang('view place');
			}
			else
			{
				return;
			}


			$data = array
			(
				'value_title'			=> $values['title'],
				'value_entry_date'		=> $values['entry_date'],
				'value_name'			=> $values['name'],
				'value_address'			=> $values['address'],
				'value_zip'			=> $values['zip'],
				'value_town'			=> $values['town'],
				'value_remark'			=> $values['remark'],
				'lang_id'			=> lang('Place ID'),
				'lang_entry_date'		=> lang('Entry date'),
				'lang_name'			=> lang('name'),
				'lang_address'			=> lang('address'),
				'lang_zip'			=> lang('zip'),
				'lang_town'			=> lang('town'),
				'lang_remark'			=> lang('remark'),

				'form_action'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uiplace.index')),
				'lang_cancel'			=> lang('cancel'),
				'value_id'			=> $place_id,
			);

			$appname	= lang('Place');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('hrm') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view' => $data));
		}

		function delete()
		{
			$place_id	= phpgw::get_var('place_id', 'int');
			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
			(
				'menuaction' => 'hrm.uiplace.index'
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete($place_id);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uiplace.delete', 'place_id'=> $place_id)),
				'lang_confirm_msg'		=> lang('do you really want to delete this entry'),
				'lang_yes'			=> lang('yes'),
				'lang_yes_statustext'		=> lang('Delete the entry'),
				'lang_no_statustext'		=> lang('Back to the list'),
				'lang_no'			=> lang('no')
			);

			$appname	= lang('Place');
			$function_msg	= lang('delete');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('hrm') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		}

	}
