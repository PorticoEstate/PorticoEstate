<?php
	/**
	* phpGroupWare - HRM: a  human resource competence management system.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package hrm
	* @subpackage user
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package hrm
	 */

	class hrm_uiuser
	{
		var $grants;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $sub;
		var $currentapp = 'hrm';

		var $public_functions = array
		(
			'index'		=> true,
			'view'		=> true,
			'training'	=> true,
			'edit'		=> true,
			'delete'	=> true,
			'view_cv'	=> true
		);

		function __construct()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$this->nextmatchs			= CreateObject('phpgwapi.nextmatchs');
			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo					= CreateObject('hrm.bouser',false);
			$this->bocommon				= CreateObject('hrm.bocommon');
			$this->bocategory			= CreateObject('hrm.bocategory');
			$this->grants 				= $this->bo->grants;
			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->allrows				= $this->bo->allrows;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'hrm::user';
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
			$GLOBALS['phpgw']->xslttpl->add_file(array('user','nextmatchs',
										'search_field'));

			$account_info = $this->bo->read();

			$content = array();
			foreach ( $account_info as $entry )
			{
				if($this->bocommon->check_perms2($entry['account_id'], $this->grants, PHPGW_ACL_READ))
				{
					$link_training				= $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uiuser.training', 'user_id'=> $entry['account_id']));
					$text_training				= lang('training');
					$lang_training_user_text	= lang('Training profile');
				}
				else
				{
					$link_training				= '';
					$text_training				= '';
					$lang_training_user_text	= '';
				}

				$content[] = array
				(
					'first_name'				=> $entry['account_firstname'],
					'last_name'					=> $entry['account_lastname'],
//					'link_edit'					=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uiuser.training', 'user_id'=> $entry['account_id'])),
					'link_training'				=> $link_training,
					'link_view'					=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uiuser.view' ,' user_id'=> $entry['account_id'])),
					'lang_view_user_text'		=> lang('view the user'),
					'lang_training_user_text'	=> $lang_training_user_text,
					'lang_edit_user_text'		=> lang('edit the user'),
					'text_view'					=> lang('view'),
					'text_edit'					=> lang('edit'),
					'text_training'				=> $text_training
				);
			}

//_debug_array($content);

			$table_header[] = array
			(
				'sort_last_name'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'account_lastname',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'		=> 'hrm.uiuser.index',
														'query'		=> $this->query,
														'allrows' 	=> $this->allrows)
										)),
				'lang_last_name'	=> lang('Last name'),
				'sort_first_name'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'account_firstname',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'		=> 'hrm.uiuser.index',
														'query'		=> $this->query,
														'allrows'	=> $this->allrows)
										)),
				'lang_first_name'	=> lang('First name'),
				'lang_training'		=> lang('training'),
				'lang_edit'			=> lang('edit'),
				'lang_view'			=> lang('view'),
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
				'menuaction'	=> 'hrm.uiuser.index',
				'sort'			=> $this->sort,
				'order'			=> $this->order,
				'query'			=> $this->query
			);

			$data = array
			(
				'menu'								=> execMethod('hrm.menu.links'),
				'allow_allrows'						=> true,
				'allrows'							=> $this->allrows,
				'start_record'						=> $this->start,
				'record_limit'						=> $record_limit,
				'num_records'						=> count($account_info),
				'all_records'						=> $this->bo->total_records,
				'link_url'							=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'							=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_searchfield_categorytext'		=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_categorytext'	=> lang('Submit the search string'),
				'query'								=> $this->query,
				'lang_search'						=> lang('search'),
				'table_header'						=> $table_header,
				'values'							=> $content
			);

			$appname	= lang('user');
			$function_msg	= lang('list user');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('hrm') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list' => $data));
			$this->save_sessiondata();
		}

		function training()
		{
			$user_id	= phpgw::get_var('user_id', 'int');

			if (!$this->bocommon->check_perms2($user_id, $this->grants, PHPGW_ACL_READ))
			{
				phpgw::no_access();
				return;
			}

			$receipt = $GLOBALS['phpgw']->session->appsession('session_data','hrm_training_receipt');

			$GLOBALS['phpgw']->session->appsession('session_data','hrm_training_receipt','');

			$GLOBALS['phpgw']->xslttpl->add_file(array('user'));


			if ($user_id)
			{
				$user_values = $this->bo->get_user_data($user_id);
				$training = $this->bo->read_training($user_id);
			}
//_debug_array($training);

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			$content = array();
			foreach ( $training as $entry )
			{
				if($entry['start_date'])
				{
					$entry['start_date']	= $GLOBALS['phpgw']->common->show_date($entry['start_date'],$dateformat);
				}
				if($entry['end_date'])
				{
					$entry['end_date']	= $GLOBALS['phpgw']->common->show_date($entry['end_date'],$dateformat);
				}

				if($this->bocommon->check_perms2($user_id, $this->grants, PHPGW_ACL_EDIT))
				{
					$link_edit	= $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uiuser.edit', 'user_id'=> $user_id, 'training_id'=> $entry['training_id']));
					$text_edit	= lang('edit');
					$lang_edit_text	= lang('edit training item');
				}
				if($this->bocommon->check_perms2($user_id,$this->grants, PHPGW_ACL_DELETE))
				{
					$link_delete		= $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uiuser.delete', 'user_id'=> $user_id, 'training_id'=> $entry['training_id']));
					$text_delete		= lang('delete');
					$lang_delete_text	= lang('delete training item');
				}

				$content[] = array
				(
					'title'			=> $entry['title'],
					'place'			=> $entry['place'],
					'credits'		=> $entry['credits'],
					'start_date'		=> $entry['start_date'],
					'end_date'		=> $entry['end_date'],
					'category'		=> $entry['category'],
					'link_edit'		=> $link_edit,
					'link_view'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uiuser.view', 'user_id'=> $user_id, 'training_id'=> $entry['training_id'])),
					'link_delete'		=> $link_delete,
					'lang_view_text'	=> lang('view training item'),
					'lang_edit_text'	=> $lang_edit_text,
					'lang_delete_text'	=> $lang_delete_text,
					'text_view'		=> lang('view'),
					'text_edit'		=> $text_edit,
					'text_delete'		=> $text_delete
				);

				unset ($link_edit);
				unset ($text_edit);
				unset ($lang_edit_edit);
				unset ($link_delete);
				unset ($text_delete);
				unset ($lang_delete_text);
			}

			$table_header[] = array
			(
				'sort_place'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'phpgw_hrm_training_place.name',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'hrm.uiuser.training',
														'user_id'	=> $user_id,
														'query'		=> $this->query,
												//		'cat_id'	=> $this->cat_id,
														'allrows' 	=> $this->allrows)
										)),
				'sort_credits'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'phpgw_hrm_training.credits',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'hrm.uiuser.training',
														'user_id'	=> $user_id,
														'query'		=> $this->query,
												//		'cat_id'	=> $this->cat_id,
														'allrows' 	=> $this->allrows)
										)),
				'sort_start_date'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'start_date',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'		=> 'hrm.uiuser.training',
														'user_id'	=> $user_id,
														'query'		=> $this->query,
												//		'cat_id'	=> $this->cat_id,
														'allrows' 	=> $this->allrows)
										)),
				'lang_place'		=> lang('place'),
				'sort_title'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'phpgw_hrm_training.title',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'hrm.uiuser.training',
														'user_id'	=> $user_id,
														'query'		=> $this->query,
												//		'cat_id'	=> $this->cat_id,
														'allrows' 	=> $this->allrows)
										)),
				'lang_category'		=> lang('category'),
				'lang_credits'		=> lang('credits'),
				'lang_title'		=> lang('training'),
				'lang_start_date'	=> lang('start date'),
				'lang_end_date'		=> lang('end date'),
				'lang_view'			=> lang('view'),
				'lang_edit'			=> lang('edit'),
				'lang_delete'		=> lang('delete'),
			);

			$function_msg = lang('list training');
			$link_data = array
			(
				'menuaction'	=> 'hrm.uiuser.edit',
				'user_id'	=> $user_id
			);

			if($this->bocommon->check_perms2($user_id, $this->grants, PHPGW_ACL_ADD))
			{
				$add_action	= $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uiuser.edit', 'user_id'=> $user_id));
				$lang_add	= lang('add');
			}

			$table_add[] = array
			(
				'lang_add'					=> $lang_add,
				'lang_add_training_text'	=> lang('add a training item'),
				'add_action'				=> $add_action,
				'lang_done'					=> lang('done'),
				'lang_done_training_text'	=> lang('back to user list'),
				'done_action'				=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uiuser.index'))
			);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$link_cv_data = array
			(
				'menuaction'	=> 'hrm.uiuser.view_cv',
				'user_id'	=> $user_id,
				'order'		=> $this->order,
				'sort'		=> $this->sort
			);


			$data = array
			(
				'link_cv'					=> $GLOBALS['phpgw']->link('/index.php',$link_cv_data),
				'lang_cv_statustext'		=> lang('A printout version of the CV'),
				'text_cv'					=> 'CV',
				'table_header_training'		=> $table_header,
				'values_training'			=> $content,
				'table_add'					=> $table_add,
				'user_values'				=> $user_values,
				'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'				=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uiuser.index')),
				'lang_id'					=> lang('training ID'),
				'lang_descr'				=> lang('Descr'),
				'lang_save'					=> lang('save'),
				'lang_cancel'				=> lang('cancel'),
				'value_id'					=> $user_id,
				'lang_id_status_text'		=> lang('Enter the training ID'),
				'lang_descr_status_text'	=> lang('Enter a description the training'),
				'lang_done_status_text'		=> lang('Back to the list'),
				'lang_save_status_text'		=> lang('Save the training'),
				'type_id'					=> isset($training['type_id'])?$training['type_id']:'',
				'value_descr'				=> isset($training['descr'])?$training['descr']:'',
				'lang_apply'				=> lang('apply'),
				'lang_apply_status_text'	=> lang('Apply the values'),
			);

			$appname					= lang('Training');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('hrm') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('training' => $data));
		}

		function edit()
		{
			$training_id	= phpgw::get_var('training_id', 'int');
			$user_id	= phpgw::get_var('user_id', 'int');
			$values		= phpgw::get_var('values');

			if(!$training_id)
			{
				if(!$this->bocommon->check_perms2($user_id, $this->grants, PHPGW_ACL_ADD))
				{
					phpgw::no_access();
					return;
				}
			}
			else
			{
				if(!$this->bocommon->check_perms2($user_id, $this->grants, PHPGW_ACL_EDIT))
				{
					phpgw::no_access();
					return;
				}
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('user'));

			$receipt = array();
			if (is_array($values))
			{
				$values['place_id']= phpgw::get_var('place_id', 'int', 'POST');
				$values['user_id']= $user_id;

				if ((isset($values['save']) && $values['save']) || (isset($values['apply']) && $values['apply']))
				{
					if(!isset($values['cat_id']) || !$values['cat_id'])
					{
						$receipt['error'][]=array('msg'=>lang('Please select a category !'));
					}

					if(!$values['start_date'])
					{
						$receipt['error'][]=array('msg'=>lang('Please select a start date !'));
					}

					if(!$values['place_id'] && !$values['new_place_name'])
					{
							$receipt['error'][]=array('msg'=>lang('Please select a place or enter a new place !'));
					}
					if($values['place_id']=='new_place')
					{
						if(!$values['new_place_address'])
						{
							$receipt['error'][]=array('msg'=>lang('Please enter an address !'));
						}
						if(!$values['new_place_zip'])
						{
							$receipt['error'][]=array('msg'=>lang('Please enter a zip code !'));
						}
						if(!$values['new_place_town'])
						{
							$receipt['error'][]=array('msg'=>lang('Please enter a town !'));
						}
					}

					if($training_id)
					{
						$values['training_id']=$training_id;
						$action='edit';
					}

					if(!$receipt['error'])
					{
						$receipt = $this->bo->save($values,$action);
						$training_id = $receipt['training_id'];

						if ($values['save'])
						{
							$GLOBALS['phpgw']->session->appsession('session_data','hrm_training_receipt',$receipt);
							$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'=> 'hrm.uiuser.training', 'user_id'=> $user_id));
						}
					}
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'=> 'hrm.uiuser.training', 'user_id'=> $user_id));
				}
			}


			if ($training_id)
			{
				if(!isset($receipt['error']) || !$receipt['error'])
				{
					$values = $this->bo->read_single_training($training_id);
				}
				$function_msg = lang('edit training');
				$action='edit';
			}
			else
			{
				$function_msg = lang('add training');
				$action='add';
			}


			$link_data = array
			(
				'menuaction'	=> 'hrm.uiuser.edit',
				'training_id'	=> $training_id,
				'user_id' 	=> $user_id
			);
//_debug_array($link_data);

			$jscal = CreateObject('phpgwapi.jscalendar');	// before phpgw_header() !!!
			$jscal->add_listener('values_start_date');
			$jscal->add_listener('values_end_date');

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$GLOBALS['phpgw_info']['flags']['java_script'] .= "\n"
				. '<script type="text/javascript">' ."\n"
				. '//<[CDATA[' ."\n"
				. '/**' ."\n"
				. '* TODO Document function so others understand it' ."\n"
				. '*/' ."\n"
				. 'function modplace(form)' ."\n"
				. '{' ."\n"
				. 'if ( !document.getElementById )' ."\n"
				. '{' ."\n"
				. 'return false; // does not support DOM so bailout' ."\n"
				. '}' ."\n"
				. 'var val = form.place_id.value;' ."\n"
				. 'var div1 = document.getElementById("div1");' ."\n"
				. 'if(val == "new_place")' ."\n"
				. '{' ."\n"
				. 'div1.style.display = "block";' ."\n"
				. '}' ."\n"
				. 'else' ."\n"
				. '{' ."\n"
				. 'div1.style.display = "none";' ."\n"
				. '}' ."\n"
				. '}' ."\n"
				. '//]]' ."\n"
				. "</script>\n";

			$data = array
			(
				'value_descr'					=> isset($values['descr']) ? $values['descr'] : '',
				'value_title'					=> isset($values['title']) ? $values['title'] : '',
				'value_start_date'				=> isset($values['start_date']) ? $values['start_date'] : '',
				'value_end_date'				=> isset($values['end_date']) ? $values['end_date'] : '',
				'value_credits'					=> isset($values['credits']) ? $values['credits'] : 0,
				'value_entry_date'				=> isset($values['entry_date']) ? $values['entry_date'] : '',
				'value_reference'				=> isset($values['reference']) ? $values['reference'] : '',
				'value_new_place_name'			=> isset($values['new_place_name']) ? $values['new_place_name'] : '',
				'value_new_place_address'		=> isset($values['new_place_address']) ? $values['new_place_address'] : '',
				'value_new_place_zip'			=> isset($values['new_place_zip']) ? $values['new_place_zip'] : '',
				'value_new_place_town'			=> isset($values['new_place_town']) ? $values['new_place_town'] : '',
				'value_new_place_remark'		=> isset($values['new_place_remark']) ? $values['new_place_remark'] : '',

				'img_cal'						=> $GLOBALS['phpgw']->common->image('phpgwapi','cal'),
				'lang_date_selector'			=> lang('date selector'),
				'lang_start_date'				=> lang('start date'),
				'lang_end_date'					=> lang('end date'),
				'lang_start_date_title'			=> lang('select start date'),
				'lang_end_date_title'			=> lang('select end date'),
				'lang_start_date_status_text'	=> lang('Select the start date for your training'),
				'lang_end_date_status_text'		=> lang('Select the end date for your training'),
				'lang_select_date'				=> lang('select date'),

				'lang_reference'				=> lang('reference'),

				'lang_entry_date'				=> lang('Entry date'),
				'lang_title'					=> lang('training'),
				'lang_title_status_text'		=> lang('Title of the training item'),
				'lang_skill'					=> lang('Skill'),
				'lang_skill_status_text'		=> lang('Select your skill'),
				'skill_list'					=> $this->bocategory->select_category_list('skill_level',$values['skill']),
				'lang_no_skill'					=> lang('select a skill'),

				'place_list'					=> $this->bo->select_place_list($values['place_id']),
				'lang_place'					=> lang('place'),
				'lang_new_place'				=> lang('new place'),
				'lang_place_status_text'		=> lang('Select a place'),
				'lang_new_place_status_text'	=> lang('Enter a new place'),
				'lang_no_place'					=> lang('select a place'),

				'lang_new_place_name'			=> lang('name'),
				'lang_new_place_address'		=> lang('address'),
				'lang_new_place_zip'			=> lang('zip'),
				'lang_new_place_town'			=> lang('town'),
				'lang_new_place_remark'			=> lang('remark'),

				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_id'						=> lang('training ID'),
				'lang_descr'					=> lang('Descr'),
				'lang_save'						=> lang('save'),
				'lang_cancel'					=> lang('cancel'),
				'value_id'						=> $training_id,
				'lang_id_status_text'			=> lang('Enter the training ID'),
				'lang_descr_status_text'		=> lang('Enter a description the training'),
				'lang_done_status_text'			=> lang('Back to the list'),
				'lang_save_status_text'			=> lang('Save the training'),
				'lang_apply'					=> lang('apply'),
				'lang_apply_status_text'		=> lang('Apply the values'),

				'lang_category'					=> lang('category'),
//				'cat_list'						=> $this->bo->select_category_list('select',$values['cat_id']),
				'cat_list'						=> $this->bocategory->select_category_list('training',$values['cat_id']),
				'lang_no_cat'					=> lang('no category'),
				'lang_cat_status_text'			=> lang('Select the category the building belongs to. To do not use a category select NO CATEGORY'),
				'select_name'					=> 'values[cat_id]',
			);

			$appname					= lang('Training');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('hrm') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
		}

		function view()
		{
			$training_id		= phpgw::get_var('training_id', 'int');
			$user_id	= phpgw::get_var('user_id', 'int');

			if(!$this->bocommon->check_perms2($user_id, $this->grants, PHPGW_ACL_READ))
			{
				phpgw::no_access();
				return;
			}
			$GLOBALS['phpgw']->xslttpl->add_file(array('user'));

			if ($training_id)
			{
				$values = $this->bo->read_single_training($training_id);
				$function_msg = lang('view training');
			}
			else
			{
				return;
			}


			$data = array
			(
				'value_descr'				=> $values['descr'],
				'value_title'				=> $values['title'],
				'value_start_date'			=> $values['start_date'],
				'value_end_date'			=> $values['end_date'],
				'value_credits'				=> isset($values['credits']) ? $values['credits'] : 0,
				'value_entry_date'			=> $values['entry_date'],
				'value_reference'			=> $values['reference'],

				'lang_start_date'			=> lang('start date'),
				'lang_end_date'				=> lang('end date'),
				'lang_start_date_title'			=> lang('select start date'),
				'lang_end_date_title'			=> lang('select end date'),
				'lang_start_date_status_text'		=> lang('Select the start date for your training'),
				'lang_end_date_status_text'		=> lang('Select the end date for your training'),
				'calendar_setup_start'			=> "Calendar.setup({inputField  : 'values[start_date]',button : 'values[start_date]-trigger'});",
				'calendar_setup_end'			=> "Calendar.setup({inputField  : 'values[end_date]',button : 'values[end_date]-trigger'});",
				'lang_reference'			=> lang('reference'),

				'lang_entry_date'			=> lang('Entry date'),
				'lang_title'				=> lang('training'),
				'lang_title_status_text'		=> lang('Title of the training item'),
				'lang_skill'				=> lang('Skill'),
				'lang_skill_status_text'		=> lang('Select your skill'),
				'skill_list'				=> $this->bocategory->select_category_list('skill_level',$values['skill']),
				'lang_no_skill'				=> lang('select a skill'),

				'place_list'				=> $this->bo->select_place_list($values['place_id']),
				'lang_place'				=> lang('place'),
				'lang_new_place'			=> lang('new place'),
				'lang_place_status_text'		=> lang('Select a place'),
				'lang_new_place_status_text'		=> lang('Enter a new place'),
				'lang_no_place'				=> lang('select a place'),

				'form_action'				=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uiuser.training', 'user_id'=> $user_id)),
				'lang_id'				=> lang('training ID'),
				'lang_descr'				=> lang('Descr'),
				'lang_save'				=> lang('save'),
				'lang_cancel'				=> lang('cancel'),
				'value_id'				=> $training_id,
				'lang_id_status_text'			=> lang('Enter the training ID'),
				'lang_descr_status_text'		=> lang('Enter a description the training'),
				'lang_done_status_text'			=> lang('Back to the list'),
				'lang_save_status_text'			=> lang('Save the training'),
				'type_id'				=> $training['type_id'],
				'lang_apply'				=> lang('apply'),
				'lang_apply_status_text'		=> lang('Apply the values'),

				'lang_category'				=> lang('category'),
//				'cat_list'				=> $this->bo->select_category_list('select',$values['cat_id']),
				'cat_list'				=> $this->bocategory->select_category_list('training',$values['cat_id']),
				'lang_no_cat'				=> lang('no category'),
				'lang_cat_status_text'			=> lang('Select the category the building belongs to. To do not use a category select NO CATEGORY'),
				'select_name'				=> 'values[cat_id]',
			);

			$appname	= lang('Training');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('hrm') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view' => $data));
		}

		function delete()
		{
			$training_id		= phpgw::get_var('training_id', 'int');
			$user_id	= phpgw::get_var('user_id', 'int');

			if(!$this->bocommon->check_perms2($user_id, $this->grants, PHPGW_ACL_DELETE))
			{
				phpgw::no_access();
				return;
			}
			$confirm = phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
			(
				'menuaction' => 'hrm.uiuser.training',
				'user_id' => $user_id
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete_training($user_id,$training_id);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uiuser.delete', 'user_id'=> $user_id, 'training_id'=> $training_id)),
				'lang_confirm_msg'		=> lang('do you really want to delete this entry'),
				'lang_yes'			=> lang('yes'),
				'lang_yes_categorytext'		=> lang('Delete the entry'),
				'lang_no_categorytext'		=> lang('Back to the list'),
				'lang_no'			=> lang('no')
			);

			$appname	= lang('Training');
			$function_msg	= lang('delete');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('hrm') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		}

		function view_cv()
		{
			$user_id	= phpgw::get_var('user_id', 'int');

			if(!$this->bocommon->check_perms2($user_id, $this->grants, PHPGW_ACL_READ))
			{
				phpgw::no_access();
				return;
			}

			$GLOBALS['phpgw_info']['flags']['noheader'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;

			if($user_id)
			{
				$user_values = $this->bo->get_user_data($user_id);
				$training = $this->bo->read_training($user_id);
			}
			else
			{
				echo 'Nothing';
				return;
			}

//	_debug_array($user_values);

//	_debug_array($training);
			$pdf	= CreateObject('phpgwapi.pdf');

			if (isSet($user_values) AND is_array($user_values))
			{
				foreach($user_values as $entry)
				{
					if(empty($entry['value']))
					{
						continue;
					}

					$content_heading[] = array
					(
						'name'	=> $entry['name'],
						'value'	=> $entry['value']
					);
				}
			}

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];



//	_debug_array($content);

			$date = $GLOBALS['phpgw']->common->show_date('',$dateformat);

			// don't want any warnings turning up in the pdf code if the server is set to 'anal' mode.
			//error_reporting(7);
			//error_reporting(E_ALL);
			set_time_limit(1800);
			$pdf -> ezSetMargins(90,70,50,50);
			$pdf->selectFont('Helvetica');

			// put a line top and bottom on all the pages
			$all = $pdf->openObject();
			$pdf->saveState();
			$pdf->setStrokeColor(0,0,0,1);
//			$pdf->line(20,40,578,40);
			$pdf->line(20,760,578,760);
			$pdf->line(200,40,200,822);

			$pdf->addText(220,770,16,'CV');
			$pdf->addText(300,34,6,$date);

/*			$pdf->setColor(1,0,0);
			$pdf->addText(500,750,40,'E',-10);
			$pdf->setColor(1,0,0);
*/

			$pdf->restoreState();
			$pdf->closeObject();
			// note that object can be told to appear on just odd or even pages by changing 'all' to 'odd'
			// or 'even'.
			$pdf->addObject($all,'all');
			$pdf->ezStartPageNumbers(500,28,10,'right','{PAGENUM} ' . lang('of') . ' {TOTALPAGENUM}',1);

			$pdf->ezTable($content_heading,'','',
							array('xPos'=>220,'xOrientation'=>'right','width'=>300,0,'shaded'=>0,'fontSize' => 10,'gridlines'=> 0,'titleFontSize' => 12,'outerLineThickness'=>0,'showHeadings'=>0
							,'cols'=>array('text'=>array('justification'=>'left','width'=>100),
									'value'=>array('justification'=>'left','width'=>200))
							)
						);

			$table_header = array(
				'start_date'=>array('justification'=>'left','width'=>70),
				'sep'=>array('justification'=>'center','width'=>15),
				'end_date'=>array('justification'=>'left','width'=>70),
				'spacer'=>array('width'=>15),
				'what'=>array('justification'=>'left','width'=>300)
				);

			$category_old	= '';
			//while (is_array($training) && list(,$entry) = each($training))
			if (is_array($training))
			{
				foreach($training as $key => $entry)
				{

					if($entry['category']!= $category_old)
					{
						$content[] = array
						(
							'start_date'		=> '',
							'sep'			=> '',
							'end_date'		=> '',
							'spacer'		=> '',
							'what'			=> $entry['category']
						);
						$pdf->ezSetDy(-20);
						$pdf->ezTable($content,'','',
								array('xPos'=>50,'xOrientation'=>'right','width'=>500,0,'shaded'=>0,'fontSize' => 12,'gridlines'=> 0,'titleFontSize' => 12,'outerLineThickness'=>2,'showHeadings'=>0
								,'cols'=>$table_header
								)
							);
						unset($content);
					}

					$category_old	= $entry['category'];

					if($entry['start_date'])
					{
						$entry['start_date']	= $GLOBALS['phpgw']->common->show_date($entry['start_date'],$dateformat);
					}
					if($entry['end_date'])
					{
						$entry['end_date']	= $GLOBALS['phpgw']->common->show_date($entry['end_date'],$dateformat);
					}

					$content[] = array
					(
						'start_date'		=> $entry['start_date'],
						'sep'			=> '-',
						'end_date'		=> $entry['end_date'],
						'spacer'		=> '',
						'what'			=> $entry['title'] . ', ' . $entry['place']
					);

					$pdf->ezTable($content,'','',
								array('xPos'=>50,'xOrientation'=>'right','width'=>500,0,'shaded'=>0,'fontSize' => 10,'gridlines'=> 0,'titleFontSize' => 12,'outerLineThickness'=>2,'showHeadings'=>0
								,'cols'=>$table_header
								)
							);

					unset($content);
				}
			}
			$document = $pdf->ezOutput();
			$pdf->print_pdf($document,'cv_'.$GLOBALS['phpgw']->accounts->id2name($user_id));
		}
	}
