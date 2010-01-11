<?php
    phpgw::import_class('phpgwapi.yui');
    phpgw::import_class('property.soitem');
    phpgw::import_class('phpgwapi.datetime');
    /**
     * FIXME: Description
     * @package property
     */

    class property_uiitem
    {
        private $so;
        public $public_functions = array
        (
            'index' => true,
            'testdata' => true,
            'emptydb' => true
        );

        public function __construct()
        {
            //$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::item::index';
            
            $this->so = property_soitem::singleton();
        }



        public function index()
        {
            // Highlight menu selection
            $GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::item::index';

            $datatable = $this->so->get();
            //$datatable = $this->so->populate($datatable);
/*
            phpgwapi_yui::load_widget('datatable');
            // Prepare template variables and process XSLT
			$template_vars = array();
			$template_vars['datatable'] = $datatable;
			$GLOBALS['phpgw']->xslttpl->add_file(array('datatable'));
	      	$GLOBALS['phpgw']->xslttpl->set_var('phpgw', $template_vars);

	      	if ( !isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css) )
	      	{
	        	$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
	      	}
            
			// Prepare CSS Style
		  	$GLOBALS['phpgw']->css->validate_file('datatable');
		  	$GLOBALS['phpgw']->css->validate_file('property');
		  	$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
*/
            _debug_array($datatable);
            
        }

        public function testdata() {
            // BIM testdata
            $GLOBALS['phpgw']->db->query("INSERT INTO property_catalog (name, description) VALUES ('NOBB', 'Norsk Byggevarebase')");

            $GLOBALS['phpgw']->db->query("INSERT INTO property_group (name, nat_group_no, bpn, parent_group, catalog_id) VALUES ('Doors', 'X', 123, NULL, (SELECT id FROM property_catalog WHERE name = 'NOBB' LIMIT 1))");
            $GLOBALS['phpgw']->db->query("INSERT INTO property_group (name, nat_group_no, bpn, parent_group, catalog_id) VALUES ('Windows', 'X', 123, NULL, (SELECT id FROM property_catalog WHERE name = 'NOBB' LIMIT 1))");

            $GLOBALS['phpgw']->db->query("INSERT INTO property_data_type (display_name, function_name) VALUES ('integer', 'dt_int')");

            $GLOBALS['phpgw']->db->query("INSERT INTO property_attr_group (name, sort) VALUES ('Dimensions', 1)");
            $GLOBALS['phpgw']->db->query("INSERT INTO property_attr_group (name, sort) VALUES ('Layout', 2)");
            
            $GLOBALS['phpgw']->db->query("INSERT INTO property_attr_def
                (name, display_name, description, data_type_id, unit_id, attr_group_id)
                VALUES (
                    'height',
                    'Height',
                    NULL,
                    (SELECT id FROM property_data_type WHERE function_name = 'dt_int'),
                    'mm',
                    (SELECT id FROM property_attr_group WHERE name = 'Dimensions')
                )"
            );
            $GLOBALS['phpgw']->db->query("INSERT INTO property_attr_def
                (name, display_name, description, data_type_id, unit_id, attr_group_id)
                VALUES (
                    'width',
                    'Width',
                    NULL,
                    (SELECT id FROM property_data_type WHERE function_name = 'dt_int'),
                    'mm',
                    (SELECT id FROM property_attr_group WHERE name = 'Dimensions')
                )"
            );
            $GLOBALS['phpgw']->db->query("INSERT INTO property_attr_def
                (name, display_name, description, data_type_id, unit_id, attr_group_id)
                VALUES (
                    'depth',
                    'Depth',
                    NULL,
                    (SELECT id FROM property_data_type WHERE function_name = 'dt_int'),
                    'mm',
                    (SELECT id FROM property_attr_group WHERE name = 'Dimensions')
                )"
            );
            $GLOBALS['phpgw']->db->query("INSERT INTO property_attr_def
                (name, display_name, description, data_type_id, unit_id, attr_group_id)
                VALUES (
                    'tiles',
                    'No of tiles',
                    NULL,
                    (SELECT id FROM property_data_type WHERE function_name = 'dt_int'),
                    'mm',
                    (SELECT id FROM property_attr_group WHERE name = 'Layout')
                )"
            );
            // Items
            $GLOBALS['phpgw']->db->query("INSERT INTO property_item
                (group_id, location_id, vendor_id, installed)
                VALUES (
                    (SELECT id FROM property_group WHERE name = 'Doors'),
                    1,
                    104533,
                    ".phpgwapi_datetime::user_localtime()."
                )"
            );
            $GLOBALS['phpgw']->db->query("INSERT INTO property_item
                (group_id, location_id, vendor_id, installed)
                VALUES (
                    (SELECT id FROM property_group WHERE name = 'Doors'),
                    1,
                    104533,
                    ".phpgwapi_datetime::user_localtime()."
                )"
            );

        }

        public function emptydb() {
            $GLOBALS['phpgw']->db->query("DELETE FROM property_item_attr");
            $GLOBALS['phpgw']->db->query("DELETE FROM property_group_attr");
            $GLOBALS['phpgw']->db->query("DELETE FROM property_attr_def");
            $GLOBALS['phpgw']->db->query("DELETE FROM property_attr_value");
            $GLOBALS['phpgw']->db->query("DELETE FROM property_attr_group");
            $GLOBALS['phpgw']->db->query("DELETE FROM property_attr_choice");
            $GLOBALS['phpgw']->db->query("DELETE FROM property_data_type");
            $GLOBALS['phpgw']->db->query("DELETE FROM property_item");
            $GLOBALS['phpgw']->db->query("DELETE FROM property_group");
            $GLOBALS['phpgw']->db->query("DELETE FROM property_catalog");
        }

    }
