<?php
	class booking_bocommon
	{
		public function __construct()
		{
		}
		
		/**
		 * Forwards method invocations to so
		 */
		public function __call($method, $arguments) 
		{
			return call_user_func_array(array($this->so, $method), $arguments);
		}

		function read_single($id)
		{
			return $this->so->read_single($id);
		}
		
		function show_all_objects()
		{
			$_SESSION['showall'] = "1";
		}
		
		function unset_show_all_objects()
		{
			unset($_SESSION['showall']);
		}
		
		public function link($data)
		{
			if($GLOBALS['phpgw_info']['flags']['currentapp'] == 'bookingfrontend')
				return $GLOBALS['phpgw']->link('/bookingfrontend/', $data);
			else
				return $GLOBALS['phpgw']->link('/index.php', $data);
		}

		function read()
		{
			return $this->so->read($this->build_default_read_params());
		}
		
		/**
		 * Returns all rows matching current filters using no limit.
		 */
		function read_all() {
			return $this->so->read($this->build_read_all_params());
		}
		
		protected function build_read_all_params() {
			$params = $this->build_default_read_params();
			unset($params['start']);
			$params['results'] = 'all';
			return $params;
		}
		
		protected function build_default_read_params()
		{
			$start = phpgw::get_var('startIndex', 'int', 'REQUEST', 0);
			$results = phpgw::get_var('results', 'int', 'REQUEST', null);
			$query = phpgw::get_var('query');
			$sort = phpgw::get_var('sort');
			$dir = phpgw::get_var('dir');
			
			$filters = array();
			foreach($this->so->get_field_defs() as $field => $params) {
				if(phpgw::get_var("filter_$field")) {
					$filters[$field] = phpgw::get_var("filter_$field");
				}
			}
			
			if(!isset($_SESSION['showall'])) {
				if(!isset($filters['application_id'])) {
					$filters['active'] = "1";
				}
			}
			
			return array(
				'start' => $start,
				'results' => $results,
				'query'	=> $query,
				'sort'	=> $sort,
				'dir'	=> $dir,
				'filters' => $filters
			);
		}

		function add($entity)
		{
			return $this->so->add($entity);
		}
		function smart_read($entity)
		{
			return $this->so->read($entity);
		}
		
		public function create_error_stack($errors = array())
		{
			return $this->so->create_error_stack($errors);
		}
		
		function validate($entity)
		{
			$error_stack = $this->create_error_stack($this->so->validate($entity));
			$this->doValidate($entity, $error_stack);
			return $error_stack->getArrayCopy();
		}
		
		/**
		 * Implement in subclasses to perform custom validation.
		 */
		protected function doValidate($entity, booking_errorstack $error_stack)
		{
		}

		function update($entity)
		{
			return $this->so->update($entity);
		}

		function delete($id)
		{
			return $this->so->delete($id);
		}
		
		function set_active($id, $active)
		{
			return $this->so->set_active($id, $active);
		}

		/**
		 * Checks if the current user has any role
		 * Use booking_sopermission::ROLE_MANAGER or booking_sopermission::CASE_OFFICER for the role parameter
		 */
		function has_role($role)
		{
			$permission_root_bo = CreateObject('booking.bopermission_root');
			$filters['filters']['role'] = $role;
			$filters['filters']['subject_id'] = $GLOBALS['phpgw_info']['user']['id']; // id for the current user

			$booking_roles = $permission_root_bo->so->read($filters);

			if (intval($booking_roles['total_records']) == 1)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}
