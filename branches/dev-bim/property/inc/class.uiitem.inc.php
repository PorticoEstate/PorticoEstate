<?php
phpgw::import_class('phpgwapi.yui');
phpgw::import_class('property.soitem');
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
        $this->sogroup = property_sogroup::singleton();
    }



    function index() {
        $menu_sub = array(
                'tenant'=>'invoice',
                'owner'	=>'admin',
                'vendor'=>'invoice'
        );

        $dry_run=false;
        $lookup = ''; //Fix this

        $datatable = array();
        $values_combo_box = array('');

        /*$receipt = $GLOBALS['phpgw']->session->appsession('session_data','actor_receipt_' . $this->role);
        $GLOBALS['phpgw']->session->appsession('session_data','actor_receipt_' . $this->role,'');*/


        if(phpgw::get_var('phpgw_return_as') != 'json') {

            if(!$lookup) {
                $datatable['menu']	= $this->bocommon->get_menu();
            }

            $datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
            (
                'menuaction'=> 'property.uiitem.index',
                'lookup'    => $lookup,
                'cat_id'	=>$this->cat_id,
                'query'		=>$this->query,
                'role'		=> $this->role,
                'member_id'	=> $this->member_id
            ));
            $datatable['config']['allow_allrows'] = true;

            $datatable['config']['base_java_url'] = "menuaction:'property.uiitem.index',"

                    ."lookup:'{$lookup}',"
                    ."query:'{$this->query}',"
                    ."cat_id:'{$this->cat_id}',"
                    ."role:'{$this->role}',"
                    ."member_id:'{$this->member_id}'";
            //die(_debug_array($datatable));

            $values_combo_box[0] = '';
            $default_value = array ('cat_id'=>'','name'=>lang('no member'));
            array_unshift ($values_combo_box[0]['cat_list'],$default_value);

            $values_combo_box[1] = $this->bocommon->select_category_list(array('format'=>'filter','selected' => $this->cat_id,'type' => $this->role,'order'=>'descr'));
            $default_value = array ('id'=>'','name'=> lang('no category'));
            array_unshift ($values_combo_box[1],$default_value);

            $datatable['actions']['form'] = array(
                    array(
                            'action'	=> $GLOBALS['phpgw']->link('/index.php',
                            array(
                            'menuaction' 		=> 'property.uiactor.index',
                            'lookup'        		=> $lookup,
                            'cat_id'	=> $this->cat_id,
                            'query'		=> $this->query,
                            'role'		=> $this->role,
                            'member_id'	=> $this->member_id
                            )
                            ),
                            'fields'	=> array(
                                    'field' => array(
                                            array(
                                                    'id' => 'btn_member_id',
                                                    'name' => 'member_id',
                                                    'value'	=> lang('Member'),
                                                    'type' => 'button',
                                                    'style' => 'filter',
                                                    'tab_index' => 1
                                            ),
                                            array(
                                                    'id' => 'btn_cat_id',
                                                    'name' => 'cat_id',
                                                    'value'	=> lang('Category'),
                                                    'type' => 'button',
                                                    'style' => 'filter',
                                                    'tab_index' => 2
                                            ),
                                            array(
                                                    'type'=> 'link',
                                                    'id'  => 'btn_columns',
                                                    'url' => "Javascript:window.open('".$GLOBALS['phpgw']->link('/index.php',
                                                            array(
                                                            'menuaction' => 'property.uiactor.columns',
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
                                            array( //boton     SEARCH
                                                    'id' => 'btn_search',
                                                    'name' => 'search',
                                                    'value'    => lang('search'),
                                                    'type' => 'button',
                                                    'tab_index' => 4
                                            ),
                                            array( // TEXT IMPUT
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
                                            array( //div values  combo_box_0
                                                    'id' => 'values_combo_box_0',
                                                    'value'	=> $this->bocommon->select2String($values_combo_box[0]['cat_list'], 'cat_id') //i.e.  id,value/id,vale/
                                            ),
                                            array( //div values  combo_box_1
                                                    'id' => 'values_combo_box_1',
                                                    'value'	=> $this->bocommon->select2String($values_combo_box[1])
                                            )
                                    )
                            )
                    )
            );

            if($this->role == 'tenant') {
                unset($datatable['actions']['form'][0]['fields']['field'][0]);
            }

            if(!$this->acl_add) {
                unset($datatable['actions']['form'][0]['fields']['field'][3]);
            }
            $dry_run=true;
        }

        $actor_list = array();
        $actor_list = $this->bo->read($dry_run);

        //echo $dry_run; count($actor_list); die(_debug_array($actor_list));

        $uicols	= $this->bo->uicols;

        $j=0;
        if (isset($actor_list) && is_array($actor_list)) {
            foreach($actor_list as $actor) {
                for ($i=0;$i<count($uicols['name']);$i++) {
                    if($uicols['input_type'][$i]!='hidden') {
                        if(isset($actor['query_location'][$uicols['name'][$i]])) {
                            $datatable['rows']['row'][$j]['column'][$i]['name'] 			= $uicols['name'][$i];
                            $datatable['rows']['row'][$j]['column'][$i]['statustext']		= lang('search');
                            $datatable['rows']['row'][$j]['column'][$i]['value']			= $actor[$uicols['name'][$i]];
                            $datatable['rows']['row'][$j]['column'][$i]['format'] 			= 'link';
                            $datatable['rows']['row'][$j]['column'][$i]['java_link']		= true;
                            $datatable['rows']['row'][$j]['column'][$i]['link']				= $actor['query_location'][$uicols['name'][$i]];
                        }
                        else {
                            $datatable['rows']['row'][$j]['column'][$i]['value'] 			= $actor[$uicols['name'][$i]];
                            $datatable['rows']['row'][$j]['column'][$i]['name'] 			= $uicols['name'][$i];
                            $datatable['rows']['row'][$j]['column'][$i]['lookup'] 			= $lookup;
                            $datatable['rows']['row'][$j]['column'][$i]['align'] 			= (isset($uicols['align'][$i])?$uicols['align'][$i]:'center');

                            if(isset($uicols['datatype']) && isset($uicols['datatype'][$i]) && $uicols['datatype'][$i]=='link' && $actor[$uicols['name'][$i]]) {
                                $datatable['rows']['row'][$j]['column'][$i]['value']		= lang('link');
                                $datatable['rows']['row'][$j]['column'][$i]['link']		= $actor[$uicols['name'][$i]];
                                $datatable['rows']['row'][$j]['column'][$i]['target']	= '_blank';
                            }
                        }
                    }
                    else {
                        $datatable['rows']['row'][$j]['column'][$i]['name'] 			= $uicols['name'][$i];
                        $datatable['rows']['row'][$j]['column'][$i]['value']			= $actor[$uicols['name'][$i]];
                    }

                    $datatable['rows']['row'][$j]['hidden'][$i]['value'] 			= $actor[$uicols['name'][$i]];
                    $datatable['rows']['row'][$j]['hidden'][$i]['name'] 			= $uicols['name'][$i];
                }

                $j++;
            }
        }

        // NO pop-up
        $datatable['rowactions']['action'] = array();
        if(!$lookup) {
            $parameters = array
                    (
                    'parameter' => array
                    (
                            array
                            (
                                    'name'		=> 'actor_id',
                                    'source'	=> 'id'
                            )
                    )
            );

            if($this->acl_read) {
                $datatable['rowactions']['action'][] = array(
                        'my_name' 			=> 'view',
                        'text' 			=> lang('view'),
                        'action'		=> $GLOBALS['phpgw']->link('/index.php',array
                        (
                        'menuaction'	=> 'property.uiactor.view',
                        'role'	=> $this->role
                        )),
                        'parameters'	=> $parameters
                );
                $datatable['rowactions']['action'][] = array(
                        'my_name' 			=> 'view',
                        'text' 			=> lang('open view in new window'),
                        'action'		=> $GLOBALS['phpgw']->link('/index.php',array
                        (
                        'menuaction'	=> 'property.uiactor.view',
                        'role'			=> $this->role,
                        'target'		=> '_blank'
                        )),
                        'parameters'	=> $parameters
                );
            }
            if($this->acl_edit) {
                $datatable['rowactions']['action'][] = array(
                        'my_name' 			=> 'edit',
                        'text' 			=> lang('edit'),
                        'action'		=> $GLOBALS['phpgw']->link('/index.php',array
                        (
                        'menuaction'	=> 'property.uiactor.edit',
                        'role'	=> $this->role
                        )),
                        'parameters'	=> $parameters
                );
                $datatable['rowactions']['action'][] = array(
                        'my_name' 		=> 'edit',
                        'text' 			=> lang('open edit in new window'),
                        'action'		=> $GLOBALS['phpgw']->link('/index.php',array
                        (
                        'menuaction'	=> 'property.uiactor.edit',
                        'role'			=> $this->role,
                        'target'		=> '_blank'
                        )),
                        'parameters'	=> $parameters
                );
            }
            if($this->acl_delete) {
                $datatable['rowactions']['action'][] = array(
                        'my_name' 			=> 'delete',
                        'text' 			=> lang('delete'),
                        'confirm_msg'	=> lang('do you really want to delete this entry'),
                        'action'		=> $GLOBALS['phpgw']->link('/index.php',array
                        (
                        'menuaction'	=> 'property.uiactor.delete',
                        'role'	=> $this->role
                        )),
                        'parameters'	=> $parameters
                );
            }
            if($this->acl_add) {
                $datatable['rowactions']['action'][] = array(
                        'my_name' 			=> 'add',
                        'text' 			=> lang('add'),
                        'action'		=> $GLOBALS['phpgw']->link('/index.php',array
                        (
                        'menuaction'	=> 'property.uiactor.edit',
                        'role'	=> $this->role
                        ))
                );
            }
            unset($parameters);
        }

        $uicols_count	= count($uicols['descr']);

        for ($i=0;$i<$uicols_count;$i++) {

            //all colums should be have formatter
            $datatable['headers']['header'][$i]['formatter'] = ($uicols['formatter'][$i]==''?  '""' : $uicols['formatter'][$i]);

            if($uicols['input_type'][$i]!='hidden') {
                $datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
                $datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
                $datatable['headers']['header'][$i]['visible'] 			= true;
                $datatable['headers']['header'][$i]['format'] 			= $this->bocommon->translate_datatype_format($uicols['datatype'][$i]);
                $datatable['headers']['header'][$i]['sortable']			= false;

                if(isset($uicols['datatype'][$i]) && $uicols['datatype'][$i]!='T' && $uicols['datatype'][$i]!='CH') {
                    $datatable['headers']['header'][$i]['sortable']		= true;
                    $datatable['headers']['header'][$i]['sort_field']	= $uicols['name'][$i];
                }
            }
            else {
                $datatable['headers']['header'][$i]['name'] 			= 'id2';
                $datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
                $datatable['headers']['header'][$i]['visible'] 			= false;
                $datatable['headers']['header'][$i]['sortable']			= false;
                $datatable['headers']['header'][$i]['format'] 			= 'hidden';
            }
        }

        // path for property.js
        $datatable['property_js'] =  $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

        // Pagination and sort values
        $datatable['pagination']['records_start'] 	= (int)$this->bo->start;
        $datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
        $datatable['pagination']['records_returned'] = count($actor_list);
        $datatable['pagination']['records_total'] 	= $this->bo->total_records;

        //$datatable['sorting']['order'] 	= phpgw::get_var('order', 'string'); // Column
        //$datatable['sorting']['sort'] 	= phpgw::get_var('sort', 'string'); // ASC / DESC

        if($this->role == 'tenant') {
            if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== "")) {
                $datatable['sorting']['order'] 			= 'first_name'; // name key Column in myColumnDef
                $datatable['sorting']['sort'] 			= 'asc'; // ASC / DESC
            }
            else {
                $datatable['sorting']['order']			= phpgw::get_var('order', 'string'); // name of column of Database
                $datatable['sorting']['sort'] 			= phpgw::get_var('sort', 'string'); // ASC / DESC
            }
        }
        else {
            if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== "")) {
                $datatable['sorting']['order'] 			= 'org_name'; // name key Column in myColumnDef
                $datatable['sorting']['sort'] 			= 'asc'; // ASC / DESC
            }
            else {
                $datatable['sorting']['order']			= phpgw::get_var('order', 'string'); // name of column of Database
                $datatable['sorting']['sort'] 			= phpgw::get_var('sort', 'string'); // ASC / DESC
            }
        }

        _debug_array($datatable);

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
        $GLOBALS['phpgw']->js->validate_file( 'yahoo', 'actor.index', 'property' );

        //$this->save_sessiondata();
    }



    public function index2() {
        // Highlight menu selection
        $GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::item::index';

        $item_list = $this->so->read(null);

        $uicols = array(
                'name' =>       array('id',      'group_id', 'location_id', 'vendor_id', 'installed_date'),
                'datatype' =>   array('integer', 'integer',   'integer',    'integer',   'date'),
                'hidden' =>     array(false,     false,       false,        false,       false)
        );

        $datatable = array();

        $i = 0;
        foreach($item_list as $item) {
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

        // Prepare YUI Library
        $GLOBALS['phpgw']->js->validate_file( 'yahoo', 'actor.index', 'property' );

        //_debug_array($datatable);

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
