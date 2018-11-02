<?php
	/**
	 * phpGroupWare - controller: a part of a Facilities Management System.
	 *
	 * @author Erik Holm-Larsen <erik.holm-larsen@bouvet.no>
	 * @author Torstein Vadla <torstein.vadla@bouvet.no>
	 * @copyright Copyright (C) 2011,2012 Free Software Foundation, Inc. http://www.fsf.org/
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
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/
	 * @package property
	 * @subpackage controller
	 * @version $Id$
	 */
	phpgw::import_class('phpgwapi.uicommon_jquery');
	phpgw::import_class('controller.soprocedure');
	phpgw::import_class('controller.socontrol');

	include_class('controller', 'procedure', 'inc/model/');

	class controller_uiprocedure extends phpgwapi_uicommon_jquery
	{

		private $so;
		private $_category_acl;
		private $so_control;
		private $so_control_group_list;
		private $so_control_group;
		private $read;
		private $add;
		private $edit;
		private $delete;
		public $public_functions = array
			(
			'index' => true,
			'query' => true,
			'edit' => true,
			'view' => true,
			'add' => true,
			'get_procedures' => true,
			'view_procedures_for_control' => true,
			'print_procedure' => true
		);

		protected $dateformat;
		public function __construct()
		{
			parent::__construct();

			$this->so = CreateObject('controller.soprocedure');
			$this->so_control = CreateObject('controller.socontrol');
			$this->so_control_group_list = CreateObject('controller.socontrol_group_list');
			$this->so_control_group = CreateObject('controller.socontrol_group');

			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "controller::procedure";

			$this->read = $GLOBALS['phpgw']->acl->check('.control', PHPGW_ACL_READ, 'controller');//1
			$this->add = $GLOBALS['phpgw']->acl->check('.control', PHPGW_ACL_ADD, 'controller');//2
			$this->edit = $GLOBALS['phpgw']->acl->check('.control', PHPGW_ACL_EDIT, 'controller');//4
			$this->delete = $GLOBALS['phpgw']->acl->check('.control', PHPGW_ACL_DELETE, 'controller');//8

			$config = CreateObject('phpgwapi.config', 'controller');
			$config->read();
			$this->_category_acl = isset($config->config_data['acl_at_control_area']) && $config->config_data['acl_at_control_area'] == 1 ? true : false;
			$this->dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
//			$GLOBALS['phpgw']->css->add_external_file('controller/templates/base/css/base.css');
		}

		public function index()
		{
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}
			// Sigurd: START as categories
			$cats = CreateObject('phpgwapi.categories', -1, 'controller', '.control');
			$cats->supress_info = true;

			$control_areas = $cats->formatted_xslt_list(array('format' => 'filter', 'selected' => '',
				'globals' => true, 'use_acl' => $this->_category_acl));
			array_unshift($control_areas['cat_list'], array('cat_id' => '', 'name' => lang('select value')));
			$control_areas_array2 = array();
			foreach ($control_areas['cat_list'] as $cat_list)
			{
				$control_areas_array2[] = array
					(
					'id' => $cat_list['cat_id'],
					'name' => $cat_list['name'],
				);
			}
			// END as categories

			$data = array(
				'datatable_name' => 'Prosedyrer', //lang('procedures'),
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array('type' => 'filter',
								'name' => 'control_areas',
								'text' => lang('Control_area') . ':',
								'list' => $control_areas_array2,
							)
						),
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'controller.uiprocedure.index',
						'phpgw_return_as' => 'json')),
					'new_item' => self::link(array('menuaction' => 'controller.uiprocedure.add')),
					'allrows' => true,
					'field' => array(
//						array(
//							'key' => 'id',
//							'label' => lang('ID'),
//							'sortable' => true,
//							'formatter' => 'JqueryPortico.formatLink'
//						),
						array(
							'key' => 'title',
							'label' => lang('Procedure title'),
							'sortable' => true,
							'formatter' => 'JqueryPortico.formatLink'
						),
						array(
							'key' => 'purpose',
							'label' => lang('Procedure purpose'),
							'sortable' => false
						),
						array(
							'key' => 'control_area',
							'label' => lang('Control area'),
							'sortable' => false
						),
						array(
							'key' => 'revision_no',
							'label' => lang('Procedure revision'),
							'sortable' => true
						),
						array(
							'key' => 'revision_date',
							'label' => lang('Procedure revision date'),
							'sortable' => true
						),
						array(
							'key' => 'modified_by_name',
							'label' => lang('modified by'),
							'sortable' => false
						),
						array(
							'key' => 'modified_date',
							'label' => lang('Modified date'),
							'sortable' => true
						),
						array(
							'key' => 'link',
							'hidden' => true
						)
					)
				),
			);

			$parameters = array
				(
				'parameter' => array
					(
					array
						(
						'name' => 'id',
						'source' => 'id'
					),
				)
			);
			$data['datatable']['actions'][] = array
				(
				'my_name' => 'view',
				'statustext' => lang('view'),
				'text' => lang('view'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'controller.uiprocedure.view'
				)),
				'parameters' => json_encode($parameters)
			);

			self::render_template_xsl(array('datatable_jquery'), $data);
		}

		public function edit()
		{
			$procedure_id = phpgw::get_var('id', 'int');
			if (isset($procedure_id) && $procedure_id > 0)
			{
				$procedure = $this->so->get_single($procedure_id);
			}
			else
			{
				$procedure = new controller_procedure();
			}

			if ($procedure->get_procedure_id())
			{
				phpgwapi_cache::message_set(lang('Do not edit archived version' ), 'error');
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uiprocedure.view',
					'id' => $procedure_id));
			}

			$_edit_mode = phpgw::get_var('edit_mode');
			$edit_mode = $_edit_mode ? $_edit_mode : 'edit_procedure';

			$error = false;

			if (isset($_POST['save_procedure'])) // The user has pressed the save button
			{
				if (!$this->add && !$this->edit)
				{
					phpgwapi_cache::message_set('No access', 'error');
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uiprocedure.view',
						'id' => $procedure_id));
				}

				$start_date = phpgw::get_var('start_date', 'date');
				if (!$start_date)
				{
					phpgwapi_cache::message_set(lang('missing start date'), 'error');
					$error = true;
				}

				if (isset($procedure)) // Edit procedure
				{
					$description_txt = phpgw::get_var('description', 'html');
					$description_txt = str_replace("&nbsp;", " ", $description_txt);
					$purpose_txt = phpgw::get_var('purpose', 'html');
					$purpose_txt = str_replace("&nbsp;", " ", $purpose_txt);
					$reference_txt = phpgw::get_var('reference', 'html');
					$reference_txt = str_replace("&nbsp;", " ", $reference_txt);
					$responsibility_txt = phpgw::get_var('responsibility', 'html');
					$responsibility_txt = str_replace("&nbsp;", " ", $responsibility_txt);
					$procedure->set_title(phpgw::get_var('title'));
					$procedure->set_purpose($purpose_txt);
					$procedure->set_responsibility($responsibility_txt);
					$procedure->set_description($description_txt);
					$procedure->set_reference($reference_txt);
					$procedure->set_attachment(phpgw::get_var('attachment'));
					$procedure->set_start_date($start_date);
					$procedure->set_end_date(phpgw::get_var('end_date', 'date'));
					$procedure->set_revision_date(phpgw::get_var('revision_date', 'date'));
					$procedure->set_control_area_id(phpgw::get_var('control_area'));
					$procedure->set_modified_date(time());
					$procedure->set_modified_by($GLOBALS['phpgw_info']['user']['account_id']);

					$revision = (int)$procedure->get_revision_no();
					if ($revision && is_numeric($revision) && $revision > 0)
					{
						$procedure->set_revision_no($revision);
					}
					else
					{
						$procedure->set_revision_no(1);
					}

					if (isset($procedure_id) && $procedure_id > 0)
					{
						$proc_id = $procedure_id;
						$this->so->transaction_begin();
						if ($this->so->store($procedure))
						{
							$revised_procedures = $this->so->get_other_revisions($procedure->get_id());
							$old_revision_arr = end($revised_procedures);
							if(!empty($old_revision_arr['id']))
							{
								$old_revision = $this->so->get_single($old_revision_arr['id']);
								$old_revision->set_end_date($procedure->get_start_date());
								$this->so->store($old_revision);

							}
							$message = lang('messages_saved_form');
							$this->so->transaction_commit();
						}
						else
						{
							$this->so->transaction_abort();
							$error = lang('messages_form_error');
						}
					}
					else
					{
						$proc_id = $this->so->add($procedure);
						if ($proc_id)
						{
							$message = lang('messages_saved_form');
						}
						else
						{
							$error = lang('messages_form_error');
						}
					}
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uiprocedure.view',
						'id' => $proc_id));
				}
			}
			else if (isset($_POST['revisit_procedure'])) // The user has pressed the revisit button
			{
				if (!$this->add && !$this->edit)
				{
					phpgwapi_cache::message_set('No access', 'error');
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uiprocedure.view',
						'id' => $procedure_id));
				}

				//Sigurd 20170808: we are keeping the old one as reference as is, and creating a new one for future checklists
				$old_procedure = $this->so->get_single($procedure_id);
				if (isset($procedure)) // Edit procedure
				{

					$description_txt = phpgw::get_var('description', 'html');
					$description_txt = str_replace("&nbsp;", " ", $description_txt);
					$purpose_txt = phpgw::get_var('purpose', 'html');
					$purpose_txt = str_replace("&nbsp;", " ", $purpose_txt);
					$reference_txt = phpgw::get_var('reference', 'html');
					$reference_txt = str_replace("&nbsp;", " ", $reference_txt);

					$new_procedure = new controller_procedure();
					$revision = (int)$procedure->get_revision_no();
					if ($revision && is_numeric($revision))
					{
						$revision++;
						$new_procedure->set_revision_no($revision);
					}
					else
					{
						$new_procedure->set_revision_no(2);
					}
					$new_procedure->set_title(phpgw::get_var('title'));
					$new_procedure->set_purpose($purpose_txt);
					$new_procedure->set_responsibility(phpgw::get_var('responsibility'));
					$new_procedure->set_description($description_txt);
					$new_procedure->set_reference($reference_txt);
					$new_procedure->set_attachment(phpgw::get_var('attachment'));

					$start_date = phpgw::get_var('start_date', 'date');
					$start_date = $start_date ? $start_date : time();
					$new_procedure->set_start_date($start_date);

					$new_procedure->set_end_date(phpgw::get_var('end_date', 'date'));

					$revision_date = phpgw::get_var('revision_date', 'date');
					$revision_date = $revision_date ? $revision_date : time();
					$new_procedure->set_revision_date($revision_date);

					$new_procedure->set_control_area_id(phpgw::get_var('control_area'));
					$new_procedure->set_modified_date(time());
					$new_procedure->set_modified_by($GLOBALS['phpgw_info']['user']['account_id']);

					if (isset($procedure_id) && $procedure_id > 0)
					{
						$this->so->transaction_begin();
						if ($proc_id = $this->so->add($new_procedure)) //add the revised prosedure as new
						{
							$sodocuments = createObject('controller.sodocument');
							$documents = $sodocuments->get(0, 0, '', false, '', '', array('procedure_id' => $procedure_id));
							foreach ($documents as $document)
							{
								$document->set_id(0);//new
								$document->set_type_id(1);
								$document->set_procedure_id($proc_id);
								$sodocuments->store($document);
							}

							$old_end_date = $start_date ? $start_date : time();
							$old_procedure->set_end_date($old_end_date);
							$old_procedure->set_revision_date($revision_date);
							$old_procedure->set_procedure_id($proc_id);
							if ($this->so->store($old_procedure)) //add revision of the old procedure to history
							{
								$message = lang('messages_saved_form');
							}
							else
							{
								$this->so->transaction_abort();
								$error = lang('messages_form_error');
							}
						}
						$this->so->transaction_commit();
					}

					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uiprocedure.view',
						'id' => $proc_id));
				}
			}
			else if (isset($_POST['cancel_procedure'])) // The user has pressed the cancel button
			{
				if (isset($procedure_id) && $procedure_id > 0)
				{
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uiprocedure.view',
						'id' => $procedure_id));
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uiprocedure.index'));
				}
			}
			else
			{
				if ($this->flash_msgs)
				{
					$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($this->flash_msgs);
					$msgbox_data = $GLOBALS['phpgw']->common->msgbox($msgbox_data);
				}

				// Sigurd: START as categories
				$cats = CreateObject('phpgwapi.categories', -1, 'controller', '.control');
				$cats->supress_info = true;

				$control_areas = $cats->formatted_xslt_list(array('format' => 'filter', 'selected' => $procedure->get_control_area_id(),
					'globals' => true, 'use_acl' => $this->_category_acl));
				array_unshift($control_areas['cat_list'], array('cat_id' => '', 'name' => lang('select value')));
				$control_areas_array2 = array();
				//_debug_array($control_areas);
				foreach ($control_areas['cat_list'] as $cat_list)
				{
					if ($cat_list['cat_id'] == $procedure->get_control_area_id())
					{
						$control_areas_array2[] = array
							(
							'id' => $cat_list['cat_id'],
							'name' => $cat_list['name'],
							'selected' => 1,
						);
					}
					else
					{
						$control_areas_array2[] = array
							(
							'id' => $cat_list['cat_id'],
							'name' => $cat_list['name'],
						);
					}
				}
				// END as categories
				/* 				$control_area_array = $this->so_control_area->get_control_area_array();
				  foreach ($control_area_array as $control_area)
				  {
				  if($procedure->get_control_area_id() && $control_area->get_id() == $procedure->get_control_area_id())
				  {
				  $control_area_options[] = array
				  (
				  'id'	=> $control_area->get_id(),
				  'name'	=> $control_area->get_title(),
				  'selected' => 'yes'
				  );
				  }
				  else
				  {
				  $control_area_options[] = array
				  (
				  'id'	=> $control_area->get_id(),
				  'name'	=> $control_area->get_title()
				  );
				  }
				  }
				 */

				/*
				 * hack to fix display of &nbsp; char 
				 */
				$procedure->set_description(str_replace("&nbsp;", " ", $procedure->get_description()));
				$procedure->set_responsibility(str_replace('&nbsp;', ' ', $procedure->get_responsibility()));
				$procedure->set_reference(str_replace('&nbsp;', ' ', $procedure->get_reference()));

				$procedure_array = $procedure->toArray();
				//_debug_array($procedure_array);

				$tabs = array(
					'procedure' => array('label' => lang('Procedure'), 'link' => '#procedure'),
					'documents' => array('label' => lang('View_documents_for_procedure'), 'link' => '#documents')
				);

				$GLOBALS['phpgw']->jqcal->add_listener('start_date');
				$GLOBALS['phpgw']->jqcal->add_listener('end_date');
				$GLOBALS['phpgw']->jqcal->add_listener('revision_date');

				$end_date = $GLOBALS['phpgw']->common->show_date($procedure->get_end_date(), $this->dateformat);
				$revision_date = $GLOBALS['phpgw']->common->show_date($procedure->get_revision_date(), $this->dateformat);

				if(!empty($procedure_array['modified_by']))
				{
					$procedure_array['modified_by_name'] = $GLOBALS['phpgw']->accounts->id2name($procedure_array['modified_by']);
				}

				$data = array
					(
					'tabs' => phpgwapi_jquery::tabview_generate($tabs, 'procedure', 'procedure_tabview'),
					'view' => "view_procedure",
					'value_id' => !empty($procedure) ? $procedure->get_id() : 0,
					'start_date' => $GLOBALS['phpgw']->common->show_date($procedure->get_start_date() ? $procedure->get_start_date() : time(), $this->dateformat),
					'end_date' => $end_date ? $end_date : '',
					'revision_date' => $revision_date ? $revision_date : '',
					'editable' => true,
					'procedure' => $procedure_array,
					//'control_area'				=> array('options' => $control_area_options),
					'control_area' => array('options' => $control_areas_array2),
					'edit_mode'	=> $edit_mode
				);


				$GLOBALS['phpgw_info']['flags']['app_header'] = lang('controller') . '::' . lang('Procedure');

				switch ($edit_mode)
				{
					case 'edit_procedure':
						$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('edit');
						break;
					default:
						$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('new revision');
						break;
				}
				phpgwapi_jquery::formvalidator_generate(array('date', 'security','file'));

				$this->use_yui_editor(array('responsibility', 'description', 'reference'));

				self::render_template_xsl(array('procedure/procedure_tabs', 'procedure/procedure_item'), $data);
			}
		}

		// Returns check list info as JSON
		public function get_procedures()
		{
			$control_area_id = phpgw::get_var('control_area_id');

			$procedures_array = $this->so->get_procedures_by_control_area($control_area_id);

			if (count($procedures_array) > 0)
			{
				return json_encode($procedures_array);
			}
			else
			{
				return null;
			}
		}

		/**
		 * Public method. Forwards the user to edit mode.
		 */
		public function add()
		{
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uiprocedure.edit'));
		}

		/**
		 * Public method. Called when a user wants to view information about a procedure.
		 * @param HTTP::id	the procedure ID
		 */
		public function view()
		{
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('view');
			$view_revision = phpgw::get_var('view_revision');
			$procedure_id = (int)phpgw::get_var('id');
			if (isset($_POST['edit_procedure']))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uiprocedure.edit',
					'id' => $procedure_id, 'edit_mode' => 'edit_procedure' ));
			}
			else if (isset($_POST['new_revison']))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uiprocedure.edit',
					'id' => $procedure_id, 'edit_mode' => 'new_revison' ));
			}
			else
			{
				//Retrieve the procedure object
				if (isset($procedure_id) && $procedure_id > 0)
				{
					$procedure = $this->so->get_single($procedure_id);
				}
				else
				{
					$this->render('permission_denied.php', array('error' => lang('invalid_request')));
					return;
				}

				if ($this->flash_msgs)
				{
					$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($this->flash_msgs);
					$msgbox_data = $GLOBALS['phpgw']->common->msgbox($msgbox_data);
				}

				$category = execMethod('phpgwapi.categories.return_single', $procedure->get_control_area_id());
				$procedure->set_control_area_name($category[0]['name']);

				/* hack to fix display of &nbsp; char */
				$procedure->set_description(str_replace("&nbsp;", " ", $procedure->get_description()));
				$procedure->set_responsibility(str_replace('&nbsp;', ' ', $procedure->get_responsibility()));
				$procedure->set_reference(str_replace('&nbsp;', ' ', $procedure->get_reference()));


				if ($procedure->get_start_date() && $procedure->get_start_date() != null)
				{
					//$procedure_start_date = date($this->dateformat, $procedure->get_start_date());
					$procedure->set_start_date(date($this->dateformat, $procedure->get_start_date()));
				}
				else
				{
					$procedure->set_start_date("");
				}
				if ($procedure->get_end_date() && $procedure->get_end_date() != null)
				{
					//$procedure_end_date	= date($this->dateformat, $procedure->get_end_date());
					$procedure->set_end_date(date($this->dateformat, $procedure->get_end_date()));
				}
				else
				{
					$procedure->set_end_date("");
				}
				if ($procedure->get_revision_date() && $procedure->get_revision_date() != null)
				{
					//$procedure_revision_date = date($this->dateformat, $procedure->get_revision_date());
					$procedure->set_revision_date(date($this->dateformat, $procedure->get_revision_date()));
				}
				else
				{
					$procedure->set_revision_date("");
				}
				$procedure_array = $procedure->toArray();

//				if (!$view_revision)
				{
					$table_header[] = array('header' => lang('Procedure revision'));
					$table_header[] = array('header' => lang('Procedure title'));
					$table_header[] = array('header' => lang('Procedure start date'));
					$table_header[] = array('header' => lang('Procedure end date'));
					$table_header[] = array('header' => lang('Modified date'));
					$table_header[] = array('header' => lang('Modified by'));

					$revised_procedures = $this->so->get_other_revisions($procedure->get_id());
					$table_values = array();
					foreach ($revised_procedures as &$rev)
					{
						if($rev['modified_by'])
						{
							$rev['modified_by_name'] = $GLOBALS['phpgw']->accounts->id2name($rev['modified_by']);
						}
						$rev['modified_date'] = $GLOBALS['phpgw']->common->show_date($rev['modified_date'], $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
						$rev['link'] = self::link(array('menuaction' => 'controller.uiprocedure.view',
								'id' => $rev['id'], 'view_revision' => 'yes'));
						$table_values[] = array('row' => $rev);
					}
				}

				$tabs = array(
					'procedure' => array('label' => lang('Procedure'), 'link' => '#procedure'),
					'documents' => array(
						'label' => lang('View_documents_for_procedure'),
						'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uidocument.show',
							'procedure_id' => $procedure->get_id(), 'type' => 'procedure'))
					)
				);

				if(!empty($procedure_array['modified_by']))
				{
					$procedure_array['modified_by_name'] = $GLOBALS['phpgw']->accounts->id2name($procedure_array['modified_by']);
				}
				$data = array
					(
					'tabs' => phpgwapi_jquery::tabview_generate($tabs, 'procedure', 'procedure_tabview'),
					'view' => "view_procedure",
					'value_id' => !empty($procedure) ? $procedure->get_id() : 0,
					'procedure' => $procedure_array,
					'values' => $table_values,
					'table_header' => $table_header,
				);

				/*
				 * This is not the latest version
				 */
				if ($procedure->get_procedure_id())
				{
					$data['inactive'] = true;
				}

				$GLOBALS['phpgw_info']['flags']['app_header'] = lang('controller') . '::' . lang('Procedure');
				self::render_template_xsl(array('procedure/procedure_tabs', 'procedure/procedure_item'), $data);
			}
		}

		public function view_procedures_for_control()
		{

			$check_list_id = phpgw::get_var('check_list_id', 'int');
			$control_id = phpgw::get_var('control_id', 'int');
			$location_code = phpgw::get_var('location_code', 'string');

			$control = $this->so_control->get_single($control_id);

			$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));

			$control_procedure = $this->so->get_single_with_documents($control->get_procedure_id(),$check_list_id, "return_array");

			$control_groups = $this->so_control_group_list->get_control_groups_by_control($control_id);

			$group_procedures_array = array();

			foreach ($control_groups as $control_group)
			{
				$group_procedure = $this->so->get_single_no_documents($control_group->get_procedure_id(), $check_list_id );
				if (isset($group_procedure))
				{
					$group_procedures_array[] = array("control_group" => $control_group->toArray(),
						"procedure" => $group_procedure->toArray());
				}
			}

			$data = array
				(
				'location' => $location_array,
				'control' => $control,
				'control_procedure' => $control_procedure,
				'group_procedures_array' => $group_procedures_array
			);

			self::render_template_xsl('procedure/view_procedures_for_control', $data);
		}

		public function print_procedure()
		{
			$procedure_id = phpgw::get_var('procedure_id');
			$location_code = phpgw::get_var('location_code');
			$control_id = phpgw::get_var('control_id');
			$control_group_id = phpgw::get_var('control_group_id');

			$control = $this->so_control->get_single($control_id);

			$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));

			$procedure = $this->so->get_single($procedure_id);

			$category = execMethod('phpgwapi.categories.return_single', $procedure->get_control_area_id());
			$procedure->set_control_area_name($category[0]['name']);

			$data = array
				(
				'location' => $location_array,
				'control' => $control->toArray(),
				'procedure' => $procedure->toArray(),
				'dateformat' => $this->dateformat
			);
			//var_dump($procedure->toArray());

			if (!empty($control_group_id))
			{
				$control_group = $this->so_control_group->get_single($control_group_id);
				$data['control_group'] = $control_group->toArray();
			}

			$GLOBALS['phpgw']->css->add_external_file('controller/templates/base/css/base.css');

			self::render_template_xsl('procedure/print_procedure', $data);
		}

		public function query()
		{
			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');

			$params = array(
				'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query' => $search['value'],
				'order' => $columns[$order[0]['column']]['data'],
				'sort' => $order[0]['dir'],
				'allrows' => phpgw::get_var('length', 'int') == -1,
			);


			$search_for = $params['query'];

			$start_index = $params['start'];
			$num_of_objects = $params['results'] > 0 ? $params['results'] : 0;
			$sort_field = ($params['order']) ? $params['order'] : '';
			$filters = array();

			$ctrl_area = phpgw::get_var('control_areas');
			if (isset($ctrl_area) && $ctrl_area > 0)
			{
				$filters['control_areas'] = $ctrl_area;
			}
			$sort_ascending = $params['sort'] == 'desc' ? false : true;
			// Form variables
			$search_type = phpgw::get_var('search_option', 'string', 'REQUEST', '');
			// Create an empty result set
			$result_objects = array();
			$result_count = 0;

			//Retrieve a contract identifier and load corresponding contract
			$procedure_id = phpgw::get_var('procedure_id');

			$exp_param = phpgw::get_var('export');
			$export = false;
			if (isset($exp_param) && $exp_param)
			{
				$export = true;
				$num_of_objects = null;
			}

			//Retrieve the type of query and perform type specific logic
			$query_type = phpgw::get_var('type');
			switch ($query_type)
			{
				default: // ... all composites, filters (active and vacant)
					phpgwapi_cache::session_set('controller', 'procedure_query', $search_for);
					//$filters = array();
					$result_objects = $this->so->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
					$object_count = $this->so->get_count($search_for, $search_type, $filters);
					break;
			}

			//Create an empty row set
			$rows = array();
			foreach ($result_objects as $result)
			{
				if (isset($result))
				{
					$rows[] = $result->serialize();
				}
			}

			foreach ($rows as &$row)
			{
				if(!empty($row['modified_by']))
				{
					$row['modified_by_name'] = $GLOBALS['phpgw']->accounts->id2name($row['modified_by']);
				}
				$row['modified_date'] = $GLOBALS['phpgw']->common->show_date($row['modified_date'],$this->dateformat);
			}

			// ... add result data
			$results = array('results' => $rows);

			$results['total_records'] = $object_count;
			$results['start'] = $params['start'];
			$results['sort'] = $params['order'];
			$results['dir'] = $params['sort'];
			$results['draw'] = $draw;

			if (!$export)
			{
				//Add action column to each row in result table
				array_walk(
					$results['results'], array($this, '_add_links'), "controller.uiprocedure.view");
			}

			return $this->jquery_results($results);
		}

		public function add_actions( &$value, $key, $params )
		{
			//Defining new columns
			$value['ajax'] = array();
			$value['actions'] = array();
			$value['labels'] = array();

			// Get parameters
			$procedure_id = $params[0];
			$editable = $params[1];

			// Depending on the type of query: set an ajax flag and define the action and label for each row
			switch ($type)
			{
				default:
					$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'controller.uiprocedure.view',
							'id' => $value['id'])));
					$value['labels'][] = lang('show');
					$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'controller.uiprocedure.edit',
							'id' => $value['id'])));
					$value['labels'][] = lang('edit');
			}
		}
	}