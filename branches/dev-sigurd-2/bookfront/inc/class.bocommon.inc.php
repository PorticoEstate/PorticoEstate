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
			return $this->so->read(array(
				'start' => $start,
				'results' => $results,
				'query'	=> $query,
				'sort'	=> $sort,
				'dir'	=> $dir,
				'filters' => $filters
			));
		}

		function add($building)
		{
			return $this->so->add($building);
		}

		function modify($building)
		{
			return $this->so->modify($building);
		}

		function delete($id)
		{
			return $this->so->delete($id);
		}
	}
