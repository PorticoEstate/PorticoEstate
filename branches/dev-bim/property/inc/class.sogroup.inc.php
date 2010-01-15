<?php

    /**
     * Description of sogroup
     *
     * @author Espen
     */
    class property_soitem_group {
        private $db;
        private static $instance;

        private function __construct() {
            $this->db = & $GLOBALS['phpgw']->db;
        }


        /**
         * @return property_sogroup
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
         * Retreive any number of groups.
         * @param integer $specific_group_id
         * @param integer $offset
         * @param integer $limit
         * @return array Array of zero or more items
         */
        public function read($specific_group_id = null, $offset = null, $limit = null) {
            $items = array();

            $select_cols = array('g.id',
                    'g.name',
                    'g.nat_group_no',
                    'g.bpn',
                    'g.parent_group',
                    'g.catalog_id');
            $from_tables = array('property_group g');
            $joins = array(
                    //$this->db->left_join.' property_group g ON i.group_id = g.id',
                    $this->db->left_join.' property_catalog c ON g.catalog_id = c.id'
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
            while($this->db->next_record()) {
                $items[$i]['id']        = $this->db->f('id');
                $items[$i]['name']      = $this->db->f('name');
                $items[$i]['ngno']      = $this->db->f('nat_group_no');
                $items[$i]['bpn']       = $this->db->f('bpn');
                $items[$i]['parent']    = $this->db->f('parent_group');
                $items[$i]['catalog_id']= $this->db->f('catalog_id');

                $i++;
            }

            return $items;
        }

        // TODO
        public function read_single($id) {
            
        }


        /**
         * Creates fully populated objects out of an item array.
         *
         * @param array $items Array of items in the same format as that returned from get_items().
         * @return mixed Array of item objects og null if failed.
         */
        public function populate(array $groups) {
            if(!is_array($groups)) {
                return null;
            }

            $return_objects = array();
            $socatalog = property_socatalog::get_instance();

            foreach($groups as $group) {
                $group_obj = new property_bogroup();
                $group_obj->set_bpn($group['bpn']);
                $group_obj->set_name($group['name']);
                $group_obj->set_nat_group_no($group['ngno']);
                $group_obj->set_catalog($socatalog->get($group['catalog_id']));

                $return_objects[] = $group_obj;
            }

            return $return_objects;
        }

        
    }
