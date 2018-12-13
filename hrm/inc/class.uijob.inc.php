<?php
	/**
	* phpGroupWare - HRM: a  human resource competence management system.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package hrm
	* @subpackage job
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package hrm
	 */

	class hrm_uijob
	{
		var $grants;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $sub;
		var $currentapp;

		/**
		* ???
		*/
		private $cat_id;

		/**
		* ???
		*/
		private $filter;

		var $public_functions = array
		(
			'index'  			=> true,
			'view_qualification'		=> true,
			'view_job'   			=> true,
			'edit_job'			=> true,
			'delete_job'			=> true,
			'reset_job_type_hierarchy'	=> true,
			'edit_qualification'		=> true,
			'delete_qualification'		=> true,
			'qualification'			=> true,
			'task'				=> true,
			'edit_task'			=> true,
			'view_task'			=> true,
			'delete_task'			=> true,
			'lookup_qualification'		=> true,
			'edit_qualification_type'	=> true,
			'hierarchy'			=> true,
			'print_pdf'			=> true
		);

		public function __construct()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
		//	$this->currentapp			= 'hrm';
			$this->nextmatchs			= CreateObject('phpgwapi.nextmatchs');
			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo				= CreateObject('hrm.bojob',true);
			$this->bocommon				= CreateObject('hrm.bocommon');
			$this->bocategory			= CreateObject('hrm.bocategory');
			$this->acl				= CreateObject('phpgwapi.acl');

			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->allrows				= $this->bo->allrows;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'hrm::job';
		}

		function save_sessiondata()
		{
			$data = array
			(
				'start'		=> $this->start,
				'query'		=> $this->query,
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'cat_id'	=> $this->cat_id,
				'filter'	=> $this->filter
			);
			$this->bo->save_sessiondata($data);
		}

		function index()
		{

			if(!$this->acl->check('.job', PHPGW_ACL_READ, 'hrm'))
			{
				$this->bocommon->no_access();
				return;
			}

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::job_type';

			$GLOBALS['phpgw']->js->validate_file('base', 'check', 'hrm');

			$GLOBALS['phpgw']->xslttpl->add_file(array('job','nextmatchs','menu',
										'search_field'));


			$receipt = $GLOBALS['phpgw']->session->appsession('session_data','hrm_job_receipt');
			$GLOBALS['phpgw']->session->appsession('session_data','hrm_job_receipt','');

			$job_info = $this->bo->read();

			$content = array();
			foreach ( $job_info as $entry )
			{

				if ($entry['level'] > 0)
				{
					$space = '--';
					$spaceset = str_repeat($space,$entry['level']);
					$entry['name'] = $spaceset . $entry['name'];
				}

				$content[] = array
				(
					'id'					=> $entry['id'],
					'name'					=> $entry['name'],
					'descr'					=> $entry['descr'],
					'task_count'				=> $entry['task_count'],
					'quali_count'				=> $entry['quali_count'],
					'link_add_sub'				=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uijob.edit_job','parent_id'=> $entry['id'])),
					'link_edit'				=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uijob.edit_job', 'id'=> $entry['id'])),
					'link_delete'				=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uijob.delete_job', 'job_id'=> $entry['id'])),
					'link_view'				=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uijob.view_job', 'id'=> $entry['id'])),
					'link_qualification'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uijob.qualification', 'job_id'=> $entry['id'])),
					'link_task'				=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uijob.task', 'job_id'=> $entry['id'])),
					'lang_qualification_job_text'		=> lang('qualifications for this job'),
					'lang_task_job_text'			=> lang('tasks for this job'),
					'lang_view_job_text'			=> lang('view the job'),
					'lang_edit_job_text'			=> lang('edit the job'),
					'lang_delete_job_text'			=> lang('delete the job'),
					'lang_add_sub_text'			=> lang('Add a new sub-job'),
					'text_qualification'			=> lang('qualification'),
					'text_task'				=> lang('task'),
					'text_view'				=> lang('view'),
					'text_edit'				=> lang('edit'),
					'text_delete'				=> lang('delete'),
					'text_add_sub'				=> lang('Add sub'),
				);
			}

//_debug_array($content);

			$table_header[] = array
			(
				'sort_name'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'name',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'hrm.uijob.index',
														'query'		=> $this->query,
														'cat_id'	=> $this->cat_id,
														'allrows'	=> $this->allrows)
										)),
				'lang_add_sub'		=> lang('Add sub'),
				'lang_name'		=> lang('name'),
				'lang_descr'		=> lang('descr'),
				'lang_edit'		=> lang('edit'),
				'lang_delete'		=> lang('delete'),
				'lang_view'		=> lang('view'),
				'lang_qualification'	=> lang('qualification'),
				'lang_task'		=> lang('task'),
				'lang_print'		=> lang('print')
			);

			$table_add[] = array
			(
				'lang_add'		=> lang('add'),
				'lang_add_statustext'	=> lang('add a category'),
				'add_action'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uijob.edit_job')),
				'lang_reset'		=> lang('reset hierarchy'),
				'lang_reset_statustext'	=> lang('Reset the hierarchy'),
				'reset_action'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uijob.reset_job_type_hierarchy'))
			);

			$table_reset_job_type[] = array
			(
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
				'menuaction'	=> 'hrm.uijob.index',
				'sort'			=> $this->sort,
				'order'			=> $this->order,
				'cat_id'		=> $this->cat_id,
				'filter'		=> $this->filter,
				'query'			=> $this->query
			);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'menu'							=> execMethod('hrm.menu.links'),
				'allow_allrows'					=> true,
				'allrows'						=> $this->allrows,
				'start_record'					=> $this->start,
				'record_limit'					=> $record_limit,
				'num_records'					=> count($job_info),
				'all_records'					=> $this->bo->total_records,
				'link_url'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'						=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_searchfield_statustext'	=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'	=> lang('Submit the search string'),
				'query'							=> $this->query,
				'lang_search'					=> lang('search'),
				'table_header'					=> $table_header,
				'values'						=> $content,
				'table_add_job'					=> $table_add,
				'lang_select_all'				=> lang('Select All'),
				'img_check'						=> $GLOBALS['phpgw']->common->get_image_path('hrm').'/check.png',
				'lang_print'					=> lang('print'),
				'print_action'					=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uijob.print_pdf')),
			);

			$appname	= lang('job');
			$function_msg	= lang('list job');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('hrm') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list' => $data));
			$this->save_sessiondata();
		}

		function print_pdf()
                                                                  {
			if(!$this->acl->check('.job', PHPGW_ACL_READ, 'hrm'))
			{
				$this->bocommon->no_access();
				return;
			}

			$GLOBALS['phpgw_info']['flags']['noheader'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;

			$pdf	= CreateObject('phpgwapi.pdf');
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$date = $GLOBALS['phpgw']->common->show_date('',$dateformat);

			$values	= phpgw::get_var('values');

			// don't want any warnings turning up in the pdf code if the server is set to 'anal' mode.
			//error_reporting(7);
			//error_reporting(E_ALL);
			set_time_limit(1800);
			$pdf -> ezSetMargins(50,70,100,50);
			$pdf->selectFont('Helvetica');

			// put a line top and bottom on all the pages
			$all = $pdf->openObject();
			$pdf->saveState();
			$pdf->setStrokeColor(0,0,0,1);

//			$pdf->line(20,760,578,760);
//			$pdf->line(200,40,200,822);

			$pdf->addText(220,770,16,'Stillingsbeskrivelse');
			$pdf->addText(300,34,6,$date);

			$pdf->restoreState();
			$pdf->closeObject();
			// note that object can be told to appear on just odd or even pages by changing 'all' to 'odd'
			// or 'even'.
			$pdf->addObject($all,'all');
			$pdf->ezStartPageNumbers(500,28,10,'right','{PAGENUM} ' . lang('of') . ' {TOTALPAGENUM}',1);

			$pdf->ezSetDy(-50);

			$i = 0;

			//while (is_array($values) && list(,$job_id) = each($values['select']))
			if (is_array($values['select']))
			{
				foreach($values['select'] as $key => $job_id)
				{
					if($i > 0)
					{
						$pdf->ezNewPage();
					}
					$job_info = $this->bo->read_single_job($job_id);
					$qualification = $this->bo->read_qualification($job_id);
					$task = $this->bo->read_task($job_id);
					$i++;

					$pdf->ezSetY(720);
					$pdf->ezText($job_info['name'],14);
					$pdf->ezSetDy(-10);
					$pdf->ezText(lang('tasks') . ':',12);
					$pdf->ezSetDy(-5);
					$j = 1;
					//while (is_array($task) && list(,$entry) = each($task))
					if (is_array($task))
					{
						foreach($task as $key => $entry)
						{
							if($entry['descr'])
							{
								$pdf->ezText($j . ' ' . $entry['name'] . ': ',12,array('left' => 10));
								$pdf->ezText($entry['descr'],12,array('left' => 30));
							}
							else
							{
								$pdf->ezText($j . ' ' . $entry['name'],12,array('left' => 10));
							}
							$j++;
						}
					}
					$pdf->ezSetDy(-10);

					$pdf->ezText(lang('qualification') . ':',12);
					$pdf->ezSetDy(-5);
					$j = 1;
					//while (is_array($qualification) && list(,$entry) = each($qualification))
					if (is_array($qualification))
					{
						foreach($qualification as $key => $entry)
						{
							if($entry['descr'])
							{
								$pdf->ezText($j . ' ' . $entry['name'] . ': ',12,array('left' => 10));
								$pdf->ezText($entry['descr'],12,array('left' => 30));
							}
							else
							{
								$pdf->ezText($j . ' ' . $entry['name'],12,array('left' => 10));
							}
							$j++;
						}
					}
				}
			}

			$document = $pdf->ezOutput();
			$pdf->print_pdf($document,'job');
		}

		function qualification()
		{
			if(!$this->acl->check('.job', PHPGW_ACL_READ, 'hrm'))
			{
				$this->bocommon->no_access();
				return;
			}

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::job_type';

			$job_id	= phpgw::get_var('job_id', 'int');
			$id	= phpgw::get_var('id', 'int');
			$resort	= phpgw::get_var('resort');

			if($resort)
			{
				$this->bo->resort_value(array('resort'=>$resort,'job_id' => $job_id,'id' => $id,'type'=>'qualification'));
			}

			$receipt = $GLOBALS['phpgw']->session->appsession('session_data','hrm_quali_receipt');
			$GLOBALS['phpgw']->session->appsession('session_data','hrm_quali_receipt','');

			$GLOBALS['phpgw']->xslttpl->add_file(array('job'));

			if ($job_id)
			{
				$job_info = $this->bo->read_single_job($job_id);
				$qualification = $this->bo->read_qualification($job_id);
			}

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			//while (is_array($qualification) && list(,$entry) = each($qualification))
			if (is_array($qualification))
			{
				foreach($qualification as $key => $entry)
				{

					$content[] = array
					(
						'sorting'		=> $entry['value_sort'],
						'link_up'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uijob.qualification', 'resort'=> 'up', 'id'=> $entry['quali_id'], 'job_id'=> $job_id, 'allrows'=> $this->allrows)),
						'link_down'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uijob.qualification', 'resort'=> 'down', 'id'=> $entry['quali_id'], 'job_id'=> $job_id, 'allrows'=> $this->allrows)),
						'text_up'		=> lang('up'),
						'text_down'		=> lang('down'),

						'id'			=> $entry['quali_id'],
						'name'			=> $entry['name'],
						'descr'			=> $entry['descr'],
						'remark'		=> $entry['remark'],
						'category'		=> $entry['category'],
						'link_edit'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uijob.edit_qualification', 'job_id'=> $job_id, 'quali_id'=> $entry['quali_id'])),
						'link_view'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uijob.view_qualification', 'job_id'=> $job_id, 'quali_id'=> $entry['quali_id'])),
						'link_delete'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uijob.delete_qualification', 'job_id'=> $job_id, 'quali_id'=> $entry['quali_id'])),
						'lang_view_text'	=> lang('view qualification item'),
						'lang_edit_text'	=> lang('edit qualification item'),
						'lang_delete_text'	=> lang('delete qualification item'),
						'text_view'		=> lang('view'),
						'text_edit'		=> lang('edit'),
						'text_delete'		=> lang('delete')
					);
				}
			}


			$table_header[] = array
			(

				'sort_name'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'name',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'hrm.uijob.qualification',
														'job_id'	=> $job_id,
														'query'		=> $this->query,
														'cat_id'	=> $this->cat_id,
														'allrows'	=> $this->allrows)
										)),
				'sort_sorting'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'value_sort',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'hrm.uijob.qualification',
														'job_id'	=> $job_id,
														'query'		=> $this->query,
														'cat_id'	=> $this->cat_id,
														'allrows' 	=> $this->allrows)
										)),


				'lang_name'	=> lang('name'),
				'lang_descr'	=> lang('descr'),
				'lang_remark'	=> lang('remark'),
				'lang_category'	=> lang('category'),
				'lang_view'	=> lang('view'),
				'lang_edit'	=> lang('edit'),
				'lang_delete'	=> lang('delete'),
				'lang_sorting'	=> lang('sorting')
			);



			$function_msg = lang('list qualification');


			$link_data = array
			(
				'menuaction'	=> 'hrm.uijob.edit_qualification',
				'job_id'	=> $job_id
			);

			$table_add[] = array
			(
				'lang_add'			=> lang('add'),
				'lang_add_qualification_text'	=> lang('add a qualification item'),
				'add_action'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uijob.edit_qualification', 'job_id'=> $job_id)),
				'lang_done'			=> lang('done'),
				'lang_done_qualification_text'	=> lang('back to user list'),
				'done_action'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uijob.index'))
			);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'lang_job_name'				=> lang('Job name'),
				'value_job_name'			=> $job_info['name'],
				'table_header_qualification'		=> $table_header,
				'values_qualification'			=> $content,
				'table_add'				=> $table_add,
				'user_values'				=> $user_values,
				'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'				=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uijob.index')),
				'lang_id'				=> lang('qualification ID'),
				'lang_descr'				=> lang('Descr'),
				'lang_save'				=> lang('save'),
				'lang_cancel'				=> lang('cancel'),
				'value_id'				=> $job_id,
				'lang_id_status_text'			=> lang('Enter the qualification ID'),
				'lang_descr_status_text'		=> lang('Enter a description the qualification'),
				'lang_done_status_text'			=> lang('Back to the list'),
				'lang_save_status_text'			=> lang('Save the qualification'),
				'type_id'				=> $qualification['type_id'],
				'value_descr'				=> $qualification['descr'],
				'lang_apply'				=> lang('apply'),
				'lang_apply_status_text'		=> lang('Apply the values'),
			);

			$appname = lang('qualification');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('hrm') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('qualification' => $data));
		}

		function task()
		{
			if(!$this->acl->check('.job', PHPGW_ACL_READ, 'hrm'))
			{
				$this->bocommon->no_access();
				return;
			}

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::job_type';

			$job_id	= phpgw::get_var('job_id', 'int');
			$id	= phpgw::get_var('id', 'int');
			$resort	= phpgw::get_var('resort');

			if($resort)
			{
				$this->bo->resort_value(array('resort'=>$resort,'job_id' => $job_id,'id' => $id,'type'=>'task'));
			}

			$receipt = $GLOBALS['phpgw']->session->appsession('session_data','hrm_task_receipt');
			$GLOBALS['phpgw']->session->appsession('session_data','hrm_task_receipt','');

			$GLOBALS['phpgw']->xslttpl->add_file(array('job'));

			if ($job_id)
			{
				$job_info = $this->bo->read_single_job($job_id);
				$task = $this->bo->read_task($job_id);
			}

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			//while (is_array($task) && list(,$entry) = each($task))
			if (is_array($task))
			{
				foreach($task as $key => $entry)
				{

					$content[] = array
					(
						'sorting'		=> $entry['value_sort'],
						'link_up'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uijob.task', 'resort'=> 'up', 'id'=> $entry['id'], 'job_id'=> $job_id, 'allrows'=> $this->allrows)),
						'link_down'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uijob.task', 'resort'=> 'down', 'id'=> $entry['id'], 'job_id'=> $job_id, 'allrows'=> $this->allrows)),
						'text_up'		=> lang('up'),
						'text_down'		=> lang('down'),

						'id'			=> $entry['id'],
						'name'			=> $entry['name'],
						'descr'			=> $entry['descr'],
						'link_edit'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uijob.edit_task', 'job_id'=> $job_id, 'id'=> $entry['id'])),
						'link_view'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uijob.view_task', 'job_id'=> $job_id, 'id'=> $entry['id'])),
						'link_delete'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uijob.delete_task', 'job_id'=> $job_id, 'id'=> $entry['id'])),
						'lang_view_text'	=> lang('view task item'),
						'lang_edit_text'	=> lang('edit task item'),
						'lang_delete_text'	=> lang('delete task item'),
						'text_view'		=> lang('view'),
						'text_edit'		=> lang('edit'),
						'text_delete'		=> lang('delete')
					);
				}
			}

			$table_header[] = array
			(

				'sort_name'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'phpgw_hrm_task.name',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'hrm.uijob.task',
														'job_id'	=> $job_id,
														'query'		=> $this->query,
														'cat_id'	=> $this->cat_id,
														'allrows'	=> $this->allrows)
										)),
				'sort_sorting'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'value_sort',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'hrm.uijob.task',
														'job_id'	=> $job_id,
														'query'		=> $this->query,
														'cat_id'	=> $this->cat_id,
														'allrows'	=> $this->allrows)
										)),

				'lang_name'	=> lang('name'),
				'lang_descr'	=> lang('descr'),
				'lang_view'	=> lang('view'),
				'lang_edit'	=> lang('edit'),
				'lang_delete'	=> lang('delete'),
				'lang_sorting'	=> lang('sorting')
			);

			$function_msg = lang('list task');


			$link_data = array
			(
				'menuaction'	=> 'hrm.uijob.edit_task',
				'job_id'	=> $job_id
			);

			$table_add[] = array
			(
				'lang_add'			=> lang('add'),
				'lang_add_task_text'		=> lang('add a task item'),
				'add_action'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uijob.edit_task', 'job_id'=> $job_id)),
				'lang_done'			=> lang('done'),
				'lang_done_task_text'		=> lang('back to user list'),
				'done_action'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uijob.index'))
			);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'lang_job_name'				=> lang('Job name'),
				'value_job_name'			=> $job_info['name'],
				'table_header_task'			=> $table_header,
				'values_task'				=> $content,
				'table_add'				=> $table_add,
				'user_values'				=> $user_values,
				'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'				=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uijob.index')),
				'lang_id'				=> lang('task ID'),
				'lang_descr'				=> lang('Descr'),
				'lang_save'				=> lang('save'),
				'lang_cancel'				=> lang('cancel'),
				'value_id'				=> $job_id,
				'lang_id_status_text'			=> lang('Enter the task ID'),
				'lang_descr_status_text'		=> lang('Enter a description the task'),
				'lang_done_status_text'			=> lang('Back to the list'),
				'lang_save_status_text'			=> lang('Save the task'),
				'type_id'				=> $task['type_id'],
				'value_descr'				=> $task['descr'],
				'lang_apply'				=> lang('apply'),
				'lang_apply_status_text'		=> lang('Apply the values'),
			);

			$appname = lang('task');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('hrm') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('task' => $data));
		}

		function edit_task()
		{
			$id		= phpgw::get_var('id', 'int');
			$job_id	= phpgw::get_var('job_id', 'int');
			$parent_id	= phpgw::get_var('parent_id', 'int');
			$values		= phpgw::get_var('values');

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::job_type';

			if(!$id)
			{
				if(!$this->acl->check('.job', PHPGW_ACL_ADD, 'hrm'))
				{
					$this->bocommon->no_access();
					return;
				}
			}
			else
			{
				if(!$this->acl->check('.job', PHPGW_ACL_EDIT, 'hrm'))
				{
					$this->bocommon->no_access();
					return;
				}
			}


			$GLOBALS['phpgw']->xslttpl->add_file(array('job'));

			if (is_array($values))
			{
				$values['job_id']= $job_id;

				if ($values['save'] || $values['apply'])
				{
					if(!$values['name'])
					{
						$receipt['error'][]=array('msg'=>lang('Please enter a name !'));
					}

					if($id)
					{
						$values['id']=$id;
						$action='edit';
					}

					if(!$receipt['error'])
					{
						$receipt = $this->bo->save_task($values,$action);
						$id = $receipt['id'];

						if ($values['save'])
						{
							$GLOBALS['phpgw']->session->appsession('session_data','hrm_task_receipt',$receipt);
							$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'=> 'hrm.uijob.task', 'job_id'=> $job_id));
						}
					}
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'=> 'hrm.uijob.task', 'job_id'=> $job_id));
				}
			}

			if ($id)
			{
				$values = $this->bo->read_single_task($id);
				$function_msg = lang('edit task');
				$action='edit';
			}
			else
			{
				$function_msg = lang('add task');
				$action='add';
			}


			if($parent_id)
			{
				$values['parent_id'] = $parent_id;
			}

			$link_data = array
			(
				'menuaction'	=> 'hrm.uijob.edit_task',
				'id'		=> $id,
				'job_id'	=> $job_id
			);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_id'				=> lang('task ID'),
				'lang_descr'				=> lang('Descr'),
				'lang_save'				=> lang('save'),
				'value_id'				=> $id,

				'parent_list'				=> $this->bo->select_task_list($values['parent_id'],$id,$job_id),
				'lang_parent'				=> lang('parent'),
				'lang_parent_status_text'		=> lang('Select this tasks parent'),
				'lang_no_parent'			=> lang('select a parent'),

				'lang_name'				=> lang('name'),
				'lang_name_status_text'			=> lang('name of the task-type'),

				'lang_id_statustext'			=> lang('Enter the category ID'),
				'lang_descr_statustext'			=> lang('Enter a description the task'),
				'lang_done_statustext'			=> lang('Back to the list'),
				'lang_save_statustext'			=> lang('Save the task'),
				'type_id'				=> $values['type_id'],
				'value_descr'				=> $values['descr'],
				'value_name'				=> $values['name'],

				'lang_cancel'				=> lang('cancel'),
				'lang_apply'				=> lang('apply'),
				'lang_apply_status_text'		=> lang('Apply the values'),
			);

			$appname	= lang('task');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('hrm') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit_task' => $data));
		}

		function view_task()
		{
			$id		= phpgw::get_var('id', 'int');
			$job_id		= phpgw::get_var('job_id', 'int');
			$parent_id	= phpgw::get_var('parent_id', 'int');

			if(!$this->acl->check('.job', PHPGW_ACL_READ, 'hrm'))
			{
				$this->bocommon->no_access();
				return;
			}

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::job_type';

			$GLOBALS['phpgw']->xslttpl->add_file(array('job'));

			if ($id)
			{
				$values = $this->bo->read_single_task($id);
				$function_msg = lang('view task');
			}

			if($parent_id)
			{
				$values['parent_id'] = $parent_id;
			}

			$link_data = array
			(
				'menuaction'	=> 'hrm.uijob.task',
				'id'		=> $id,
				'job_id'	=> $job_id
			);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'done_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_id'				=> lang('task ID'),
				'lang_descr'				=> lang('Descr'),
				'value_id'				=> $id,

				'parent_list'				=> $this->bo->select_task_list($values['parent_id'],$id,$job_id),
				'lang_parent'				=> lang('parent'),
				'lang_parent_status_text'		=> lang('Select this tasks parent'),

				'lang_name'				=> lang('name'),
				'lang_name_status_text'			=> lang('name of the task-type'),

				'lang_done_statustext'			=> lang('Return to the list'),
				'type_id'				=> $values['type_id'],
				'value_descr'				=> $values['descr'],
				'value_name'				=> $values['name'],

				'lang_cancel'				=> lang('cancel'),
			);

			$appname	= lang('task');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('hrm') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view_task' => $data));
		}



		function lookup_qualification()
		{
			if(!$this->acl->check('.job', PHPGW_ACL_ADD, 'hrm'))
			{
				$this->bocommon->no_access();
				return;
			}

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::job_type';

			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$GLOBALS['phpgw_info']['flags']['headonly']=true;

			$receipt = $GLOBALS['phpgw']->session->appsession('session_data','hrm_quali_tp_receipt');
			$GLOBALS['phpgw']->session->appsession('session_data','hrm_quali_tp_receipt','');

			$GLOBALS['phpgw']->xslttpl->add_file(array('job','nextmatchs','search_field'));

			$qualification = $this->bo->read_qualification_type();

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			if($this->acl->check('.job', PHPGW_ACL_EDIT, 'hrm'))
			{
				$allowed_edit = true;
			}

			//while (is_array($qualification) && list(,$entry) = each($qualification))
			if (is_array($qualification))
			{
				foreach($qualification as $key => $entry)
				{
					if ($allowed_edit)
					{
						$link_edit		= $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uijob.edit_qualification_type', 'quali_type_id'=> $entry['id']));
						$text_edit		= lang('edit');
					}

					$content[] = array
					(
						'id'			=> $entry['id'],
						'name'			=> $entry['name'],
						'descr'			=> $entry['descr'],
						'link_edit'		=> $link_edit,
	//					'link_view'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uijob.edit_qualification_type', 'quali_type_id'=> $entry['id']));
	//					'link_delete'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uijob.edit_qualification_type', 'quali_type_id'=> $entry['id']));
						'lang_select'		=> lang('select'),
						'text_delete'		=> lang('delete'),
						'text_edit'		=> $text_edit,
						'lang_edit_text'	=> lang('edit this item')
					);
					unset ($link_edit);
					unset ($text_edit);
				}
			}

			$table_header[] = array
			(
				'sort_name'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'name',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'hrm.uijob.lookup_qualification',
														'query'		=> $this->query,
														'cat_id'	=> $this->cat_id,
														'allrows'	=> $this->allrows)
										)),
				'lang_name'	=> lang('name'),
				'lang_descr'	=> lang('descr'),
				'lang_select'	=> lang('select'),
				'lang_view'	=> lang('view'),
				'lang_edit'	=> lang('edit'),
				'lang_delete'	=> lang('delete'),
			);



			$function_msg = lang('list qualification');


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
				'menuaction'	=> 'hrm.uijob.lookup_qualification',
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'cat_id'	=> $this->cat_id,
				'filter'	=> $this->filter,
				'query'		=> $this->query
			);

			$table_add[] = array
			(
				'lang_add'			=> lang('add'),
				'lang_add_qualification_text'	=> lang('add a qualification item'),
				'add_action'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uijob.edit_qualification_type', 'job_id'=> $job_id)),
				'lang_done'			=> lang('done'),
				'lang_done_qualification_text'	=> lang('back to user list'),
				'done_action'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uijob.lookup_qualification'))
			);


			$GLOBALS['phpgw_info']['flags']['java_script'] .= "\n"
				. '<script language="JavaScript">' ."\n"
				. 'function Exchangequalification(thisform)' ."\n"
				. '{' ."\n"
				. 'opener.document.qualification_form.elements[' . "'values[quali_type_id]']" . '.value = thisform.elements[0].value;' ."\n"
				. 'opener.document.qualification_form.elements[' . "'values[name]']" . '.value = thisform.elements[1].value;' ."\n"
				. 'opener.document.qualification_form.elements[' . "'values[descr]']" . '.value = thisform.elements[2].value;' ."\n"
				. 'window.close()' ."\n"
				. '}' ."\n"
				. "</script>\n";

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'allow_allrows'					=> true,
				'allrows'					=> $this->allrows,
				'start_record'					=> $this->start,
				'record_limit'					=> $record_limit,
				'num_records'					=> count($qualification),
				'all_records'					=> $this->bo->total_records,
				'link_url'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'					=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_searchfield_statustext'			=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'			=> lang('Submit the search string'),
				'query'						=> $this->query,
				'lang_search'					=> lang('search'),

				'lang_name_status_text'				=> lang('Enter the name of the qualification-type'),

				'table_header_lookup_qualification'		=> $table_header,
				'values_lookup_qualification'			=> $content,
//				'table_add'					=> $table_add,

				'lang_add'					=> lang('add'),
				'lang_add_qualification_text'			=> lang('add a qualification item'),
				'add_action'					=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uijob.edit_qualification_type')),

				'user_values'					=> $user_values,
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'					=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uijob.index')),
				'lang_id'					=> lang('qualification ID'),
				'lang_descr'					=> lang('Descr'),
				'lang_save'					=> lang('save'),
				'lang_cancel'					=> lang('cancel'),
				'value_id'					=> $job_id,
				'lang_id_status_text'				=> lang('Enter the qualification ID'),
				'lang_descr_status_text'			=> lang('Enter a description the qualification'),
				'lang_done_status_text'				=> lang('Back to the list'),
				'lang_save_status_text'				=> lang('Save the qualification'),
				'type_id'					=> $qualification['type_id'],
				'value_descr'					=> $qualification['descr'],
				'lang_apply'					=> lang('apply'),
				'lang_apply_status_text'			=> lang('Apply the values'),
			);

			$appname	= lang('qualification');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('hrm') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('lookup_qualification' => $data));
		}


		function edit_qualification_type()
		{
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$GLOBALS['phpgw_info']['flags']['headonly']=true;

			$quali_type_id	= phpgw::get_var('quali_type_id', 'int');
			$values		= phpgw::get_var('values');

			$GLOBALS['phpgw']->xslttpl->add_file(array('job'));

			if (is_array($values))
			{
				if ($values['save'] || $values['apply'])
				{

					if(!$values['name'])
					{
						$receipt['error'][]=array('msg'=>lang('Please enter a name !'));
					}

					if($quali_type_id)
					{
						$values['quali_type_id']=$quali_type_id;
						$action='edit';
					}

					if(!$receipt['error'])
					{
						$receipt = $this->bo->save_qualification_type($values,$action);
						$quali_type_id = $receipt['quali_type_id'];

						if ($values['save'])
						{
							$GLOBALS['phpgw']->session->appsession('session_data','hrm_quali_tp_receipt',$receipt);
							$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'=> 'hrm.uijob.lookup_qualification', 'query'=> $values['name']));
						}
					}
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'=> 'hrm.uijob.lookup_qualification'));
				}
			}


			if ($quali_type_id)
			{
				if(!$receipt['error'])
				{
					$values = $this->bo->read_single_qualification_type($quali_type_id);
				}
				$function_msg = lang('edit qualification type');
				$action='edit';
			}
			else
			{
				$function_msg = lang('add qualification type');
				$action='add';
			}

			$link_data = array
			(
				'menuaction'	=> 'hrm.uijob.edit_qualification_type',
				'quali_type_id'	=> $quali_type_id
			);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);


			$data = array
			(
				'value_title'				=> $values['title'],
				'value_entry_date'			=> $values['entry_date'],
				'value_name'				=> $values['name'],
				'value_descr'				=> $values['descr'],
				'lang_entry_date'			=> lang('Entry date'),
				'lang_name'				=> lang('name'),
				'lang_descr'				=> lang('descr'),
				'lang_descr_status_text'		=> lang('Enter a description'),

				'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_id'				=> lang('qualification type ID'),
				'lang_save'				=> lang('save'),
				'lang_cancel'				=> lang('cancel'),
				'value_id'				=> $quali_type_id,
				'lang_done_status_text'			=> lang('Back to the list'),
				'lang_save_status_text'			=> lang('Save the training'),
				'lang_apply'				=> lang('apply'),
				'lang_apply_status_text'		=> lang('Apply the values'),
			);

			$appname	= lang('Place');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('hrm') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit_qualification_type' => $data));
		}

		function edit_job()
		{
			$id			= phpgw::get_var('id', 'int');
			$parent_id	= phpgw::get_var('parent_id', 'int');
			$values		= phpgw::get_var('values');
			$type		= ''; //FIXME this only supresses a notice
			$type_id	= ''; //FIXME this only supresses a notice

			if(!$id)
			{
				if(!$this->acl->check('.job', PHPGW_ACL_ADD, 'hrm'))
				{
					$this->bocommon->no_access();
					return;
				}
			}
			else
			{
				if(!$this->acl->check('.job', PHPGW_ACL_EDIT, 'hrm'))
				{
					$this->bocommon->no_access();
					return;
				}
			}

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::job_type';

			$GLOBALS['phpgw']->xslttpl->add_file(array('job'));

			// Initialise some variables to prevent notices
			$receipt = array();
			$action = null;

			if (is_array($values))
			{
				if ($values['save'] || $values['apply'])
				{
					if(!$values['name'])
					{
						$receipt['error'][]=array('msg'=>lang('Please enter a name !'));
					}


					if($id)
					{
						$values['id']=$id;
						$action='edit';
					}

					if( !isset($receipt['error']) || !$receipt['error'] )
					{
						$receipt = $this->bo->save_job($values, $action);
						$id = $receipt['id'];

						if ($values['save'])
						{
							$GLOBALS['phpgw']->session->appsession('session_data','hrm_job_receipt',$receipt);
							$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'=> 'hrm.uijob.index'));
						}
					}
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'=> 'hrm.uijob.index'));
				}
			}

			if ($id)
			{
				$values = $this->bo->read_single_job($id);
				$function_msg = lang('edit job');
				$action='edit';
			}
			else
			{
				$function_msg = lang('add job');
				$action='add';
			}

			if($parent_id)
			{
				$values['parent_id'] = $parent_id;
			}

			$link_data = array
			(
				'menuaction'	=> 'hrm.uijob.edit_job',
				'id'		=> $id
			);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'				=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uijob.index' ,'type'=> $type, 'type_id'=> $type_id)),
				'lang_id'				=> lang('category ID'),
				'lang_descr'				=> lang('Descr'),
				'lang_save'				=> lang('save'),
				'value_id'				=> $id,

				'parent_list'				=> $this->bo->select_job_list($values['parent_id']),
				'lang_parent'				=> lang('parent'),
				'lang_parent_status_text'		=> lang('Select this jobs parent'),
				'lang_no_parent'			=> lang('select a parent'),

				'lang_name'				=> lang('name'),
				'lang_name_status_text'			=> lang('name of the job-type'),


				'lang_id_statustext'			=> lang('Enter the category ID'),
				'lang_descr_statustext'			=> lang('Enter a description the job'),
				'lang_done_statustext'			=> lang('Back to the list'),
				'lang_save_statustext'			=> lang('Save the job'),
				'type_id'				=> $values['type_id'],
				'value_descr'				=> $values['descr'],
				'value_name'				=> $values['name'],

				'lang_cancel'				=> lang('cancel'),
				'lang_apply'				=> lang('apply'),
				'lang_apply_status_text'		=> lang('Apply the values'),
			);

			$appname = lang('job');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('hrm') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit_job' => $data));
		}

		function view_job()
		{
			$id		= phpgw::get_var('id', 'int');
			$parent_id	= phpgw::get_var('parent_id', 'int');

			if(!$this->acl->check('.job', PHPGW_ACL_READ, 'hrm'))
			{
				$this->bocommon->no_access();
				return;
			}

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::job_type';

			$GLOBALS['phpgw']->xslttpl->add_file(array('job'));


			if ($id)
			{
				$values = $this->bo->read_single_job($id);
				$function_msg = lang('view job');
			}

			if($parent_id)
			{
				$values['parent_id'] = $parent_id;
			}

			$link_data = array
			(
				'menuaction'	=> 'hrm.uijob.edit_job',
				'id'		=> $id
			);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'done_action'				=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uijob.index', 'type'=> $type, 'type_id'=> $type_id)),
				'lang_id'				=> lang('category ID'),
				'lang_descr'				=> lang('Descr'),
				'lang_save'				=> lang('save'),
				'value_id'				=> $id,

				'parent_list'				=> $this->bo->select_job_list($values['parent_id']),
				'lang_parent'				=> lang('parent'),
				'lang_parent_status_text'		=> lang('Select this jobs parent'),
				'lang_no_parent'			=> lang('select a parent'),

				'lang_name'				=> lang('name'),
				'lang_name_status_text'			=> lang('name of the job-type'),

				'lang_id_statustext'			=> lang('Enter the category ID'),
				'lang_descr_statustext'			=> lang('Enter a description the job'),
				'lang_done_statustext'			=> lang('Back to the list'),
				'lang_save_statustext'			=> lang('Save the job'),
				'type_id'				=> $values['type_id'],
				'value_descr'				=> $values['descr'],
				'value_name'				=> $values['name'],

				'lang_cancel'				=> lang('cancel'),
				'lang_apply'				=> lang('apply'),
				'lang_apply_status_text'		=> lang('Apply the values'),
			);

			$appname	= lang('job');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('hrm') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view_job' => $data));
		}

		function edit_qualification()
		{
			$quali_id	= phpgw::get_var('quali_id', 'int');
			$job_id	= phpgw::get_var('job_id', 'int');

			if(!$quali_id)
			{
				if(!$this->acl->check('.job', PHPGW_ACL_ADD, 'hrm'))
				{
					$this->bocommon->no_access();
					return;
				}
			}
			else
			{
				if(!$this->acl->check('.job', PHPGW_ACL_EDIT, 'hrm'))
				{
					$this->bocommon->no_access();
					return;
				}
			}

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::job_type';

			$values		= phpgw::get_var('values');

			$GLOBALS['phpgw']->xslttpl->add_file(array('job'));

			if (is_array($values))
			{
				$values['job_id']= $job_id;

				$values['alternative_qualification']	= phpgw::get_var('alternative_qualification');

				if ($values['save'] || $values['apply'])
				{
					if(!$values['cat_id'])
					{
						$receipt['error'][]=array('msg'=>lang('Please select a category !'));
					}
					if(!$values['name'])
					{
						$receipt['error'][]=array('msg'=>lang('Please enter a name !'));
					}

					if($quali_id)
					{
						$values['quali_id']=$quali_id;
						$action='edit';
					}

					if(!$receipt['error'])
					{
						$receipt = $this->bo->save_qualification($values,$action);
						$quali_id = $receipt['quali_id'];

						if ($values['save'])
						{
							$GLOBALS['phpgw']->session->appsession('session_data','hrm_quali_receipt',$receipt);
							$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'=> 'hrm.uijob.qualification', 'job_id'=> $job_id));
						}
					}
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'=> 'hrm.uijob.qualification' ,'job_id'=> $job_id));
				}
			}


			if ($quali_id)
			{
				if(!$receipt['error'])
				{
					$values = $this->bo->read_single_qualification($quali_id);
				}
				$function_msg = lang('edit qualification');
				$action='edit';
			}
			else
			{
				$function_msg = lang('add qualification');
				$action='add';
			}


			$link_data = array
			(
				'menuaction'	=> 'hrm.uijob.edit_qualification',
				'quali_id'	=> $quali_id,
				'job_id' 	=> $job_id
			);
//_debug_array($link_data);

			$link_data_lookup = array
			(
				'menuaction'	=> 'hrm.uijob.lookup_qualification',
				'quali_id'	=> $quali_id,
				'job_id' 	=> $job_id
			);

			$GLOBALS['phpgw_info']['flags']['java_script'] .= "\n"
				. '<script language="JavaScript">' ."\n"
					. 'self.name="first_Window";' ."\n"
					. 'function qualifications_popup()' ."\n"
					. '{' ."\n"
						. 'Window1=window.open("' . $GLOBALS['phpgw']->link('/index.php',$link_data_lookup) . '","Search","width=800,height=600,toolbar=no,scrollbars=yes,resizable=yes");' ."\n"
					. '}' ."\n"
				. "</script>\n";

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$qualification_list			= $this->bo->select_qualification_list($job_id,$quali_id);

			$data = array
			(
				'value_quali_type_id'			=> $values['quali_type_id'],
				'value_descr'				=> $values['descr'],
				'value_remark'				=> $values['remark'],
				'value_name'				=> $values['name'],
				'value_entry_date'			=> $values['entry_date'],
				'lang_entry_date'			=> lang('Entry date'),
				'lang_name'				=> lang('name'),
				'lang_name_status_text'			=> lang('name of the qualification item'),
				'lang_skill'				=> lang('Skill level'),
				'lang_skill_status_text'		=> lang('Select your skill'),
				'skill_list'				=> $this->bocategory->select_category_list('skill_level',$values['skill_id']),
				'lang_no_skill'				=> lang('select a level'),
				'experience_list'			=> $this->bocategory->select_category_list('experience',$values['experience_id']),
				'lang_experience'			=> lang('experience'),
				'lang_experience_status_text'		=> lang('Select a experience level'),
				'lang_no_experience'			=> lang('select experience'),
				'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_id'				=> lang('qualification ID'),
				'lang_descr'				=> lang('Descr'),
				'lang_remark'				=> lang('remark'),
				'lang_remark_status_text'		=> lang('Enter a remark'),
				'lang_save'				=> lang('save'),
				'lang_cancel'				=> lang('cancel'),
				'value_id'				=> $quali_id,
				'lang_descr_status_text'		=> lang('Enter a description the qualification'),
				'lang_done_status_text'			=> lang('Back to the list'),
				'lang_save_status_text'			=> lang('Save the qualification'),
				'lang_apply'				=> lang('apply'),
				'lang_apply_status_text'		=> lang('Apply the values'),
				'lang_category'				=> lang('category'),
				'cat_list'				=> $this->bocategory->select_category_list('qualification',$values['cat_id']),
				'lang_no_cat'				=> lang('no category'),
				'lang_cat_status_text'			=> lang('Select the category the qualification belongs to. To do not use a category select NO CATEGORY'),
				'lang_alternative'			=> lang('alternative'),
				'lang_open_popup'			=> lang('open popup window'),
				'lang_no_alternative'			=> lang('select alternative'),
				'qualification_list'				=> $qualification_list,
				'qualification_list_size'			=> count($qualification_list)

			);

			$job_info = $this->bo->read_single_job($job_id);

			$appname					= lang('qualification') .' ' . $job_info['name'];

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('hrm') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit_qualification' => $data));
		}

		function view_qualification()
		{
			$quali_id	= phpgw::get_var('quali_id', 'int');
			$job_id	= phpgw::get_var('job_id', 'int');

			if(!$this->acl->check('.job', PHPGW_ACL_READ, 'hrm'))
			{
				$this->bocommon->no_access();
				return;
			}

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::job_type';

			$GLOBALS['phpgw']->xslttpl->add_file(array('job'));

			$values = $this->bo->read_single_qualification($quali_id);
			$function_msg = lang('view qualification');

			$link_data = array
			(
				'menuaction'	=> 'hrm.uijob.qualification',
				'job_id' 	=> $job_id
			);

			$qualification_list			= $this->bo->select_qualification_list($job_id,$quali_id);
			$data = array
			(
				'value_descr'				=> $values['descr'],
				'value_remark'				=> $values['remark'],
				'value_name'				=> $values['name'],
				'value_entry_date'			=> $values['entry_date'],
				'lang_entry_date'			=> lang('Entry date'),
				'lang_name'				=> lang('name'),
				'lang_name_status_text'			=> lang('name of the qualification item'),
				'lang_skill'				=> lang('Skill level'),
				'lang_skill_status_text'		=> lang('Select your skill'),
				'skill_list'				=> $this->bocategory->select_category_list('skill_level',$values['skill_id']),
				'lang_no_skill'				=> lang('select a level'),
				'experience_list'			=> $this->bocategory->select_category_list('experience',$values['experience_id']),
				'lang_experience'			=> lang('experience'),
				'lang_experience_status_text'		=> lang('Select a experience level'),
				'lang_no_experience'			=> lang('select experience'),

				'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_id'				=> lang('qualification ID'),
				'lang_descr'				=> lang('Descr'),
				'lang_remark'				=> lang('remark'),
				'lang_save'				=> lang('save'),
				'lang_cancel'				=> lang('cancel'),
				'value_id'				=> $quali_id,

				'lang_descr_status_text'		=> lang('Enter a description the qualification'),
				'lang_done_status_text'			=> lang('Back to the list'),
				'lang_save_status_text'			=> lang('Save the qualification'),
				'lang_apply'				=> lang('apply'),
				'lang_apply_status_text'		=> lang('Apply the values'),

				'lang_category'				=> lang('category'),
				'cat_list'				=> $this->bocategory->select_category_list('qualification',$values['cat_id']),
				'lang_no_cat'				=> lang('no category'),
				'lang_cat_status_text'			=> lang('Select the category the qualification belongs to. To do not use a category select NO CATEGORY'),

				'lang_alternative'			=> lang('alternative'),
				'qualification_list'				=> $qualification_list,
				'qualification_list_size'			=> count($qualification_list)
			);

			$job_info = $this->bo->read_single_job($job_id);
			$appname					= lang('qualification') .' ' . $job_info['name'];
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('hrm') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view_qualification' => $data));
		}

		function delete_job()
		{
			if(!$this->acl->check('.job', PHPGW_ACL_DELETE, 'hrm'))
			{
				$this->bocommon->no_access();
				return;
			}

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::job_type';

			$job_id	= phpgw::get_var('job_id', 'int');
			$confirm		= phpgw::get_var('confirm', 'bool', 'POST');
			$link_data = array
			(
				'menuaction'	=> 'hrm.uijob.index',
				'job_id' 	=> $job_id
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete_job($job_id);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));
			$data = array
			(
				'done_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uijob.delete_job', 'job_id'=> $job_id)),
				'lang_confirm_msg'		=> lang('do you really want to delete this entry'),
				'lang_yes'			=> lang('yes'),
				'lang_yes_statustext'		=> lang('Delete the entry'),
				'lang_no_statustext'		=> lang('Back to the list'),
				'lang_no'			=> lang('no')
			);

			$appname	= lang('job');
			$function_msg	= lang('delete');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('hrm') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		}

		function delete_task()
		{
			if(!$this->acl->check('.job', PHPGW_ACL_DELETE, 'hrm'))
			{
				$this->bocommon->no_access();
				return;
			}

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::job_type';

			$id		= phpgw::get_var('id', 'int');
			$job_id	= phpgw::get_var('job_id', 'int');
			$confirm		= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
			(
				'menuaction'	=> 'hrm.uijob.task',
				'job_id'	=> $job_id
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete_task($id);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uijob.delete_task', 'job_id'=> $job_id, 'id'=> $id)),
				'lang_confirm_msg'		=> lang('do you really want to delete this entry'),
				'lang_yes'			=> lang('yes'),
				'lang_yes_categorytext'		=> lang('Delete the entry'),
				'lang_no_categorytext'		=> lang('Back to the list'),
				'lang_no'			=> lang('no')
			);

			$appname	= lang('task');
			$function_msg	= lang('delete');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('hrm') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		}

		function delete_qualification()
		{
			if(!$this->acl->check('.job', PHPGW_ACL_DELETE, 'hrm'))
			{
				$this->bocommon->no_access();
				return;
			}

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::job_type';

			$quali_id	= phpgw::get_var('quali_id', 'int');
			$job_id		= phpgw::get_var('job_id', 'int');
			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
			(
				'menuaction'	=> 'hrm.uijob.qualification',
				'job_id'	=> $job_id
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete_qualification($job_id,$quali_id);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uijob.delete_qualification', 'job_id'=> $job_id, 'quali_id'=> $quali_id)),
				'lang_confirm_msg'		=> lang('do you really want to delete this entry'),
				'lang_yes'			=> lang('yes'),
				'lang_yes_categorytext'		=> lang('Delete the entry'),
				'lang_no_categorytext'		=> lang('Back to the list'),
				'lang_no'			=> lang('no')
			);

			$appname	= lang('qualification');
			$function_msg	= lang('delete');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('hrm') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		}

		function reset_job_type_hierarchy()
		{
			if(!$this->acl->check('.job', PHPGW_ACL_DELETE, 'hrm'))
			{
				$this->bocommon->no_access();
				return;
			}

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::job_type';

			$confirm		= phpgw::get_var('confirm', 'bool', 'POST');
			$link_data = array
			(
				'menuaction' => 'hrm.uijob.index'
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->reset_job_type_hierarchy();
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uijob.reset_job_type_hierarchy')),
				'lang_confirm_msg'		=> lang('do you really want to reset the hierarchy'),
				'lang_yes'			=> lang('yes'),
				'lang_yes_statustext'		=> lang('Reset the hierarchy'),
				'lang_no_statustext'		=> lang('Back to the list'),
				'lang_no'			=> lang('no')
			);

			$appname	= lang('job');
			$function_msg	= lang('delete');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('hrm') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		}

		function hierarchy()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::organisation';
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;
			$GLOBALS['phpgw']->common->phpgw_header(true);
			echo '<div class="msg">FIXME: Implement something here!</div>';
			$GLOBALS['phpgw']->common->phpgw_footer();
		}
	}
