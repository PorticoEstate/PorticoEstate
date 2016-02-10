<?php
	phpgw::import_class('bim.soitem');
	phpgw::import_class('bim.sobim');
	phpgw::import_class('bim.sovfs');
	phpgw::import_class('bim.sobimmodel');
	phpgw::import_class('bim.sobim_converter');
	phpgw::import_class('bim.soitem_group');
	phpgw::import_class('bim.bobimmodel');
	phpgw::import_class('phpgwapi.datetime');

//phpgw::import_class('bim.bobimcreate');
	/**
 * FIXME: Description
 * @package bim
 */
	class bim_uiitem
	{
	
	 public static $virtualFileSystemPath = "ifc";
    private $so;
    private $db;
    private $sogroup;
    private $bocommon;
    private $dry_run;
    public $public_functions = array
    (
        'index' => true,
    	'foo' => true,
    	'showModels' => true,
    	'getModelsJson' => true,
    	'removeModelJson' => true,
    	'getFacilityManagementXmlByModelId' => true,
    	'upload' => true,
    	'uploadFile' => true,
        'testdata' => true,
    	'ifc' => true,
        'emptydb' => true
    );

		public function __construct()
		{
        $this->bocommon = CreateObject('property.bocommon');

        $GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
        $GLOBALS['phpgw_info']['flags']['menu_selection'] = 'bim::item::index';
		$this->db = & $GLOBALS['phpgw']->db;
        $this->so = bim_soitem::singleton();
        $this->sogroup = bim_soitem_group::singleton();
    }

		function index()
		{
    	
        $menu_sub = array(
				'tenant' => 'invoice',
				'owner' => 'admin',
				'vendor' => 'invoice'
        );

        $this->dry_run = false;

        $datatable = array();
        $this->populateDataTable($datatable);
		$json = $this->populateJson($datatable);
			if(phpgw::get_var('phpgw_return_as') == 'json')
 		{
            return $json;
        }
		$datatable['json_data'] = json_encode($json);

        // Prepare template variables and process XSLT
        $template_vars = array();
        $template_vars['datatable'] = $datatable;
        $GLOBALS['phpgw']->xslttpl->add_file(array('datatable'));
        //print_r($template_vars);
        $GLOBALS['phpgw']->xslttpl->set_var('phpgw', $template_vars);

        $this->setupCss();

        //Title of Page
        $GLOBALS['phpgw_info']['flags']['app_header'] = lang('actor') . ': ' . lang('list ' . $this->role);
    }

		private function populateJson(&$datatable)
		{
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
			if(isset($datatable['rows']['row']) && is_array($datatable['rows']['row']))
			{
				foreach($datatable['rows']['row'] as $row)
				{
                    $json_row = array();
					foreach($row['column'] as $column)
					{
						if(isset($column['format']) && $column['format'] == "link" && $column['java_link'] == true)
						{
							$json_row[$column['name']] = "<a href='#' id='" . $column['link'] . "' onclick='javascript:filter_data(this.id);'>" . $column['value'] . "</a>";
						}
						elseif(isset($column['format']) && $column['format'] == "link")
						{
							$json_row[$column['name']] = "<a href='" . $column['link'] . "'>" . $column['value'] . "</a>";
						}
						else
						{
                            $json_row[$column['name']] = $column['value'];
                        }
                    }
                    $json['records'][] = $json_row;
                }
            }
            
    // right in datatable
			if(isset($datatable['rowactions']['action']) && is_array($datatable['rowactions']['action']))
			{
                $json ['rights'] = $datatable['rowactions']['action'];
            }
		return $json;
    }

		private function populateDataTable(&$datatable)
		{
			if(phpgw::get_var('phpgw_return_as') != 'json')
			{
            $this->setFormAndNonJsonProperties($datatable);
        }

        $item_list = $this->so->read($this->dry_run);

        $uicols	= $this->so->uicols;
        $uicols_count = count($uicols['name']);

        
		$this->populateDatatableRows($item_list, $datatable, $uicols, $uicols_count);
		$this->addRowActionsToDatatable($datatable);
		$this->populateColumnNames($datatable, $uicols, $uicols_count);
        


        // Pagination and sort values
		$this->setPagination($datatable, $item_list);
		$this->setSorting($datatable);
    }

		private function setFormAndNonJsonProperties(&$datatable)
		{
    	// Set base URL. FIXME: Add more URL parameters when needed
            $datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
            (
				'menuaction' => 'bim.uiitem.index',
            ));
            $datatable['config']['allow_allrows'] = true;
            $datatable['config']['base_java_url'] = "menuaction:'bim.uiitem.index',group:'all'";
			$this->setForm($datatable);
			$this->dry_run = true;
    }
    /*
     * form on top of screen ( above the datatable)
     * @see /phpgwapi/templates/base/datatable.xsl
     */

		private function setForm(&$datatable)
		{
    	 $values_combo_box_0 = $this->sogroup->read(null);
            $default_value = array('id' => -1, 'name' => 'Alle grupper');
            array_unshift($values_combo_box_0, $default_value);
    	$datatable['actions']['form'] = array(
                array(
					'action' => $GLOBALS['phpgw']->link('/index.php', array(
                                'menuaction' 	=> 'bim.uiitem.index',
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
								'type' => 'link',
                                    'id'  => 'btn_columns',
								'url' => "Javascript:window.open('" . $GLOBALS['phpgw']->link('/index.php', array(
                                            'menuaction' => 'bim.uiitem.columns',
                                            'role'		=> $this->role
								)) . "','','width=350,height=370')",
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
								'value' => '', //$query,
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
    }

		private function setPagination(&$datatable, &$item_list)
		{
			$datatable['pagination']['records_start'] = (int)$this->bo->start;
        $datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned'] = count($item_list);
        $datatable['pagination']['records_total'] 	= $this->so->total_records();
    }
    
		private function setSorting(&$datatable)
		{
			if((phpgw::get_var("start") == "") && (phpgw::get_var("order", 'string') == ""))
        {
            $datatable['sorting']['order'] 			= 'id'; // name key Column in myColumnDef
            $datatable['sorting']['sort'] 			= 'asc'; // ASC / DESC
        }
        else
        {
            $datatable['sorting']['order']			= phpgw::get_var('order', 'string'); // name of column of Database
            $datatable['sorting']['sort'] 			= phpgw::get_var('sort', 'string'); // ASC / DESC
        }
    }

		private function populateColumnNames(&$datatable, &$uicols, &$uicols_count)
		{
			for($i = 0; $i < $uicols_count; $i++)
			{

            //all colums should be have formatter
				$datatable['headers']['header'][$i]['formatter'] = ($uicols['formatter'][$i] == '' ? '""' : $uicols['formatter'][$i]);

				if($uicols['input_type'][$i] != 'hidden')
				{
                $datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
                $datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
                $datatable['headers']['header'][$i]['visible'] 			= true;
                $datatable['headers']['header'][$i]['format'] 			= $this->bocommon->translate_datatype_format($uicols['datatype'][$i]);
                $datatable['headers']['header'][$i]['sortable']			= false;

                // If datatype is not T or CH
					if(!in_array($uicols['datatype'][$i], array('T', 'CH')))
					{
                    $datatable['headers']['header'][$i]['sortable']		= true;
                    $datatable['headers']['header'][$i]['sort_field']	= $uicols['name'][$i];
                }
            }
        }
    }

		private function populateDatatableRows(&$item_list, &$datatable, &$uicols, &$uicols_count)
		{
			$j = 0;
			if(is_array($item_list))
			{
            // For each item...
				foreach($item_list as $item)
				{
                // For each column definition...
					for($i = 0; $i < $uicols_count; $i++)
					{
                    
						if($uicols['input_type'][$i] != 'hidden')
						{
                        $datatable['rows']['row'][$j]['column'][$i]['value'] 	= $item[$uicols['name'][$i]];
                        $datatable['rows']['row'][$j]['column'][$i]['name'] 	= $uicols['name'][$i];
                        $datatable['rows']['row'][$j]['column'][$i]['lookup'] 	= '$lookup';
                        $datatable['rows']['row'][$j]['column'][$i]['align'] 	= (isset($uicols['align'][$i]) ? $uicols['align'][$i] : 'center');

							/* if($uicols['datatype'][$i] == 'link' && $item[$uicols['name'][$i]]) {
                            $datatable['rows']['row'][$j]['column'][$i]['value']    = lang('link');
                            $datatable['rows']['row'][$j]['column'][$i]['link']		= $item[$uicols['name'][$i]];
                            $datatable['rows']['row'][$j]['column'][$i]['target']	= '_blank';
							  } */
                    }
						else
						{
                        $datatable['rows']['row'][$j]['column'][$i]['name'] 	= $uicols['name'][$i];
                        $datatable['rows']['row'][$j]['column'][$i]['value']	= $item[$uicols['name'][$i]];
                    }

                    $datatable['rows']['row'][$j]['hidden'][$i]['value']    = $item[$uicols['name'][$i]];
                    $datatable['rows']['row'][$j]['hidden'][$i]['name']     = $uicols['name'][$i];
                }

                $j++;
            }
        }
    }
    
		private function addRowActionsToDatatable(&$datatable)
		{
    	// NO pop-up
        $datatable['rowactions']['action'] = array();
			$parameters = array('parameter' => array(array('name' => 'item_id', 'source' => 'id')));
        


        $datatable['rowactions']['action'][] = array(
                'my_name' 		=> 'view',
                'text' 			=> lang('view'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
                (
                    'menuaction'	=> 'bim.uiitem.view',
                    'role'          => $this->role
                )),
                'parameters'	=> $parameters
        );
        $datatable['rowactions']['action'][] = array(
                'my_name' 		=> 'view',
                'text' 			=> lang('open view in new window'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
                (
                    'menuaction'	=> 'bim.uiitem.view',
                    'role'			=> $this->role,
                    'target'		=> '_blank'
                )),
                'parameters'	=> $parameters
        );


        $datatable['rowactions']['action'][] = array(
                'my_name' 		=> 'edit',
                'text' 			=> lang('edit'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
                (
					'menuaction' => 'bim.uiitem.edit',
                    'role'      => $this->role
                )),
                'parameters'	=> $parameters
        );
        $datatable['rowactions']['action'][] = array(
                'my_name' 		=> 'edit',
                'text' 			=> lang('open edit in new window'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
                (
                'menuaction'	=> 'bim.uiitem.edit',
                'role'			=> $this->role,
                'target'		=> '_blank'
                )),
                'parameters'	=> $parameters
        );

        $datatable['rowactions']['action'][] = array(
                'my_name' 			=> 'delete',
                'text' 			=> lang('delete'),
                'confirm_msg'	=> lang('do you really want to delete this entry'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
                (
                'menuaction'	=> 'bim.uiitem.delete',
                'role'	=> $this->role
                )),
                'parameters'	=> $parameters
        );
        $datatable['rowactions']['action'][] = array(
                'my_name' 			=> 'add',
                'text' 			=> lang('add'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
                (
                'menuaction'	=> 'bim.uiitem.edit',
                'role'	=> $this->role
                ))
        );

        unset($parameters);
    }

		private function setupCss()
		{
			if(!isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css))
			{
            $GLOBALS['phpgw']->css = createObject('phpgwapi.css');
        }
        
        $GLOBALS['phpgw']->css->add_external_file('bim/templates/base/css/bim.css');
    }

		private function setupBimCss()
		{
			if(!isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css))
			{
            $GLOBALS['phpgw']->css = createObject('phpgwapi.css');
        }
        $GLOBALS['phpgw']->css->add_external_file('bim/templates/base/css/bim.css');
    }
    
		public function foo()
		{
    	/*
    	$formTest = array();
    	$formTest['msgbox_text']= "ble1";
    	$formTest['msgbox_class']= "classy";
    	
    	//$formTest['form_elm']['button']['value'] = "ble2";
    	$template_vars = array();
        $template_vars['msgbox_data'] = $formTest;
        
        $GLOBALS['phpgw']->xslttpl->add_file(array('msgbox'));
        //print_r($template_vars);
        $GLOBALS['phpgw']->xslttpl->set_var('phpgw', $template_vars);
        */
			$xml = <<<XML
    	<PHPGW>
    	<project ifcObjectType="ifcproject">
    <attributes>
        <guid>3KFKb0sfrDJwSHalGIQFZT</guid>
        <longName>FM Architectural Handover</longName>
        <name>FM-A-01</name>
    </attributes>
    <ownerHistory>
        <changeAction>ADDED</changeAction>
        <creationDate>1179073813</creationDate>
        <owningApplication>
            <applicationDeveloper>
                <name>AEC3</name>
            </applicationDeveloper>
            <applicationFullName>IFC text editor</applicationFullName>
            <applicationIdentifier>IFCtext</applicationIdentifier>
            <version>Version 1</version>
        </owningApplication>
        <owningUser>
            <organization>
                <name>AEC3</name>
            </organization>
            <person>
                <familyName>Liebich</familyName>
                <givenName>Thomas</givenName>
            </person>
        </owningUser>
    </ownerHistory>
    <decomposition>
        <buildings>
            <guid>28hfXoRX9EMhvGvGhmaaae</guid>
        </buildings>
        <site>28hfXoRX9EMhvGvGhmaaad</site>
    </decomposition>
    <units>
        <unit>
            <name>LENGTHUNIT</name>
            <value>METRE</value>
        </unit>
        <unit>
            <name>PLANEANGLEUNIT</name>
            <value>DEGREE</value>
        </unit>
        <unit>
            <name>AREAUNIT</name>
            <value>SQUARE_METRE</value>
        </unit>
        <unit>
            <name>VOLUMEUNIT</name>
            <value>CUBIC_METRE</value>
        </unit>
    </units>
</project>
</PHPGW>
XML;

    	$GLOBALS['phpgw']->xslttpl->add_file(array('testProject3'));
    	$GLOBALS['phpgw']->xslttpl->set_xml_data($xml);
   		
    	$this->setupCss();
    }
   
		private function getFacilityManagementXmlFromIfc($fileWithPath)
		{
		$sobim_converter = new sobim_converter_impl();
		$sobim_converter->setFileToSend($fileWithPath);
			try
			{
			$returnedXml =  $sobim_converter->getFacilityManagementXml();
			$sxe = simplexml_load_string($returnedXml);
			return $sxe;			
			}
			catch(Exception $e)
			{
			echo $e;
		}
	}
    
		public function showModels()
		{
    	
    	$GLOBALS['phpgw']->xslttpl->add_file(array('bim_showmodels'));
    	$bobimmodel = new bobimmodel_impl();
    	$sobimmodel = new sobimmodel_impl($this->db);
     	$bobimmodel->setSobimmodel($sobimmodel);
     	$output = $bobimmodel->createBimModelList();
     	$loadingImage = $GLOBALS['phpgw']->common->find_image('bim', 'ajaxLoader.gif');
			$data = array(
     		'models' => $output,
     		'loadingImage' => $loadingImage
     	);
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('modelData' => $data));
        $this->setupBimCss();
    }
    
		private $form_upload_field_filename = "ifc_file_name";
		private $form_upload_field_modelname = "ifc_model_name";
    
		public function upload()
		{
     	$GLOBALS['phpgw']->xslttpl->add_file(array('bim_upload_ifc'));
    	
			$import_action = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bim.uiitem.uploadFile',
				'id' => $id));
        $data = array
			(
				'importfile'					=> $importfile,
				'values'						=> $content,
				'form_field_modelname'			=> $this->form_upload_field_modelname,
				'form_field_filename'			=> $this->form_upload_field_filename,
				'import_action'					=> $import_action,
				'lang_import_statustext'		=> lang('import to this location from spreadsheet'),
				'lang_import'					=> lang('import'),
				'lang_cancel'					=> lang('cancel')
			);
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('upload' => $data));
        $this->setupBimCss();
     }
    
		public function uploadFile()
		{
     	$uploadedFileArray = $_FILES[$this->form_upload_field_filename];
     	$modelName = phpgw::get_var($this->form_upload_field_modelname);
     	$filename = $uploadedFileArray['name'];
		$filenameWithPath = $uploadedFileArray['tmp_name'];
     	$bobimmodel = new bobimmodel_impl();
     	$sovfs = new sovfs_impl($filename, $filenameWithPath, $this->virtualFileSystemPath);
     	$bobimmodel->setVfsObject($sovfs);
     	$sobimmodel = new sobimmodel_impl($this->db);
     	$bobimmodel->setSobimmodel($sobimmodel);
     	$bobimmodel->setModelName($modelName);
     	$error = "";
			try
			{
     		$bobimmodel->addUploadedIfcModel();
			}
			catch(FileExistsException $e)
			{
     		$error =  $e;
			}
			catch(Exception $e)
			{
     		$error =  $e;
     	}
     	
     	
     	
     	 $data = array
			(
				'importfile'					=> print_r($_FILES, true),
				'modelName'						=> $modelName,
				'error'							=> $error
			);
     	
     	
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('upload' => $data));
     }
     
		public function listModels()
		{
     	
     }
     
		public function ifc()
		{
     	$GLOBALS['phpgw']->xslttpl->add_file(array('ifc'));
     }

		public function testdata()
		{
        // BIM testdata
		$GLOBALS['phpgw']->db->transaction_begin();
        
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
                    (SELECT id FROM fm_standard_unit WHERE id = 'mm'),
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
                    (SELECT id FROM fm_standard_unit WHERE id = 'mm'),
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
                    1,
                    " . time() . "
                )"
        );
        $GLOBALS['phpgw']->db->query("INSERT INTO fm_item
                (group_id, location_id, vendor_id, installed)
                VALUES (
                    (SELECT id FROM fm_item_group WHERE name = 'Doors'),
                    1,
                    1,
                    " . time() . "
                )"
        );
		$GLOBALS['phpgw']->db->transaction_commit();
    }

		public function emptydb()
		{
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