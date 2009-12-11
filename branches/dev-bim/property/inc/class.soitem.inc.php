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
        
        $cols = array('*');
        $tables = array('property_item');
        $where = ($specific_item_id ? 'WHERE id = '.$specific_item_id : '');
        
        $sql  = "SELECT ";
        $sql .= implode(',', $cols);
        $sql .= ' FROM ';
        $sql .= implode(',', $tables);
        $sql .= $where;

        $this->db->query($sql);
        while($this->db->next_record())
        {
            $uicols['id'][]         = $this->db->f('id');
            $uicols['group_id'][]   = $this->db->f('group_id');
            $uicols['location_id'][]= $this->db->f('location_id');
            $uicols['vendor_id'][]  = $this->db->f('vendor_id');
            $uicols['installed'][]  = $this->db->f('installed');
        }

        return $uicols;
    }

    public function populate(array $items) {
        
    }
}
?>
