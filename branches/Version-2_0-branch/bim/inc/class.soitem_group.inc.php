<?php

    /**
     * Description of sogroup
     *
     * @author Espen
     */
	class bim_soitem_group
	{

        private $db;
        private static $instance;

		private function __construct()
		{
            $this->db = & $GLOBALS['phpgw']->db;
        }

        /**
         * @return bim_sogroup
         */
        public static function singleton()
        {
			if(!isset(self::$instance))
            {
                $c = __CLASS__;
                self::$instance = new $c;
            }
            return self::$instance;
        }

        /**
         * Retreive any number of groups.
         * @param array $data
         * @return array Array of zero or more groups
         */
        public function read(array $data)
        {
            $start  	= isset($data['start']) ? $data['start'] : 0;
            $filter     = isset($data['filter']) ? $data['filter'] : 'none';
            $query		= $data['query'];
            $sort		= isset($data['sort']) ? $data['sort'] : 'DESC';
            $order		= $data['order'];
            $allrows	= $data['allrows'];
            $dry_run	= $data['dry_run'];

            $ret = array();

            $entity_table   = 'fm_item_group';
			$cols = array($entity_table . '.*');
            $where_clauses  = array(' WHERE 1=1');
            $joins          = array();

            $sql  = 'SELECT ' . implode($cols, ', ') .
			" FROM $entity_table " .
                    implode($joins, ' ') .
                    implode($where_clauses, ' AND ');

            $this->db->query($sql);
            $i = 0;
			while($this->db->next_record())
			{
                $items[$i]['id']        = $this->db->f('id');
                $items[$i]['name']      = $this->db->f('name');
                $items[$i]['ngno']      = $this->db->f('nat_group_no');
                $items[$i]['bpn']       = $this->db->f('bpn');
                $items[$i]['parent']    = $this->db->f('parent_group');
				$items[$i]['catalog_id'] = $this->db->f('catalog_id');

                $i++;
            }

            return $items;
        }

        // TODO
        public function read_single($id)
        {
            
        }
        
        /**
         * Creates fully populated objects out of an item array.
         *
         * @param array $items Array of items in the same format as that returned from get_items().
         * @return mixed Array of item objects og null if failed.
         */
		public function populate(array $groups)
		{
			if(!is_array($groups))
			{
                return null;
            }

            $return_objects = array();
            $socatalog = bim_socatalog::get_instance();

			foreach($groups as $group)
			{
                $group_obj = new bim_bogroup();
                $group_obj->set_bpn($group['bpn']);
                $group_obj->set_name($group['name']);
                $group_obj->set_nat_group_no($group['ngno']);
                $group_obj->set_catalog($socatalog->get($group['catalog_id']));

                $return_objects[] = $group_obj;
            }

            return $return_objects;
        }
    }
