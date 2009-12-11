<?php

    /**
     * Description of classbogroupinc
     *
     * @author Espen
     */
    class property_bogroup extends property_boattribute_owner
    {
        private $name, $bpn, $nat_group_no;

        public function __construct()
        {

        }

        public function get_name()
        {
            return $this->name;
        }

        public function set_name($name)
        {
            $this->name = $name;
        }

        public function get_bpn()
        {
            return $this->bpn;
        }

        public function set_bpn($bpn)
        {
            $this->bpn = $bpn;
        }

        public function get_nat_group_no()
        {
            return $this->nat_group_no;
        }

        public function set_nat_group_no($nat_group_no)
        {
            $this->nat_group_no = $nat_group_no;
        }


    }
