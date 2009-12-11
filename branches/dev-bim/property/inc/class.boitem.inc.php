<?php
    /**
     * FIXME: Description
     *
     * @author Espen
     */
    class property_boitem extends property_boattribute_owner
    {
        private $installed_date;

        public function __construct($installed_date = null)
        {
            if($installed_date) {
                $this->set_installed_date($installed_date);
            }
            $this->attributes = array();
        }


        public function set_installed_date($installed_date)
        {
            $this->installed_date = $installed_date;
        }

        public function get_installed_date()
        {
            return (int) $this->installed_date;
        }
        
    }
