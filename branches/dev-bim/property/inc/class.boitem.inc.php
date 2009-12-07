<?php
    /**
     * FIXME: Description
     *
     * @author Espen
     */
    class property_boitem implements property_has_attributes
    {
        private $attributes, $installed_date;

        public function __construct(int $installed_date = null)
        {
            if($installed_date) {
                $this->set_installed_date($installed_date);
            }
            $this->attributes = array();
        }


        public function set_installed_date(int $installed_date)
        {
            $this->installed_date = $installed_date;
        }

        public function get_installed_date()
        {
            return (int) $this->installed_date;
        }

        
        public function add_attribute($attr_def, $attr_value)
        {

        }

        public function edit_attribute($attr_def, $attr_value)
        {
            ;
        }

        public function get_attribute($attr_def)
        {
            ;
        }

        public function get_attribute_list()
        {
            ;
        }

        public function remove_attribute($attr_def)
        {
            ;
        }
        
    }
