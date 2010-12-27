<?php


//phpgw::import_class('property.boitem');

    interface sobim {
    	
		const bimItemTable = 'fm_bim_data';
    	/*
    	 * @return array of BIM objects
    	 */
    	public function getAll();
    	/*
    	 * @param int id
    	 * @return BIMItem 
    	 */
    	public function getBimObject($bimObjectId);
    	
    	
    }
    class sobim_impl implements sobim
    {
    	/* @var phpgwapi_db_ */
		private $db;

        public function __construct(& $db) {
           // $this->db = & $GLOBALS['phpgw']->db;
           $this->db = $db;
        }
        /*
         * @return Array an array of BimItem objects
         */
        public function getAll() {
        	$sql  = 'SELECT fm_bim_data.id, fm_bim_type."name" AS "type", fm_bim_data.guid, fm_bim_data.xml_representation '.
					'FROM public.fm_bim_data,  public.fm_bim_type '.
					'WHERE  fm_bim_data."type" = fm_bim_type.id;';
        	$bimItemArray = array();
            $this->db->query($sql);
            while($this->db->next_record())
            {
                $bimItem = new BimItem($this->db->f('id'),$this->db->f('guid'), $this->db->f('type'), $this->db->f('xml_representation'));
                array_push($bimItemArray, $bimItem);
            }
            
            return $bimItemArray;
        }
        
        
        public function getBimObject($bimObjectGuid){
        	
        }
        


        
        /**
         * Retreive any number of items.
         * @param array $data
         * @return array Array of zero or more items
         */
        public function read(array $data)
        {

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
                $items[$i]['id']       = $this->db->f('id');
                $items[$i]['group']    = $this->db->f('group_id');
                $items[$i]['location'] = $this->db->f('location_id');
                $items[$i]['vendor']   = $this->db->f('vendor_id');
                $items[$i]['installed']= $this->db->f('installed');

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
            return (int) $this->db->f('rows');
        }
    }
    
    class BimItem {
    	private $databaseId;
    	private $guid;
    	private $type;
    	private $xml;
    	
    	function __construct($databaseId = null, $guid = null, $type = null, $xml = null) {
    		//$this->databaseId = (is_null($databaseId)) ? null : (int)$databaseId;
    		$this->databaseId = (int)$databaseId;
    		$this->guid =  $guid;
    		$this->type = $type;
    		$this->xml = $xml;
    	}
    	function getDatabaseId() {
    		return $this->databaseId;
    	}
    	function getGuid() {
    		return $this->guid;
    	}
    	function getType() {
    		return $this->type;
    	}
    	function getXml() {
    		return $this->xml;
    	}
    	
    }
    
    
