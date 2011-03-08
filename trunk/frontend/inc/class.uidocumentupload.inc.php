<?php
phpgw::import_class('frontend.uifrontend');

class frontend_uidocumentupload extends frontend_uifrontend
{
	public static $ROOT_FOR_DOCUMENTS = '/frontend';
	public static $HELP_DOCUMENTS = '/help';
	public static $LANG_DIR = '/NO';
	
	protected static $so;
	protected $document_types; // Used for caching the values
	
	public $public_functions = array(
            'index'     => true,
			'read_helpfile_from_vfs'	=> true
	);

	public function __construct()
	{
		//parent::__construct();
		// This module uses XSLT templates
		$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
		$GLOBALS['phpgw_info']['flags']['menu_selection'] = "admin::frontend::documents";
	}
	
	public function index()
	{
		if(isset($_POST['file_upload'])){
			//$filename = phpgw::get_var('help_filename');
			$test2 = "testing litt";			
			if ($_FILES["file"]["error"] > 0)
			{
				//error
			}
			else
			{
				$filename =  $_FILES["help_filename"]["name"];
				$stored_in = $_FILES["help_filename"]["tmp_name"];
				$success = $this->store_doc_to_vfs($_FILES["help_filename"]);
			}
		}
		
		$form_action = $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'frontend.uidocumentupload.index'));
		$data = array (
			'msgbox_data'   => $GLOBALS['phpgw']->common->msgbox($GLOBALS['phpgw']->common->msgbox_data($msgbox)),
			'documentupload_data' => 	array(
					'test' => $test2,
					'filename' => $filename,
					'storage' => $stored_in,
					'success' => $success,
					'file' => $_FILES["help_filename"],
					'form_action' => $form_action)
		);
		$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('app_data' => $data));
		$GLOBALS['phpgw']->xslttpl->add_file(array('frontend','documentupload'));
	}
	
	public function store_doc_to_vfs($file)
	{
		$root_directory = self::$ROOT_FOR_DOCUMENTS;
		$type_directory = self::$HELP_DOCUMENTS;
		$lang_directory = self::$LANG_DIR;
		
		$vfs = CreateObject('phpgwapi.vfs');
		$vfs->override_acl = 1;
		
		$path = $root_directory;
		$dir = array('string' => $path, RELATIVE_NONE);
		if(!$vfs->file_exists($dir)){
			if(!$vfs->mkdir($dir))
			{
				return;
			}
		}
		
		$path .= $type_directory;
		$dir = array('string' => $path, RELATIVE_NONE);
		if(!$vfs->file_exists($dir)){
			if(!$vfs->mkdir($dir))
			{
				return;
			}
		}
		
		$path .= $lang_directory;
		$dir = array('string' => $path, RELATIVE_NONE);
		if(!$vfs->file_exists($dir)){
			if(!$vfs->mkdir($dir))
			{
				return;
			}
		}

		$mime_magic = createObject('phpgwapi.mime_magic');
		
		$mime       = $mime_magic->filename2mime($file['name']);

		if( $mime != 'application/pdf' )
		{
			$message ='Only PDF is supported for this one';
			phpgwapi_cache::message_set($message, 'error');
			return $message;		
		}

		$file_path = "{$path}/helpdesk.index.pdf";

		$result = $vfs->write
		(
			array
			(
				'string' => $file_path,
				RELATIVE_NONE,
				'content' => file_get_contents($file['tmp_name'])
			)
		);

		if($result)
		{
			$message ='Stored in vfs';
			phpgwapi_cache::message_set($message, 'message');
			return $message;
		}
		return "something failed...";
	}
	
	public function get_id_field_name($extended_info = false)
	{
		if(!$extended_info)
		{
			$ret = 'document_id';
		}
		else
		{
			$ret = array
			(
				'table'			=> 'rental_document', // alias
				'field'			=> 'id',
				'translated'	=> 'document_id'
			);
		}
		return $ret;
	}
	
	private function get_document_path(string $filename)
	{
		$root_directory = self::$ROOT_FOR_DOCUMENTS;
		$type_directory = self::$HELP_DOCUMENTS;
		$lang_directory = self::$LANG_DIR;
		
		$vfs = CreateObject('phpgwapi.vfs');
		$vfs->override_acl = 1;
		
		$path = "{$root_directory}";
		$dir = array('string' => $path, RELATIVE_NONE);
		if(!$vfs->file_exists($dir)){
			if(!$vfs->mkdir($dir))
			{
				return false;
			}
		}
		
		$path .= "{$type_directory}";
		$dir = array('string' => $path, RELATIVE_NONE);
		if(!$vfs->file_exists($dir)){
			if(!$vfs->mkdir($dir))
			{
				return false;
			}
		}
		
		$path .= "{$lang_directory}";
		$dir = array('string' => $path, RELATIVE_NONE);
		if(!$vfs->file_exists($dir)){
			if(!$vfs->mkdir($dir))
			{
				return false;
			}
		}
		
		$path .= "/{$filename}";
		$dir = array('string' => $path, RELATIVE_NONE);
		if(!$vfs->file_exists($dir)){
			if(!$vfs->mkdir($dir))
			{
				return false;
			}
		}

		return "{$root_directory}{$type_directory}{$lang_directory}/{$filename}";
	}
	
	public function write_document_to_vfs(string $filename)
	{
	
		$path = $this->get_document_path($filename);
		
		if(!$path)
		{
			return false;
		}
		
		$vfs = CreateObject('phpgwapi.vfs');
		$vfs->override_acl = 1;

		$file = array('string' => $path, RELATIVE_NONE);
		
		return $vfs->write
		(
			array
			(
				'string' => $path,
				RELATIVE_NONE,
				'content' => file_get_contents($filename)
			)
		);
	} 
	
	public function read_document_from_vfs(string $filename)
	{
		if(!isset($filename))
		{
			$filename = 'helpdesk.index.pdf';
		}
		$path = $this->get_document_path($filename);
		
		$vfs = CreateObject('phpgwapi.vfs');
		$vfs->override_acl = 1;
		
		$vfs->read
		(
			array
			(
				'string' => $path,
				RELATIVE_NONE
			)
		);
	}
	
	public function read_helpfile_from_vfs()
	{
		$GLOBALS['phpgw_info']['flags']['noheader'] = true;
		$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
		$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;
		
		$directory = "/frontend/help/NO";

		$vfs = CreateObject('phpgwapi.vfs');
		$vfs->override_acl = 1;

		$file = "{$directory}/helpdesk.index.pdf";
		$ls_array = $vfs->ls(array(
			'string'		=>  $file,
			'relatives' 	=> array(RELATIVE_NONE),
			'checksubdirs'	=> false,
			'nofiles'		=> true
		));


		$document = $vfs->read(array(
			'string'	=> $file,
			'relatives' => array(RELATIVE_NONE))
		);

		$vfs->override_acl = 0;

		$mime_type = 'text/plain';
		if ($ls_array[0]['mime_type'])
		{
			$mime_type = $ls_array[0]['mime_type'];
		}
		
		$browser = CreateObject('phpgwapi.browser');
		$browser->content_header($ls_array[0]['name'],$ls_array[0]['mime_type'],$ls_array[0]['size']);
		echo $document;
	}
	
	public function delete_document_from_vfs(string $filename)
	{
		$path = $this->get_document_path($filename);

		$vfs = CreateObject('phpgwapi.vfs');
		$vfs->override_acl = 1;
		
		return $vfs->rm
		(
			array
			(
				'string' => $path,
				RELATIVE_NONE
			)
		);
	}
}
