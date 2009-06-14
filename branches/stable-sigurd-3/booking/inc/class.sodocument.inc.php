<?php
	phpgw::import_class('booking.socommon');
	
	abstract class booking_sodocument extends booking_socommon
	{
		const CATEGORY_HMS_DOCUMENT = 'HMS_document';
		const CATEGORY_PRICE_LIST = 'price_list';
		const CATEGORY_PICTURE = 'picture';
		const CATEGORY_DRAWING = 'drawing';
		const CATEGORY_OTHER = 'other';
		
		protected 
			$defaultCategories = array(
			   self::CATEGORY_HMS_DOCUMENT,
			   self::CATEGORY_PRICE_LIST,
			   self::CATEGORY_PICTURE,
			   self::CATEGORY_DRAWING,
			   self::CATEGORY_OTHER,
			),
			$uploadRootDir;
		
		protected $ownerType = null;
		
		function __construct()
		{
			$this->ownerType = substr(get_class($this), 19);
			
			parent::__construct(sprintf('bb_document_%s', $this->get_owner_type()), 
				array(
					'id'			=> array('type' => 'int'),
					'name'			=> array('type' => 'string', 'query' => true),
					'owner_id'		=> array('type' => 'int', 'required' => true),
					'category'		=> array('type' => 'string', 'required' => true),
					'description'	=> array('type' => 'string', 'required' => false),
					'owner_name'	=> array(
						'type' => 'string',
						 'query' => true,
						 'join' => array(
							'table' => sprintf('bb_%s', $this->get_owner_type()),
							'fkey' => 'owner_id',
							'key' => 'id',
							'column' => 'name'
						)
					)
				)
			);
			$this->account	= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->uploadRootDir = $GLOBALS['phpgw_info']['server']['files_dir'].DIRECTORY_SEPARATOR.'booking';
		}
		
		public function get_categories()
		{
			return $this->defaultCategories;
		}
		
		public function get_owner_type()
		{
			return $this->ownerType;
		}
		
		public function get_files_root()
		{
			return $this->uploadRootDir;
		}
		
		public function get_files_path()
		{
			return self::get_files_root().DIRECTORY_SEPARATOR.$this->get_owner_type();
		}
		
		public function generate_filename($document_id, $document_name)
		{
			return $this->get_files_path().DIRECTORY_SEPARATOR.'/'.$document_id.'_'.$document_name;
		}
		
		function read_single($id)
		{
			$document = parent::read_single($id);
			if (is_array($document)) { 
				$document['filename'] = $this->generate_filename($document['id'], $document['name']); 
			}
			return $document;
		}
		
		public function read_parent($owner_id)
		{
			$parent_so = CreateObject(sprintf('booking.so%s', $this->get_owner_type()));
			return $parent_so->read_single($owner_id);
		}
		
		protected function doValidate($document, booking_errorstack $errors)
		{	
			$this->newFile = null;
			
			if (!$document['id'])
			{
				$fileValidator = createObject('booking.sfValidatorFile');
				$files = $document['files'];
				unset($document['files']);
				try {
					if ($this->newFile = $fileValidator->clean($files['name'])) {
						$document['name'] = $this->newFile->getOriginalName();
					}
				} catch (sfValidatorError $e) {
					if ($e->getCode() == 'required') {
						$errors['name'] = lang('Missing file for document');
						return;
					}
					throw $e;
				}
			}
			
			if (!in_array($document['category'], $this->defaultCategories))
			{
				$errors['category'] = 'Invalid category';
			}
		}
		
		function add($document)
		{
			if (!$this->newFile) { throw new LogicException('Missing file'); }
			
			$this->db->transaction_begin();
			
			$document['name'] = $this->newFile->getOriginalName();
			$receipt = parent::add($document);
			
			if ($this->db->transaction_commit()) { 
				$filePath = $this->generate_filename($receipt['id'], $document['name']);
				$this->newFile->save($filePath);
				return $receipt;
			}
			
			throw new UnexpectedValueException('Transaction failed.');
		}
		
		function delete($id)
		{
			if (!is_array($document = $this->read_single($id))) {
				return false;
			}
			
			$this->db->transaction_begin();
			
			parent::delete($id);
			
			if ($this->db->transaction_commit()) { 
				unlink($document['filename']);
				return true;
			}
			
			return false;
		}
		
		function has_results(&$result)
		{
			return is_array($result) && isset($result['total_records']) && $result['total_records'] > 0 && isset($result['results']);
		}
		
		function read($params)
		{
			$result = parent::read($params);
			
			if ($this->has_results($result)) {
				foreach($result['results'] as &$record) {
					$record['is_image'] = $this->is_image($record);
				}
			}
			
			return $result;
		}
		
		public function is_image(array &$entity)
		{
			if ($entity['category'] != self::CATEGORY_PICTURE) { return false; }
			
			switch ($this->get_file_extension($entity)) {
				case 'png':
				case 'gif':
				case 'jpg':
				case 'jpeg':
					return true;
			}
			
			return false;
		}
		
		public function read_images($params = array())
		{
			if (!isset($params['filters'])) { $params['filters'] = array(); }
			$params['filters']['category'] = booking_sodocument::CATEGORY_PICTURE;
			
			$documents = $this->read($params);
			$images = array('results' => array(), 'total_records' => 0);
			foreach($documents['results'] as &$document) {
				if ($document['is_image']) {
					$images['results'][] = $document;
					$images['total_records']++;
				} 
			}
			
			return $images;
		}
		
		public function get_file_extension(array &$entity)
		{
			return (false === $pos = strrpos($entity['name'], '.')) ? false : substr($entity['name'], $pos+1);
		}
	}
