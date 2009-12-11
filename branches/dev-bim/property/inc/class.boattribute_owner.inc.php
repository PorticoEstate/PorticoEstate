<?php
    /**
     * Description of classboattrobjectinc
     *
     * @author Espen
     */
    class property_boattribute_owner
    {
        private $attributes;


        /**
         * Set the value of a given attribute.
         * If used on a group the attribute will be added if not already existing
         * and (default) value will be set to new value.
         * 
         * If used on an item it will override the value set by the group.
         * Attributes not already set on the group, however, cannot be set on
         * an item and will return false.
         *
         * @param string $attr_def
         * @param mixed $attr_value
         * @return bool FALSE if failed, TRUE otherwise.
         */
        public function set_attribute($attr_def, $attr_value)
        {
            
        }


        /**
         * Get the value of a given attribute.
         *
         * @param string $attr_def
         * @return mixed The value.
         */
        public function get_attribute($attr_def)
        {
            
        }


        /**
         * Fetches a list of attributes (without values) on this object.
         *
         * @return array
         */
        public function get_attribute_list()
        {
            return $this->attributes;
        }


        /**
         * Removes an attribute and its value from the object.
         *
         * If used on a group the attribute will be removed completely on the
         * group and items belonging to it.
         *
         * If used on an item it will only be removed from the item, which will
         * then inherit the attribute from the group it belongs to.
         *
         * @param string $attr_def
         * @return void
         */
        public function remove_attribute($attr_def)
        {
            
        }
    }

