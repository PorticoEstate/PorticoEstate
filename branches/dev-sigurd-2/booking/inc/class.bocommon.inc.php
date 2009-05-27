<?php
	class booking_bocommon
	{
		public function __construct()
		{
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
			return $GLOBALS['phpgw']->link('/index.php', $data);
		}

		function read()
		{
			$start = phpgw::get_var('startIndex', 'int', 'REQUEST', 0);
			$results = phpgw::get_var('results', 'int', 'REQUEST', null);
			$query = phpgw::get_var('query');
			$sort = phpgw::get_var('sort');
			$dir = phpgw::get_var('dir');
			$filters = array();
			foreach($this->so->fields as $field => $params)
			{
				if(phpgw::get_var("filter_$field"))
				{
					$filters[$field] = phpgw::get_var("filter_$field");
				}
			}
			if(!isset($_SESSION['showall']))
			{
				$filters['active'] = "1";
			}		
			return $this->so->read(array(
				'start' => $start,
				'results' => $results,
				'query'	=> $query,
				'sort'	=> $sort,
				'dir'	=> $dir,
				'filters' => $filters
			));
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
	}
