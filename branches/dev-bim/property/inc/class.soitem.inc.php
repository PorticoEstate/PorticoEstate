<?php

    /**
     * Description of soitem
     *
     * @author Espen
     */
    class property_soitem
    {
        private $db;
        private static $instance;

        private function __construct() {
            $this->db = & $GLOBALS['phpgw']->db;
        }


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
         * @param integer $specific_item_id
         * @param integer $offset
         * @param integer $limit
         * @return array Array of zero or more items
         */
        public function get($specific_item_id = null, $offset = null, $limit = null)
        {
            $items = array();

            $select_cols = array('i.id',
                'i.group_id',
                'i.location_id',
                'i.vendor_id',
                'i.installed'/*,
                'g.name',
                'g.nat_group_no AS ngno',
                'g.bpn',
                'g.parent_group',
                'g.catalog_id'*/);
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
                $items[$i]['installed_date'] = $this->db->f('installed');
                /*$items[$i]['group_name']   = $this->db->f('name');
                $items[$i]['group_ngno']     = $this->db->f('ngno');
                $items[$i]['group_bpn']      = $this->db->f('bpn');
                $items[$i]['group_parent']   = $this->db->f('parent_group');
                $items[$i]['group_catalog_id'] = $this->db->f('catalog_id');*/

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
            $sogroup = property_sogroup::get_instance();

            foreach($items as $item)
            {
                $item_obj = new property_boitem($items['installed_date']);
                $item_obj->set_group($sogroup->get_group(null, null, $item['group_id']));

                $return_objects[] = $item_obj;
            }

            return $return_objects;
        }
    }
