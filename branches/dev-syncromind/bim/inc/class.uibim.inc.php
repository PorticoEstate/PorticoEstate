<?php
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

	interface uibim
	{
		
	}
	phpgw::import_class('phpgwapi.uicommon_jquery');

	class bim_uibim extends phpgwapi_uicommon_jquery
	{

	public static $virtualFileSystemPath = "ifc";
	private $db;
	/* @var $bocommon property_bocommon */
	private $bocommon;
	private $bimconverterUrl = "http://localhost:8080/bimconverter/rest/";

		public function __construct()
		{
			parent::__construct();
			$this->acl = & $GLOBALS['phpgw']->acl;
			$this->acl_location = 'admin';
			$this->acl_read = $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'bim');
			$this->acl_add = $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'bim');
			$this->acl_edit = $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'bim');
			$this->acl_delete = $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'bim');
			$this->acl_manage = $this->acl->check($this->acl_location, PHPGW_ACL_PRIVATE, 'bim'); // manage

		$this->bocommon = CreateObject('property.bocommon');

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

		private function setupBimCss()
		{
			if(!isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css))
			{
			$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
		}
		$GLOBALS['phpgw']->css->add_external_file('bim/templates/base/css/bim.css');
	}

		public function getModelsJson()
		{
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

		function query()
		{
			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');

			$params = array
				(
				'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query' => $search['value'],
				'order' => $columns[$order[0]['column']]['data'],
				'sort' => $order[0]['dir'],
				'filter' => $this->filter,
				'allrows' => phpgw::get_var('length', 'int') == -1,
				'status_id' => phpgw::get_var('status_id')
			);

			$bobimmodel = new bobimmodel_impl();
			$sobimmodel = new sobimmodel_impl($this->db);
			$bobimmodel->setSobimmodel($sobimmodel);
			$output = $bobimmodel->createBimModelList();

			$results['results'] = $output;
			$results['total_records'] = count($output);
			$results['start'] = $params['start'];
			$results['sort'] = 'databaseId';
			$results['dir'] = $params['sort'] ? $params['sort'] : 'ASC';
			$results['draw'] = $draw;

//_debug_array($results);
			return $this->jquery_results($results);
		}
	/*
	 *
	 */

		public function removeModelJson($modelId = null)
		{
			if(!$this->acl_delete)
			{
				return lang('sorry - insufficient rights');
			}
		$output = array();
		$output["result"] = 1;
			if($modelId == null)
			{
				$modelId = (int)phpgw::get_var("modelId");
		}
			
		$bobimmodel = new bobimmodel_impl();
		$sovfs = new sovfs_impl();
		$sovfs->setSubModule(self::$virtualFileSystemPath);
		$bobimmodel->setVfsObject($sovfs);
		$sobimmodel = new sobimmodel_impl($this->db);
		$sobimmodel->setModelId($modelId);
		$bobimmodel->setSobimmodel($sobimmodel);
			try
			{
			$bobimmodel->removeIfcModelByModelId();
				return $output;
			}
			catch(InvalidArgumentException $e)
			{
			$output["result"] = 0;
			$output["error"] = "Invalid arguments";
			$output["exception"] = $e;
				return $output;
			}
			catch(ModelDoesNotExistException $e)
			{
			$output["result"] = 0;
			$output["error"] = "Model does not exist!";
			$output["exception"] = $e;
				return $output;
			}
			catch(Exception $e)
			{
			$output["result"] = 0;
			$output["error"] = "General error";
			$output["exception"] = $e;
				return $output;
		}
	}

		public function getFacilityManagementXmlByModelId($modelId = null)
		{
		$GLOBALS['phpgw_info']['flags']['noheader'] = true;
		$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
		$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;
		header("Content-type: application/xml");
		$restUrl = $this->bimconverterUrl;
			if($modelId == null)
			{
				$modelId = (int)phpgw::get_var("modelId");
		}
		//echo "ModelId is:".$modelId;
		$bobimmodel = new bobimmodel_impl();
		$sovfs = new sovfs_impl();
		$sovfs->setSubModule(self::$virtualFileSystemPath);
		$bobimmodel->setVfsObject($sovfs);
		$sobimmodel = new sobimmodel_impl($this->db);
		$sobimmodel->setModelId($modelId);
		$bobimmodel->setSobimmodel($sobimmodel);
			$sobimmodelinformation = new sobimmodelinformation_impl($this->db, $modelId);
		
		
			try
			{
				if($bobimmodel->checkBimModelIsUsed())
				{
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
			}
			catch(NoResponseException $e)
			{
			$result = array();
			$result["result"] = 0;
			$result["error"] = "Could not connect to BIM converter rest service!";
			$result["Exception"] = $e;
				echo phpgwapi_xmlhelper::toXML($result, 'PHPGW');
				//echo json_encode($result);
			}
			catch(Exception $e)
			{
			$result = array();
			$result["result"] = 0;
				$result["error"] = "General error!\nMessage: " . $e->getMessage();
			echo json_encode($result);
		}
	}

		private function getFacilityManagementXmlFromIfc($fileWithPath)
		{
		$sobim_converter = new sobim_converter_impl();
		$sobim_converter->setBaseUrl($this->bimconverterUrl);
		$sobim_converter->setFileToSend($fileWithPath);
		
			try
			{
			$returnedXml =  $sobim_converter->getFacilityManagementXml();
			$sxe = simplexml_load_string($returnedXml);
			return $sxe;
			}
			catch(NoResponseException $e)
			{
			throw $e;
			}
			catch(InvalidArgumentException $e)
			{
			throw $e;
			}
			catch(Exception $e)
			{
			echo $e;
		}
	}

		function showModels()
		{
			if(phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			$data = array(
				'datatable_name' => lang('Model data'),
				'js_lang' => js_lang('edit', 'add'),
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array(
								'type' => 'link',
								'value' => lang('new'),
								'href' => self::link(array('menuaction' => 'bim.uibim.upload')),
								'class' => 'new_item'
							),
						)
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'bim.uibim.showModels', 'phpgw_return_as' => 'json')),
					'ungroup_buttons' => true,
					'allrows' => true,
					'field' => array(
						array(
							'key' => 'databaseId',
							'label' => lang('Database id'),
							'sortable' => true,
						),
						array(
							'key' => 'guid',
							'label' => lang('guid'),
							'sortable' => true,
						),
						array(
							'key' => 'name',
							'label' => lang('Name'),
							'sortable' => true
						),
						array(
							'key' => 'creationDate',
							'label' => lang('creationDate'),
							'sortable' => true
						),
						array(
							'key' => 'fileSize',
							'label' => lang('fileSize'),
							'sortable' => true,
							'formatter' => 'JqueryPortico.FormatterCenter'
						),
						array(
							'key' => 'fileName',
							'label' => lang('fileName'),
							'sortable' => false,
						),
						array(
							'key' => 'usedItemCount',
							'label' => lang('usedItemCount'),
							'sortable' => false,
						),
						array(
							'key' => 'vfsFileId',
							'label' => lang('vfsFileId'),
							'sortable' => false,
						),
						array(
							'key' => 'used',
							'label' => lang('used'),
							'sortable' => false,
						),
					),
				),
		);
 
			$parameters = array
				(
				'parameter' => array
					(
					array
						(
						'name' => 'modelId',
						'source' => 'databaseId'
					),
				)
			);
 
			$parameters2 = array
				(
				'parameter' => array
					(
					array
						(
						'name' => 'modelGuid',
						'source' => 'guid'
					),
				)
			);
 
 
			$data['datatable']['actions'][] = array
				(
				'my_name' => 'view',
				'text' => lang('view'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'bim.uibimitem.showItems'
				)),
				'parameters' => json_encode($parameters)
			);
			$data['datatable']['actions'][] = array
				(
				'my_name' => 'load',
				'text' => lang('load'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'bim.uibim.getFacilityManagementXmlByModelId'
				)),
				'parameters' => json_encode($parameters)
			);
			$data['datatable']['actions'][] = array
				(
				'my_name' => 'delete',
				'text' => lang('Remove'),
				'confirm_msg' => lang('do you really want to delete this entry'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'bim.uibim.removeModelJson',
				)),
				'parameters' => json_encode($parameters)
			);
			$data['datatable']['actions'][] = array
				(
				'my_name' => 'info',
				'text' => lang('info'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'bim.uibim.displayModelInformation'
				)),
				'parameters' => json_encode($parameters)
			);

			self::render_template_xsl(array('datatable_jquery'), $data);
	}

		private $form_upload_field_filename = "ifc_file_name";
		private $form_upload_field_modelname = "ifc_model_name";

		public function upload()
		{
			if(!$this->acl_add)
			{
				phpgw::no_access();
			}
			
			$import_action = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bim.uibim.uploadFile',
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
		$this->setupBimCss();
			self::render_template_xsl('bim_upload_ifc', array('upload' => $data));
	}

		public function uploadFile($uploadedFileArray = null, $modelName = null, $unitTest = false)
		{
			if(!$unitTest)
			{
			$GLOBALS['phpgw']->xslttpl->add_file(array('bim_upload_ifc_result'));
		}
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;

			if(!$this->acl_add)
			{
				phpgw::no_access();
			}

			if(!$uploadedFileArray)
			{
			$uploadedFileArray = $_FILES[$this->form_upload_field_filename];
		}
			if(!$modelName)
			{
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
			try
			{
			$bobimmodel->addUploadedIfcModel();
			}
			catch(FileExistsException $e)
			{
			$error = true;
			$errorMessage =  "Filename in use! \n Try renaming the file";
				if($unitTest)
				{
				throw $e;
			}
			}
			catch(Exception $e)
			{
			$error = true;
			$errorMessage =  $e->getMessage();
				if($unitTest)
				{
				throw $e;
			}
		}


			$link_to_models = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bim.uibim.showModels'));
			$link_to_upload = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bim.uibim.upload'));
		$data = array
		(
				'modelName'						=> $modelName,
				'error'							=> $error,
				'errorMessage'					=> $errorMessage,
				'linkToModels'					=> $link_to_models,
				'linkToUpload'					=> $link_to_upload
		);

			if(!$unitTest)
			{
				$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('uploadResult' => $data));
		}

		return $data;
	}

		public function displayModelInformation()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
		$GLOBALS['phpgw']->xslttpl->add_file(array('bim_modelinformation'));
		$modelId = phpgw::get_var("modelId");
		//$modelId = 3;
			if(empty($modelId))
			{
			echo "No modelId!";
			}
			else
			{
			$sobimInfo = new sobimmodelinformation_impl($this->db, $modelId);
			/* @var $modelInfo BimModelInformation */
			$modelInfo = $sobimInfo->getModelInformation();
			$sobimmodel = new sobimmodel_impl($this->db);
			$sobimmodel->setModelId($modelId);
			/* @var $model BimModel */
			$model = $sobimmodel->retrieveBimModelInformationById();
				$data = array(
				'model'		=> $model->transformObjectToArray(),
				'information' => $modelInfo->transformObjectToArray()
			);
				$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('modelInformation' => $data));
		}
		
//			$this->setupBimCss();
		}
	}
