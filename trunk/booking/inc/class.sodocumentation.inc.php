<?php
	phpgw::import_class('booking.socommon');

	class booking_sodocumentation extends booking_socommon
	{

		const CATEGORY_BACKEND = 'backend';
		const CATEGORY_FRONTEND = 'frontend';

		protected
			$defaultCategories = array(
				self::CATEGORY_BACKEND,
				self::CATEGORY_FRONTEND,
				),
			$uploadRootDir,
			$ownerType = null;

		function __construct()
		{
			parent::__construct('bb_documentation', array(
				'id' => array('type' => 'int'),
				'name' => array('type' => 'string', 'query' => true),
				'category' => array('type' => 'string', 'required' => true),
				'description' => array('type' => 'string', 'required' => false),
				)
			);
			$this->account = $GLOBALS['phpgw_info']['user']['account_id'];

			$server_files_dir = $this->_chomp_dir_sep($GLOBALS['phpgw_info']['server']['files_dir']);

			if (!file_exists($server_files_dir) || !is_dir($server_files_dir))
			{
				throw new LogicException('The upload directory is not properly configured: ' . $server_files_dir);
			}

			if (!is_writable($server_files_dir))
			{
				throw new LogicException('The upload directory is not writable');
			}

			$this->uploadRootDir = $server_files_dir . DIRECTORY_SEPARATOR . 'booking';
		}
#		public static function get_document_owners() {
#			return self::$document_owners;
#		}
#		

		public function get_categories()
		{
			return $this->defaultCategories;
		}
#		
#		public function get_owner_type()
#		{
#			return $this->ownerType;
#		}
#		

		public function get_files_root()
		{
			return $this->uploadRootDir;
		}

		public function get_files_path()
		{
			return self::get_files_root() . DIRECTORY_SEPARATOR . 'manual';
		}

		private function _chomp_dir_sep( $string )
		{
			$sep = DIRECTORY_SEPARATOR == '/' ? '\\/' : preg_quote(DIRECTORY_SEPARATOR);
			return preg_replace('/(' . $sep . ')+$/', '', trim($string));
		}

		public function generate_filename( $document_id, $document_name )
		{
			return $this->get_files_path() . DIRECTORY_SEPARATOR . $document_id . '_' . $document_name;
		}

		function read_single( $id )
		{
			$document = parent::read_single($id);
			if (is_array($document))
			{
				$document['filename'] = $this->generate_filename($document['id'], $document['name']);
			}
			return $document;
		}

		public function read_parent( $owner_id )
		{
			$parent_so = CreateObject(sprintf('booking.so%s', $this->get_owner_type()));
			return $parent_so->read_single($owner_id);
		}

		protected function doValidate( $document, booking_errorstack $errors )
		{
			$this->newFile = null;

			if (!$document['id'])
			{
				$fileValidator = createObject('booking.sfValidatorFile');
				$files = $document['files'];
				unset($document['files']);
				try
				{
					if ($this->newFile = $fileValidator->clean($files['name']))
					{
						$document['name'] = $this->newFile->getOriginalName();
					}
				}
				catch (sfValidatorError $e)
				{
					if ($e->getCode() == 'required')
					{
						$errors['name'] = lang('Missing file for document');
						return;
					}
					throw $e;
				}
			}

			if (!in_array($document['category'], $this->defaultCategories))
			{
				$errors['category'] = lang('Invalid category');
			}
		}

		function add( $document )
		{
			if (!$this->newFile)
			{
				throw new LogicException('Missing file');
			}

			$this->db->transaction_begin();

			$document['name'] = $this->newFile->getOriginalName();
			$receipt = parent::add($document);

			$filePath = $this->generate_filename($receipt['id'], $document['name']);
			$this->newFile->save($filePath);

			// make sure that uploaded images are "web friendly"
			// automatically resize pictures that are too big

			if ($this->db->transaction_commit())
			{
				return $receipt;
			}

			throw new UnexpectedValueException('Transaction failed.');
		}

		function delete( $id )
		{
			if (!is_array($document = $this->read_single($id)))
			{
				return false;
			}

			$this->db->transaction_begin();

			parent::delete($id);

			if ($this->db->transaction_commit())
			{
				if (file_exists($document['filename']))
				{
					unlink($document['filename']);
				}
				return true;
			}

			return false;
		}

		function has_results( &$result )
		{
			return is_array($result) && isset($result['total_records']) && $result['total_records'] > 0 && isset($result['results']);
		}

		function read( $params )
		{
			$result = parent::read($params);

			return $result;
		}

		public function get_file_extension( array &$entity )
		{
			return (false === $pos = strrpos($entity['name'], '.')) ? false : substr($entity['name'], $pos + 1);
		}

		public function getMyRole( $id )
		{

			$db = $this->db;
			$db->limit_query("SELECT role FROM bb_permission_root WHERE id=" . intval($GLOBALS['phpgw_info']['user']['account_id']), 0, __LINE__, __FILE__, 1);
			if ($db->next_record())
			{
				return $db->f('role', false);
			}
			return false;
		}

		public function getFrontendDoc()
		{
			$this->db->query("SELECT id,name FROM bb_documentation WHERE category='frontend' ORDER BY id DESC", __LINE__, __FILE__);
			if ($this->db->next_record())
			{
				$id = $this->db->f('id', false);
				return $GLOBALS['phpgw']->link('/bookingfrontend/index.php', array('menuaction' => 'bookingfrontend.uidocumentation.download', 'id'=> $id));
			}
			return null;
		}

		public function getBackendDoc()
		{
			$this->db->query("SELECT id,name FROM bb_documentation WHERE category='backend' ORDER BY id DESC", __LINE__, __FILE__);
			if ($this->db->next_record())
			{
				$id = $this->db->f('id', false);
				return $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uidocumentation.download', 'id'=> $id));
			}
			return null;
		}
	}