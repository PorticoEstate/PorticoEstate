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
            $this->bocommon 		= $this->bo->bocommon;
            
            $GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::item::index';
            
            $this->so = property_soitem::singleton();
        }



        public function index()
        {
            // Highlight menu selection
            $GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::item::index';

            $item_list = $this->so->read(null);

            $uicols = array(
                'name' =>       array('id',      'group_id', 'location_id', 'vendor_id', 'installed_date'),
                'datatype' =>   array('integer', 'integer',   'integer',    'integer',   'date'),
                'hidden' =>     array(false,     false,       false,        false,       false)
            );

            $datatable = array();

            $datatable['info'] = array
            (
            'file' => __FILE__,
            'line' => __LINE__
            );


            $i = 0;
            foreach($item_list as $item)
            {
                $j = 0;
                $datatable['rows']['row'][$i]['column'][$j]['name'] = 'id';
                $datatable['rows']['row'][$i]['column'][$j]['value'] = $item['id'];
                $datatable['rows']['row'][$i]['column'][$j]['lookup'] = '';
                $datatable['rows']['row'][$i]['column'][$j]['align'] = 'center';
                $j++;
                $datatable['rows']['row'][$i]['column'][$j]['name'] = 'installed';
                $datatable['rows']['row'][$i]['column'][$j]['value'] = $item['installed'];
                $datatable['rows']['row'][$i]['column'][$j]['lookup'] = '';
                $datatable['rows']['row'][$i]['column'][$j]['align'] = 'center';
                $j++;
                
                $i++;
            }

            $datatable['rowactions']['action'] = array();

            $datatable['headers']['header'][0]['name']      = 'id';
            $datatable['headers']['header'][0]['text']      = 'ID';
            $datatable['headers']['header'][0]['visible'] 	= true;
            $datatable['headers']['header'][0]['format'] 	= '';
            $datatable['headers']['header'][0]['sortable']	= false;
            $datatable['headers']['header'][0]['formatter']	= '""';

            $datatable['headers']['header'][1]['name']      = 'installert';
            $datatable['headers']['header'][1]['text']      = 'desc';
            $datatable['headers']['header'][1]['visible'] 	= true;
            $datatable['headers']['header'][1]['format'] 	= '';
            $datatable['headers']['header'][1]['sortable']	= false;
            $datatable['headers']['header'][1]['formatter']	= '""';

            $datatable['property_js'] =  $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= 0;
			$datatable['pagination']['records_limit'] 	= 15;
			$datatable['pagination']['records_returned'] = 2;
			$datatable['pagination']['records_total'] 	= 2;

            $datatable['sorting'] = array
            (
                'order' => 'id',
                'sort' => 'asc'
            );


            if( phpgw::get_var('phpgw_return_as') == 'json' ) {
                //values for Pagination
                $json = array
                        (
                        'recordsReturned' 	=> $datatable['pagination']['records_returned'],
                        'totalRecords' 		=> (int)$datatable['pagination']['records_total'],
                        'startIndex' 		=> $datatable['pagination']['records_start'],
                        'sort'				=> $datatable['sorting']['order'],
                        'dir'				=> $datatable['sorting']['sort'],
                        'records'			=> array()
                );

                // values for datatable
                if(isset($datatable['rows']['row']) && is_array($datatable['rows']['row'])) {
                    foreach( $datatable['rows']['row'] as $row ) {
                        $json_row = array();
                        foreach( $row['column'] as $column) {
                            if(isset($column['format']) && $column['format']== "link" && $column['java_link']==true) {
                                $json_row[$column['name']] = "<a href='#' id='".$column['link']."' onclick='javascript:filter_data(this.id);'>" .$column['value']."</a>";
                            }
                            elseif(isset($column['format']) && $column['format']== "link") {
                                $json_row[$column['name']] = "<a href='".$column['link']."'>" .$column['value']."</a>";
                            }else {
                                $json_row[$column['name']] = $column['value'];
                            }
                        }
                        $json['records'][] = $json_row;
                    }
                }

                // right in datatable
                if(isset($datatable['rowactions']['action']) && is_array($datatable['rowactions']['action'])) {
                    $json ['rights'] = $datatable['rowactions']['action'];
                }

                return $json;
            }


            phpgwapi_yui::load_widget('dragdrop');
		  	phpgwapi_yui::load_widget('datatable');
		  	phpgwapi_yui::load_widget('menu');
		  	phpgwapi_yui::load_widget('connection');
		  	//// cramirez: necesary for include a partucular js
		  	phpgwapi_yui::load_widget('loader');
		  	//cramirez: necesary for use opener . Avoid error JS
			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('paginator');
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
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');

	  		// Prepare YUI Library
  			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'actor.index', 'property' );

            //_debug_array($datatable);
            
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
