<?php
/**
 * Interface for any class that will have a set of attributes
 *
 * @author Espen
 */
interface property_has_attributes
{

    /**
     * Adds an attribute to the object. Returns false if attribute already
     * exists on the object.
     *
     * @param string $attr_def
     * @param mixed $attr_value
     * @return bool FALSE if the attribute already exists or otherwise fails to be added.
     */
    public function add_attribute($attr_def, $attr_value);


    /**
     * Edits the value of a given attribute.
     *
     * @param string $attr_def
     * @param mixed $attr_value
     * @return bool FALSE if edit somehow failed, TRUE otherwise.
     */
    public function edit_attribute($attr_def, $attr_value);


    /**
     * Removes an attribute and its value from the object. If the attribute is
     * inherited the attribute itself will remain but value will be set to null.
     *
     * @param string $attr_def
     * @return void
     */
    public function remove_attribute($attr_def);


    /**
     * Get the value of a given attribute.
     *
     * @param string $attr_def
     * @return mixed The value.
     */
    public function get_attribute($attr_def);


    /**
     * Fetches a list of attributes (without values) on this object.
     *
     * @return array
     */
    public function get_attribute_list();
}
?>
