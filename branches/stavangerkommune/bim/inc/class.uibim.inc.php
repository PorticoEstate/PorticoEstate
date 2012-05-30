<?php
phpgw::import_class('phpgwapi.yui');
phpgw::import_class('bim.soitem');
phpgw::import_class('bim.sobim');
phpgw::import_class('bim.sovfs');
phpgw::import_class('bim.sobimmodel');
phpgw::import_class('bim.sobim_converter');
phpgw::import_class('bim.soitem_group');
phpgw::import_class('bim.bobimmodel');
phpgw::import_class('bim.bobimitem');
phpgw::import_class('bim.sobimitem');
phpgw::import_class('bim.sobimtype');
phpgw::import_class('bim.sobimmodelinformation');
/*
 * This class serves as the 'Controller' or 'Container' in a dependancy injection context
 */
interface uibim {

}
class bim_uibim implements uibim {
	public static $virtualFileSystemPath = "ifc";
	private $db;
	/* @var $bocommon property_bocommon */
	private $bocommon;
	private $bimconverterUrl = "http://localhost:8080/bimconverter/rest/";

	public function __construct() {
		$this->bocommon = CreateObject('property.bocommon');

		$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
		$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'bim::item::index';
		$this->db = & $GLOBALS['phpgw']->db;
	}

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
        'displayModelInformation' => true
	);
	private function setupBimCss() {
		if ( !isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css) ) {
			$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
		}
		$GLOBALS['phpgw']->css->add_external_file('bim/templates/base/css/bim.css');
	}
	public function getModelsJson() {
		$GLOBALS['phpgw_info']['flags']['noheader'] = true;
		$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
		$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;
		header("Content-type: application/json");
		$bobimmodel = new bobimmodel_impl();
		$sobimmodel = new sobimmodel_impl($this->db);
		$bobimmodel->setSobimmodel($sobimmodel);
		$output = $bobimmodel->createBimModelList();
//		return $output;
		echo json_encode($output);
	}
	/*
	 *
	 */
	public function removeModelJson($modelId = null) {
		$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;
		header("Content-type: application/json");
		$output = array();
		$output["result"] = 1;
		if($modelId == null) {
			$modelId = (int) phpgw::get_var("modelId");
		}
			
		$bobimmodel = new bobimmodel_impl();
		$sovfs = new sovfs_impl();
		$sovfs->setSubModule(self::$virtualFileSystemPath);
		$bobimmodel->setVfsObject($sovfs);
		$sobimmodel = new sobimmodel_impl($this->db);
		$sobimmodel->setModelId($modelId);
		$bobimmodel->setSobimmodel($sobimmodel);
		try {
			$bobimmodel->removeIfcModelByModelId();
			echo json_encode($output);
		} catch (InvalidArgumentException $e) {
			$output["result"] = 0;
			$output["error"] = "Invalid arguments";
			$output["exception"] = $e;
			echo json_encode($output);
		} catch (ModelDoesNotExistException $e) {
			$output["result"] = 0;
			$output["error"] = "Model does not exist!";
			$output["exception"] = $e;
			echo json_encode($output);
		} catch (Exception $e) {
			$output["result"] = 0;
			$output["error"] = "General error";
			$output["exception"] = $e;
			echo json_encode($output);
		}
	}

	public function getFacilityManagementXmlByModelId($modelId = null) {
		$GLOBALS['phpgw_info']['flags']['noheader'] = true;
		$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
		$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;
		header("Content-type: application/xml");
		$restUrl = $this->bimconverterUrl;
		if($modelId == null) {
			$modelId = (int) phpgw::get_var("modelId");
		}
		//echo "ModelId is:".$modelId;
		$bobimmodel = new bobimmodel_impl();
		$sovfs = new sovfs_impl();
		$sovfs->setSubModule(self::$virtualFileSystemPath);
		$bobimmodel->setVfsObject($sovfs);
		$sobimmodel = new sobimmodel_impl($this->db);
		$sobimmodel->setModelId($modelId);
		$bobimmodel->setSobimmodel($sobimmodel);
		$sobimmodelinformation = new sobimmodelinformation_impl($this->db,$modelId);
		
		
		try {
			if($bobimmodel->checkBimModelIsUsed()) {
				throw new Exception("Model is already in use!");
			}
			$ifcFileWithRealPath = $bobimmodel->getIfcFileNameWithRealPath();
			$xmlResult = $this->getFacilityManagementXmlFromIfc($ifcFileWithRealPath);
			$bobimitem = new bobimitem_impl();
			$bobimitem->setSobimmodelinformation($sobimmodelinformation);
			$bobimitem->setIfcXml($xmlResult);

			$bobimitem->setSobimitem(new sobimitem_impl($this->db));
			$bobimitem->setSobimtype(new sobimtype_impl($this->db));

			$bobimitem->loadIfcItemsIntoDatabase();
			
			$result = array();
			$result["result"] = 1;
			$result["error"] = "";
			echo json_encode($result);
		} catch (NoResponseException $e) {
			$result = array();
			$result["result"] = 0;
			$result["error"] = "Could not connect to BIM converter rest service!";
			$result["Exception"] = $e;
			echo json_encode($result);
		} catch (Exception $e) {
			$result = array();
			$result["result"] = 0;
			$result["error"] = "General error!\nMessage: ".$e->getMessage();
			echo json_encode($result);
		}

		
			
	}

	private function getFacilityManagementXmlFromIfc($fileWithPath) {
		$sobim_converter = new sobim_converter_impl();
		$sobim_converter->setBaseUrl($this->bimconverterUrl);
		$sobim_converter->setFileToSend($fileWithPath);
		
		try {
			$returnedXml =  $sobim_converter->getFacilityManagementXml();
			$sxe = simplexml_load_string($returnedXml);
			return $sxe;
		} catch (NoResponseException $e) {
			throw $e;
		} catch (InvalidArgumentException $e) {
			throw $e;
		} catch ( Exception $e) {
			echo $e;
		}
	}

	public function showModels() {
		$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'bim.modellist', 'bim' );
		/*$GLOBALS['phpgw_info']['flags']['noheader'] = false;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = false;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;
			$GLOBALS['phpgw']->common->phpgw_header(true);*/
			
			
			
		$GLOBALS['phpgw']->xslttpl->add_file(array('bim_showmodels'));
		$bobimmodel = new bobimmodel_impl();
		$sobimmodel = new sobimmodel_impl($this->db);
		$bobimmodel->setSobimmodel($sobimmodel);
		$output = $bobimmodel->createBimModelList();
		$loadingImage = $GLOBALS['phpgw']->common->find_image('bim', 'ajaxLoader.gif');
		$data = array (
     		'models' => $output,
     		'loadingImage' => $loadingImage
		);
		$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('modelData' => $data));
		$this->setupBimCss();
		$GLOBALS['phpgw']->js->validate_file( 'yui3', 'yui/yui-min', 'phpgwapi' );
		// echo '<script type="text/javascript" src="http://yui.yahooapis.com/3.3.0/build/yui/yui-min.js"></script>';
		$ble =  <<<HTML
        <script>YUI().use("event-delegate", function(Y) {
 
    Y.delegate("click", function(e) {
 
        //  The list item that matched the provided selector is the
        //  default 'this' object
        Y.log("Default scope: " + this.get("id"));
 
        //  The list item that matched the provided selector is
        //  also available via the event's currentTarget property
        //  in case the 'this' object is overridden in the subscription.
        Y.log("Clicked list item: " + e.currentTarget.get("id"));
 
        //  The actual click target, which could be the matched item or a
        //  descendant of it.
        Y.log("Event target: " + e.target);
 
        //  The delegation container is added to the event facade
        Y.log("Delegation container: " + e.container.get("id"));
 
 
    }, "#container44", "li");
 
});</script>
HTML;

		$someOutput =  '<div id="container44"><ul id="list"><li id="li-1">List Item 1</li>
        <li id="li-2">List Item 2</li> 
	        <li id="li-3">List Item 3</li> 
	        <li id="li-4">List Item 4</li> 
	        <li id="li-5">List Item 5</li> 
	    </ul> 
	</div> <script>doDelegate()</script>';
	}

	private $form_upload_field_filename ="ifc_file_name";
	private $form_upload_field_modelname ="ifc_model_name";

	public function upload() {
		$GLOBALS['phpgw']->xslttpl->add_file(array('bim_upload_ifc'));
			
		$import_action	= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'bim.uibim.uploadFile', 'id'=> $id));
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
		$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('upload' => $data));
		$this->setupBimCss();
	}


	public function uploadFile($uploadedFileArray = null, $modelName = null, $unitTest = false) {
		if(!$unitTest) {
			$GLOBALS['phpgw']->xslttpl->add_file(array('bim_upload_ifc_result'));
		}

		if(!$uploadedFileArray) {
			$uploadedFileArray = $_FILES[$this->form_upload_field_filename];
		}
		if(!$modelName) {
			$modelName = phpgw::get_var($this->form_upload_field_modelname);
		}
		$returnValue = array();

		$filename = $uploadedFileArray['name'];
		$filenameWithPath = $uploadedFileArray['tmp_name'];
		$bobimmodel = new bobimmodel_impl();
		$sovfs = new sovfs_impl($filename, $filenameWithPath, self::$virtualFileSystemPath);
		$bobimmodel->setVfsObject($sovfs);
		$sobimmodel = new sobimmodel_impl($this->db);
		$bobimmodel->setSobimmodel($sobimmodel);
		$bobimmodel->setModelName($modelName);
		$errorMessage = "";
		$error = false;
		try {
			$bobimmodel->addUploadedIfcModel();

		} catch (FileExistsException $e) {
			$error = true;
			$errorMessage =  "Filename in use! \n Try renaming the file";
			if($unitTest) {
				throw $e;
			}
		} catch (Exception $e) {
			$error = true;
			$errorMessage =  $e->getMessage();
			if($unitTest) {
				throw $e;
			}
		}


		$link_to_models	= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'bim.uibim.showModels'));
		$link_to_upload	= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'bim.uibim.upload'));
		$data = array
		(
				'modelName'						=> $modelName,
				'error'							=> $error,
				'errorMessage'					=> $errorMessage,
				'linkToModels'					=> $link_to_models,
				'linkToUpload'					=> $link_to_upload
		);

		if(!$unitTest) {
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('uploadResult' => $data));
		}

		return $data;
	}

	public function displayModelInformation() {
		/*$GLOBALS['phpgw_info']['flags']['noheader'] = false;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = false;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;
			$GLOBALS['phpgw']->common->phpgw_header(true);*/
		$GLOBALS['phpgw']->xslttpl->add_file(array('bim_modelinformation'));
		$modelId = phpgw::get_var("modelId");
		//$modelId = 3;
		if(empty($modelId)) {
			// go apeshit
			echo "No modelId!";
			
		} else {
			$sobimInfo = new sobimmodelinformation_impl($this->db, $modelId);
			/* @var $modelInfo BimModelInformation */
			$modelInfo = $sobimInfo->getModelInformation();
			$sobimmodel = new sobimmodel_impl($this->db);
			$sobimmodel->setModelId($modelId);
			/* @var $model BimModel */
			$model = $sobimmodel->retrieveBimModelInformationById();
			$data = array (
				'model'		=> $model->transformObjectToArray(),
				'information' => $modelInfo->transformObjectToArray()
			);
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('modelInformation' => $data));
			
		}
		
		$this->setupBimCss();
		
	}


}
