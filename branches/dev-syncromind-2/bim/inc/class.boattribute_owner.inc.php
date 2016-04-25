<?php

    /**
     * Description of classboattrobjectinc
     *
     * @author Espen
     * @abstract
     */
    abstract class bim_boattribute_owner
    {

        protected $attributes;

        /**
         * Set the value-object of a given attribute.
         * If used on a group the attribute will be added if not already existing
         * and (default) value will be set.
         * 
         * If used on an item it will override the value set by the group.
         * Attributes not already set on the group, however, cannot be set on
         * an item and will return false.
         *
         * @abstract
         * @param string $attr_def
         * @param bim_boattribute $attr
         * @return bool FALSE if failed, TRUE otherwise.
         */
        public abstract function set_attribute($attr_def, bim_boattribute $attr);

        /**
         * Get the value of a given attribute.
         *
         * @param string $attr_def
         * @return mixed The value.
         */
        public function get_attribute($attr_def)
        {
            return ($this->attributes[$attr_def] instanceof bim_boattribute ? $this->attributes[$attr_def] : null);
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
         * @abstract
         * @param string $attr_def
         */
        public abstract function remove_attribute($attr_def);
    }
