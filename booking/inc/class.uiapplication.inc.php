<?php
	phpgw::import_class('booking.uicommon');
	phpgw::import_class('booking.account_helper');

	phpgw::import_class('booking.uidocument_building');
	phpgw::import_class('booking.uipermission_building');
	phpgw::import_class('booking.sobuilding');
	phpgw::import_class('booking.boapplication');

//	phpgw::import_class('phpgwapi.uicommon_jquery');

	class booking_uiapplication extends booking_uicommon
	{

		const COMMENT_TYPE_OWNERSHIP = 'ownership';
		const ORGNR_SESSION_KEY = 'orgnr';

		public $public_functions	 = array(
			'index'						 => true,
			'query'						 => true,
			'add'						 => true,
			'confirm'					 => true,
			'show'						 => true,
			'edit'						 => true,
			'associated'				 => true,
			'toggle_show_inactive'		 => true,
			'custom_fields_example'		 => true,
			'export_pdf'				 => true,
			'add_comment_to_application' => true,
			'payments'					 => true,
			'cancel_payment'			 => true,
			'refund_payment'			 => true,
			'get_purchase_order'		 => true,
			'delete'					 => true,
			'get_activity_data'			 => true,
			'get_applications'			 => true
		);
		protected $customer_id,
			$default_module = 'bookingfrontend',
			$module;
		protected $application_bo;
		protected $building_so;
		protected $errors = array();
		private $acl_delete;
		var $event_bo, $activity_bo,$audience_bo,$assoc_bo,$agegroup_bo,$resource_bo,$building_bo,$organization_bo,
		$document_building,$document_resource,$fields,$display_name;

		public function __construct()
		{
			parent::__construct();

			phpgwapi_jquery::load_widget('autocomplete');

			$this->set_module();
//			Analizar esta linea self::process_booking_unauthorized_exceptions();
			$this->bo				 = CreateObject('booking.boapplication');
			$this->customer_id		 = CreateObject('booking.customer_identifier');
			$this->event_bo			 = CreateObject('booking.boevent');
			$this->activity_bo		 = CreateObject('booking.boactivity');
			$this->audience_bo		 = CreateObject('booking.boaudience');
			$this->assoc_bo			 = new booking_boapplication_association();
			$this->agegroup_bo		 = CreateObject('booking.boagegroup');
			$this->resource_bo		 = CreateObject('booking.boresource');
			$this->building_bo		 = CreateObject('booking.bobuilding');
			$this->organization_bo	 = CreateObject('booking.boorganization');
			$this->document_building = CreateObject('booking.bodocument_building');
			$this->document_resource = CreateObject('booking.bodocument_resource');
			$this->building_so		 = new booking_sobuilding();
			$this->application_bo	 = new booking_boapplication();
			$this->acl_delete		 = $GLOBALS['phpgw']->acl->check('.application', PHPGW_ACL_DELETE, 'booking');

			self::set_active_menu('booking::applications::applications');
			$this->fields = array(
				'formstage' => 'string',
				'name' => 'string',
				'organizer' => 'string',
				'homepage' => 'string',
				'description' => 'string',
				'equipment' => 'string',
				'resources' => 'string',
				'activity_id' => 'string',
				'building_id' => 'string',
				'building_name' => 'string',
				'contact_name' => 'string',
				'contact_email' => 'string',
				'contact_phone' => 'string',
				'audience' => 'string',
				'active' => 'string',
				'accepted_documents' => 'string',
				'responsible_street' => 'string',
				'responsible_zip_code' => 'string',
				'responsible_city' => 'string',
				'agreement_requirements' => 'html',
				'customer_organization_id' => 'string',
				'customer_organization_name' => 'string',
				'customer_identifier_type' => 'string'
			);

			$this->display_name = lang('application');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('booking') . "::{$this->display_name}";
		}

		public function get_applications()
		{
			if(!$GLOBALS['phpgw']->acl->check('.application', PHPGW_ACL_READ, 'booking'))
			{
				phpgw::no_access();
			}
			$ssn =  phpgw::get_var('ssn', 'string');
			$application_data = CreateObject('booking.souser')->get_applications($ssn);
			return $application_data;
		}


		// --- SIGURD::START EXAMPLE -- //

		/**
		 * Example on how to retrieve custom fields organized in nested groups
		 */
		public function custom_fields_example()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;

			$locations = $GLOBALS['phpgw']->locations->get_locations($grant = false, 'booking', $allow_c_attrib = true, $allow_c_functions = false);
			echo "Available locations within Booking:</br>";

			foreach ($locations as $location => $name)
			{
				if (!preg_match('/(^.application|^.resource)/i', $location))
				{
					continue;
				}

				echo "<b>{$location}::{$name}:</b></br>";
				echo "Custom fields:</br>";

				// THIS ONE IS WHAT YOU WANT
				$organized_fields = $this->get_attributes($location);

				$url_acl = self::link(array('menuaction' => 'preferences.uiadmin_acl.list_acl',
						'acl_app' => 'booking', 'module' => $location, 'cat_id' => 'groups'));
				$url_groups = self::link(array('menuaction' => 'admin.ui_custom.list_attribute_group',
						'appname' => 'booking', 'location' => $location, 'menu_selection' => 'booking::settings::custom_field_groups'));
				$url_fields = self::link(array('menuaction' => 'admin.ui_custom.list_attribute',
						'appname' => 'booking', 'location' => $location, 'menu_selection' => 'booking::settings::custom_field_groups'));

				if (count($organized_fields) > 1)
				{
					_debug_array($organized_fields);
					echo "<a href='{$url_groups}'>Make more groups here</a></br>";
					echo "<a href='{$url_fields}'>And fields here</a></br>";
				}
				else
				{
					echo "No custom fields is defined... yet..</br>";
					echo "<a href='{$url_acl}'>Define your acl rights here</a></br>";
					echo "<a href='{$url_groups}'>Make groups here</a></br>";
					echo "<a href='{$url_fields}'>And fields here</a></br>";
				}
			}
		}

		/**
		 *
		 * @param type $location
		 * @return  array the grouped attributes
		 */
		private function get_attributes( $location )
		{
			$appname = 'booking';
			$attributes = $GLOBALS['phpgw']->custom_fields->find($appname, $location, 0, '', 'ASC', 'attrib_sort', true, true);
			return $this->get_attribute_groups($appname, $location, $attributes);
		}

		/**
		 * Arrange attributes within groups
		 *
		 * @param string  $location    the name of the location of the attribute
		 * @param array   $attributes  the array of the attributes to be grouped
		 *
		 * @return array the grouped attributes
		 */
		private function get_attribute_groups( $appname, $location, $attributes = array() )
		{
			return $GLOBALS['phpgw']->custom_fields->get_attribute_groups($appname, $location, $attributes);
		}

		// --- END EXAMPLE -- //

		protected function set_module( $module = null )
		{
			$this->module = is_string($module) ? $module : $this->default_module;
		}

		public function get_module()
		{
			return $this->module;
		}

		protected function is_assigned_to_current_user( &$application )
		{
			$current_account_id = $this->current_account_id();
			if (empty($current_account_id) || !isset($application['case_officer_id']))
			{
				return false;
			}
			return $application['case_officer_id'] == $current_account_id;
		}

		protected function check_application_assigned_to_current_user( &$application )
		{
			if (!$this->is_assigned_to_current_user($application))
			{
				throw new booking_unauthorized_exception('write', 'current user is not assigned to application');
			}

			return true;
		}

		protected function assign_to_current_user( &$application )
		{
			$current_account_id = $this->current_account_id();

			if (!empty($current_account_id) && is_array($application) &&
				!isset($application['case_officer_id']) || $application['case_officer_id'] != $current_account_id)
			{
				$application['case_officer_id'] = $current_account_id;
				$this->add_ownership_change_comment($application, sprintf(lang("User '%s' was assigned"), $this->current_account_fullname()));
				return true;
			}

			return false;
		}

		protected function unassign_current_user( &$application )
		{
			$current_account_id = $this->current_account_id();

			if (!empty($current_account_id) && is_array($application) && array_key_exists('case_officer_id', $application) && $application['case_officer_id'] == $current_account_id)
			{
				$application['case_officer_id'] = null;
				$this->add_ownership_change_comment($application, sprintf(lang("User '%s' was unassigned"), $this->current_account_fullname()));
				return true;
			}

			return false;
		}

		protected function assign_to_new_user( &$application, $account_id )
		{
			if (!empty($account_id) && is_array($application) &&
				!isset($application['case_officer_id']) || $application['case_officer_id'] != $account_id)
			{
				$application['case_officer_id'] = $account_id;
				$case_officer_full_name = $GLOBALS['phpgw']->accounts->get($application['case_officer_id'])->__toString();
				$this->add_ownership_change_comment($application, sprintf(lang("User '%s' was assigned"), $case_officer_full_name));
				return true;
			}

			return false;
		}

		protected function set_display_in_dashboard( &$application, $bool, $options = array() )
		{
			if (!is_bool($bool) || $application['display_in_dashboard'] === $bool)
			{
				return false;
			}
			$options = array_merge(
				array('force' => false), $options
			);

			if ($options['force'] === false &&
				(!isset($application['case_officer_id']) || $application['case_officer_id'] != $this->current_account_id())
			)
			{
				return false;
			}

			$application['display_in_dashboard'] = ($bool === true ? 1 : 0);
			return true;
		}

		protected function is_assigned_to(&$application)
		{
			if(!empty($application['case_officer_id']))
			{
				$application['case_officer_full_name'] = $GLOBALS['phpgw']->accounts->get($application['case_officer_id'])->__toString();
			}
		}

		function add_comment_to_application($application_id, $comment, $changeStatus = null, $customer_name = null)
		{
			$application = $this->application_bo->read_single($application_id);

			$this->add_comment($application, $comment, 'comment', $customer_name);
			$this->set_display_in_dashboard($application, true, array('force' => true));
			$application['frontend_modified'] = 'now';

			if ($changeStatus && $application['status'] != $changeStatus)
			{
				$application['status'] = $changeStatus;
				$log_msg = "Status: ". strtolower(lang($application['status']));
				$this->add_comment($application, $log_msg, 'comment', $customer_name);
			}

			$this->bo->send_admin_notification($application, $comment);
			$this->bo->update($application);
		}


		protected function add_comment( &$application, $comment, $type = 'comment' , $customer_name = null)
		{
			$application['comments'][] = array(
				'time' => 'now',
				'author' => $customer_name ? $customer_name : $this->current_account_fullname(),
				'comment' => $comment,
				'type' => $type
			);
		}

		protected function add_ownership_change_comment( &$application, $comment )
		{
			$this->add_comment($application, $comment, self::COMMENT_TYPE_OWNERSHIP);
		}

		/**
		 * Filters application comments based on their types.
		 *
		 *
		 */
		protected function filter_application_comments( array &$application, array $types )
		{
			$types = array_fill_keys($types, true); //Convert to associative array with types as keys and values as true

			if (count($types) == 0 || !array_key_exists('comments', $application) || !is_array($application['comments']))
			{
				return;
			}

			$filtered_comments = array();
			foreach ($application['comments'] as &$comment)
			{
				isset($types[$comment['type']]) AND $filtered_comments[] = $comment;
			}
			$application['comments'] = $filtered_comments;
		}

		function _get_user_list()
		{

			$user_list = $this->bo->so->get_user_list();
			array_unshift($user_list, array(
				'id'		 => -1 * $this->current_account_id(),
				'name'		 => lang('My assigned applications'),
				'selected'	 => 1
			));

			return $user_list;
		}

		private function _get_filters()
		{
			$filters = array();

			$filters[]	 = array(
				'type'	 => 'filter',
				'name'	 => 'status',
				'text'	 => lang('Status') . ':',
				'list'	 => array(
					array(
						'id'	 => 'none',
						'name'	 => lang('Not selected')
					),
					array(
						'id'		 => 'NEW',
						'name'		 => lang('NEW'),
						'selected'	 => 1
					),
					array(
						'id'	 => 'PENDING',
						'name'	 => lang('PENDING')
					),
					array(
						'id'	 => 'REJECTED',
						'name'	 => lang('REJECTED')
					),
					array(
						'id'	 => 'ACCEPTED',
						'name'	 => lang('ACCEPTED')
					)
				)
			);
			$filters[]	 = array(
				'type'				 => 'autocomplete',
				'name'				 => 'building',
				'ui'				 => 'building',
				'text'				 => lang('Building') . ':',
				'onItemSelect'		 => 'updateBuildingFilter',
				'onClearSelection'	 => 'clearBuildingFilter'
			);
			$filters[]	 = array(
				'type'	 => 'filter',
				'name'	 => 'activities',
				'text'	 => lang('Activity') . ':',
				'list'	 => $this->bo->so->get_activities_main_level(),
			);
//			if (!isset($GLOBALS['phpgw_info']['user']['apps']['admin']) && !$this->bo->has_role(booking_sopermission::ROLE_MANAGER))
			{
				$filters[] = array(
					'type'		 => 'filter',
					'multiple'	 => true,
					'name'		 => 'filter_case_officer_id',
					'text'		 => lang('case officer') . ':',
					'list'		 => $this->_get_user_list(),
				);
			}
			return $filters;
		}

		public function index()
		{
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}
			phpgwapi_jquery::load_widget('autocomplete');
			phpgwapi_jquery::load_widget('bootstrap-multiselect');

			$data = array(
				'datatable_name' => $this->display_name,
				'form' => array(
					'toolbar' => array(
						'item' => array(),
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'booking.uiapplication.index',
						'phpgw_return_as' => 'json')),
					'sorted_by' => array('key' => 4, 'dir' => 'desc'),//created
					'field' => array(
						array(
							'key' => 'id',
							'label' => lang('ID'),
							'formatter' => 'JqueryPortico.formatLink'
						),
						array(
							'key' => 'status',
							'label' => lang('Status')
						),
						array(
							'key' => 'building_name',
							'label' => lang('Building')
						),
						array(
							'key' => 'resource_names',
							'label' => lang('resources'),
							'sortable' => false
						),
						array(
							'key' => 'created',
							'label' => lang('Created')
						),
						array(
							'key' => 'modified',
							'label' => lang('last modified')
						),
						array(
							'key' => 'from_',
							'label' => lang('From'),
							'sortable' => false
						),
						array(
							'key' => 'activity_name',
							'label' => lang('Activity')
						),
						array(
							'key' => 'contact_name',
							'label' => lang('Contact')
						),
						array(
							'key' => 'case_officer_name',
							'label' => lang('case officer'),
							'sortable' => false
						),

						array(
							'key' => 'link',
							'hidden' => true
						)
					)
				),
			);

			$filters = $this->_get_filters();

			foreach ($filters as $filter)
			{
				$data['form']['toolbar']['item'][] = $filter;
			}

			$parameters			 = array(
				'parameter' => array(
					array(
						'name'	 => 'id',
						'source' => 'id'
					),
				)
			);

			if ($this->acl_delete)
			{
				$data['datatable']['actions'][] = array
					(
					'my_name'		 => 'delete',
					'statustext'	 => lang('delete application'),
					'text'			 => lang('delete'),
					'confirm_msg'	 => lang('do you really want to delete this application'),
					'action'		 => $GLOBALS['phpgw']->link('/index.php', array(
						'menuaction' => 'booking.uiapplication.delete'
					)),
					'parameters'	 => json_encode($parameters)
				);
			}
			else
			{
				$data['datatable']['actions'][] = array();
			}

			$data['datatable']['new_item'] = self::link(array('menuaction' => 'booking.uiapplication.add'));

			self::render_template_xsl('datatable_jquery', $data);
		}

		public function query()
		{
			$filters = array();
			$building_id = phpgw::get_var('filter_building_id', 'int', 'REQUEST', null);
			$case_officer_id = phpgw::get_var('filter_case_officer_id', 'int');

			$filter_id_sql = $this->bo->accessable_applications(!empty($case_officer_id) ? array_map('abs', $case_officer_id) : null, $building_id);

			$filters['where'] = "(bb_application.id IN ({$filter_id_sql}))";

			$activity_id = phpgw::get_var('activities', 'int', 'REQUEST', null);
			if ($activity_id)
			{
				$filters['activity_id'] = $this->bo->so->get_activities($activity_id);
			}
			else
			{
				unset($filters['activity_id']);
			}
			$filters['status'] = 'NEW';

			$test = phpgw::get_var('status', 'string', 'REQUEST', null);
			if (phpgw::get_var('status') == 'none')
			{
				$filters['status'] = array('NEW', 'PENDING', 'REJECTED', 'ACCEPTED');
			}
			elseif (isset($test))
			{
				$filters['status'] = $test;
			}
			else
			{
				$filters['status'] = 'NEW';
			}


			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$columns = phpgw::get_var('columns');

			$params = array(
				'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('length', 'int', 'REQUEST', null),
				'query' => $search['value'],
				'sort' => $columns[$order[0]['column']]['data'],
				'dir' => $order[0]['dir'],
				'filters' => $filters
			);

			$applications = $this->bo->so->read($params);
//                        var_dump($params);
//                        exit();

			foreach ($applications['results'] as &$application)
			{
				if (strstr($application['building_name'], "%"))
				{
					$search = array('%2C', '%C3%85', '%C3%A5', '%C3%98', '%C3%B8', '%C3%86',
						'%C3%A6');
					$replace = array(',', 'Å', 'å', 'Ø', 'ø', 'Æ', 'æ');
					$application['building_name'] = str_replace($search, $replace, $application['building_name']);
				}

				if($application['case_officer_id'])
				{
					$application['case_officer_name'] = $GLOBALS['phpgw']->accounts->get($application['case_officer_id'])->__toString();
				}

				$dates = array();
				foreach ($application['dates'] as $data)
				{
					$dates[] = $data['from_'];
					break;
				}
				$fromdate = implode(',', $dates);
				$application['from_'] = pretty_timestamp($fromdate);
				$application['status'] = lang($application['status']);
				$application['created'] = pretty_timestamp($application['created']);
				$application['modified'] = pretty_timestamp($application['modified']);
				$application['frontend_modified'] = pretty_timestamp($application['frontend_modified']);
				$resources = $this->resource_bo->so->read(array('results' =>'all', 'filters' => array(
						'id' => $application['resources'])));

				$resource_names = array();

				if ($resources['results'])
				{
					foreach ($resources['results'] as $resource)
					{
						$resource_names[] = $resource['name'];
					}
				}
				$application['resource_names'] = implode(', ', $resource_names);
			}
			array_walk($applications["results"], array($this, "_add_links"), "booking.uiapplication.show");

			return $this->jquery_results($applications);
		}

		public function associated()
		{
			$application_id = phpgw::get_var('filter_application_id', 'int');
			$application = $this->bo->read_single($application_id);
			$case_officer = false;

			if ($this->is_assigned_to_current_user($application))// || $GLOBALS['phpgw']->acl->check('admin', phpgwapi_acl::ADD, 'booking'))
			{
				$case_officer = true;
			}

			$associations = $this->assoc_bo->read();
			foreach ($associations['results'] as &$association)
			{
				if ($GLOBALS['phpgw_info']['flags']['currentapp'] == 'bookingfrontend')
				{
					unset($association['cost']);
				}
				$association['from_'] = pretty_timestamp($association['from_']);
				$association['to_'] = pretty_timestamp($association['to_']);
				$association['link'] = self::link(array('menuaction' => 'booking.ui' . $association['type'] . '.edit',
						'id' => $association['id']));
				$association['dellink'] = $case_officer ? self::link(array('menuaction' => 'booking.ui' . $association['type'] . '.delete',
						'id' => $association['id'], 'application_id' => $association['application_id'])) : '';
				$association['type'] = lang($association['type']);
			}
			return $associations;
		}

		public function payments()
		{
			$application_id	 = phpgw::get_var('application_id', 'int');

			$params = array(
				'application_id' => $application_id,
				'sort' => phpgw::get_var('sort', 'string'),
				'dir' => phpgw::get_var('dir', 'string')
			);
			$payments		 = $this->bo->so->get_application_payments($params);

			$status_text = array(
				'completed'			 => lang('completed'),
				'new'				 => lang('new'),
				'pending'			 => lang('pending'),
				'voided'			 => lang('interrupted'),
				'refunded'			 => lang('refunded'),
				'partially_refunded' => lang('partially refunded'),
			);

			foreach ($payments['data'] as &$payment)
			{
				$payment['created_value'] = $GLOBALS['phpgw']->common->show_date($payment['created']);
				$payment['status_text'] = $status_text[$payment['status']];
				if ($GLOBALS['phpgw_info']['flags']['currentapp'] == 'booking')
				{
					switch ($payment['status'])
					{
						case 'completed':
							$payment['option_delete']	 = self::link(array(
									'menuaction'	 => 'booking.uiapplication.refund_payment',
									'id'			 => $payment['id'],
									'application_id' => $application_id));
							$payment['option_edit']		 = false;
							break;
						case 'pending':
						case 'new':
							$payment['option_edit']		 = self::link(array(
									'menuaction'	 => 'booking.uiapplication.cancel_payment',
									'id'			 => $payment['id'],
									'application_id' => $application_id));
							$payment['option_delete']	 = false;
							break;
						default:
							$payment['option_delete']	 = false;
							$payment['option_edit']		 = false;
							break;
					}
				}
			}
			return $payments;
		}

		function refund_payment()
		{
			$payment_id = phpgw::get_var('id', 'int');
			$application_id = phpgw::get_var('application_id', 'int');
			$application = $this->bo->read_single($application_id);

			if($this->is_assigned_to_current_user($application))
			{
				$payment		 = $this->bo->so->get_payment($payment_id);
				$payment_method = $payment['payment_method'];
				$remote_order_id = $payment['remote_id'];
				$amount = $payment['amount'] * 100;
				$payment_helper = createObject("bookingfrontend.{$payment_method}_helper");
				$payment_helper->refund_payment($remote_order_id, $amount);
				$comment_text = "Refund: {$payment['amount']}";
				$this->add_comment($application, $comment_text);
				$this->bo->update($application);
			}
			else
			{
				phpgwapi_cache::message_set('current user is not assigned to application', 'error');
			}

			self::redirect(array('menuaction' => $this->url_prefix . '.show', 'id' => $application_id));

		}

		function cancel_payment()
		{
			$payment_id = phpgw::get_var('id', 'int');
			$application_id = phpgw::get_var('application_id', 'int');
			$application = $this->bo->read_single($application_id);
			if($this->is_assigned_to_current_user($application))
			{
				$payment		 = $this->bo->so->get_payment($payment_id);
				$payment_method = $payment['payment_method'];
				$remote_order_id = $payment['remote_id'];
				$payment_helper = createObject("bookingfrontend.{$payment_method}_helper");
				$payment_helper->cancel_payment($remote_order_id);
				$comment_text = "Cancel: {$payment['amount']}";
				$this->add_comment($application, $comment_text);
				$this->bo->update($application);
			}
			else
			{
				phpgwapi_cache::message_set('current user is not assigned to application', 'error');
			}
			self::redirect(array('menuaction' => $this->url_prefix . '.show', 'id' => $application_id));

		}

		function get_purchase_order()
		{
			$order_id = phpgw::get_var('id', 'int');
//			$purchase_order = $this->bo->so->get_single_purchase_order($order_id);
			$purchase_order = createObject('booking.sopurchase_order')->get_single_purchase_order($order_id);

			if(!empty($purchase_order['lines']))
			{
				foreach ($purchase_order['lines'] as &$line)
				{
					$line['sum'] = number_format( ($line['amount'] + $line['tax']) , 2, '.', ' ');
					$line['amount'] = number_format($line['amount'], 2, '.', ' ');
					$line['tax'] = number_format($line['tax'], 2, '.', ' ');
					$line['unit_price'] = number_format($line['unit_price'], 2, '.', ' ');
				}
			}


			return $purchase_order;
		}
		private function _combine_dates( $from_, $to_ )
		{
			return array('from_' => $from_, 'to_' => $to_);
		}

		protected function get_customer_identifier()
		{
			return $this->customer_id;
		}

		protected function extract_customer_identifier( &$data )
		{
			$this->get_customer_identifier()->extract_form_data($data);
		}

		protected function validate_customer_identifier( &$data )
		{
			return $this->get_customer_identifier()->validate($data);
		}

		protected function install_customer_identifier_ui( &$entity )
		{
			$this->get_customer_identifier()->install($this, $entity);
		}

		protected function validate( &$entity )
		{
			$errors = array_merge($this->validate_customer_identifier($entity), $this->bo->validate($entity));
			return $errors;
		}

		protected function set_case_officer( &$application )
		{
			if (!empty($application['case_officer_id']))
			{
				$application['case_officer'] = array(
					'id' => $application['case_officer_id'],
					'name' => $application['case_officer_name'],
				);

				if ($application['case_officer_id'] == $this->current_account_id())
				{
					$application['case_officer']['is_current_user'] = true;
				}
			}
		}

		protected function extract_form_data( $defaults = array() )
		{
			$entity = array_merge($defaults, extract_values($_POST, $this->fields));
			$entity['agegroups'] = array();
			$this->agegroup_bo->extract_form_data($entity);
			$this->extract_customer_identifier($entity);
			return $entity;
		}

		protected function create_accepted_documents_comment_text( $application )
		{
			if (count($application['accepted_documents']) < 1)
			{
				return null;
			}
			$comment_text = lang('The user has accepted the following documents') . ': ';
			foreach ($application['accepted_documents'] as $doc)
			{
				$doc_info = explode('::', $doc);
				$doc_type = $doc_info[0];
				$doc_id = $doc_info[1];
				switch ($doc_type)
				{
					default:
					case 'building':
						$document = $this->document_building->read_single($doc_id);
						break;
					case 'resource':
						$document = $this->document_resource->read_single($doc_id);
						break;
				}
				$comment_text .= $document['description'] . ' (' . $document['name'] . '), ';
			}
			$comment_text = substr($comment_text, 0, -2);

			return $comment_text;
		}

		public function cancel_block()
		{
			$resource_id = phpgw::get_var('resource_id', 'int' ,'REQUEST');
			$building_id = phpgw::get_var('building_id', 'int' ,'REQUEST');

			$from_ = date('Y-m-d H:i:s', phpgwapi_datetime::date_to_timestamp(phpgw::get_var('from_', 'string', 'GET')));
			$to_ = date('Y-m-d H:i:s', phpgwapi_datetime::date_to_timestamp( phpgw::get_var('to_', 'string', 'GET')));

			$bo_block = createObject('booking.boblock');

			$session_id = $GLOBALS['phpgw']->session->get_session_id();

			if (!empty($session_id) && $resource_id)
			{
				$bo_block = createObject('booking.boblock');
				$bo_block->cancel_block($session_id, array(array('from_' =>  $from_, 'to_' =>  $to_)),array($resource_id));
			}

			self::redirect(array('menuaction' => 'bookingfrontend.uiresource.show', 'id' => $resource_id, 'building_id' => $building_id));
			//self::redirect(array());

		}
		public function set_block()
		{
			$resource_id = phpgw::get_var('resource_id', 'int' ,'REQUEST', -1 );
			$timezone	 = !empty($GLOBALS['phpgw_info']['user']['preferences']['common']['timezone']) ? $GLOBALS['phpgw_info']['user']['preferences']['common']['timezone'] : 'UTC';

			try
			{
				$DateTimeZone	 = new DateTimeZone($timezone);
			}
			catch (Exception $ex)
			{
				throw $ex;
			}

			$from_ = (new DateTime(phpgw::get_var('from_'), $DateTimeZone));
			$to_ = (new DateTime(phpgw::get_var('to_'), $DateTimeZone));
			$from_ = phpgw::get_var('from_');
			$to_ = phpgw::get_var('to_');

//			$from_->setTimezone(new DateTimeZone('UTC'));
//			$to_->setTimezone(new DateTimeZone('UTC'));

			$bo_block = createObject('booking.boblock');

			$session_id = $GLOBALS['phpgw']->session->get_session_id();
//			$collision = $this->bo->so->check_collision(array($resource_id), $from_->format('Y-m-d H:i:s'), $to_->format('Y-m-d H:i:s'), $session_id);
			$collision = $this->bo->so->check_collision(array($resource_id), $from_, $to_, $session_id);

			$status = '';
			$message = '';
			if ($collision)
			{
				$status = 'reserved';
				return array(
					'status' => $status,
					'message'	=> $message
				);
			}

			$previous_block = $bo_block->so->read(array(
				'filters' => array('where' =>  "(bb_block.active = 1"
					. " AND bb_block.session_id = '{$session_id}'"
					. " AND bb_block.resource_id = {$resource_id}"
//					. " AND bb_block.from_ = '" . $from_->format('Y-m-d H:i:s') . "'"
//					. " AND bb_block.to_ = '" . $to_->format('Y-m-d H:i:s') . "')"),
					. " AND bb_block.from_ = '{$from_}'"
					. " AND bb_block.to_ = '{$to_}')"),
				'results' => 1));


			if($previous_block['total_records'] > 0)
			{
				$status = 'registered';
			}
			else
			{
				$block = array(
					'session_id'	=> $session_id,
					'resource_id'	=> $resource_id,
//					'from_'			=> $from_->format('Y-m-d H:i:s'),
//					'to_'			=> $to_->format('Y-m-d H:i:s')
					'from_'			=> $from_,
					'to_'			=> $to_
					);
				$receipt = $bo_block->add($block);
				if($receipt['id'])
				{
					$status = 'saved';
					$message = $receipt['message'][0]['msg'];
				}
			}

			return array(
				'status' => $status,
				'message'	=> $message
			);
		}

		private function validate_limit_number($resource_id, $ssn, &$errors )
		{
			$resource = $this->resource_bo->so->read_single($resource_id);
			if($resource['booking_limit_number_horizont'] > 0 && $resource['booking_limit_number'] > 0)
			{
				$limit_reached = $this->bo->so->check_booking_limit(
					$GLOBALS['phpgw']->session->get_session_id(),
					$resource_id,
					$ssn,
					$resource['booking_limit_number_horizont'],
					$resource['booking_limit_number'] );

				if($limit_reached)
				{
					$errors['error_message'] = lang('quantity limit (%1) exceeded for %2: maximum %3 times within a period of %4 days',
						$limit_reached,
						$resource['name'],
						$resource['booking_limit_number'],
						$resource['booking_limit_number_horizont']);
				}
			}
		}

		public function add()
		{
			$organization_number = phpgwapi_cache::session_get($this->module, self::ORGNR_SESSION_KEY);

			$building_id = phpgw::get_var('building_id', 'int' ,'REQUEST', -1 );
			$resource_id = phpgw::get_var('resource_id', 'int');
			$resource = $this->resource_bo->so->read_single($resource_id);
			$simple = phpgw::get_var('simple', 'bool');

			if($GLOBALS['phpgw_info']['flags']['currentapp'] == 'bookingfrontend' && $resource['simple_booking_start_date'] && $resource['simple_booking_start_date'] < time() && !$simple)
			{
				self::redirect(array('menuaction' => 'bookingfrontend.uiresource.show', 'id' => $resource_id, 'building_id' => $building_id, 'simple' => true));
			}

			$bouser = CreateObject('bookingfrontend.bouser');

			$errors = array();
			$user_data = phpgwapi_cache::session_get($bouser->get_module(), $bouser::USERARRAY_SESSION_KEY);
			if($user_data['ssn'])
			{
				$this->validate_limit_number($resource_id, $user_data['ssn'],$errors);
			}

			$application_id = phpgw::get_var('application_id', 'int');
			if (isset($application_id))
			{
				$existing_application = $this->application_bo->read_single($application_id);
				$building_info = $this->bo->so->get_building_info($application_id);
				$building_id = $building_info['id'];

				if ($_SERVER['REQUEST_METHOD'] != 'POST')
				{
					$external_login_info = $bouser->validate_ssn_login(array('menuaction' => 'bookingfrontend.uiapplication.add'));

					if ($existing_application['customer_organization_number'] == $organization_number || $existing_application['customer_ssn'] == $external_login_info['ssn'])
					{
						$application['resources'] = $existing_application['resources'];
						$application['building_name'] = $existing_application['building_name'];
						$application['building_id'] = $building_id;
						$application['activity_id'] = $existing_application['activity_id'];
						$application['name'] = $existing_application['name'];
						$application['organizer'] = $existing_application['organizer'];
						$application['homepage'] = $existing_application['homepage'];
						$application['description'] = $existing_application['description'];
						$application['equipment'] = $existing_application['equipment'];
						$application['audience'] = $existing_application['audience'];
						$application['agegroups'] = $existing_application['agegroups'];
					}
					else
					{
						$errors['copy_permission'] = lang('Could not copy application');
					}
				}
			}

			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$building = $this->building_bo->so->read(array('filters' => array('id' => $building_id)));

				array_set_default($_POST, 'resources', array());
				array_set_default($_POST, 'accepted_documents', array());
				array_set_default($_POST, 'from_', array());
				array_set_default($_POST, 'to_', array());

				$application = $this->extract_form_data();

				foreach ($_POST['from_'] as &$from)
				{
					$from = ($from) ? date("Y-m-d H:i:s", phpgwapi_datetime::date_to_timestamp($from)) : "";
				}
				foreach ($_POST['to_'] as &$to)
				{
					$to = ($to) ? date("Y-m-d H:i:s", phpgwapi_datetime::date_to_timestamp($to)) : "";
				}

				$application['dates'] = array_map(array($this, '_combine_dates'), $_POST['from_'], $_POST['to_']);
				$application['dates'] = array_map("unserialize", array_unique(array_map("serialize", $application['dates'])));
				$application['active'] = '1';
				$application['status'] = 'NEW';
				$application['created'] = 'now';
				$application['modified'] = 'now';
				$application['secret'] = $this->generate_secret();
//				$application['secret_timestamp'] = time();
				$application['owner_id'] = $GLOBALS['phpgw_info']['user']['account_id'];
				$application['building_name'] = $building['results'][0]['name'];

				// Handle a partial application
				$is_partial1 = false;
				$session_id_ok = true;
				if (isset($application['formstage']) && $application['formstage'] == 'partial1')
				{
					$is_partial1 = true;
					$application['status'] = 'NEWPARTIAL1';
					$session_id = $GLOBALS['phpgw']->session->get_session_id();
					if (!empty($session_id))
					{
						$application['session_id'] = $session_id;
					}
					else
					{
						$session_id_ok = false;
					}
					// Application contains only event details. Use dummy values for contact fields
					$dummyfields_string = array('contact_name','contact_phone','responsible_city','responsible_street');
					foreach ($dummyfields_string as $field)
					{
						$application[$field] = 'dummy';
					}
					$application['contact_email'] = 'dummy@example.com';
					$application['contact_email2'] = 'dummy@example.com';
					$application['responsible_zip_code'] = '0000';
					$application['customer_identifier_type'] = 'organization_number';
					$application['customer_organization_number'] = '';
				}
				else if (isset($application['formstage']) && $application['formstage'] == 'partial2')
				{
					$is_partial1 = true;
					$application['status'] = 'NEWPARTIAL1';
					$session_id = $GLOBALS['phpgw']->session->get_session_id();
					if (!empty($session_id))
					{
						$application['session_id'] = $session_id;
					}
					else
					{
						$session_id_ok = false;
					}
					// Application contains only event details. Use dummy values for contact fields
					$dummyfields_string = array('contact_name','contact_phone','responsible_city',
						'responsible_street', 'name', 'organizer', 'homepage', 'description', 'equipment'
						);
					foreach ($dummyfields_string as $field)
					{
						$application[$field] = 'dummy';
					}

					$application['contact_email'] = 'dummy@example.com';
					$application['contact_email2'] = 'dummy@example.com';
					$application['responsible_zip_code'] = '0000';
					$application['customer_identifier_type'] = 'organization_number';
					$application['customer_organization_number'] = '';

				}
				else if(isset($application['formstage']) && $application['formstage'] == 'legacy')
				{
					$application['name'] = $application['description'] ;
				}

				/**
				 * Start dealing with the purchase_order..
				 */
				$purchase_order = array('status' => 0, 'customer_id' => -1, 'lines' => array());
				$selected_articles = (array)phpgw::get_var('selected_articles');

				foreach ($selected_articles as $selected_article)
				{
					$_article_info = explode('_', $selected_article);

					if(empty($_article_info[0]))
					{
						continue;
					}

					/**
					 * the value selected_articles[]
					 * <mapping_id>_<quantity>_<tax_code>_<ex_tax_price>_<parent_mapping_id>
					 */
					$purchase_order['lines'][] = array(
						'article_mapping_id'	=> $_article_info[0],
						'quantity'				=> $_article_info[1],
						'tax_code'				=> $_article_info[2],
						'ex_tax_price'			=> $_article_info[3],
						'parent_mapping_id'		=> !empty($_article_info[4]) ? $_article_info[4] : null
					);
				}

				/**
				 * In rare case of refresh or double-click
				 */
				if ($GLOBALS['phpgw']->session->is_repost())
				{
					if ($is_partial1)
					{
						phpgwapi_cache::message_set(
							lang("Complete application text booking") .
							'<br/><button onclick="GoToApplicationPartialTwo()" class="btn btn-light mt-4" data-bind="visible: applicationCartItems().length > 0">' .
							lang("Complete applications") .
							'</button><button onclick="window.location.href = phpGWLink(\'bookingfrontend/\', {})" class="ml-2 btn btn-light mt-4" data-bind="visible: applicationCartItems().length > 0">' .
							lang("new application") .
							'</button>'
						);
						// Redirect to same URL so as to present a new, empty form
						self::redirect(array('menuaction' => $this->url_prefix . '.add', 'building_id' => $building_id, 'simple' => $simple, 'resource_id' => $resource_id));
					}
					else
					{
						$repost_add_application = 	phpgwapi_cache::session_get('booking', 'repost_add_application', $receipt['id']);
						$application = $this->bo->read_single($repost_add_application);
						self::redirect(array('menuaction' => $this->url_prefix . '.show', 'id' => $repost_add_application,
								'secret' => $application['secret']));
					}
				}

				$errors = $this->validate($application);
				if (!$session_id_ok)
				{
					$errors['session_id'] = lang('No session ID found, application aborted');
				}

				// if logged in
				$user_data = phpgwapi_cache::session_get($bouser->get_module(), $bouser::USERARRAY_SESSION_KEY);
				if($user_data['ssn'])
				{
					$resources = $this->resource_bo->so->read(array(
								'sort'    => 'sort',
								'results' =>'all',
								'filters' => array('id' => $application['resources']), 'results' =>'all'
					));

					foreach ($resources['results'] as $resource)
					{
						$this->validate_limit_number($resource['id'], $user_data['ssn'], $errors );
					}
					unset($resources);
					unset($resource);
				}
				if (!$is_partial1)
				{
					if ($_POST['contact_email'] != $_POST['contact_email2'])
					{
						$errors['email'] = lang('The e-mail addresses you entered do not match');
						$application['contact_email2'] = phpgw::get_var('contact_email2', 'string', 'POST');
					}
					else
					{
						$application['contact_email2'] = phpgw::get_var('contact_email2', 'string', 'POST');
					}
				}

				$audval_present = false;
				if (!empty($_POST['audience']))
				{
					foreach ($_POST['audience'] as $audval)
					{
						if (!empty($audval))
						{
							$audval_present = true;
							break;
						}
					}
				}

				if($is_partial1 && !$audval_present)
				{
					$application['audience'] = array(1); // Dummy
				}
				else if (!$audval_present)
				{
					$errors['audience'] = lang("Select a target audience");
				}

				foreach ($application['agegroups'] as $ag)
				{
					if ($ag['male'] > 9999 || $ag['female'] > 9999)
					{
						$errors['agegroups'] = lang('Agegroups can not be larger than 9999 peoples');
					}
				}
				if ($building['results'][0]['deactivate_application'])
				{
					$errors['application_deactivated'] = lang('Application on this building is not possible.');
				}


				if (!$errors)
				{
					$comment_text = $this->create_accepted_documents_comment_text($application);
					if ($comment_text)
					{
						$this->add_comment($application, $comment_text);
					}

					$receipt = $this->bo->add($application);
					$application['id'] = $receipt['id'];

					if(!empty($purchase_order['lines']))
					{
						$purchase_order['application_id'] = $application['id'];
						createObject('booking.sopurchase_order')->add_purchase_order($purchase_order);
					}

					if( isset($_FILES['name']['name']) && $_FILES['name']['name'] )
					{
						/** Start attachment * */
						$document_application = createObject('booking.uidocument_application');

						$document = array(
							'category' => 'other',
							'owner_id' => $application['id'],
							'files' => $this->get_files_from_post()
						);

						$document_errors = $document_application->bo->validate($document);

						if (!$document_errors)
						{
							try
							{
								booking_bocommon_authorized::disable_authorization();
								$document_receipt = $document_application->bo->add($document);
							}
							catch (booking_unauthorized_exception $e)
							{
								phpgwapi_cache::message_set(lang('Could not add object due to insufficient permissions'),'error');
							}
						}
						else
						{
							$this->flash_form_errors($document_errors);
						}
					}
					/** End attachment * */
					$this->bo->so->update_id_string();
					if ($is_partial1)
					{
						// Redirect to same URL so as to present a new, empty form
						if($simple)
						{
							self::redirect(array('menuaction' => $this->url_prefix . '.add_contact',  'id' => $resource_id, 'building_id' => $building_id ));
						}
						else
						{
							phpgwapi_cache::message_set(
								lang("Complete application text booking") .
								'<br/><button onclick="GoToApplicationPartialTwo()" class="btn btn-light mt-4" data-bind="visible: applicationCartItems().length > 0">' .
								lang("Complete applications") .
								'</button><button onclick="window.location.href = phpGWLink(\'bookingfrontend/\', {})" class="ml-2 btn btn-light mt-4" data-bind="visible: applicationCartItems().length > 0">' .
								lang("new application") .
								'</button>'
							);
							self::redirect(array('menuaction' => $this->url_prefix . '.add', 'building_id' => $building_id, 'simple' => $simple));
						}
					}
					else
					{
						$this->bo->send_notification($application, true);
						phpgwapi_cache::message_set(lang("Your application has now been registered and a confirmation email has been sent to you.") . "<br />" .
							lang("A Case officer will review your application as soon as possible.") . "<br />" .
							lang("Please check your Spam Filter if you are missing mail."
						));
//						$this->flash(lang("Your application has now been registered and a confirmation email has been sent to you.")."<br />".
//								 lang("A Case officer will review your application as soon as possible.")."<br />".
//								 lang("Please check your Spam Filter if you are missing mail."));

						phpgwapi_cache::session_set('booking', 'repost_add_application', $receipt['id']);
						self::redirect(array('menuaction' => $this->url_prefix . '.show', 'id' => $receipt['id'],
							'secret' => $application['secret']));
					}
				}
				else
				{
					phpgwapi_cache::session_clear('phpgwapi', 'history');
				}
			}
			if (phpgw::get_var('resource') == 'null' || !phpgw::get_var('resource'))
			{
				array_set_default($application, 'resources', array());
			}
			else
			{
				$resources = explode(",", phpgw::get_var('resource'));
				if ($resources)
				{
					$resources_id = $resources[0];
					$resource = $this->resource_bo->read_single($resources_id);
					$activity_id = $resource['activity_id'];
				}

				array_set_default($application, 'resources', $resources);
			}
			array_set_default($application, 'building_id', $building_id);

			$_building = $this->building_bo->so->read_single($building_id);

			array_set_default($application, 'building_name', $_building['name']);
			array_set_default($application, 'audience', array());

			if ($application['building_name'] && strstr($application['building_name'], "%"))
			{
				$search = array('%C3%85', '%C3%A5', '%C3%98', '%C3%B8', '%C3%86', '%C3%A6');
				$replace = array('Å', 'å', 'Ø', 'ø', 'Æ', 'æ');
				$application['building_name'] = str_replace($search, $replace, $application['building_name']);
			}

            if (phpgw::get_var('dates', 'string'))
            {
                $dates_input = explode(',', phpgw::get_var('dates', 'string'));
                $timezone = !empty($GLOBALS['phpgw_info']['user']['preferences']['common']['timezone']) ? $GLOBALS['phpgw_info']['user']['preferences']['common']['timezone'] : 'UTC';

                try
                {
                    $DateTimeZone = new DateTimeZone($timezone);
                }
                catch (Exception $ex)
                {
                    throw $ex;
                }

                $combined_dates = [];
                foreach ($dates_input as $date_pair_str)
                {
                    list($start_timestamp, $end_timestamp) = explode('_', $date_pair_str);
                    $_start_time = new DateTime(date('Y-m-d H:i:s', $start_timestamp));
                    $_end_time = new DateTime(date('Y-m-d H:i:s', $end_timestamp));
                    $_start_time->setTimezone($DateTimeZone);
                    $_end_time->setTimezone($DateTimeZone);

                    $combined_dates[] = $this->_combine_dates($_start_time->format('Y-m-d H:i:s'), $_end_time->format('Y-m-d H:i:s'));
                }

                $default_dates = $combined_dates;
            } else if (phpgw::get_var('from_', 'string'))
			{
				$default_dates = array_map(array($this, '_combine_dates'), phpgw::get_var('from_', 'string'), phpgw::get_var('to_', 'string'));
			}
			else if (phpgw::get_var('start', 'bool'))
			{
				$timezone	 = !empty($GLOBALS['phpgw_info']['user']['preferences']['common']['timezone']) ? $GLOBALS['phpgw_info']['user']['preferences']['common']['timezone'] : 'UTC';

				try
				{
					$DateTimeZone	 = new DateTimeZone($timezone);
				}
				catch (Exception $ex)
				{
					throw $ex;
				}

				$_start_time =  (new DateTime(date('Y-m-d H:i:s', phpgw::get_var('start', 'int')/1000)));
				$_end_time = ( new DateTime(date('Y-m-d H:i:s', phpgw::get_var('end', 'int')/1000)));
				$_start_time->setTimezone($DateTimeZone);
				$_end_time->setTimezone($DateTimeZone);

				$default_dates = array_map(array($this, '_combine_dates'), (array) $_start_time->format('Y-m-d H:i:s'),(array)$_end_time->format('Y-m-d H:i:s'));
			}
			else
			{
				$default_dates = array_map(array($this, '_combine_dates'), array(), array());
			}

			array_set_default($application, 'dates', $default_dates);

			$this->flash_form_errors($errors);

			if(empty($application['resources']))
			{
				$application['resources_json'] = json_encode(array($resource_id));
			}
			else
			{
				$application['resources_json'] = json_encode(array_map('intval', $application['resources']));
			}
			$application['accepted_documents_json'] = json_encode($application['accepted_documents']);
			$application['dates_json'] = json_encode($application['dates']);
			$application['agegroups_json'] = json_encode($application['agegroups']);
			$top_level_activity = false;
			if (!$activity_id)
			{
				$activity_id = $_building['activity_id'];
				$top_level_activity = $activity_id;
			}
			if (!$activity_id)
			{
				$activity_id = phpgw::get_var('activity_id', 'int', 'REQUEST', -1);
			}
			if (!$top_level_activity)
			{
				$activity_path = $this->activity_bo->get_path($activity_id);
				$top_level_activity = $activity_path ? $activity_path[0]['id'] : -1;
			}
			$filter_activity_top = 0;
			if ($GLOBALS['phpgw_info']['flags']['currentapp'] == 'booking')
			{
				$application['cancel_link'] = self::link(array('menuaction' => 'booking.uiapplication.index'));
			}
			else if ($GLOBALS['phpgw_info']['flags']['currentapp'] == 'bookingfrontend')
			{
				$application['cancel_link'] = self::link(array('menuaction' => 'bookingfrontend.uibuilding.schedule',
						'id' => $building_id));
				$filter_activity_top = $top_level_activity > 0 ? $top_level_activity : 0;
			}
			$application['frontpage_link'] = self::link(array());
			array_set_default($application, 'activity_id', $activity_id);
			$activities = $this->activity_bo->fetch_activities($filter_activity_top);
			$activities = $activities['results'];
			$agegroups = $this->agegroup_bo->fetch_age_groups($top_level_activity);
			$agegroups = $agegroups['results'];
			$audience = $this->audience_bo->fetch_target_audience($top_level_activity);
			//hack
			if($application['audience'] == -1)
			{
				$application['audience'] = array();
			}
			$application['audience_json'] = json_encode(array_map('intval', $application['audience']));

			$audience = $audience['results'];

			$this->install_customer_identifier_ui($application);

			$application['customer_identifier_types']['ssn'] = 'SSN';
			if ($organization_number)
			{
				$application['customer_identifier_type'] = 'organization_number';
				$application['customer_organization_number'] = $organization_number;
				$orgid = $this->organization_bo->so->get_orgid($organization_number);
				$organization = $this->organization_bo->read_single($orgid);
				if ($organization['contacts'][0]['name'] != '')
				{
					$application['contact_name'] = $organization['contacts'][0]['name'];
					$application['contact_email'] = $organization['contacts'][0]['email'];
					$application['contact_phone'] = $organization['contacts'][0]['phone'];
				}
				else
				{
					$application['contact_name'] = $organization['contacts'][1]['name'];
					$application['contact_email'] = $organization['contacts'][1]['email'];
					$application['contact_phone'] = $organization['contacts'][1]['phone'];
				}
			}

			foreach ($application['dates'] as &$date)
			{
				$date['from_'] = pretty_timestamp($date['from_']);
				$date['to_'] = pretty_timestamp($date['to_']);
			}


			if (phpgw::get_var('phpgw_return_as', 'string', 'GET') == 'json' )
			{
				echo json_encode(array(
						'application' => $application,
						'activities' => $activities,
						'agegroups' => $agegroups,
						'audience' => $audience
					)
				);

				$GLOBALS['phpgw']->common->phpgw_exit();
			}



			if ($GLOBALS['phpgw_info']['flags']['currentapp'] != 'bookingfrontend')
			{
				$GLOBALS['phpgw']->jqcal2->add_listener('start_date', 'datetime', !empty($default_dates) ? strtotime($default_dates[0]['from_']) :0);
				$GLOBALS['phpgw']->jqcal2->add_listener('end_date', 'datetime', !empty($default_dates) ? strtotime($default_dates[0]['to_']) :0);
				self::adddatetimepicker();
				$tabs = array();
				$tabs['generic'] = array('label' => lang('Application Add'), 'link' => '#application_add');
				$active_tab = 'generic';
				$application['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);

				self::add_javascript('booking', 'base', 'application.js');
				phpgwapi_jquery::formvalidator_generate(array('location', 'date', 'security',
					'file'), 'application_form');
			}

			// Get resources
			$resource_filters = array('active' => 1, 'rescategory_active' => 1, 'building_id' => $building_id );
			$_resources = $this->resource_bo->so->read(array('filters' => $resource_filters, 'sort' => 'sort', 'results' => -1));

			$resource_ids = array();
			$resources = array();
			$direct_booking = false;

			if(!empty($_resources['results']))
			{
				$_building_simple_booking = 0;
				foreach ($_resources['results'] as $_resource)
				{
					if (!empty($_resource['direct_booking']) && $_resource['direct_booking'] < time())
					{
						$_resource['name'] .= ' *';
						$direct_booking = true;
						$resource_ids[] = $_resource['id'];
					}

					if($_resource['simple_booking'] == 1)
					{
						$_building_simple_booking ++;
					}

					if($simple && !$_resource['simple_booking'] == 1)
					{
						continue;
					}

					$resources[] = array(
						'id' => $_resource['id'],
						'name' => $_resource['name'],
						'selected' => $resource_id == $_resource['id'] ? 1 : 0
					);

				}

				if($_building_simple_booking == count($_resources['results']))
				{
					$simple = true;
				}
			}

			if(!$simple)
			{
				$simple = phpgw::get_var('formstage') == 'partial2' ? true : false;
			}

			if($GLOBALS['phpgw_info']['flags']['currentapp'] == 'bookingfrontend' && $simple)
			{
				$template = 'application_new_simple';

				/**
				 * possible initial value from calendar
				 * millisec
				 */
				$start = phpgw::get_var('start', 'int');

				if($start)
				{
					$default_start_date = $start/1000;//seconds
				}
				elseif (!empty($default_dates))
				{
					$default_start_date = $default_dates[0]['from_'];
				}
				else
				{
					$default_start_date = 0;

				}

				$_months_ahead = 2;
				$_date_ahead_ts = strtotime(date('Y-m-d') . " +{$_months_ahead} month");
				$_last_day_in_month = date('t', $_date_ahead_ts);
				$_year_ahead  = date('Y', $_date_ahead_ts);
				$_month_ahead  = date('n', $_date_ahead_ts);
				$_max_date_ts = mktime(0, 0, 0, $_month_ahead, $_last_day_in_month, $_year_ahead);

//				_debug_array(date('Y-m-d', $_max_date_ts));

				$GLOBALS['phpgw']->jqcal2->add_listener('start_date', 'date', $default_start_date, array(
					'min_date' => time(),
					'max_date' => $_max_date_ts,
					));
				self::add_javascript('bookingfrontend', 'base', 'application_new_simple.js', true);
			}
			else
			{
				$template = 'application_new';
			}

			$config = CreateObject('phpgwapi.config', 'booking')->read();
			if(!empty($config['activate_application_articles']))
			{
				if($GLOBALS['phpgw_info']['flags']['currentapp'] == 'bookingfrontend')
				{
					self::add_javascript('bookingfrontend', 'base', 'purchase_order_add.js', true);
				}
				else
				{
					self::add_javascript('booking', 'base', 'purchase_order_edit.js');
				}
			}

            if ($GLOBALS['phpgw_info']['user']['preferences']['common']['template_set'] == 'bookingfrontend_2') {
                self::add_javascript('bookingfrontend', 'bookingfrontend_2', 'components/light-box.js', true);
                $GLOBALS['phpgw']->css->add_external_file("bookingfrontend/js/bookingfrontend_2/components/light-box.css");

            }

            if($GLOBALS['phpgw_info']['flags']['currentapp'] == 'bookingfrontend' && !$simple)
			{
				$GLOBALS['phpgw']->js->add_external_file("phpgwapi/templates/bookingfrontend/js/build/aui/aui-min.js");
				self::add_javascript('bookingfrontend', 'base', 'application_new.js', true);
			}

			self::add_javascript('phpgwapi', 'dateformatter', 'dateformatter.js');
			$GLOBALS['phpgw']->js->validate_file('alertify', 'alertify.min', 'phpgwapi');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/alertify/css/alertify.min.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/alertify/css/themes/bootstrap.min.css');

            $_building['part_of_town'] = self::cleanTownName(execMethod('property.solocation.get_part_of_town', $_building['location_code'])['part_of_town']);


            $articles = CreateObject('booking.soarticle_mapping')->get_articles($resource_ids);
//            _debug_array(array(
//                    'add_action'	 => self::link(array('menuaction' => $this->url_prefix . '.add', 'building_id' => $building_id, 'resource_id' => $resource_id, 'simple' => $simple)),
//                    'application'	 => $application,
//                    'activities'	 => $activities,
//                    'agegroups'		 => $agegroups,
//                    'audience'		 => $audience,
//                    'building'	     => $_building,
//                    'resource_list'	 => array('options' => $resources),
//                    'direct_booking' => $direct_booking,
//                    'config'		 => $config,
//                    'has_articles'	 => !!$articles,
//                    'tax_code_list'	 => json_encode(execMethod('booking.bogeneric.read', array('location_info' => array('type' => 'tax', 'order' => 'id')))),
//                )
//            );die();
            self::add_external_css_with_search($template . '.css', false);
            self::render_template_xsl($template, array(
				'add_action'	 => self::link(array('menuaction' => $this->url_prefix . '.add', 'building_id' => $building_id, 'resource_id' => $resource_id, 'simple' => $simple)),
				'application'	 => $application,
				'activities'	 => $activities,
				'agegroups'		 => $agegroups,
				'audience'		 => $audience,
                'building'	     => $_building,
				'resource_list'	 => array('options' => $resources),
				'direct_booking' => $direct_booking,
				'config'		 => $config,
				'has_articles'	 => !!$articles,
				'tax_code_list'	 => json_encode(execMethod('booking.bogeneric.read', array('location_info' => array('type' => 'tax', 'order' => 'id')))),
				)
			);
		}

		public function check_booking_limit($session_id, $ssn, $resources )
		{
			$_limit_reached = 0;
			foreach ($resources['results'] as $resource)
			{
				if ($resource['booking_limit_number_horizont'] > 0 && $resource['booking_limit_number'] > 0)
				{
					$limit_reached = $this->bo->so->check_booking_limit(
						$session_id,
						$resource['id'],
						$ssn,
						$resource['booking_limit_number_horizont'],
						$resource['booking_limit_number']);

					if ($limit_reached)
					{
						$_limit_reached += $limit_reached;
						$error_message = lang('quantity limit (%1) exceeded for %2: maximum %3 times within a period of %4 days',
							$limit_reached,
							$resource['name'],
							$resource['booking_limit_number'],
							$resource['booking_limit_number_horizont']);

						phpgwapi_cache::message_set($error_message, 'error');
					}
				}
			}
			return $_limit_reached;
		}

		function update_contact_informtation(&$partial2 = array())
		{
			/**
			 * check external login - and return here
			 */
			$bouser = CreateObject('bookingfrontend.bouser');

			$external_login_info = $bouser->validate_ssn_login( array(), true);

			if(!$organization_number = phpgw::get_var('session_org_id', 'string', 'GET'))
			{
				$organization_number = phpgwapi_cache::session_get($this->module, self::ORGNR_SESSION_KEY);
			}

			$errors = array();

			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$partial2 = $this->extract_form_data();

				$customer_organization_number_fallback		 = phpgw::get_var('customer_organization_number_fallback');
				$customer_organization_number_arr			 = explode('_', phpgw::get_var('customer_organization_number'));
				if (!empty($customer_organization_number_arr[0]))
				{
					$partial2['customer_organization_id']		 = $customer_organization_number_arr[0];
					$partial2['customer_organization_number']	 = $customer_organization_number_arr[1];
					$organization								 = $this->organization_bo->read_single(intval($customer_organization_number_arr[0]));
					$partial2['customer_organization_name']		 = $organization['name'];

					if(!empty($organization['city']))
					{
						$partial2['responsible_city']		 = $organization['city'];
					}
					if(!empty($organization['street']))
					{
						$partial2['responsible_street']		 = $organization['street'];
					}
					if(!empty($organization['zip_code']))
					{
						$partial2['responsible_zip_code']		 = $organization['zip_code'];
					}

					$update_org = false;
					if(!$organization['customer_identifier_type'] == 'organization_number')
					{
						$organization['customer_identifier_type'] = 'organization_number';
						$update_org = true;
					}

					if(!empty($customer_organization_number_arr[1]) && empty($organization['customer_organization_number']))
					{
						$organization['customer_organization_number'] = $customer_organization_number_arr[1];
						$update_org = true;
					}

					if($update_org && !$this->organization_bo->validate($organization) && $organization_number == $customer_organization_number_arr[1])
					{
						$this->organization_bo->update($organization);
					}
				}
				else if($customer_organization_number_fallback)
				{
					$partial2['customer_organization_number']	 = str_replace(" ", "", $customer_organization_number_fallback);
					$partial2['customer_identifier_type']	 = 'organization_number';
					$organization_info = $this->organization_bo->get_organization_info($partial2['customer_organization_number']);
					if(!empty($organization_info['id']))
					{
						$partial2['customer_organization_id']		 = $organization_info['id'];
					}
					else
					{
						$organization_info = $this->add_organization($partial2, $external_login_info['ssn']);
						if(!empty($organization_info['id']))
						{
							$partial2['customer_organization_id']		 = $organization_info['id'];
							$partial2['customer_organization_name']		 = $organization_info['name'];

							/**
							 * Do something clever later on, as redirect to organization details - or something
							 */
							$organization_created = true;
						}
					}

					if(!empty($organization_info['city']))
					{
						$partial2['responsible_city']		 = $organization_info['city'];
					}
					if(!empty($organization_info['street']))
					{
						$partial2['responsible_street']		 = $organization_info['street'];
					}
					if(!empty($organization_info['zip_code']))
					{
						$partial2['responsible_zip_code']		 = $organization_info['zip_code'];
					}
				}

				// Application contains only contact details. Use dummy values for event fields
				$dummyfields_string = array('building_name','name','organizer','secret','status');
				foreach ($dummyfields_string as $field)
				{
					$partial2[$field] = 'dummy';
				}
				$dummyfields_int = array('activity_id','owner_id');
				foreach ($dummyfields_int as $field)
				{
					$partial2[$field] = 1;
				}
				$partial2['agegroups'] = array(array('agegroup_id' => 1, 'male' => 1, 'female' => 1));
				$partial2['audience']  = array(1);
				//dummy-dates in the future, to pass the temporary validation
				$partial2['dates']     = array(array('from_' => '2099-01-01 00:00:00', 'to_' => '2099-01-01 01:00:00'));
				$partial2['resources'] = array(-1);

				$errors = array_merge($this->errors, $this->validate($partial2));

				$session_id = $GLOBALS['phpgw']->session->get_session_id();
				if (empty($session_id))
				{
					$errors['session_id'] = lang('No session ID found, application aborted');
				}

				if ($_POST['contact_email'] != $_POST['contact_email2'])
				{
					$errors['email'] = lang('The e-mail addresses you entered do not match');
				}
				$partial2['contact_email2'] = phpgw::get_var('contact_email2', 'string', 'POST');

				if (!$errors)
				{
					// Get data on prior partial applications for this session ID
					$partials = $this->bo->get_partials_list($session_id);
					if ($partials['total_records'] == 0)
					{
						$errors['records'] = lang("No partial applications exist for this session, contact details are not saved");
						// Redirect to the front page
						self::redirect(array());
					}
					else
					{
						$partial2_fields = array('contact_email','contact_name','contact_phone',
							'customer_identifier_type','customer_organization_number','customer_organization_id',
							'customer_organization_name','customer_ssn',
							'responsible_city','responsible_street','responsible_zip_code');
						foreach ($partials['results'] as &$application)
						{
							// Remove certain unused fields from the update
							unset($application['frontend_modified']);
							// Add the contact data from partial2
							foreach ($partial2_fields as $field)
							{
									$application[$field] = $partial2[$field];
							}
							// Update status fields
							$application['created'] = 'now';
							$application['modified'] = 'now';

							if(empty($application['customer_ssn']))
							{
								$application['customer_ssn'] = phpgw::get_var('customer_ssn', 'string', 'POST');
							}

							$receipt = $this->bo->update($application);

							$this->update_user_info($application, $external_login_info);


							/**
							 * Handle limit
							 */
							$resources = $this->resource_bo->so->read(array(
									'sort'    => 'sort',
									'results' =>'all',
									'filters' => array('id' => $application['resources']), 'results' =>'all'
								));

							$direct_booking = false;
							$check_direct_booking = 0;

							$from_dates = array();
							foreach ($application['dates'] as $date)
							{
								$from_dates[] = strtotime( $date['from_']);
							}
							unset($date);

							foreach ($resources['results'] as $resource)
							{
								$max_date = max($from_dates);

								if($resource['direct_booking'] && $resource['direct_booking'] < $max_date)
								{
									$check_direct_booking ++;
								}
							}
							if($resources['results'] && count($resources['results']) == $check_direct_booking)
							{
								$collision_dates = array();
								foreach ($application['dates'] as &$date)
								{
									$collision = $this->bo->so->check_collision($application['resources'], $date['from_'], $date['to_'], $session_id);
									if ($collision)
									{
										$collision_dates[] = $date['from_'];
									}
								}

								if(!$collision_dates)
								{
									$direct_booking = true;
								}
							}


							$limit_reached = $this->check_booking_limit($session_id, $external_login_info['ssn'], $resources);

							if($limit_reached)
							{
								$errors['error_message'] = lang('quantity limit (%1) exceeded for %2: maximum %3 times within a period of %4 days',
									$limit_reached,
									$resource['name'],
									$resource['booking_limit_number'],
									$resource['booking_limit_number_horizont']);


								$GLOBALS['phpgw']->db->transaction_begin();
								CreateObject('booking.souser')->collect_users($application['customer_ssn']);
								$bo_block = createObject('booking.boblock');
								$bo_block->cancel_block($session_id, $application['dates'],$application['resources']);

								createObject('booking.sopurchase_order')->delete_purchase_order($application['id']);
								$this->bo->delete_application($application['id']);
								$GLOBALS['phpgw']->db->transaction_commit();
								if(!phpgw::get_var('phpgw_return_as', 'string', 'GET') == 'json' )
								{
									phpgwapi_cache::message_set(implode("<br/>", array_values($errors) ));
									self::redirect(array());
								}
							}
						}
					}
				}
			}

			/**
			 * hack
			 */
			if(isset($errors['from_']) && is_array($errors['from_']))
			{
				$errors['from_'] = implode(', ', $errors['from_']);
			}
			if(isset($errors['customer_organization_number']) && is_array($errors['customer_organization_number']))
			{
				$errors['customer_organization_number'] = implode(', ', $errors['customer_organization_number']);
			}

			if(empty($partial2['responsible_street']) && !empty($external_login_info['street']))
			{
				$partial2['responsible_street'] = $external_login_info['street'];
			}
			if(empty($partial2['responsible_zip_code']) && !empty($external_login_info['zip_code']))
			{
				$partial2['responsible_zip_code'] = $external_login_info['zip_code'];
			}
			if(empty($partial2['responsible_city']) && !empty($external_login_info['city']))
			{
				$partial2['responsible_city'] = $external_login_info['city'];
			}
	
			$contact_info = array(
				'responsible_street' => $partial2['responsible_street'],
				'responsible_zip_code' => $partial2['responsible_zip_code'],
				'responsible_city' => $partial2['responsible_city']
			);
			return array(
				'status'		 => $errors ? 'error' : 'saved',
				'direct_booking' => $direct_booking,
				'message'		 => preg_replace('/\<br(\s*)?\/?\>/i', PHP_EOL, implode(', ', array_values($errors))),
				'contact_info'	 => $contact_info
			);
		}

		function add_contact()
		{

			/**
			 * When returning from vipps
			 */
			$payment_order_id = phpgw::get_var('payment_order_id', 'string', 'GET');

			/**
			 * check external login - and return here
			 */
			$bouser = CreateObject('bookingfrontend.bouser');

			$external_login_info = $bouser->validate_ssn_login( array
			(
				'menuaction' => 'bookingfrontend.uiapplication.add_contact'
			));

			if(!$organization_number = phpgw::get_var('session_org_id', 'string', 'GET'))
			{
				$organization_number = phpgwapi_cache::session_get($this->module, self::ORGNR_SESSION_KEY);
			}

			$errors = array();

			$partial2 = array();
			$partial2['frontpage_url'] = self::link(array('menuaction' => 'bookingfrontend.uisearch.index'));

			//inspect resources for prepayment
			$session_id	 = $GLOBALS['phpgw']->session->get_session_id();
			$partials	 = $this->bo->get_partials_list($session_id);
			foreach ($partials['results'] as $application)
			{
				$resources = $this->resource_bo->so->read(array(
					'sort'		 => 'sort',
					'results'	 => 'all',
					'filters'	 => array('id' => $application['resources']), 'results'	 => 'all'
				));

				$activate_prepayment = 0;
				foreach ($resources['results'] as $resource)
				{
					if ($resource['activate_prepayment'])
					{
						$activate_prepayment++;
					}
				}
			}

			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$this->update_contact_informtation($partial2);

				// Get data on prior partial applications for this session ID
				if ($partials['total_records'] == 0)
				{
					$errors['records'] = lang("No partial applications exist for this session, contact details are not saved");
					// Redirect to the front page
					self::redirect(array());
				}
				else
				{
					foreach ($partials['results'] as &$application)
					{
						// Remove certain unused fields from the update
						unset($application['frontend_modified']);
						// Add the contact data from partial2
						// Update status fields


						$GLOBALS['phpgw']->db->transaction_begin();

//						$application['status'] = 'NEW';
//						$application['created'] = 'now';
//						$application['modified'] = 'now';
//						$application['session_id'] = null;
//						$receipt = $this->bo->update($application);

						/**
						 * Start direct booking
						 */

						$resources = $this->resource_bo->so->read(array(
								'sort'    => 'sort',
								'results' =>'all',
								'filters' => array('id' => $application['resources']), 'results' =>'all'
							));

						$direct_booking = false;
						$check_direct_booking = 0;

						$from_dates = array();
						foreach ($application['dates'] as $date)
						{
							$from_dates[] = strtotime( $date['from_']);
						}
						unset($date);

						foreach ($resources['results'] as $resource)
						{
							$max_date = max($from_dates);

							if($resource['direct_booking'] && $resource['direct_booking'] < $max_date)
							{
								$check_direct_booking ++;
							}
						}
						if($resources['results'] && count($resources['results']) == $check_direct_booking)
						{
							$collision_dates = array();
							foreach ($application['dates'] as &$date)
							{
								$collision = $this->bo->so->check_collision($application['resources'], $date['from_'], $date['to_'], $session_id);
								if ($collision)
								{
									$collision_dates[] = $date['from_'];
								}
							}

							if(!$collision_dates)
							{
								$direct_booking = true;
							}
						}

						if($direct_booking)
						{
							$application['status'] = 'ACCEPTED';
							$receipt = $this->bo->update($application);

							$event = $application;
							unset($event['id']);
							unset($event['id_string']);
							$event['application_id'] = $application['id'];
							$event['completed'] = '0';
							$event['is_public'] = 0;
							$event['include_in_list'] = 0;
							$event['reminder'] = 0;
							$event['customer_internal'] = 0;
							$this->get_event_cost($event);

							$building_info = $this->bo->so->get_building_info($application['id']);
							$event['building_id'] = $building_info['id'];
							$booking_boevent = createObject('booking.boevent');
							$errors = array();

							/**
							 * Validate timeslots
							 */
							foreach ($application['dates'] as $checkdate)
							{
								$event['from_'] = $checkdate['from_'];
								$event['to_'] = $checkdate['to_'];
								$errors = array_merge($errors, $booking_boevent->validate($event));
							}
							unset($checkdate);

							if (!$errors)
							{
								CreateObject('booking.souser')->collect_users($application['customer_ssn']);
								$bo_block = createObject('booking.boblock');
								$bo_block->cancel_block($session_id, $application['dates'],$application['resources']);

								/**
								 * Add event for each timeslot
								 */
								foreach ($application['dates'] as $checkdate)
								{
									$event['from_'] = $checkdate['from_'];
									$event['to_'] = $checkdate['to_'];
									$receipt = $booking_boevent->so->add($event);
								}

								$booking_boevent->so->update_id_string();
								createObject('booking.sopurchase_order')->identify_purchase_order($application['id'], $receipt['id'], 'event');

								$this->add_payment(array($application['id']));

								$GLOBALS['phpgw']->db->transaction_commit();
								$this->bo->send_notification($application);
							}
							else
							{
								$GLOBALS['phpgw']->db->transaction_abort();
								foreach ($errors as $key => $error_values)
								{
									phpgwapi_cache::message_set($error_values, 'error');
								}
							}
						}
						/**
						 * End Direct booking
						 */
						else
						{
							$application['status'] = 'NEW';
							$application['created'] = 'now';
							$application['modified'] = 'now';
							$application['session_id'] = null;
							$receipt = $this->bo->update($application);

							CreateObject('booking.souser')->collect_users($application['customer_ssn']);
							$GLOBALS['phpgw']->db->transaction_commit();
							$this->bo->send_notification($application, true);
						}

					}

					if(!$errors)
					{
						if($direct_booking)
						{
							$messages = array(
								'one' => array(
									'registered' => "Your application has now been processed and a confirmation email has been sent to you.",
									'review' => ""),
								'multiple' => array(
									'registered' => "Your applications have now been processed and confirmation emails have been sent to you.",
									'review' => "")
								);
						}
						else
						{
							$messages = array(
								'one' => array(
									'registered' => "Your application has now been registered and a confirmation email has been sent to you.",
									'review' => "A Case officer will review your application as soon as possible."),
								'multiple' => array(
									'registered' => "Your applications have now been registered and confirmation emails have been sent to you.",
									'review' => "A Case officer will review your applications as soon as possible.")
								);

						}

						$msgset = $partials['total_records'] > 1 ? 'multiple' : 'one';

						$message_arr = array();

						$message_arr[] = lang($messages[$msgset]['registered']);
						if($messages[$msgset]['review'])
						{
							$message_arr[] = lang($messages[$msgset]['review']);
						}
						$message_arr[] = lang("Please check your Spam Filter if you are missing mail.");

						phpgwapi_cache::message_set(implode("<br/>", $message_arr ));
					}
					// Redirect to the front page
					self::redirect(array());
				}
			}

			if(!empty($external_login_info['ssn']))
			{
				$user_id = CreateObject('booking.souser')->get_user_id($external_login_info['ssn']);
				if($user_id)
				{
					$user = CreateObject('booking.bouser')->read_single($user_id);
					$external_login_info['phone'] = $user['phone'] ? $user['phone'] : $external_login_info['phone'];
					$external_login_info['email'] = $user['email'] ? $user['email'] : $external_login_info['email'];
					$external_login_info['name'] = $user['name'] ? $user['name'] : $external_login_info['name'];
					$external_login_info['street'] = $user['street'] ? $user['street'] : $external_login_info['street'];
					$external_login_info['zip_code'] = $user['zip_code'] ? $user['zip_code'] : $external_login_info['zip_code'];
					$external_login_info['city'] = $user['city'] ? $user['city'] : $external_login_info['city'];
				}

				$partial2['customer_ssn'] = $external_login_info['ssn'];
			}
			if(empty($partial2['contact_email']) && !empty($external_login_info['email']))
			{
				$partial2['contact_email'] = $external_login_info['email'];
				$partial2['contact_email2'] = $external_login_info['email'];
			}
			if(empty($partial2['contact_phone']) && !empty($external_login_info['phone']))
			{
				$partial2['contact_phone'] = $external_login_info['phone'];
			}


			if(empty($partial2['contact_name']) && !empty($external_login_info['name']))
			{
				$partial2['contact_name'] = $external_login_info['name'];
			}
			if(empty($partial2['responsible_street']) && !empty($external_login_info['street']))
			{
				$partial2['responsible_street'] = $external_login_info['street'];
			}
			if(empty($partial2['responsible_zip_code']) && !empty($external_login_info['zip_code']))
			{
				$partial2['responsible_zip_code'] = $external_login_info['zip_code'];
			}
			if(empty($partial2['responsible_city']) && !empty($external_login_info['city']))
			{
				$partial2['responsible_city'] = $external_login_info['city'];
			}

			$this->flash_form_errors($errors);
			$partial2['cancel_link'] = self::link(array());
			self::add_javascript('bookingfrontend', 'base', 'application.js');


			if(!$bouser->is_logged_in())
			{
				$bouser->log_in();
			}

			$orgs = (array)phpgwapi_cache::session_get($bouser->get_module(), $bouser::ORGARRAY_SESSION_KEY);

			$orgnumbers = array();
			foreach ($orgs as $org)
			{
				$orgnumbers[] = $org['orgnumber'];
			}

			$session_org_id = phpgw::get_var('session_org_id');

			if($session_org_id && in_array($session_org_id, $orgnumbers))
			{
				$organization_number = $session_org_id;
			}
			else
			{
				$organization_number = $bouser->orgnr;

			}

			$delegate_data = CreateObject('booking.souser')->get_delegate($external_login_info['ssn'], $organization_number);

			$filtered_delegate_data = array();
			foreach ($delegate_data as $delegate_entry)
			{
				if($delegate_entry['active'])
				{
					$filtered_delegate_data[] = $delegate_entry;
				}
			}

			/**
			 * This one is for bookingfrontend
			 */
			self::add_javascript('bookingfrontend', 'base', 'application_contact.js', true);
			phpgwapi_jquery::load_widget('select2');


			$location_id		 = $GLOBALS['phpgw']->locations->get_id('booking', 'run');
			$custom_config		 = CreateObject('admin.soconfig', $location_id)->read();

			$payment_methods = array();
			if($activate_prepayment && !empty($custom_config['payment']['method']) && !empty($custom_config['Vipps']['active']))
			{
				$vipps_logo = 'continue_with_vipps_rect_210';

				switch ($GLOBALS['phpgw_info']['user']['preferences']['common']['lang'])
				{
					case 'no':
					case 'nn':
						$vipps_logo .="_NO";
						break;

					default:
						$vipps_logo .="_EN";
						break;
				}
				$payment_methods[] = array(
					'method'	 => 'vipps',
					'logo'		 => $GLOBALS['phpgw']->common->image('bookingfrontend', $vipps_logo)
				);

			}

			/**
			 * Check on return from external payment operator
			 */
			$selected_payment_method =  phpgwapi_cache::session_get('bookingfrontend', 'payment_method');

			self::render_template_xsl('application_contact', array(
				'application'			 => $partial2,
				'delegate_data'			 => $filtered_delegate_data,
				'payment_methods'		 => $payment_methods,
				'selected_payment_method'=> $selected_payment_method,
				'add_img'				 => $GLOBALS['phpgw']->common->image('phpgwapi', 'add2'),
				'config'				 => CreateObject('phpgwapi.config', 'booking')->read(),
				'payment_order_id'		 => $payment_order_id
				)
			);
		}

		private function add_organization( $partial2, $ssn )
		{
			try
			{
				$organization_number = createObject('booking.sfValidatorNorwegianOrganizationNumber')->clean($partial2['customer_organization_number']);
			}
			catch (sfValidatorError $e)
			{
				return false;
			}

			if($organization_number == '000000000')
			{
				return false;
			}

			$organization_info = createObject('bookingfrontend.organization_helper')->get_organization($organization_number);

			if(!$organization_info)
			{
				$this->errors['organization_number'] = "Kunne ikke finne nummeret '{$organization_number}' i Brønnøysundregistrene";
			}

			$activities = CreateObject('booking.soactivity')->read(array('filters' => array('active' => 1)));

			// just guessing...
			$first_activity = $activities['results'][0]['id'];

			if (!empty($organization_info['organisasjonsnummer']))
			{
				$postadresse	 = $organization_info['postadresse'];
				$organization	 = array(
					'customer_internal'				 => 0,
					'show_in_portal'				 => 1,
					'customer_ssn'					 => $ssn,
					'active'						 => 1,
					'organization_number'			 => $organization_number,
					'customer_identifier_type'		 => 'organization_number',
					'customer_organization_number'	 => $organization_number,
					'name'							 => $organization_info['navn'] . ' [ikke validert]',
					'shortname'						 => substr($organization_info['navn'], 0, 11),
					'street'						 => implode(' ', $postadresse['adresse']),
					'zip_code'						 => $postadresse['postnummer'],
					'city'							 => $postadresse['poststed'],
					'activity_id'					 => $first_activity,
					'homepage'						 => 'N/A',
					'phone'							 => 'N/A',
					'description'					 => 'N/A',
					'district'						 => 'N/A',
					'contacts'						 => array(
						array(
							'name'	 => $partial2['contact_name'],
							'email'	 => $partial2['contact_email'],
							'phone'	 => $partial2['contact_phone']
						)
					)
				);

				if( $organization_info['hjemmeside'])
				{
					$organization['homepage'] = $organization_info['hjemmeside'];
				}
			}
			else
			{
				return false;
			}

			$receipt = array();

			$errors = $this->organization_bo->validate($organization);

			if(!$errors)
			{
				/**
				 * Email won't validate
				 */
				$organization['email'] = 'N/A';

				/**
				 * TEMPORARY!!
				 * Bypassing acl on create
				 */
				$receipt = CreateObject('booking.soorganization')->add($organization);
//				$receipt = $this->organization_bo->add($organization);
				$organization['id'] = $receipt['id'];
			}

			return $organization;
		}

		private function update_user_info($application, $external_login_info = array() )
		{
			if(empty($external_login_info['ssn']))
			{
				return;
			}

			$user_id = CreateObject('booking.souser')->get_user_id($external_login_info['ssn']);
			if($user_id)
			{
				$bo_user = CreateObject('booking.bouser');
				$user	 = $bo_user->read_single($user_id);

				$update_user = false;
				if((empty($user['phone']) && $application['contact_phone']) || $user['phone'] != $application['contact_phone'])
				{
					$update_user = true;
					$user['phone'] = $application['contact_phone'];
				}
				if(empty($user['email']) && $application['contact_email'])
				{
					$update_user = true;
					$user['email'] = $application['contact_email'];
				}
				if($external_login_info['street'])
				{
					$update_user = true;
					$user['street'] = $external_login_info['street'];
				}
				if($external_login_info['zip_code'])
				{
					$update_user = true;
					$user['zip_code'] = $external_login_info['zip_code'];
				}
				if($external_login_info['city'])
				{
					$update_user = true;
					$user['city'] = $external_login_info['city'];
				}

				if($external_login_info['last_name'])
				{
					$update_user = true;
					$user['name'] = empty($external_login_info['middle_name']) ? "{$external_login_info['last_name']} {$external_login_info['first_name']}" :  "{$external_login_info['last_name']} {$external_login_info['first_name']} {$external_login_info['middle_name']}";
				}

				if($update_user && !$bo_user->validate($user))
				{
					$bo_user->update($user);
				}
			}
		}
		public function confirm() {
        	self::render_template_xsl('application_new_confirm', array());
        }

		public function edit()
		{
			$id = phpgw::get_var('id', 'int');
			if (!$id)
			{
				phpgw::no_access('booking', lang('missing id'));
			}
			$application = $this->bo->read_single($id);

			if(!$application)
			{
				phpgw::no_access('booking', lang('missing entry. Id %1 is invalid', $id));
			}

			$resource_participant_limit_gross = CreateObject('booking.soresource')->get_participant_limit($application['resources'], true);
			if(!empty($resource_participant_limit_gross['results'][0]['quantity']) && $resource_participant_limit_gross['results'][0]['quantity'] > 0)
			{
				$resource_participant_limit = $resource_participant_limit_gross['results'][0]['quantity'];
				phpgwapi_cache::message_set(lang('overridden participant limit is set to %1', $resource_participant_limit),'message');
			}

			$activity_path = $this->activity_bo->get_path($application['activity_id']);
			$top_level_activity = $activity_path ? $activity_path[0]['id'] : 0;

			$this->check_application_assigned_to_current_user($application);

			$building_info = $this->bo->so->get_building_info($id);
			$application['building_id'] = $building_info['id'];
			$application['building_name'] = $building_info['name'];

			$errors = array();
			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				array_set_default($_POST, 'resources', array());
				array_set_default($_POST, 'accepted_documents', array());

				$application = array_merge($application, extract_values($_POST, $this->fields));
				$application['message'] = phpgw::get_var('comment', 'html', 'POST');
				$this->agegroup_bo->extract_form_data($application);
				$this->extract_customer_identifier($application);

				if ($application['frontend_modified'] == '')
				{
					unset($application['frontend_modified']);
				}

				foreach ($_POST['from_'] as &$from)
				{
					$from = date("Y-m-d H:i:s", phpgwapi_datetime::date_to_timestamp($from));
				}
				foreach ($_POST['to_'] as &$to)
				{
					$to = date("Y-m-d H:i:s", phpgwapi_datetime::date_to_timestamp($to));
				}

				$application['dates'] = array_map(array($this, '_combine_dates'), $_POST['from_'], $_POST['to_']);

				$errors = $this->validate($application);

				if (!$errors)
				{
					$receipt = $this->bo->update($application);
					/**
					 * Start dealing with the purchase_order..
					 */
					$purchase_order = array(
						'application_id' => $id,
						'status' => 0,
						'customer_id' => -1,
						'lines' => array());

					$selected_articles = (array)phpgw::get_var('selected_articles');

					foreach ($selected_articles as $selected_article)
					{
						$_article_info = explode('_', $selected_article);

						if(empty($_article_info[0]))
						{
							continue;
						}

						/**
						 * the value selected_articles[]
						 * <mapping_id>_<quantity>_<tax_code>_<ex_tax_price>_<parent_mapping_id>
						 */
						$purchase_order['lines'][] = array(
							'article_mapping_id'	=> $_article_info[0],
							'quantity'				=> $_article_info[1],
							'tax_code'				=> $_article_info[2],
							'ex_tax_price'			=> $_article_info[3],
							'parent_mapping_id'		=> !empty($_article_info[4]) ? $_article_info[4] : null
						);
					}

					if(!empty($purchase_order['lines']))
					{
						createObject('booking.sopurchase_order')->add_purchase_order($purchase_order);
					}


					$this->bo->send_notification($application);
					self::redirect(array('menuaction' => $this->url_prefix . '.show', 'id' => $application['id']));
				}
			}

			$GLOBALS['phpgw']->jqcal2->add_listener('start_date', 'datetime', !empty($application['dates'][0]['from_']) ? strtotime($application['dates'][0]['from_']) : 0);
			$GLOBALS['phpgw']->jqcal2->add_listener('end_date', 'datetime', !empty($application['dates'][0]['to_']) ? strtotime($application['dates'][0]['to_']) : 0);

			foreach ($application['dates'] as &$date)
			{
				$date['from_'] = pretty_timestamp($date['from_']);
				$date['to_'] = pretty_timestamp($date['to_']);
			}

			$this->flash_form_errors($errors);
			$this->set_case_officer($application);
			self::adddatetimepicker();

			$current_app = $GLOBALS['phpgw_info']['flags']['currentapp'] ? $GLOBALS['phpgw_info']['flags']['currentapp'] : 'booking';

			$application['resources_json'] = json_encode(array_map('intval', $application['resources']));
			$application['accepted_documents_json'] = json_encode($application['accepted_documents']);
			$application['cancel_link'] = self::link(array('menuaction' => $current_app . '.uiapplication.index'));
			$activities = $this->activity_bo->fetch_activities();
			$activities = $activities['results'];
			$agegroups = $this->agegroup_bo->fetch_age_groups($top_level_activity, $include_inactive = true);
			$agegroups = $agegroups['results'];
			$audience = $this->audience_bo->fetch_target_audience($top_level_activity);
			$audience = $audience['results'];
			$this->install_customer_identifier_ui($application);
			$application['customer_identifier_types']['ssn'] = 'SSN';
			$application['audience_json'] = json_encode(array_map('intval', $application['audience']));

			if (phpgw::get_var('phpgw_return_as', 'string', 'GET') == 'json' )
			{
				echo json_encode(array(
						'application' => $application,
						'activities' => $activities,
						'agegroups' => $agegroups,
						'audience' => $audience
					)
				);

				$GLOBALS['phpgw']->common->phpgw_exit();
			}
			$config = CreateObject('phpgwapi.config', 'booking')->read();

			if ($GLOBALS['phpgw_info']['flags']['currentapp'] != 'bookingfrontend')
			{
				self::rich_text_editor('field_agreement_requirements');
				$tabs = array();
				$tabs['generic'] = array('label' => lang('Application Edit'), 'link' => '#application_edit');
				$active_tab = 'generic';
				self::add_javascript('booking', 'base', 'application.js');
				$associations = $this->assoc_bo->so->read(array('filters' => array('application_id' => $application['id']),
				'sort' => 'from_', 'dir' => 'asc', 'results' =>'all'));

				self::add_javascript('phpgwapi', 'dateformatter', 'dateformatter.js');

				if(!empty($config['activate_application_articles']))
				{
					if($associations['total_records'] > 0)
					{
						self::add_javascript('bookingfrontend', 'base', 'purchase_order_show.js');
					}
					else
					{
						self::add_javascript('booking', 'base', 'purchase_order_edit.js');
					}
				}

				$application['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			}
			else
			{
				self::add_javascript('bookingfrontend', 'base', 'application.js');
			}

			phpgwapi_jquery::formvalidator_generate(array('location', 'date', 'security',
				'file'), 'application_form');

			$GLOBALS['phpgw']->js->validate_file('alertify', 'alertify.min', 'phpgwapi');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/alertify/css/alertify.min.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/alertify/css/themes/bootstrap.min.css');

			self::render_template_xsl('application_edit', array(
				'application' => $application,
				'activities' => $activities,
				'agegroups' => $agegroups,
				'audience' => $audience,
				'config' => $config,
				'tax_code_list'	=> json_encode(execMethod('booking.bogeneric.read', array('location_info' => array('type' => 'tax', 'order' => 'id')))),
			));
		}

		private function check_date_availability( &$allocation )
		{
			foreach ($allocation['dates'] as &$date)
			{
				$available = $this->bo->check_timespan_availability($allocation['resources'], $date['from_'], $date['to_']);
				$date['status'] = intval($available);
				$date['allocation_params'] = $this->event_for_date($allocation, $date['id']);
				$date['booking_params'] = $this->event_for_date($allocation, $date['id']);
				$date['event_params'] = $this->event_for_date($allocation, $date['id']);
			}
		}

		private function event_for_date( $application, $date_id )
		{
			foreach ($application['dates'] as $d)
			{
				if ($d['id'] == $date_id)
				{
					$date = $d;
					break;
				}
			}
			$event = array();
			$event[] = array('from_', pretty_timestamp($date['from_']));
			$event[] = array('to_', pretty_timestamp($date['to_']));
			$event[] = array('cost', '0');
			$event[] = array('application_id', $application['id']);
			$event[] = array('reminder', '0');
			$copy = array(
				'activity_id', 'name', 'organizer', 'homepage', 'description', 'equipment', 'contact_name',
				'contact_email', 'contact_phone', 'activity_id', 'building_id', 'building_name',
				'customer_identifier_type', 'customer_ssn', 'customer_organization_number',
				'customer_organization_id',	'customer_organization_name'
			);
			foreach ($copy as $f)
			{
//				$event[] = array($f, htmlentities(html_entity_decode($application[$f])), ENT_QUOTES | ENT_SUBSTITUTE);
				$event[] = array($f, html_entity_decode((string)$application[$f]));
			}
			foreach ($application['agegroups'] as $ag)
			{
				$event[] = array('male[' . $ag['agegroup_id'] . ']', $ag['male']);
				$event[] = array('female[' . $ag['agegroup_id'] . ']', $ag['female']);
			}
			foreach ($application['audience'] as $a)
			{
				$event[] = array('audience[]', $a);
			}
			foreach ($application['resources'] as $r)
			{
				$event[] = array('resources[]', $r);
			}
			return json_encode($event, JSON_HEX_QUOT);
		}

		protected function extract_display_in_dashboard_value()
		{
			$val = phpgw::get_var('display_in_dashboard', 'int', 'POST', 0);
			if ($val <= 0)
			{
				return false;
			}
			if ($val >= 1)
			{
				return true;
			}
			return false; //Not that I think that it is necessary to return here too, but who knows, I might have overlooked something.
		}

		private function _get_pdf_header($application,$config, $preview)
		{
			$dateformat	 = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$date		 = $GLOBALS['phpgw']->common->show_date(time(), $dateformat);
			$pdf = CreateObject('phpgwapi.pdf');

			$pdf->ezSetMargins(50, 70, 50, 50);
			$pdf->selectFont('Helvetica');

			// put a line top and bottom on all the pages
			$all = $pdf->openObject();
			$pdf->saveState();

//			if (!empty($GLOBALS['phpgw_info']['server']['logo_url']))
			{
				//cheating...
				$footerlogoimg = PHPGW_SERVER_ROOT ."/phpgwapi/templates/bookingfrontend/img/logo-kristiansand.png";
		//		_debug_array($footerlogoimg);die();
		//		$pdf->addJpegFromFile($footerlogoimg, 40, 800, 80);
				$pdf->addPngFromFile($footerlogoimg, 45, 780, 80);
				$pdf->ezSetDy(-20);
		//		$pdf->ezImage($footerlogoimg,$pad = 0,$width = 60,$resize = '',$just = 'left',$border = '');
			}


			$pdf->setStrokeColor(0, 0, 0, 1);
			$pdf->line(20, 40, 578, 40);
			//	$pdf->line(20,820,578,820);
			//	$pdf->addText(50,823,6,lang('order'));
			$pdf->addText(50, 28, 6, $config['org_name']);
			$pdf->addText(300, 28, 6, $date);

			if ($preview)
			{
				$pdf->setColor(1, 0, 0);
				$pdf->addText(200, 400, 40, lang('preview'), -10);
				$pdf->setColor(1, 0, 0);
			}

			$pdf->restoreState();
			$pdf->closeObject();
			// note that object can be told to appear on just odd or even pages by changing 'all' to 'odd'
			// or 'even'.
			$pdf->addObject($all, 'all');

//			$pdf->ezSetDy(-100);

			$pdf->ezStartPageNumbers(500, 28, 6, 'right', '{PAGENUM} ' . lang('of') . ' {TOTALPAGENUM}', 1);

			$organisation	 = '';

			if (isset($config['org_name']))
			{
				$organisation = $config['org_name'];
			}
			if (isset($config['department']))
			{
				$department = $config['department'];
			}

			$data = array(
				array(
					'col1'	 => lang('application') . " <b>{$application['id']}</b>",
					'col2'	 => lang('date') . ": {$date}"
			));

			$pdf->ezTable($data, array('col1' => '', 'col2' => ''), '', array(
				'showHeadings'	 => 0,
				'shaded'		 => 0,
				'xPos'			 => 'left',
				'xOrientation'	 => 'right',
				'width'			 => 500,
				'gridlines'		 => EZ_GRIDLINE_ALL,
				'cols'			 => array(
					'col1'	 => array('justification' => 'right', 'width' => 250, 'justification' => 'left'),
					'col2'	 => array('justification' => 'right', 'width' => 250, 'justification' => 'left'),
				)
			));

			$from_name = $GLOBALS['phpgw_info']['user']['fullname'];

			$data = array(
				array(
					'col1'	 => "{$organisation}\n{$department}\nOrg.nr: {$config['org_unit_id']}",
					'col2'	 => "Saksbehandler: {$from_name}"//\nRessursnr.: {$ressursnr}"
				),
			);
			
			$pdf->ezTable($data, array('col1' => '', 'col2' => ''), '', array(
				'showHeadings'	 => 0,
				'shaded'		 => 0,
				'xPos'			 => 'left',
				'xOrientation'	 => 'right',
				'width'			 => 500,
				'gridlines'		 => EZ_GRIDLINE_ALL,
				'cols'			 => array(
					'col1'	 => array('justification' => 'right', 'width' => 250, 'justification' => 'left'),
					'col2'	 => array('justification' => 'right', 'width' => 250, 'justification' => 'left'),
				)
			));

			return $pdf;
		}

		private function _get_pdf_1($application, $config, $export_text, $preview)
		{

			set_time_limit(1800);

			$pdf = $this->_get_pdf_header($application, $config, $preview);


			$pdf->ezSetDy(-20);
			$pdf->selectFont('Helvetica-Bold');
			$pdf->ezText($export_text['title'], 14);
			$pdf->selectFont('Helvetica');

			$html2text	 = createObject('phpgwapi.html2text', $export_text['body']);
			$text		 = trim($html2text->getText());

			$pdf->ezSetDy(-20);
			$pdf->ezText($text, 12);

			return $pdf;
		}

		private function _get_pdf_2($application, $config, $export_text, $preview)
		{
			$pdf = $this->_get_pdf_header($application, $config, $preview);

			$pdf->ezSetDy(-20);
			$pdf->selectFont('Helvetica-Bold');
			$pdf->ezText($export_text['title'], 14);
			$pdf->selectFont('Helvetica');

			$html2text	 = createObject('phpgwapi.html2text', $export_text['body']);
			$text		 = trim($html2text->getText());

			$pdf->ezSetDy(-20);
			$pdf->ezText($text, 12);

			return $pdf;
		}

		public function export_pdf()
		{
//			$cases = createObject('booking.public360')->get_cases('2022000052');
//			_debug_array($cases);
//			die();


			$id = phpgw::get_var('id', 'int');
			if (!$id)
			{
				phpgw::no_access('booking', lang('missing id'));
			}
			$config	= CreateObject('phpgwapi.config', 'property')->read();
			$application = $this->bo->read_single($id);
			$this->set_case_officer($application);

			if(!$application['case_officer']['is_current_user'])
			{
				phpgw::no_access('booking', lang('not case officer'));
			}

			$preview = phpgw::get_var('preview', 'bool');

			$GLOBALS['phpgw_info']['flags']['noheader']	 = true;
			$GLOBALS['phpgw_info']['flags']['nofooter']	 = true;
			$GLOBALS['phpgw_info']['flags']['xslt_app']	 = false;


			$lang_application = lang('application');

			$export_text1 = $this->bo->get_export_text1($application, $config);
			$export_text2 = $this->bo->get_export_text2($application, $config);

			$pdf1 = $this->_get_pdf_1($application, $config, $export_text1, $preview);
			$file_data1 = $pdf1->ezOutput();
			$pdf2 = $this->_get_pdf_2($application, $config, $export_text2, $preview);
			$file_data2 = $pdf2->ezOutput();

			$file_name1 = "{$lang_application}_{$application['id']}.pdf";
			$file_name2 = "{$lang_application}_{$application['id']}_2.pdf";

			if ($preview)
			{
				$pdf_preview_alternate = phpgwapi_cache::session_get('booking', 'pdf_preview_alternate');
				if(empty($pdf_preview_alternate))
				{
					phpgwapi_cache::session_set('booking', 'pdf_preview_alternate', 1);
					$pdf1->print_pdf($file_data1, "{$lang_application}_{$application['id']}_1");
				}
				else
				{
					phpgwapi_cache::session_set('booking', 'pdf_preview_alternate', 0);
					$pdf1->print_pdf($file_data2, "{$lang_application}_{$application['id']}_2");
				}
			}
			else
			{
				$location_id = $GLOBALS['phpgw']->locations->get_id('booking', 'run');
				$custom_config = CreateObject('admin.soconfig', $location_id);
				$method = $custom_config->config_data['common_archive']['method'];

				if($method)
				{
					$archive = createObject("booking.{$method}");

					$files = array();
					$files[] = array(
						'file_name' => $file_name1,
						'file_data' => $file_data1
						);

					unset($file_data1);

					$sodocument_application = CreateObject('booking.sodocument_application');

					$submitted_files = $sodocument_application->read(array('filters' => array('owner_id' => $application['id']), 'results' =>'all'));

					foreach ($submitted_files['results'] as $submitted_file)
					{
						$document = $sodocument_application->read_single($submitted_file['id']);

						$file_content = file_get_contents($document['filename']);

						$files[] = array(
							'file_name'	 => basename($document['filename']),
							'file_data' => $file_content ? $file_content : 'Dummytext'
						);
						unset($file_content);
					}

					$attachments = $this->bo->get_related_files($application);

					foreach ($attachments as $attachment)
					{
						$file_content = file_get_contents($attachment['file']);
						$files[] = array(
							'file_name' => $attachment['name'],
							'file_data' => $file_content ? $file_content : 'Dummytext'
							);
					}

					unset($file_content);

					/**
					 * Add the outgoing file at the end of the file-list
					 */
					$files[] = array(
						'file_name' => $file_name2,
						'file_data' => $file_data2
						);

					$resourcename = implode(", ", $this->bo->get_resource_name($application['resources']));

					if(!empty($application['customer_organization_name']))
					{
						$customer_name = $application['customer_organization_name'];
					}
					else
					{
						$customer_name = $application['contact_name'];
					}
					
					$case_title = "{$application['building_name']}/{$resourcename} – utleie til arrangement – {$customer_name} - ref.nr. {$application['id']}";

					$result = $archive->export_data(
						array(
							$case_title,
							$export_text1['title'],
							$export_text2['title']
						),
						$application,
						$files
					);

					//update application with external_archive_key
					if (!empty($result['external_archive_key']))
					{
						$this->bo->update_external_archive_reference($application['id'], $result['external_archive_key']);
					}
					else
					{
						phpgwapi_cache::message_set( 'overføring feilet', 'error');
					}
				}
				else
				{
					phpgwapi_cache::message_set( 'integrasjonsmetode er ikke konfigurert', 'error');
				}
				self::redirect(array('menuaction' => $this->url_prefix . '.show', 'id' => $application['id'], 'return_after_action' => true));
			}

		}
		public function show()
		{
			$id = phpgw::get_var('id', 'int');
			if (!$id)
			{
				phpgw::no_access('booking', lang('missing id'));
			}
			$application = $this->bo->read_single($id);

			if(!$application)
			{
				phpgw::no_access('booking', lang('missing entry. Id %1 is invalid', $id));
			}

			$resource_participant_limit_gross = CreateObject('booking.soresource')->get_participant_limit($application['resources'], true);
			if(!empty($resource_participant_limit_gross['results'][0]['quantity']) && $resource_participant_limit_gross['results'][0]['quantity'] > 0)
			{
				$resource_participant_limit = $resource_participant_limit_gross['results'][0]['quantity'];
				phpgwapi_cache::message_set(lang('overridden participant limit is set to %1', $resource_participant_limit),'message');
			}

			$activity_path = $this->activity_bo->get_path($application['activity_id']);
			$top_level_activity = $activity_path ? $activity_path[0]['id'] : 0;
			$tabs = array();
			$tabs['generic'] = array('label' => lang('Application'), 'link' => '#application');
			$active_tab = 'generic';

			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				if ($_POST['create'])
				{
					self::redirect(array('menuaction' => $this->url_prefix . '.show', 'id' => $application['id']));
				}

				$update = false;
				$notify = false;

				$return_after_action = false;

				if ($application['frontend_modified'] == '')
				{
					unset($application['frontend_modified']);
				}

				if (array_key_exists('internal_note_content', $_POST))
				{
					$internal_note_content = phpgw::get_var('internal_note_content', 'string');
					$this->add_internal_note($application['id'], $internal_note_content);
					self::redirect(array('menuaction' => $this->url_prefix . '.show', 'id' => $application['id'], 'return_after_action' => true));
				}

				if (array_key_exists('message_recipient', $_POST))
				{
					$message_recipient = phpgw::get_var('message_recipient', 'int');
					$message_subject = phpgw::get_var('message_subject', 'string');
					$message_content = phpgw::get_var('message_content', 'string');
					createobject('messenger.somessenger')->send_message(array(
						'to' => $message_recipient,
						'subject' => $message_subject,
						'content' => $message_content));
					self::redirect(array('menuaction' => $this->url_prefix . '.show', 'id' => $application['id'], 'return_after_action' => true));
				}

				if (array_key_exists('assign_to_new_user', $_POST))
				{
					$update = $this->assign_to_new_user($application, phpgw::get_var('assign_to_new_user', 'int'));
					if ($application['status'] == 'NEW')
					{
						$application['status'] = 'PENDING';
					}
					$return_after_action = true;
				}
				else if (array_key_exists('assign_to_user', $_POST))
				{
					$update = $this->assign_to_current_user($application);
					if ($application['status'] == 'NEW')
					{
						$application['status'] = 'PENDING';
					}
					$return_after_action = true;
				}
				else if (isset($_POST['unassign_user']))
				{
					if ($this->unassign_current_user($application))
					{
						$this->set_display_in_dashboard($application, true, array('force' => true));
						$update = true;
					}
					$return_after_action = true;
				}
				else if (isset($_POST['display_in_dashboard']))
				{
					$this->check_application_assigned_to_current_user($application);
					$update = $this->set_display_in_dashboard($application, $this->extract_display_in_dashboard_value());
					$return_after_action = true;
				}
				else if (isset($_POST['status']))
				{
					$this->check_application_assigned_to_current_user($application);
					$application['status'] = phpgw::get_var('status', 'string', 'POST');

					if ($application['status'] == 'REJECTED')
					{
						$test = $this->assoc_bo->so->read(array('filters' => array('application_id' => $application['id']), 'results' =>'all'));
						foreach ($test['results'] as $app)
						{
							$this->bo->so->set_inactive($app['id'], $app['type']);
						}
					}

					if ($application['status'] == 'ACCEPTED')
					{
						$test = $this->assoc_bo->so->read(array('filters' => array('application_id' => $application['id']), 'results' =>'all'));
						foreach ($test['results'] as $app)
						{
							$this->bo->so->set_active($app['id'], $app['type']);
						}
					}

					$update = true;
					$notify = true;
					$return_after_action = true;
				}
				else if ($_FILES)
				{
					/** Start attachment * */
					$document_application = createObject('booking.uidocument_application');

					$oldfiles = $document_application->bo->so->read(array('filters' => array('owner_id' => $application['id']), 'results' =>'all'));
					$files = $this->get_files_from_post();
					$file_exist = false;

					if ($oldfiles['results'])
					{
						foreach ($oldfiles['results'] as $old_file)
						{
							if ($old_file['name'] == $files['name']['name'])
							{
								$file_exist = true;
								phpgwapi_cache::message_set(lang('file exists'),'error');
								break;
							}
						}
					}

					$document = array(
						'category' => 'other',
						'owner_id' => $application['id'],
						'files' => $this->get_files_from_post()
					);
					$document_errors = $document_application->bo->validate($document);

					if (!$document_errors && !$file_exist)
					{
						try
						{
							booking_bocommon_authorized::disable_authorization();
							$document_receipt = $document_application->bo->add($document);
						}
						catch (booking_unauthorized_exception $e)
						{
							phpgwapi_cache::message_set(lang('Could not add object due to insufficient permissions'),'error');
						}
					}

					/** End attachment * */
				}

				/**
				 * Sigurd 1/5-2018: har på forespørsel fra Lindås flyttet comment ut i egen blokk - sjekk om det får utilsiktede konskveser.
				 */
				if ($_POST['comment'])
				{
					$application['comment'] = phpgw::get_var('comment', 'html', 'POST');
					$this->add_comment($application, $application['comment']);
					$update = true;
					$notify = true;
					$return_after_action = true;
				}

				$update AND $receipt = $this->bo->update($application);

				if($notify)
				{
					$log_msg = '';
					$_application = $application;
					$_application['status'] = phpgw::get_var('status', 'string', 'POST');
					$recipient = $this->bo->send_notification($_application);
					if($recipient)
					{
						$log_msg .= "Epost er sendt til {$recipient}";
					}
					if(phpgw::get_var('status', 'string', 'POST'))
					{
						$log_msg .= "\nStatus: ". strtolower(lang($application['status']));
					}

					if($log_msg)
					{
						phpgwapi_cache::message_set($log_msg);
						$this->add_comment($application, $log_msg);
						$this->bo->update($application);
					}
				}

				self::redirect(array('menuaction' => $this->url_prefix . '.show', 'id' => $application['id'], 'return_after_action' => $return_after_action));
			}

			$application['dashboard_link'] = self::link(array('menuaction' => 'booking.uidashboard.index'));
			$application['applications_link'] = self::link(array('menuaction' => 'booking.uiapplication.index'));
			$application['edit_link'] = self::link(array('menuaction' => 'booking.uiapplication.edit',
					'id' => $application['id']));
			$building_info = $this->bo->so->get_building_info($id);
			$application['building_id'] = $building_info['id'];
			$application['building_name'] = $building_info['name'];

			$cal_date = strtotime($application['dates'][0]['from_']);
			$cal_date = date('Y-m-d', $cal_date);

			$application['schedule_link'] = self::link(array('menuaction' => 'bookingfrontend.uibuilding.schedule',
					'id' => $building_info['id'], 'backend' => true, 'date' => $cal_date));

			//manipulating the link. we want to use the frontend module instead of backend for displaying the schedule
			$pos = strpos($application['schedule_link'], '/index.php');
			$application['schedule_link'] = substr_replace($application['schedule_link'], 'bookingfrontend/', $pos + 1, 0);

			$simple = false;
			$resource_ids = '';
			foreach ($application['resources'] as $res)
			{
				$resource_ids = $resource_ids . '&filter_id[]=' . $res;
			}
			if (count($application['resources']) == 0)
			{
				unset($application['dates']);
			}
			else
			{
				$resource_filters = array('active' => 1, 'rescategory_active' => 1, 'id' => $application['resources'] );
				$_resources = $this->resource_bo->so->read(array('filters' => $resource_filters, 'sort' => 'sort', 'results' => -1));
				$_building_simple_booking = 0;
				foreach ($_resources['results'] as $_resource)
				{
					if($_resource['simple_booking'] == 1)
					{
						$_building_simple_booking ++;
					}
				}

				if($_building_simple_booking == count($application['resources']))
				{
					$simple = true;
				}

			}
			$application['resource_ids'] = $resource_ids;

			$this->set_case_officer($application);

			//	$comments = array_reverse($application['comments']); //fixed in db
			$agegroups = $this->agegroup_bo->fetch_age_groups($top_level_activity, $include_inactive = true);
//			_debug_array($application);
//			_debug_array($agegroups);
			$agegroups = $agegroups['results'];
			$audience = $this->audience_bo->fetch_target_audience();
			$audience = $audience['results'];
			// Check if any bookings, allocations or events are associated with this application
			$associations = $this->assoc_bo->so->read(array('filters' => array('application_id' => $application['id']),
				'sort' => 'from_', 'dir' => 'asc', 'results' =>'all'));

			$from = array();
			foreach ($associations['results'] as $assoc)
			{
				$from[] = $assoc['from_'];
			}
			$from = array("data" => implode(',', $from));
			$num_associations = $associations['total_records'];
			if ($this->is_assigned_to_current_user($application) || $GLOBALS['phpgw']->acl->check('admin', phpgwapi_acl::ADD, 'booking'))
			{
				$application['currentuser'] = true;
			}
			else
			{
				$application['currentuser'] = false;
			}

			$collision_dates = array();
			foreach ($application['dates'] as &$date)
			{
				$collision = $this->bo->so->check_collision($application['resources'], $date['from_'], $date['to_']);
				if ($collision)
				{
					$collision_dates[] = $date['from_'];
				}
			}
			$collision_dates = array("data" => implode(',', $collision_dates));
			self::check_date_availability($application);
			$application['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			phpgwapi_jquery::formvalidator_generate(array('file'), 'file_form');
			self::rich_text_editor('comment');
			$application['description'] = html_entity_decode(nl2br($application['description']));
			$application['equipment'] = html_entity_decode(nl2br($application['equipment']));

			if(!empty($application['comments']))
			{
				foreach ($application['comments'] as  &$comments)
				{
					$comments['comment'] = html_entity_decode(nl2br($comments['comment']));
				}
			}

			$location_id = $GLOBALS['phpgw']->locations->get_id('booking', 'run');
			$custom_config = CreateObject('admin.soconfig', $location_id);
			$external_archive = !empty($custom_config->config_data['common_archive']['method']) ? $custom_config->config_data['common_archive']['method'] : '';

			if(phpgw::get_var('return_after_action', 'bool'))
			{
				$js =<<<JS
				$(document).ready(function ()
				{
					var return_after_action = document.getElementById("return_after_action");
					return_after_action.scrollIntoView();
				});
JS;
				$GLOBALS['phpgw']->js->add_code('', $js);
			}

			$orgid = $this->organization_bo->so->get_orgid($application['customer_organization_number']);
			$organization = $this->organization_bo->read_single($orgid); // empty array if not found

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('application') . ' # ' . $application['id'] . ' - ' . $application['building_name'];
			$GLOBALS['phpgw_info']['flags']['breadcrumb_selection'] = $GLOBALS['phpgw_info']['flags']['app_header'];
			$this->is_assigned_to($application);

			$internal_notes = CreateObject('phpgwapi.historylog','booking', '.application')->return_array(array(),array('C'),'history_timestamp','ASC',$application['id']);

			$application['resources_json'] = json_encode(array_map('intval', $application['resources']));

			self::add_javascript('booking', 'base', 'application.show.js');
			$config = CreateObject('phpgwapi.config', 'booking')->read();
			if(!empty($config['activate_application_articles']))
			{
				self::add_javascript('bookingfrontend', 'base', 'purchase_order_show.js');			
			}
			phpgwapi_jquery::load_widget('select2');

			self::render_template_xsl('application', array(
				'application'		 => $application,
				'organization'		 => $organization,
				'audience'			 => $audience,
				'agegroups'			 => $agegroups,
				'num_associations'	 => $num_associations,
				'assoc'				 => $from,
				'collision'			 => $collision_dates,
				'comments'			 => $comments,
				'simple'			 => $simple,
				'config'			 => $config,
				'export_pdf_action'	 => self::link(array('menuaction' => 'booking.uiapplication.export_pdf', 'id' => $application['id'])),
				'external_archive'	 => !empty($GLOBALS['phpgw_info']['user']['preferences']['common']['archive_user_id']) ? $external_archive : '',
				'user_list'			 => array('options' => createObject('booking.sopermission_building')->get_user_list()),
				'internal_notes'	 => $internal_notes
				)
			);
		}

		function get_activity_data()
		{
			$activity_id = phpgw::get_var('activity_id', 'int', 'REQUEST', -1);
			$activity_path = $this->activity_bo->get_path($activity_id);
			$top_level_activity = $activity_path ? $activity_path[0]['id'] : -1;
			$agegroups = $this->agegroup_bo->fetch_age_groups($top_level_activity);
			$audience = $this->audience_bo->fetch_target_audience($top_level_activity);
			return array(
				'agegroups' => $agegroups['results'],
				'audience' => $audience['results'],
			);
		}


		// Returns a list of basic data for the partial applications for the current session ID
		function get_partials()
		{
			$ret		 = array();
			$list		 = array();
			$session_id	 = $GLOBALS['phpgw']->session->get_session_id();
			if (!empty($session_id))
			{
				$partials = $this->bo->get_partials_list($session_id);
				foreach ($partials['results'] as $partial)
				{
					$item					 = array('orders' => $partial['orders']);
					$item['id']				 = $partial['id'];
					$item['building_name']	 = $partial['building_name'];
					$item['dates']			 = $partial['dates'];
					$resources				 = $this->resource_bo->so->read(array(
						'sort'		 => 'sort',
						'results'	 => 'all',
						'filters'	 => array('id' => $partial['resources']), 'results'	 => 'all'
					));
					foreach ($resources['results'] as $resource)
					{
						$res				 = array(
							'id'	 => $resource['id'],
							'name'	 => $resource['name'],
						);
						$item['resources'][] = $res;
					}
					$list[] = $item;
				}

				$ret = array(
					'list'		 => $list,
					'total_sum'	 => $partials['total_sum'],
				);
			}
			return $ret;
		}

		function delete_partial()
		{
			$status = array('deleted' => false);
			$id = phpgw::get_var('id', 'int', 'POST');
			$session_id = $GLOBALS['phpgw']->session->get_session_id();
			if (!empty($session_id) && $id > 0)
			{
				$partials = $this->get_partials($session_id);

				$GLOBALS['phpgw']->db->transaction_begin();

				$bo_block = createObject('booking.boblock');

				$exists = false;
				foreach ($partials['list'] as $partial)
				{
					if ($partial['id'] == $id)
					{
						$bo_block->cancel_block($session_id, $partial['dates'],$partial['resources']);
						$exists = true;
						break;
					}
				}
				if ($exists)
				{
					$application_id = $id;
					createObject('booking.sopurchase_order')->delete_purchase_order($application_id);
					$this->bo->delete_application($id);
					$status['deleted'] = true;
				}

				$GLOBALS['phpgw']->db->transaction_commit();

			}
			return $status;
		}

		function delete()
		{
			if (!$this->acl_delete)
			{
				return lang('sorry - insufficient rights');
			}

			$application_id = phpgw::get_var('id', 'int', 'GET');

			$soassociation = new booking_soapplication_association();
			$associations = $soassociation->read(array('results' => -1, 'filters' => array('application_id' => $application_id )));

			if (empty($associations['total_records']) && $application_id)
			{
				$GLOBALS['phpgw']->db->transaction_begin();
				createObject('booking.sopurchase_order')->delete_purchase_order($application_id);
				$this->bo->delete_application($application_id);
				$status = lang('deleted');
				$GLOBALS['phpgw']->db->transaction_commit();
			}
			else
			{
				$status = lang('error');
			}
			return $status;
		}

		function get_event_cost(&$event)
		{
			$filters		 = array('id' => $event['application_id']);
			$params			 = array('filters' => $filters, 'results' => 'all');

			$applications	 = $this->bo->so->read($params);

			$this->bo->so->get_purchase_order($applications);

			$event['cost'] = 0;
			foreach ($applications['results'] as $application)
			{
				foreach ($application['orders'] as $order)
				{
					if (empty($order['paid']))
					{
						$event['cost'] += (float)$order['sum'];
					}
				}
			}
		}

		private function add_payment( array $application_ids )
		{
			$soapplication	 = CreateObject('booking.soapplication');
			$filters		 = array('id' => $application_ids);
			$params			 = array('filters' => $filters, 'results' => 'all');
			$applications	 = $soapplication->read($params);

			$soapplication->get_purchase_order($applications);

			foreach ($applications['results'] as $application)
			{
				foreach ($application['orders'] as $order)
				{
					if (empty($order['paid']))
					{
						$soapplication->add_payment($order['order_id'], 'local_invoice', 'live', 2);
					}
				}
			}
		}

		private function add_internal_note($id, $internal_note_content)
		{
			$historylog	= CreateObject('phpgwapi.historylog','booking', '.application');
			return $historylog->add('C', $id, $internal_note_content);
		}
	}
