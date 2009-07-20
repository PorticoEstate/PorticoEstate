<?php
	phpgw::import_class('rental.socommon');
	
	include_class('rental', 'price_item', 'inc/model/');
	
	class rental_soprice_item extends rental_socommon
	{
		function __construct()
		{
			parent::__construct('rental_price_item',
			array
			(
				'id'	=> array('type' => 'int'),
				'title' => array('type' => 'string'),
				'agresso_id' => array('type', 'string'),
	 			'is_area'	=> array('type' => 'bool'),
				'price' => array('type' => 'float')
			));
		}
		
		/**
		 * Get a list of price_item objects matching the specific filters
		 * 
		 * @param $start search result offset
		 * @param $results number of results to return
		 * @param $sort field to sort by
		 * @param $query LIKE-based query string
		 * @param $filters array of custom filters
		 * @return list of rental_composite objects
		 */
		function get_price_item_array($start = 0, $results = 1000, $sort = null, $dir = '', $query = null, $search_option = null, $filters = array())
		{
			$results = array();
			
			$condition = $this->get_conditions($query, $filters,$search_option);
			$order = $sort ? "ORDER BY $sort $dir ": '';
			
			$sql = "SELECT * FROM rental_price_item WHERE $condition $order";
			$this->db->limit_query($sql, $start, __LINE__, __FILE__, $limit);
			
			while ($this->db->next_record()) {
				$price_item = new rental_price_item($this->unmarshal($this->db->f('id', true), 'int'));
				$price_item->set_title($this->unmarshal($this->db->f('title', true), 'string'));
				$price_item->set_agresso_id($this->unmarshal($this->db->f('agresso_id', true), 'string'));
				$price_item->set_is_area($this->unmarshal($this->db->f('is_area', true), 'bool'));
				$price_item->set_price($this->unmarshal($this->db->f('price', true), 'float'));
				
				$results[] = $price_item;
			}
			
			return $results;
		}
		
		protected function get_conditions($query, $filters,$search_option)
		{	
			$clauses = array('1=1');
			if($query)
			{
				
				$like_pattern = "'%" . $this->db->db_addslashes($query) . "%'";
				$like_clauses = array();
				switch($search_option){
					case "id":
						$like_clauses[] = "rental_price_item.id = $query";
						break;
					case "title":
						$like_clauses[] = "rental_price_item.title $this->like $like_pattern";
						break;
					case "agresso_id":
						$like_clauses[] = "rental_price_item.agresso_id $this->like $like_pattern";
						break;
					case "all":
						$like_clauses[] = "rental_price_item.title $this->like $like_pattern";
						$like_clauses[] = "rental_price_item.agresso_id $this->like $like_pattern";
						break;
				}
				
				
				if(count($like_clauses))
				{
					$clauses[] = '(' . join(' OR ', $like_clauses) . ')';
				}
				
				
			}
			
			$filter_clauses = array();
			switch($filters['is_area']){
				case "true":
					$filter_clauses[] = "rental_price_item.is_area = TRUE";
					break;
				case "false":
					$filter_clauses[] = "rental_price_item.is_area = FALSE";
					break;
				case "both":
					break;
			}
				
			if(count($filter_clauses))
				{
					$clauses[] = join(' AND ', $filter_clauses);
				}
			
			return join(' AND ', $clauses);
		}
	}