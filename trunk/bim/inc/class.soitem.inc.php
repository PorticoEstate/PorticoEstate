<?php
	phpgw::import_class('bim.soitem_group');
	phpgw::import_class('bim.boitem');

    /**
     * Description of soitem
     *
     * @author Espen
     */
    class bim_soitem
    {

        private $db;
        private static $instance;
        public $uicols;

		private function __construct()
		{
            $this->db = & $GLOBALS['phpgw']->db;
        }

        /**
         * @return bim_soitem
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
         * Retreive any number of items.
         * @param array $data
         * @return array Array of zero or more items
         */
        public function read(array $data)
        {
            // TODO: Use data
            $start		= isset($data['start']) ? $data['start'] : 0;
            $filter		= $data['filter'] ? $data['filter'] : 'none';
            $query		= $data['query'];
            $sort		= $data['sort'] ? $data['sort'] : 'DESC';
            $order		= $data['order'];
            $cat_id		= $data['cat_id'];
            $allrows	= $data['allrows'];
            $member_id 	= $data['member_id'] ? $data['member_id'] : 0;
            $dry_run	= $data['dry_run'];

            $uicols = array();
            $uicols['input_type'][]		= 'text';
            $uicols['name'][]			= 'id';
            $uicols['descr'][]			= lang('ID');
            $uicols['statustext'][]		= lang('ID');
            $uicols['datatype'][]		= false;
            $uicols['attib_id'][]		= false;

            $uicols['input_type'][]		= 'text';
            $uicols['name'][]			= 'group';
            $uicols['descr'][]			= 'Gruppe';
            $uicols['statustext'][]		= 'Gruppe';
            $uicols['datatype'][]		= false;
            $uicols['attib_id'][]		= false;

            $uicols['input_type'][]		= 'text';
            $uicols['name'][]			= 'location';
            $uicols['descr'][]			= 'Location';
            $uicols['statustext'][]		= 'Location';
            $uicols['datatype'][]		= false;
            $uicols['attib_id'][]		= false;

            $uicols['input_type'][]		= 'text';
            $uicols['name'][]			= 'installed';
            $uicols['descr'][]			= 'Installert';
            $uicols['statustext'][]		= 'Installert';
            $uicols['datatype'][]		= false;
            $uicols['attib_id'][]		= false;

            $this->uicols = $uicols;

            $select_cols = array(
                'i.id',
                'i.group_id',
                'i.location_id',
                'i.vendor_id',
                'i.installed'
            );
            $from_tables = array('fm_item i');
            $joins = array(
                //$this->db->left_join.' fm_item_group g ON i.group_id = g.id',
				$this->db->left_join . ' fm_vendor v ON i.vendor_id = v.id'
            );
            $where_clauses = array(' WHERE 1=1');

			if($specific_item_id)
			{
                // FIXME Sanitize input!!
                $where_clauses[] = "i.id = $specific_item_id";
            }

            $sql  = 'SELECT ' . implode($select_cols, ', ') .
                    ' FROM ' . implode($from_tables, ', ') .
                    implode($joins, ' ') .
                    implode($where_clauses, ' AND ');

            $this->db->query($sql);
            $i = 0;
            while($this->db->next_record())
            {
                $items[$i]['id']       = $this->db->f('id');
                $items[$i]['group']    = $this->db->f('group_id');
                $items[$i]['location'] = $this->db->f('location_id');
                $items[$i]['vendor']   = $this->db->f('vendor_id');
				$items[$i]['installed'] = $this->db->f('installed');

                $i++;
            }

            return $items;
        }

        /**
         * Creates fully populated objects out of an item array.
         *
         * @param array $items Array of items in the same format as that returned from get_items().
         * @return mixed Array of item objects og null if failed.
         */
        private function populate(array $items)
        {
            if(!is_array($items))
            {
                return null;
            }

            $return_objects = array();
            $sogroup = bim_sogroup::singleton();

            foreach($items as $item)
            {
                $item_obj = new bim_boitem($items['installed_date']);
                $item_obj->set_group($sogroup->get($item['group_id']));

                $return_objects[] = $item_obj;
            }

            return $return_objects;
        }

        /**
         * Save changes on an item to database or insert a new one if ID is empty.
         *
         * @param bim_boitem $obj
         */
        public function save(bim_boitem $obj)
        {
            // If item has an ID, do an update, otherwise, do an insert
            $ins_or_upd = ($obj->get_id() != null ? 'UPDATE' : 'INSERT INTO');
            $table = 'fm_item';
            $cols = array('id', 'group_id', 'location_id', 'vendor_id', 'installed');
            $values = array($obj->get_id(),
                $obj->get_group()->get_id(),
                $obj->get_location_id(),
                $obj->get_vendor_id(),
                $obj->get_installed_date());
        }

        /**
         * Get total number of records (rows) in item table
         *
         * @return integer No. of records
         */
        public function total_records()
        {
            $sql  = 'SELECT COUNT(id) AS rows FROM fm_item';

            $this->db->query($sql);
            // Move pointer to first row
            $this->db->next_record();
            // Get value of 'rows' column
			return (int)$this->db->f('rows');
        }
    }
