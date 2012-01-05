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
	phpgw::import_class('controller.socheck_list');

	include_class('controller', 'check_list', 'inc/model/');
	include_class('controller', 'check_item', 'inc/model/');

	class controller_uicheck_list extends controller_uicommon
	{
		private $so;
		private $so_control;
		private $so_control_group;
		private $so_control_group_list;
		private $so_control_item;
		private $so_check_list;
		private $so_check_item;

		public $public_functions = array
		(
			'index'	=>	true,
			'view_check_lists_for_control'		=>	true,
			'save_check_list'					=>	true,
			'view_check_list'					=>	true,
			'edit_check_list'					=>	true,
			'save_check_items'					=>	true,
			'save_check_item'					=>	true,
			'get_check_list_info'				=>	true,
			'control_calendar_status_overview'	=>	true,
			'add_check_item_to_list'			=>	true,
			'update_check_list'					=>	true,
			'view_control_items'				=>	true
		);

		public function __construct()
		{
			parent::__construct();

			$this->so = CreateObject('controller.socheck_list');
			$this->so_control = CreateObject('controller.socontrol');
			$this->so_control_group = CreateObject('controller.socontrol_group');
			$this->so_control_group_list = CreateObject('controller.socontrol_group_list');
			$this->so_control_item = CreateObject('controller.socontrol_item');
			$this->so_check_list = CreateObject('controller.socheck_list');
			$this->so_check_item = CreateObject('controller.socheck_item');

			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "controller::check_list";
		}

		public function index()
		{
/*			$check_list_array = $this->so->get_check_list();

			$data = array
			(
				'check_list_array'	=> $check_list_array
			);

			self::render_template_xsl('control_check_lists', $data);
			*/
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
								'name' => 'status',
								'text' => lang('Status'),
								'list' => array(
									array(
										'id' => 'none',
										'name' => lang('Not selected')
									), 
									array(
										'id' => 'NEW',
										'name' => lang('NEW')
									), 
									array(
										'id' => 'PENDING',
										'name' =>  lang('PENDING')
									), 
									array(
										'id' => 'REJECTED',
										'name' => lang('REJECTED')
									), 
									array(
										'id' => 'ACCEPTED',
										'name' => lang('ACCEPTED')
									)
								)
							),
							array('type' => 'text', 
								'text' => lang('searchfield'),
								'name' => 'query'
							),
							array(
								'type' => 'submit',
								'name' => 'search',
								'value' => lang('Search')
							),
						),
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'controller.uicheck_list.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'id',
							'label' => lang('ID'),
							'sortable'	=> true,
							'formatter' => 'YAHOO.portico.formatLink'
						),
						array(
							'key'	=>	'title',
							'label'	=>	lang('Control title'),
							'sortable'	=>	false
						),
						array(
							'key' => 'start_date',
							'label' => lang('start_date'),
							'sortable'	=> false
						),
						array(
							'key' => 'planned_date',
							'label' => lang('planned_date'),
							'sortable'	=> false
						),
						array(
							'key' => 'end_date',
							'label' => lang('end_date'),
							'sortable'	=> false
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

		public function view_check_list()
		{
			$check_list_id = phpgw::get_var('check_list_id');
			$check_list = $this->so_check_list->get_single_with_control_items($check_list_id);

			$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			$data = array
			(
				'check_list' => $check_list,
				'date_format' => $date_format
			);

			self::render_template_xsl('view_check_list', $data);
		}

		// Returns check list info as JSON
		public function get_check_list_info()
		{
			$check_list_id = phpgw::get_var('check_list_id');
			$check_list = $this->so_check_list->get_single_with_check_items($check_list_id, "open");
			
			return json_encode( $check_list );
		}

		public function edit_check_list()
		{
			$check_list_id = phpgw::get_var('check_list_id');
			$check_list = $this->so_check_list->get_single_with_control_items($check_list_id);

			$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			$data = array
			(
				'check_list' 	=> $check_list,
				'date_format' 	=> $date_format
			);

			self::render_template_xsl('edit_check_list', $data);
		}
		
		public function update_check_list(){
			$check_list_id = phpgw::get_var('check_list_id');
			$status = phpgw::get_var('status');
			$comment = phpgw::get_var('comment');
			$deadline_date = phpgw::get_var('deadline_date');
			$completed_date = phpgw::get_var('completed_date');
			$planned_date = phpgw::get_var('planned_date');
			
			$planned_date_ts = $this->get_timestamp_from_date( $planned_date ); 
			$completed_date_ts = $this->get_timestamp_from_date( $completed_date );
			
			// Fetches check_list from DB
			$update_check_list = $this->so_check_list->get_single($check_list_id);
			$update_check_list->set_status( $status );
			$update_check_list->set_comment( $comment );
			$update_check_list->set_completed_date( $completed_date_ts );
			$update_check_list->set_planned_date( $planned_date_ts );

			$check_list_id = $this->so_check_list->update( $update_check_list );
			
			if($check_list_id > 0)
				return json_encode( array( "saveStatus" => "updated" ) );
			else
				return json_encode( array( "saveStatus" => "not_updated" ) );
		}

		public function control_calendar_status_overview()
		{
			$control_id = phpgw::get_var('control_id');
			$control = $this->so_control->get_single($control_id);

			$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			$check_list_array = $this->so->get_check_lists_for_control( $control_id );

			$data = array
			(
				'control_as_array'	=> $control->toArray(),
				'check_list_array'	=> $check_list_array,
				'date_format' 		=> $date_format
			);

			self::render_template_xsl('control_calendar_status_overview', $data);
		}

		public function view_control_items(){
			$control_id = phpgw::get_var('control_id');
			
			$control = $this->so_control->get_single($control_id);
			
			$control_groups = $this->so_control_group_list->get_control_groups_by_control($control_id);
			
			$saved_groups_with_items_array = array();
			
			//Populating array with saved control items for each group
			foreach ($control_groups as $control_group)
			{	
				$saved_control_items = $this->so_control_item->get_control_items_by_control_id_and_group($control_id, $control_group->get_id());
				
				$control_item = $this->so_control_item->get_single($control_item_id);
				
				$saved_groups_with_items_array[] = array("control_group" => $control_group->toArray(), "control_items" => $saved_control_items);
			}
			
			$data = array
			(
				'saved_groups_with_items_array'	=> $saved_groups_with_items_array
			);
			
			$xslttemplate = CreateObject('phpgwapi.xslttemplates');
			
            $xslttemplate->add_file(array(PHPGW_SERVER_ROOT . '/controller/templates/base/control_item/view_control_items'));
           
            $xslttemplate->set_var('phpgw',array('view_control_items' => $data));
            
            $xslttemplate->xsl_parse();
	        $xslttemplate->xml_parse();
	
	        $xml = new DOMDocument;
	        $xml->loadXML($xslttemplate->xmldata);

	        $xsl = new DOMDocument;
	        $xsl->loadXML($xslttemplate->xsldata);

	        // Configure the transformer
	        $proc = new XSLTProcessor;
	        $proc->importStyleSheet($xsl); // attach the xsl rules
	
	        $html = $proc->transformToXML($xml);

	       	echo $html;
		}

		public function view_check_lists_for_control()
		{
			$control_id = phpgw::get_var('id');
			$control = $this->so_control->get_single($control_id);

			$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			$check_list_array = $this->so->get_check_lists_for_control( $control_id );

			$data = array
			(
				'control_as_array'	=> $control->toArray(),
				'check_list_array'	=> $check_list_array,
				'date_format' 		=> $date_format
			);

			self::render_template_xsl('view_check_lists', $data);
		}

		public function view_control_items_for_control()
		{
			$control_id = phpgw::get_var('control_id');
			
			$control = $this->so_control->get_single($control_id);
			$control_groups_array = $this->so_control_group_list->get_control_groups_by_control( $control_id );

			$saved_groups_with_items_array = array();

			foreach ($control_groups_array as $control_group)
			{
				$control_group_id = $control_group->get_id();
				$saved_control_items = $this->so_control_item->get_control_items_by_control_id_and_group($control_id, $control_group_id);

				$saved_groups_with_items_array[] = array("control_group" => $control_group->toArray(), "control_items" => $saved_control_items);
			}

			$data = array
			(
				'control_as_array'				=> $control->toArray(),
				'saved_groups_with_items_array'	=> $saved_groups_with_items_array
			);

			self::render_template_xsl('view_check_list', $data);
		}

		public function save_check_items(){
			$check_item_ids = phpgw::get_var('check_item_ids');
			$check_list_id = phpgw::get_var('check_list_id');

			foreach($check_item_ids as $check_item_id){
				$status = phpgw::get_var('status_' . $check_item_id);
				$comment = phpgw::get_var('comment_' . $check_item_id);

				$check_item = $this->so_check_item->get_single($check_item_id);

				$check_item->set_status( $status );
				$check_item->set_comment( $comment );

				$this->so_check_item->store( $check_item );
			}

			$this->redirect(array('menuaction' => 'controller.uicheck_list.view_check_list', 'check_list_id'=>$check_list_id));
		}
		
		public function save_check_item(){
			$check_item_id = phpgw::get_var('check_item_id');
			$comment = phpgw::get_var('comment');
			$status = phpgw::get_var('status');
						
			$check_item = $this->so_check_item->get_single($check_item_id);
			$check_item->set_status( $status );
			$check_item->set_comment( $comment );
			
			$check_item_id = $this->so_check_item->store( $check_item );

			if($status == 0)
				$status_text = "not_fixed";
			else
				$status_text = "fixed";
			
			if($check_item_id > 0)
				return json_encode( array( "saveStatus" => "saved", "fixedStatus" => $status_text ) );
			else
				return json_encode( array( "status" => "not_saved" ) );
		}
		
		public function add_check_item_to_list(){
			$control_item_id = phpgw::get_var('control_item_id');
			$check_list_id = phpgw::get_var('check_list_id');
			$comment = phpgw::get_var('comment');
			$status = phpgw::get_var('status');
			$type = phpgw::get_var('type');

			$check_item_obj = new controller_check_item();
			$check_item_obj->set_status($status);
			$check_item_obj->set_comment($comment);
			$check_item_obj->set_check_list_id($check_list_id);
			$check_item_obj->set_control_item_id($control_item_id);

			if($type == 'control_item_type_2'){
				$measurement = phpgw::get_var('measurement');
				$check_item_obj->set_measurement($measurement);
			}
			
			$check_item_id = $this->so_check_item->store( $check_item_obj );

			if($check_item_id > 0)
				return json_encode( array( "saveStatus" => "saved" ) );
			else
				return json_encode( array( "saveStatus" => "not_saved" ) );
		}

		public function save_check_list(){
			$control_id = phpgw::get_var('control_id');
			$control = $this->so_control->get_single($control_id);

			$start_date = $control->get_start_date();
			$end_date = $control->get_end_date();
			$repeat_type = $control->get_repeat_type();
			$repeat_interval = $control->get_repeat_interval();

			$status = "FALSE";
			$comment = "Kommentar for sjekkliste";
			$deadline = $start_date;

			// Saving check_list
			$new_check_list = new controller_check_list();
			$new_check_list->set_control_id( $control_id );
			$new_check_list->set_status( $status );
			$new_check_list->set_comment( $comment );
			$new_check_list->set_deadline( $deadline );

			$check_list_id = $this->so_check_list->store( $new_check_list );

			$control_items_list = $this->so_control_item->get_control_items_by_control_id($control_id);

			foreach($control_items_list as $control_item){

				$status = 0;
				$comment = "Kommentar for sjekk item";

				// Saving check_items for a list
				$new_check_item = new controller_check_item();
				$new_check_item->set_check_list_id( $check_list_id );

				$new_check_item->set_control_item_id( $control_item->get_id() );
				$new_check_item->set_status( $status );
				$new_check_item->set_comment( $comment );

				$saved_check_item = $this->so_check_item->store( $new_check_item );
			}

			$this->redirect(array('menuaction' => 'controller.uicheck_list.view_check_list_for_control', 'control_id'=>$control_id));
		}

		public function make_check_list_for_control(){
			$control_id = phpgw::get_var('control_id');
			$control = $this->so_control->get_single($control_id);

			$start_date = $control->get_start_date();
			$end_date = $control->get_end_date();
			$repeat_type = $control->get_repeat_type();
			$repeat_interval = $control->get_repeat_interval();

			$status = true;
			$comment = "Kommentar for sjekkliste";
			$deadline = $start_date;

			// Saving check_list
			$new_check_list = new controller_check_list();
			$new_check_list->set_control_id( $control_id );
			$new_check_list->set_status( $status );
			$new_check_list->set_comment( $comment );
			$new_check_list->set_deadline( $deadline );

			$check_list_id = $this->so_check_list->store( $new_check_list );

			$control_items_list = $this->so_control_item->get_control_items_by_control_id($control_id);

			foreach($control_items_list as $control_item){

				$status = true;
				$comment = "Kommentar for sjekk item";

				// Saving check_items for a list
				$new_check_item = new controller_check_item();
				$new_check_item->set_check_list_id( $check_list_id );

				$new_check_item->set_control_item_id( $control_item->get_id() );
				$new_check_item->set_status( $status );
				$new_check_item->set_comment( $comment );

				$saved_check_item = $this->so_check_item->store( $new_check_item );
			}

			$this->redirect(array('menuaction' => 'controller.uicheck_list.view_check_list_for_control', 'control_id'=>$control_id));
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

			$search_for = phpgw::get_var('query');

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
			if($sort_field == null)
			{
				$sort_field = 'control_id';
			}
			$sort_ascending	= phpgw::get_var('dir') == 'desc' ? false : true;
			//Create an empty result set
			$records = array();

			//Retrieve a contract identifier and load corresponding contract
/*			$control_id = phpgw::get_var('control_id');
			if(isset($control_id))
			{
				$control = $this->so->get_single($control_id);
			}
*/
			$result_objects = $this->so->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
			$object_count = $this->so->get_count($search_for, $search_type, $filters);
			//var_dump($result_objects);

			$results = array();

			foreach($result_objects as $check_list_obj)
			{
				$results['results'][] = $check_list_obj->serialize();
			}

			$results['total_records'] = $object_count;
			$results['start'] = $params['start'];
			$results['sort'] = $params['sort'];
			$results['dir'] = $params['dir'];

			array_walk($results["results"], array($this, "_add_links"), "controller.uicheck_list.view_check_lists_for_control");

			return $this->yui_results($results);
		}
		
		function get_timestamp_from_date( $date_string ){
			$pos_day = strpos($date_string, "/"); 
			$day =  substr($date_string, 0, $pos_day);
			
			$pos_month = strpos($date_string, "-");
			$len_month = $pos_month - $pos_day -1;
			$month = substr($date_string, $pos_day+1, $len_month);
			
			$year = substr($date_string, $pos_month + $len_month-1, strlen($date_string)-1);
			
			return mktime(0, 0, 0, $month, $day, $year);
		}
	}
