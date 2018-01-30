<?php
include_class('handyman', 'location', 'inc/model/');
include_class('handyman', 'location_repository', 'inc/repository/');
include_class('handyman', 'location_xml_model', 'inc/xmlmodel/');

class export
{

    var $public_functions = array(
        'index' => true,
        'location_list' => true,
        'location' => true,
        'locations_as_xml' => true,
        'export' => true,
    );

    private $_db = null;

//        protected function highlight($data){
    /*            highlight_string("<?php\n\$data =\n" . var_export($data, true) . ";\n?>");*/
//        }

    public function __construct()
    {
        $this->_db = $GLOBALS['phpgw']->db;
    }

    public function location()
    {
        header("Content-type: application/json");
        $rep = new handyman_location_repository($this->_db);
        $location_code = phpgw::get_var('location_code');
        $location = $rep->get_one_by_id($location_code);
        echo json_encode($location->to_array(), JSON_PRETTY_PRINT);
    }

    public function location_list()
    {
        header("Content-type: application/json");
        $rep = new handyman_location_repository($this->_db);
        $location_list = $rep->get_object_list();
        echo json_encode($location_list);
    }

    public function locations_as_xml()
    {
        header("Content-type: text/xml");
        $rep = new handyman_location_repository($this->_db);
        $obj_arr = $rep->get_object_list();
        $xml = handyman_location::array_to_XML($obj_arr);
        echo $xml->asXML();
    }

    public function export()
    {
        header("Content-type: text/xml");
        $rep = new handyman_location_repository($this->_db);
        $obj_arr = $rep->get_object_list();
        $xml = handyman_location_xml_model::array_to_XML($obj_arr);
        echo $xml->asXML();
    }
}
