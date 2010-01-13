<?php

phpgw::import_class('property.sogroup');
phpgw::import_class('property.boitem');

    /**
     * Description of soitem
     *
     * @author Espen
     */
    class property_soitem
    {
        private $db;
        public $uicols;
        private static $instance;

        private function __construct() {
            $this->uicols = array();
            $this->db = & $GLOBALS['phpgw']->db;
        }


        /**
         * @return property_soitem
         */
        public static function singleton()
        {
            if (!isset(self::$instance))
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

            $uicols['input_type'][]		= 'hidden';
            $uicols['name'][]			= 'id';
            $uicols['descr'][]			= false;
            $uicols['statustext'][]		= false;
            $uicols['datatype'][]		= false;
            $uicols['attib_id'][]		= false;

            $uicols['input_type'][]		= 'text';
            $uicols['name'][]			= 'category';
            $uicols['descr'][]			= lang('category');
            $uicols['statustext'][]		= lang('category');
            $uicols['datatype'][]		= false;
            $uicols['attib_id'][]		= false;

            $uicols['input_type'][]		= 'text';
            $uicols['name'][]			= 'entry_date';
            $uicols['descr'][]			= lang('entry date');
            $uicols['statustext'][]		= lang('entry date');
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
            $from_tables = array('property_item i');
            $joins = array(
                //$this->db->left_join.' property_group g ON i.group_id = g.id',
                $this->db->left_join.' fm_vendor v ON i.vendor_id = v.id'
            );
            $where_clauses = array(' WHERE 1=1');

            if($specific_item_id) {
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
                $items[$i]['id']             = $this->db->f('id');
                $items[$i]['group_id']       = $this->db->f('group_id');
                $items[$i]['location_id']    = $this->db->f('location_id');
                $items[$i]['vendor_id']      = $this->db->f('vendor_id');
                $items[$i]['installed'] = $this->db->f('installed');

                $this->uicols['input_type'][]   = 'text';
                $this->uicols['name'][]         = $this->db->f('id');
                $this->uicols['descr'][]        = $this->db->f('group_id');
                $this->uicols['statustext'][]   = $this->db->f('location_id');
                $this->uicols['datatype'][]     = $this->db->f('vendor_id');
                $this->uicols['attib_id'][]     = $this->db->f('installed');

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
        public function populate(array $items)
        {
            if(!is_array($items))
            {
                return null;
            }

            $return_objects = array();
            $sogroup = property_sogroup::singleton();

            foreach($items as $item)
            {
                $item_obj = new property_boitem($items['installed_date']);
                $item_obj->set_group($sogroup->get($item['group_id']));

                $return_objects[] = $item_obj;
            }

            return $return_objects;
        }


        /**
         * Save changes on an item to database or insert a new one if ID is empty.
         *
         * @param property_boitem $obj
         */
        public function save(property_boitem $obj)
        {
            // If item has an ID, do an update, otherwise, do an insert
            $ins_or_upd = ($obj->get_id() != null ? 'UPDATE' : 'INSERT INTO');
            $table = 'property_item';
            $cols = array('id', 'group_id', 'location_id', 'vendor_id', 'installed');
            $values = array($obj->get_id(),
                $obj->get_group()->get_id(),
                $obj->get_location_id(),
                $obj->get_vendor_id(),
                $obj->get_installed_date());
        }
    }
