<?php
	/**
	* phpGroupWare - controller: a part of a Facilities Management System.
	*
	* @author Erink Holm-Larsen <erik.holm-larsen@bouvet.no>
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

	phpgw::import_class('controller.uicommon');
	phpgw::import_class('controller.soprocedure');
	phpgw::import_class('controller.socontrol_area');

	include_class('controller', 'procedure', 'inc/model/');

	class controller_uiprocedure extends controller_uicommon
	{
		private $so;
		private $so_control_area;

		public $public_functions = array
		(
			'index'				=>	true,
			'query'				=>	true,
			'edit'				=>	true,
			'view'				=>	true,
			'add'				=>	true,
			'get_procedures'	=>	true
		);

		public function __construct()
		{
			parent::__construct();

			$this->so = CreateObject('controller.soprocedure');
			$this->so_control_area = CreateObject('controller.socontrol_area');
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "controller::procedure";
			//$this->bo = CreateObject('property.boevent',true);
		}

		public function index()
		{
			//self::set_active_menu('controller::control_item2::control_item_list2');
			if(phpgw::get_var('phpgw_return_as') == 'json') {
				return $this->query();
			}
			self::add_javascript('controller', 'yahoo', 'datatable.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');

			$data = array(
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array('type' => 'filter',
								'name' => 'control_areas',
								'text' => lang('Control_area').':',
								'list' => $this->so_control_area->get_control_area_select_array(),
							),
							array('type' => 'text', 
								'text' => lang('search'),
								'name' => 'query'
							),
							array(
								'type' => 'submit',
								'name' => 'search',
								'value' => lang('Search')
							),
							array(
								'type' => 'link',
								'value' => lang('t_new_procedure'),
								'href' => self::link(array('menuaction' => 'controller.uiprocedure.add')),
								'class' => 'new_item'
							),
						),
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'controller.uiprocedure.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'id',
							'label' => lang('ID'),
							'sortable'	=> true,
							'formatter' => 'YAHOO.portico.formatLink'
						),
						array(
							'key' => 'title',
							'label' => lang('Procedure title'),
							'sortable'	=> false
						),
						array(
							'key' => 'purpose',
							'label' => lang('Procedure purpose'),
							'sortable'	=> false
						),
						array(
							'key' => 'control_area',
							'label' => lang('Control area'),
							'sortable'	=> false
						),
						array(
							'key' => 'revision_date',
							'label' => lang('Procedure revision date'),
							'sortable'	=> true
						),
						array(
							'key' => 'link',
							'hidden' => true
						)
					)
				),
			);
//_debug_array($data);

			self::render_template_xsl('datatable', $data);
		}

		public function edit()
		{
			$procedure_id = phpgw::get_var('id');
			if(isset($procedure_id) && $procedure_id > 0)
			{
				$procedure = $this->so->get_single($procedure_id);
			}
			else
			{
				$procedure = new controller_procedure();
			}


			if(isset($_POST['save_procedure'])) // The user has pressed the save button
			{
				if(isset($procedure)) // Edit procedure
				{
					$procedure->set_title(phpgw::get_var('title'));
					$procedure->set_purpose(phpgw::get_var('purpose','html'));
					$procedure->set_responsibility(phpgw::get_var('responsibility'));
					$procedure->set_description(phpgw::get_var('description','html'));
					$procedure->set_reference(phpgw::get_var('reference'));
					$procedure->set_attachment(phpgw::get_var('attachment'));
					$procedure->set_start_date(strtotime(phpgw::get_var('start_date_hidden')));
					$procedure->set_end_date(strtotime(phpgw::get_var('end_date_hidden')));
					$procedure->set_revision_date(strtotime(phpgw::get_var('revision_date_hidden')));
					$procedure->set_control_area_id(phpgw::get_var('control_area'));

					$revision = (int)$procedure->get_revision_no();
					if($revision && is_numeric($revision) && $revision > 0)
					{
						$procedure->set_revision_no($revision);
					}
					else
					{
						$procedure->set_revision_no(1);
					}

					if(isset($procedure_id) && $procedure_id > 0)
					{
						$proc_id = $procedure_id;
						if($this->so->store($procedure))
						{
							$message = lang('messages_saved_form');
						}
						else
						{
							$error = lang('messages_form_error');
						}
					}
					else
					{
						$proc_id = $this->so->add($procedure);
						if($proc_id)
						{
							$message = lang('messages_saved_form');
						}
						else
						{
							$error = lang('messages_form_error');
						}
					}
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uiprocedure.view', 'id' => $proc_id));
				}
			}
			else if(isset($_POST['revisit_procedure'])) // The user has pressed the revisit button
			{
				$old_procedure = $this->so->get_single($procedure_id);
				if(isset($procedure)) // Edit procedure
				{
					$revision = (int)$procedure->get_revision_no();
					if($revision && is_numeric($revision))
					{
						$revision++;
						$procedure->set_revision_no($revision);
					}
					else
					{
						$procedure->set_revision_no(2);
					}
					$procedure->set_title(phpgw::get_var('title'));
					$procedure->set_purpose(phpgw::get_var('purpose','html'));
					$procedure->set_responsibility(phpgw::get_var('responsibility'));
					$procedure->set_description(phpgw::get_var('description','html'));
					$procedure->set_reference(phpgw::get_var('reference'));
					$procedure->set_attachment(phpgw::get_var('attachment'));
					$procedure->set_start_date(strtotime(phpgw::get_var('start_date_hidden')));
					$procedure->set_end_date(strtotime(phpgw::get_var('end_date_hidden')));
					$procedure->set_control_area_id(phpgw::get_var('control_area'));

					if(isset($procedure_id) && $procedure_id > 0)
					{
						$proc_id = $procedure_id;
						$old_procedure->set_id(null);
						$old_procedure->set_end_date(time());
						$old_procedure->set_procedure_id($proc_id);
						if($this->so->add($old_procedure)) //add old revision of procedure to history
						{
							if($this->so->store($procedure))
							{
								$message = lang('messages_saved_form');
							}
							else
							{
								$error = lang('messages_form_error');
							}
						}
					}

					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uiprocedure.view', 'id' => $proc_id));
				}
			}
			else if(isset($_POST['cancel_procedure'])) // The user has pressed the cancel button
			{
				if(isset($procedure_id) && $procedure_id > 0)
				{
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uiprocedure.view', 'id' => $procedure_id));
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uiprocedure.index'));
				}
			}
			else
			{
				if($this->flash_msgs)
				{
					$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($this->flash_msgs);
					$msgbox_data = $GLOBALS['phpgw']->common->msgbox($msgbox_data);
				}
				$control_area_array = $this->so_control_area->get_control_area_array();
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
				$procedure_array = $procedure->toArray();
				//_debug_array($procedure_array);

				$data = array
				(
					'value_id'				=> !empty($procedure) ? $procedure->get_id() : 0,
					'start_date'			=> $GLOBALS['phpgw']->yuical->add_listener('start_date',date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], ($procedure->get_start_date())?$procedure->get_start_date():time())),
					'end_date'				=> $GLOBALS['phpgw']->yuical->add_listener('end_date',date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], ($procedure->get_end_date())?$procedure->get_end_date():'')),
					'revision_date'			=> $GLOBALS['phpgw']->yuical->add_listener('revision_date',date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], ($procedure->get_revision_date())?$procedure->get_revision_date():'')),
					'img_go_home'			=> 'rental/templates/base/images/32x32/actions/go-home.png',
					'editable' 				=> true,
					'procedure'				=> $procedure_array,
					'control_area'				=> array('options' => $control_area_options),
				);


				$GLOBALS['phpgw_info']['flags']['app_header'] = lang('controller') . '::' . lang('Procedure');

				$this->use_yui_editor(array('responsibility','description'));

				self::render_template_xsl('procedure_item', $data);
			}
		}

		// Returns check list info as JSON
		public function get_procedures()
		{
			$control_area_id = phpgw::get_var('control_area_id');

			$procedures_array = $this->so->get_procedures_by_control_area_id($control_area_id);

			if(count($procedures_array)>0)
				return json_encode( $procedures_array );
			else
				return null;
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
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::'.lang('view');
			$view_revision = phpgw::get_var('view_revision');
			//Retrieve the procedure object
			$procedure_id = (int)phpgw::get_var('id');
			if(isset($_POST['edit_procedure']))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uiprocedure.edit', 'id' => $procedure_id));
			}
			else
			{
				if(isset($procedure_id) && $procedure_id > 0)
				{
					$procedure = $this->so->get_single($procedure_id);
				}
				else
				{
					$this->render('permission_denied.php',array('error' => lang('invalid_request')));
					return;
				}

				if($this->flash_msgs)
				{
					$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($this->flash_msgs);
					$msgbox_data = $GLOBALS['phpgw']->common->msgbox($msgbox_data);
				}

				$procedure_array = $procedure->toArray();
				if($procedure->get_start_date() && $procedure->get_start_date() != null)
					$procedure_start_date = date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], $procedure->get_start_date());
				if($procedure->get_end_date() && $procedure->get_end_date() != null)
					$procedure_end_date	= date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], $procedure->get_end_date());
				if($procedure->get_revision_date() && $procedure->get_revision_date() != null)
					$procedure_revision_date	= date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], $procedure->get_revision_date());
				//_debug_array($procedure_array);

				if(!$view_revision)
				{
					$table_header[] = array('header' => lang('Procedure revision'));
					$table_header[] = array('header' => lang('Procedure title'));
					$table_header[] = array('header' => lang('Procedure start date'));
					$table_header[] = array('header' => lang('Procedure end date'));

					$revised_procedures = $this->so->get_old_revisions($procedure->get_id());
					//var_dump($revised_procedures);
					foreach($revised_procedures as $rev)
					{
						$rev['link'] = self::link(array('menuaction' => 'controller.uiprocedure.view', 'id' => $rev['id'], 'view_revision' => 'yes'));
						$table_values[] = array('row' => $rev);
					}
				}
				
				$tabs = array( array(
							'label' => lang('Procedure')

						), array(
							'label' => lang('View_documents_for_procedure'),
							'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uidocument.show', 'procedure_id' => $procedure->get_id(), 'type' => 'procedure'))
						));

				$data = array
				(
					'tabs'					=> $GLOBALS['phpgw']->common->create_tabs($tabs, 0),
					'view'					=> "view_procedure",
					'value_id'				=> !empty($procedure) ? $procedure->get_id() : 0,
					'img_go_home'			=> 'rental/templates/base/images/32x32/actions/go-home.png',
					'procedure'				=> $procedure_array,
					'dateformat'			=> $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'],
					'values'				=> $table_values,
					'table_header'			=> $table_header,
				);

				if($procedure->get_end_date())
				{
					$data['inactive'] = true;
				}


				$GLOBALS['phpgw_info']['flags']['app_header'] = lang('controller') . '::' . lang('Procedure');

				//self::render_template_xsl('procedure_item', $data);
				self::render_template_xsl(array('procedure_tabs', 'common', 'procedure_item'), $data);
			}
		}

		public function query()
		{
			$params = array(
				'start' => phpgw::get_var('startIndex', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('results', 'int', 'REQUEST', null),
				'query'	=> phpgw::get_var('query'),
				'sort'	=> phpgw::get_var('sort'),
				'dir'	=> phpgw::get_var('dir'),
				'filters' => $filters
			);

			$ctrl_area = phpgw::get_var('control_areas');
			if(isset($ctrl_area) && $ctrl_area > 0)
			{
				$filters['control_areas'] = $ctrl_area; 
			}

			if($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] > 0)
			{
				$user_rows_per_page = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else {
				$user_rows_per_page = 10;
			}
			// YUI variables for paging and sorting
			$start_index	= phpgw::get_var('startIndex', 'int');
			$num_of_objects	= phpgw::get_var('results', 'int', 'GET', $user_rows_per_page);
			$sort_field		= phpgw::get_var('sort');
			$sort_ascending	= phpgw::get_var('dir') == 'desc' ? false : true;
			// Form variables
			$search_for 	= phpgw::get_var('query');
			$search_type	= phpgw::get_var('search_option');
			// Create an empty result set
			$result_objects = array();
			$result_count = 0;

			//Retrieve a contract identifier and load corresponding contract
			$procedure_id = phpgw::get_var('procedure_id');

			$exp_param 	= phpgw::get_var('export');
			$export = false;
			if(isset($exp_param)){
				$export=true;
				$num_of_objects = null;
			}

			//Retrieve the type of query and perform type specific logic
			$query_type = phpgw::get_var('type');
			switch($query_type)
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
			foreach($result_objects as $result) {
				if(isset($result))
				{
					$rows[] = $result->serialize();
				}
			}

			// ... add result data
			$result_data = array('results' => $rows);

			$result_data['total_records'] = $object_count;
			$result_data['start'] = $params['start'];
			$result_data['sort'] = $params['sort'];
			$result_data['dir'] = $params['dir'];

			$editable = phpgw::get_var('editable') == 'true' ? true : false;

			if(!$export){
				//Add action column to each row in result table
				array_walk(
					$result_data['results'],
					array($this, '_add_links'),
					"controller.uiprocedure.view");
			}
//_debug_array($result_data);
			return $this->yui_results($result_data);

		}

			public function add_actions(&$value, $key, $params)
		{
			//Defining new columns
			$value['ajax'] = array();
			$value['actions'] = array();
			$value['labels'] = array();

			// Get parameters
			$procedure_id = $params[0];
			$editable = $params[1];

			// Depending on the type of query: set an ajax flag and define the action and label for each row
			switch($type)
			{
				default:
					$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'controller.uiprocedure.view', 'id' => $value['id'])));
					$value['labels'][] = lang('show');
					$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'controller.uiprocedure.edit', 'id' => $value['id'])));
					$value['labels'][] = lang('edit');
			}
		}
	}
