<?php
phpgw::import_class('phpgwapi.yui');
phpgw::import_class('property.soitem');
phpgw::import_class('property.soitem_group');
phpgw::import_class('phpgwapi.datetime');
/**
 * FIXME: Description
 * @package property
 */

class property_uiitem {
    private $so;
    private $sogroup;
    private $bocommon;
    public $public_functions = array
    (
        'index' => true,
        'testdata' => true,
        'emptydb' => true
    );

    public function __construct() {
        $this->bocommon = CreateObject('property.bocommon');

        $GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
        $GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::item::index';

        $this->so = property_soitem::singleton();
        $this->sogroup = property_soitem_group::singleton();
    }



    function index() {
        $menu_sub = array(
                'tenant'=>'invoice',
                'owner'	=>'admin',
                'vendor'=>'invoice'
        );

        $dry_run = false;

        $datatable = array();

        if(phpgw::get_var('phpgw_return_as') != 'json') {
            // Set base URL. FIXME: Add more URL parameters when needed
            $datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
            (
                'menuaction'=> 'property.uiitem.index',
            ));
            $datatable['config']['allow_allrows'] = true;
            $datatable['config']['base_java_url'] = "menuaction:'property.uiitem.index',"
                ."group:'all'";

            $values_combo_box_0 = $this->sogroup->read(null);
            $default_value = array('id' => -1, 'name' => 'Alle grupper');
            array_unshift($values_combo_box_0, $default_value);

            $datatable['actions']['form'] = array(
                array(
                    'action' => $GLOBALS['phpgw']->link('/index.php',
                            array(
                                'menuaction' 	=> 'property.uiitem.index',
                                'group_id'        => 0
                            )
                    ),
                    'fields' => array(
                        'field' => array(
                            array(
                                    'id' => 'btn_group_id',
                                    'name' => 'group_id',
                                    'value'	=> lang('Group'),
                                    'type' => 'button',
                                    'style' => 'filter',
                                    'tab_index' => 1
                            ),
                            array(
                                    'type'=> 'link',
                                    'id'  => 'btn_columns',
                                    'url' => "Javascript:window.open('".$GLOBALS['phpgw']->link('/index.php',
                                            array(
                                            'menuaction' => 'property.uiitem.columns',
                                            'role'		=> $this->role
                                            ))."','','width=350,height=370')",
                                    'value' => lang('columns'),
                                    'tab_index' => 6
                            ),
                            array(
                                    'type'	=> 'button',
                                    'id'	=> 'btn_new',
                                    'value'	=> lang('add'),
                                    'tab_index' => 5
                            ),
                            array(
                                    'id' => 'btn_search',
                                    'name' => 'search',
                                    'value'    => lang('search'),
                                    'type' => 'button',
                                    'tab_index' => 4
                            ),
                            array(
                                    'name'     => 'query',
                                    'id'     => 'txt_query',
                                    'value'    => '',//$query,
                                    'type' => 'text',
                                    'onkeypress' => 'return pulsar(event)',
                                    'size'    => 28,
                                    'tab_index' => 3
                            )
                        ),
                        'hidden_value' => array(
                                array(
                                        'id' => 'values_combo_box_0',
                                        'value'	=> $this->bocommon->select2string($values_combo_box_0)
                                )
                        )
                    )
                )
            );
            
            $dry_run=true;
        }

        $item_list = $this->so->read($dry_run);

        $uicols	= $this->so->uicols;
        $uicols_count = count($uicols['name']);

        $j=0;
        if(is_array($item_list)) {
            // For each item...
            foreach($item_list as $item) {
                // For each column definition...
                for($i=0; $i < $uicols_count; $i++) {
                    
                    if($uicols['input_type'][$i] != 'hidden') {
                        $datatable['rows']['row'][$j]['column'][$i]['value'] 	= $item[$uicols['name'][$i]];
                        $datatable['rows']['row'][$j]['column'][$i]['name'] 	= $uicols['name'][$i];
                        $datatable['rows']['row'][$j]['column'][$i]['lookup'] 	= '$lookup';
                        $datatable['rows']['row'][$j]['column'][$i]['align'] 	= (isset($uicols['align'][$i]) ? $uicols['align'][$i] : 'center');

                        /*if($uicols['datatype'][$i] == 'link' && $item[$uicols['name'][$i]]) {
                            $datatable['rows']['row'][$j]['column'][$i]['value']    = lang('link');
                            $datatable['rows']['row'][$j]['column'][$i]['link']		= $item[$uicols['name'][$i]];
                            $datatable['rows']['row'][$j]['column'][$i]['target']	= '_blank';
                        }*/
                    }
                    else {
                        $datatable['rows']['row'][$j]['column'][$i]['name'] 	= $uicols['name'][$i];
                        $datatable['rows']['row'][$j]['column'][$i]['value']	= $item[$uicols['name'][$i]];
                    }

                    $datatable['rows']['row'][$j]['hidden'][$i]['value']    = $item[$uicols['name'][$i]];
                    $datatable['rows']['row'][$j]['hidden'][$i]['name']     = $uicols['name'][$i];
                }

                $j++;
            }
        }

        // NO pop-up
        $datatable['rowactions']['action'] = array();

        $parameters = array
        (
            'parameter' => array
            (
                array
                (
                    'name'		=> 'item_id',
                    'source'	=> 'id'
                )
            )
        );


        $datatable['rowactions']['action'][] = array(
                'my_name' 		=> 'view',
                'text' 			=> lang('view'),
                'action'		=> $GLOBALS['phpgw']->link('/index.php',array
                (
                    'menuaction'	=> 'property.uiitem.view',
                    'role'          => $this->role
                )),
                'parameters'	=> $parameters
        );
        $datatable['rowactions']['action'][] = array(
                'my_name' 		=> 'view',
                'text' 			=> lang('open view in new window'),
                'action'		=> $GLOBALS['phpgw']->link('/index.php',array
                (
                    'menuaction'	=> 'property.uiitem.view',
                    'role'			=> $this->role,
                    'target'		=> '_blank'
                )),
                'parameters'	=> $parameters
        );


        $datatable['rowactions']['action'][] = array(
                'my_name' 		=> 'edit',
                'text' 			=> lang('edit'),
                'action'		=> $GLOBALS['phpgw']->link('/index.php',array
                (
                    'menuaction'=> 'property.uiitem.edit',
                    'role'      => $this->role
                )),
                'parameters'	=> $parameters
        );
        $datatable['rowactions']['action'][] = array(
                'my_name' 		=> 'edit',
                'text' 			=> lang('open edit in new window'),
                'action'		=> $GLOBALS['phpgw']->link('/index.php',array
                (
                'menuaction'	=> 'property.uiitem.edit',
                'role'			=> $this->role,
                'target'		=> '_blank'
                )),
                'parameters'	=> $parameters
        );

        $datatable['rowactions']['action'][] = array(
                'my_name' 			=> 'delete',
                'text' 			=> lang('delete'),
                'confirm_msg'	=> lang('do you really want to delete this entry'),
                'action'		=> $GLOBALS['phpgw']->link('/index.php',array
                (
                'menuaction'	=> 'property.uiitem.delete',
                'role'	=> $this->role
                )),
                'parameters'	=> $parameters
        );
        $datatable['rowactions']['action'][] = array(
                'my_name' 			=> 'add',
                'text' 			=> lang('add'),
                'action'		=> $GLOBALS['phpgw']->link('/index.php',array
                (
                'menuaction'	=> 'property.uiitem.edit',
                'role'	=> $this->role
                ))
        );

        unset($parameters);


        for ($i=0; $i < $uicols_count; $i++) {

            //all colums should be have formatter
            $datatable['headers']['header'][$i]['formatter'] = ($uicols['formatter'][$i]==''?  '""' : $uicols['formatter'][$i]);

            if($uicols['input_type'][$i] != 'hidden') {
                $datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
                $datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
                $datatable['headers']['header'][$i]['visible'] 			= true;
                $datatable['headers']['header'][$i]['format'] 			= $this->bocommon->translate_datatype_format($uicols['datatype'][$i]);
                $datatable['headers']['header'][$i]['sortable']			= false;

                // If datatype is not T or CH
                if(!in_array($uicols['datatype'][$i], array('T', 'CH'))) {
                    $datatable['headers']['header'][$i]['sortable']		= true;
                    $datatable['headers']['header'][$i]['sort_field']	= $uicols['name'][$i];
                }
            }
            /*else {
                $datatable['headers']['header'][$i]['name'] 			= 'id2';
                $datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
                $datatable['headers']['header'][$i]['visible'] 			= false;
                $datatable['headers']['header'][$i]['sortable']			= false;
                $datatable['headers']['header'][$i]['format'] 			= 'hidden';
            }*/
        }

        // path for property.js
        $datatable['property_js'] =  $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

        // Pagination and sort values
        $datatable['pagination']['records_start'] 	= (int) $this->bo->start;
        $datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
        $datatable['pagination']['records_returned']= count($item_list);
        $datatable['pagination']['records_total'] 	= $this->so->total_records();

        //$datatable['sorting']['order'] 	= phpgw::get_var('order', 'string'); // Column
        //$datatable['sorting']['sort'] 	= phpgw::get_var('sort', 'string'); // ASC / DESC


        if((phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
        {
            $datatable['sorting']['order'] 			= 'id'; // name key Column in myColumnDef
            $datatable['sorting']['sort'] 			= 'asc'; // ASC / DESC
        }
        else
        {
            $datatable['sorting']['order']			= phpgw::get_var('order', 'string'); // name of column of Database
            $datatable['sorting']['sort'] 			= phpgw::get_var('sort', 'string'); // ASC / DESC
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
        //FIXME this one is only needed when $lookup==true - so there is probably an error
        phpgwapi_yui::load_widget('animation');

//-- BEGIN----------------------------- JSON CODE ------------------------------

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
//-------------------- JSON CODE ----------------------


        // Prepare template variables and process XSLT
        $template_vars = array();
        $template_vars['datatable'] = $datatable;
        $GLOBALS['phpgw']->xslttpl->add_file(array('datatable'));
        $GLOBALS['phpgw']->xslttpl->set_var('phpgw', $template_vars);

        if ( !isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css) ) {
            $GLOBALS['phpgw']->css = createObject('phpgwapi.css');
        }
        // Prepare CSS Style
        $GLOBALS['phpgw']->css->validate_file('datatable');
        $GLOBALS['phpgw']->css->validate_file('property');
        $GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
        $GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
        $GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');
        $GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');

        //Title of Page
        $GLOBALS['phpgw_info']['flags']['app_header'] = lang('actor') . ': ' . lang('list ' . $this->role);

        // Prepare YUI Library
        $GLOBALS['phpgw']->js->validate_file( 'yahoo', 'item.index', 'property' );

        //$this->save_sessiondata();
    }


    public function testdata() {
        // BIM testdata
        $GLOBALS['phpgw']->db->query("INSERT INTO fm_item_catalog (name, description) VALUES ('NOBB', 'Norsk Byggevarebase')");

        $GLOBALS['phpgw']->db->query("INSERT INTO fm_item_group (name, nat_group_no, bpn, parent_group, catalog_id) VALUES ('Doors', 'X', 123, NULL, (SELECT id FROM fm_item_catalog WHERE name = 'NOBB' LIMIT 1))");
        $GLOBALS['phpgw']->db->query("INSERT INTO fm_item_group (name, nat_group_no, bpn, parent_group, catalog_id) VALUES ('Windows', 'X', 123, NULL, (SELECT id FROM fm_item_catalog WHERE name = 'NOBB' LIMIT 1))");

        $GLOBALS['phpgw']->db->query("INSERT INTO fm_attr_data_type (display_name, function_name) VALUES ('integer', 'dt_int')");

        $GLOBALS['phpgw']->db->query("INSERT INTO fm_attr_group (name, sort) VALUES ('Dimensions', 1)");
        $GLOBALS['phpgw']->db->query("INSERT INTO fm_attr_group (name, sort) VALUES ('Layout', 2)");

        $GLOBALS['phpgw']->db->query("INSERT INTO fm_attr_def
                (name, display_name, description, data_type_id, unit_id, attr_group_id)
                VALUES (
                    'height',
                    'Height',
                    NULL,
                    (SELECT id FROM fm_attr_data_type WHERE function_name = 'dt_int'),
                    'mm',
                    (SELECT id FROM fm_attr_group WHERE name = 'Dimensions')
                )"
        );
        $GLOBALS['phpgw']->db->query("INSERT INTO fm_attr_def
                (name, display_name, description, data_type_id, unit_id, attr_group_id)
                VALUES (
                    'width',
                    'Width',
                    NULL,
                    (SELECT id FROM fm_attr_data_type WHERE function_name = 'dt_int'),
                    'mm',
                    (SELECT id FROM fm_attr_group WHERE name = 'Dimensions')
                )"
        );
        $GLOBALS['phpgw']->db->query("INSERT INTO fm_attr_def
                (name, display_name, description, data_type_id, unit_id, attr_group_id)
                VALUES (
                    'depth',
                    'Depth',
                    NULL,
                    (SELECT id FROM fm_attr_data_type WHERE function_name = 'dt_int'),
                    'mm',
                    (SELECT id FROM fm_attr_group WHERE name = 'Dimensions')
                )"
        );
        $GLOBALS['phpgw']->db->query("INSERT INTO fm_attr_def
                (name, display_name, description, data_type_id, unit_id, attr_group_id)
                VALUES (
                    'tiles',
                    'No of tiles',
                    NULL,
                    (SELECT id FROM fm_attr_data_type WHERE function_name = 'dt_int'),
                    'mm',
                    (SELECT id FROM fm_attr_group WHERE name = 'Layout')
                )"
        );
        // Items
        $GLOBALS['phpgw']->db->query("INSERT INTO fm_item
                (group_id, location_id, vendor_id, installed)
                VALUES (
                    (SELECT id FROM fm_item_group WHERE name = 'Doors'),
                    1,
                    104533,
                    ".phpgwapi_datetime::user_localtime()."
                )"
        );
        $GLOBALS['phpgw']->db->query("INSERT INTO fm_item
                (group_id, location_id, vendor_id, installed)
                VALUES (
                    (SELECT id FROM fm_item_group WHERE name = 'Doors'),
                    1,
                    104533,
                    ".phpgwapi_datetime::user_localtime()."
                )"
        );
    }

    public function emptydb() {
        $GLOBALS['phpgw']->db->query("DELETE FROM fm_item_attr");
        $GLOBALS['phpgw']->db->query("DELETE FROM fm_item_group_attr");
        $GLOBALS['phpgw']->db->query("DELETE FROM fm_attr_def");
        $GLOBALS['phpgw']->db->query("DELETE FROM fm_attr_value");
        $GLOBALS['phpgw']->db->query("DELETE FROM fm_attr_group");
        $GLOBALS['phpgw']->db->query("DELETE FROM fm_attr_choice");
        $GLOBALS['phpgw']->db->query("DELETE FROM fm_attr_data_type");
        $GLOBALS['phpgw']->db->query("DELETE FROM fm_item");
        $GLOBALS['phpgw']->db->query("DELETE FROM fm_item_group");
        $GLOBALS['phpgw']->db->query("DELETE FROM fm_item_catalog");
    }

}
