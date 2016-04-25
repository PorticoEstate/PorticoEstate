<?php

	class booking_filestorage
	{

		protected
			$upload_root_dir,
			$owner_id,
			$use_include_path = false;

		public function __construct( $owner )
		{
			$this->owner_id = get_class($owner);

			$server_files_dir = $this->_chomp_dir_sep($GLOBALS['phpgw_info']['server']['files_dir']);

			if (!file_exists($server_files_dir) || !is_dir($server_files_dir))
			{
				throw new LogicException('The filestorage directory is not properly configured: ' . $server_files_dir);
			}

			if (!is_writable($server_files_dir))
			{
				throw new LogicException('The server\'s files directory is not writable');
			}

			$this->upload_root_dir = $server_files_dir . DIRECTORY_SEPARATOR . 'booking';

			$this->create_storage_dir_if_not_exists();
			$this->check_storage_dir();
		}

		protected function check_storage_dir()
		{
			if (is_dir($this->get_files_path()) && is_writable($this->get_files_path()) && is_readable($this->get_files_path())
			)
			{
				return true;
			}

			throw new Exception('Invalid file storage directory');
		}

		protected function create_storage_dir_if_not_exists()
		{
			if (is_dir($this->get_files_path()))
			{
				return false;
			}
			$this->create_storage_dir();
		}

		protected function create_storage_dir()
		{
			$dirMode = 0777;
			if (!mkdir($this->get_files_path(), $dirMode, true))
			{
				// failed to create the directory
				throw new Exception(sprintf('Failed to create file storage "%s".', $this->get_owner_id()));
			}

			// chmod the directory since it doesn't seem to work on recursive paths
			chmod($this->get_files_path(), $dirMode);
			return true;
		}

		public function get_owner_id()
		{
			return $this->owner_id;
		}

		public function get_files_root()
		{
			return $this->upload_root_dir;
		}

		public function get_files_path()
		{
			return self::get_files_root() . DIRECTORY_SEPARATOR . $this->get_owner_id();
		}

		public function get_system_identifier( booking_storage_object $sto )
		{
			return $this->build_file_path($sto);
		}

		public function build_file_path( booking_storage_object $sto )
		{
			return self::join_paths($this->get_files_path(), $sto->get_identifier());
		}

		public function attach( booking_storage_object $sto )
		{
			$sto->set_storage($this);
			return $sto;
		}

		public function delete( booking_storage_object $sto )
		{
			if (!$sto->exists())
			{
				return true;
			}
			if (unlink($this->build_file_path($sto)) === false)
			{
				throw new Exception(
				sprintf('Unable to delete "%s" from storage "%s"', $sto->get_identifier(), $this->get_owner_id())
				);
			}
		}

		public function read_all( booking_storage_object $sto )
		{
			return file_get_contents($this->build_file_path($sto), $this->use_include_path);
		}

		public function persist( booking_storage_object $sto )
		{
			$written = false;
			if (($fh = fopen($this->build_file_path($sto), 'w+', $this->use_include_path)))
			{
				$written = fwrite($fh, $sto->get_data());
				fclose($fh);
				if ($written === false)
				{
					throw new LogicException('Unable to persist ' . $this->build_file_path($sto));
				}
			}
			return $written;
		}

		public function exists( booking_storage_object $sto )
		{
			return is_file($this->build_file_path($sto));
		}

		/**
		 * @return booking_storage_object
		 */
		public function get( $identifier )
		{
			if (!$identifier)
			{
				throw new InvalidArgumentException('Invalid identifier');
			}

			$sto = $this->attach(new booking_storage_object($identifier));
			if (!$sto->exists())
			{
				throw new LogicException($identifier . ' does not exist');
			}
			return $sto;
		}

		public static function join_paths()
		{
			$path = '';
			foreach (func_get_args() as $arg)
			{
				$path = self::_chomp_dir_sep($path) . DIRECTORY_SEPARATOR . ($arg[0] == DIRECTORY_SEPARATOR ? substr($arg, 1) : $arg);
			}
			return $path;
		}

		public function chomp_dir_sep( $string )
		{
			return self::_chomp_dir_sep($string);
		}

		public static function _chomp_dir_sep( $string )
		{
			$sep = DIRECTORY_SEPARATOR == '/' ? '\\/' : preg_quote(DIRECTORY_SEPARATOR);
			return preg_replace('/(' . $sep . ')+$/', '', trim($string));
		}
	}

	class booking_storage_object
	{

		protected
			$storage,
			$data = null,
			$identifier,
			$is_data_retrieved = false,
			$dirty = false;

		function __construct( $identifier )
		{
			$this->identifier = $identifier;
		}

		public function get_system_identifier()
		{
			return $this->with_storage()->get_system_identifier($this);
		}

		public function get_identifier()
		{
			return $this->identifier;
		}

		public function set_storage( booking_filestorage $storage )
		{
			$this->storage = $storage;
		}

		public function is_attached()
		{
			return $this->storage instanceof booking_filestorage;
		}

		protected function with_storage()
		{
			if (!$this->is_attached())
				throw new booking_unattached_storage_object('Not attached to a storage');
			return $this->storage;
		}

		protected function get_storage_data()
		{
			return $this->with_storage()->read_all($this);
		}

		protected function persist_to_storage()
		{
			return $this->with_storage()->persist($this);
		}

		protected function delete_from_storage()
		{
			return $this->with_storage()->delete($this);
		}

		protected function exists_in_storage()
		{
			return $this->with_storage()->exists($this);
		}

		public function is_data_retrieved()
		{
			return $this->is_data_retrieved;
		}

		public function refresh()
		{
			if ($this->is_dirty())
			{
				$this->is_data_retrieved = false;
				return true;
			}
			else
			{
				return false;
			}
		}

		public function is_dirty()
		{
			return $this->dirty;
		}

		public function get_data()
		{
			if (!$this->is_data_retrieved() && !$this->is_dirty())
			{
				$this->exists() AND $this->data = $this->get_storage_data();
				$this->is_data_retrieved = true;
				$this->dirty = false;
			}
			return $this->data;
		}

		public function set_data( $data )
		{
			if (!is_string($data))
			{
				throw new InvalidArgumentException("Data must be a string value");
			}
			$this->data = $data;
			$this->dirty = true;
		}

		public function persist()
		{
			$retval = $this->persist_to_storage();
			$this->dirty = false;
			return $retval;
		}

		public function delete()
		{
			return $this->delete_from_storage();
		}

		public function exists()
		{
			return $this->exists_in_storage();
		}
	}

	class booking_unattached_storage_object extends Exception
	{
		
	}