<?php
	include_class('bim', 'boattribute_owner', 'inc/');

    /**
     * FIXME: Description
     *
     * @author Espen
     */
    class bim_boitem extends bim_boattribute_owner
    {

        private $id, $installed_date, $location_id, $vendor_id;

        /**
         * Should contain the group object of which this item belongs to.
         * @var bim_bogroup
         */
        private $group;

        public function __construct($id = null, $installed_date = null, $location_id = null, $vendor_id = null)
        {
            $this->set_installed_date($installed_date);
            $this->set_id($id);
            $this->set_location_id($location_id);
            $this->set_vendor_id($location_id);
        }

		public function remove_attribute($attr_def)
		{
            $this->attributes[$attr_def] = null;
        }

		public function set_attribute($attr_def, bim_boattribute $attr)
		{
            $group_attrs = $this->group->get_attribute_list();
            if(array_key_exists($attr_def, $group_attrs))
            {
                $this->attributes[$attr_def] = $attr;
                return true;
            }
            
            // Return false if array key (the attr definition) doesn't exist in group.
            return false;
        }

        public function set_installed_date($installed_date)
        {
            $this->installed_date = $installed_date;
        }

        public function get_installed_date()
        {
			return (int)$this->installed_date;
        }

		public function get_id()
		{
            return $this->id;
        }

		public function set_id($id)
		{
            $this->id = $id;
        }

        public function set_group(bim_bogroup $group)
        {
            $this->group = $group;
        }

        public function get_group()
        {
            return $this->group;
        }
        
		public function get_location_id()
		{
            return $this->location_id;
        }

		public function set_location_id($location_id)
		{
            $this->location_id = $location_id;
        }

		public function get_vendor_id()
		{
            return $this->vendor_id;
        }

		public function set_vendor_id($vendor_id)
		{
            $this->vendor_id = $vendor_id;
        }
    }
