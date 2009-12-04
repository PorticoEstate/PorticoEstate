<?php
/**
 * FIXME: Description
 *
 * @author Espen
 */
class property_boattribute
{
    private $id, $name, $display_name, $value, $data_type, $unit, $desc;


    /**
     * Constructor. Takes an optional array of values where the keys should
     * be identical to the name of the variable it is trying to set. Setter
     * methods reflect these names.
     *
     * @param array $values
     */
    public function __construct($values = null)
    {
        if($this->valid_values($values)) {
            $this->set_id($values['id']); // May be null
            $this->set_name($values['name']);
            $this->set_display_name($values['display_name']);
            $this->set_value($values['value']);
            $this->set_data_type($values['data_type']);
            $this->set_unit($values['unit']);
            $this->set_desc($values['desc']);
        }
    }

    /**
     * Simple value array validation.
     *
     * @param array $values
     * @return boolean
     */
    private function valid_values(array $values) {
        if(empty($values['name']) || empty($values['display_name']) || empty($values['value']) || empty($values['data_type']) || empty($values['unit'])) {
            return false;
        }
        return true;
    }



    public function get_id()
    {
        return $this->id;
    }

    public function set_id($id)
    {
        $this->id = $id;
    }

    public function get_name()
    {
        return $this->name;
    }

    public function set_name($name)
    {
        $this->name = $name;
    }

    public function get_display_name()
    {
        return $this->display_name;
    }

    public function set_display_name($display_name)
    {
        $this->display_name = $display_name;
    }

    public function get_value()
    {
        return $this->value;
    }

    public function set_value($value)
    {
        $this->value = $value;
    }

    public function get_data_type()
    {
        return $this->data_type;
    }

    public function set_data_type($data_type)
    {
        $this->data_type = $data_type;
    }

    public function get_desc()
    {
        return $this->desc;
    }

    public function set_desc($desc)
    {
        $this->desc = $desc;
    }

    public function get_unit()
    {
        return $this->unit;
    }

    public function set_unit($unit)
    {
        $this->unit = $unit;
    }



    public function __toString()
    {
        return $this->display_name + ": " + $this->value + " " + $this->unit;
    }


}
