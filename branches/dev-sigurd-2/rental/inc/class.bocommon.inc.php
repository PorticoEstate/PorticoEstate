<?php
	class rental_bocommon
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
			$search_option = phpgw::get_var('search_option');
			$sort = phpgw::get_var('sort');
			$dir = phpgw::get_var('dir');
			$filters['is_active'] = phpgw::get_var('is_active');
			
			
			
			if(!isset($_SESSION['showall']))
			{
				$filters['active'] = "1";
			}		
			return $this->so->read(array(
				'start' => $start,
				'results' => $results,
				'query'	=> $query,
				'search_option' => $search_option,
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

		function validate($entity)
		{
			return $this->so->validate($entity);
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
