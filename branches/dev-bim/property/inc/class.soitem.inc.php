<?php

/**
 * Description of soitem
 *
 * @author Espen
 */
class property_soitem
{
    private $db;

    public function __construct()
    {
        $this->db = & $GLOBALS['phpgw']->db;
    }

    public function get_items($offset = null, $limit = null, $specific_item_id = null)
    {
        $uicols = array();
        
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
            $uicols[$i]['id']             = $this->db->f('id');
            $uicols[$i]['group_id']       = $this->db->f('group_id');
            $uicols[$i]['location_id']    = $this->db->f('location_id');
            $uicols[$i]['vendor_id']      = $this->db->f('vendor_id');
            $uicols[$i]['installed_date'] = $this->db->f('installed');
            /*$uicols[$i]['group_name']   = $this->db->f('name');
            $uicols[$i]['group_ngno']     = $this->db->f('ngno');
            $uicols[$i]['group_bpn']      = $this->db->f('bpn');
            $uicols[$i]['group_parent']   = $this->db->f('parent_group');
            $uicols[$i]['group_catalog_id'] = $this->db->f('catalog_id');*/

            $i++;
        }

        return $uicols;
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
