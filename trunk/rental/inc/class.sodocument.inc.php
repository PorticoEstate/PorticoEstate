<?php
	phpgw::import_class('rental.socommon');

	class rental_sodocument extends rental_socommon
	{

		public static $ROOT_FOR_DOCUMENTS = 'rental';
		public static $PARTY_DOCUMENTS = 'parties';
		public static $CONTRACT_DOCUMENTS = 'contracts';
		protected static $so;
		protected $document_types; // Used for caching the values

		/**
		 * Get a static reference to the storage object associated with this model object
		 *
		 * @return the storage object
		 */

		public static function get_instance()
		{
			if (self::$so == null)
			{
				self::$so = CreateObject('rental.sodocument');
			}
			return self::$so;
		}

		public function get_id_field_name( $extended_info = false )
		{
			if (!$extended_info)
			{
				$ret = 'document_id';
			}
			else
			{
				$ret = array
					(
					'table' => 'rental_document', // alias
					'field' => 'id',
					'translated' => 'document_id'
				);
			}
			return $ret;
		}

		protected function get_query( string $sort_field, bool $ascending, string $search_for, string $search_type, array $filters, bool $return_count )
		{

			$clauses = array('1=1');

			$filter_clauses = array();

			// Search for based on search type
			if ($search_for)
			{
				$search_for = $this->marshal($search_for, 'field');
				$like_pattern = "'%" . $search_for . "%'";
				$like_clauses = array();
				switch ($search_type)
				{
					case "title":
						$like_clauses[] = "rental_document.title $this->like $like_pattern";
						break;
					case "name":
						$like_clauses[] = "rental_document.name $this->like $like_pattern";
						break;
					case "all":
						$like_clauses[] = "rental_document.title $this->like $like_pattern";
						$like_clauses[] = "rental_document.name $this->like $like_pattern";
						break;
				}

				if (count($like_clauses))
				{
					$clauses[] = '(' . join(' OR ', $like_clauses) . ')';
				}
			}

			if (isset($filters[$this->get_id_field_name()]))
			{
				$filter_clauses[] = "rental_document.id = {$this->marshal($filters[$this->get_id_field_name()], 'int')}";
			}

			if (isset($filters['contract_id']))
			{
				$filter_clauses[] = "rental_document.contract_id = {$this->marshal($filters['contract_id'], 'int')}";
			}

			if (isset($filters['party_id']))
			{
				$filter_clauses[] = "rental_document.party_id = {$this->marshal($filters['party_id'], 'int')}";
			}

			if (isset($filters['document_type']) && $filters['document_type'] != 'all')
			{
				$filter_clauses[] = "rental_document.type_id = {$this->marshal($filters['document_type'], 'int')}";
			}

			if (count($filter_clauses))
			{
				$clauses[] = join(' AND ', $filter_clauses);
			}


			$condition = join(' AND ', $clauses);

			$tables = "rental_document";
			$joins = " {$this->left_join} rental_document_types ON (rental_document.type_id = rental_document_types.id)";

			if ($return_count)
			{
				$cols = 'COUNT(DISTINCT(rental_document.id)) AS count';
			}
			else
			{
				$cols = 'rental_document.id as document_id, rental_document.title as document_title, description, name, contract_id, party_id, rental_document_types.title as type_title';
			}

			$dir = $ascending ? 'ASC' : 'DESC';
			if ($sort_field == 'title')
			{
				$sort_field = 'rental_document.title';
			}
			else if ($sort_field == 'type')
			{
				$sort_field = 'rental_document_types.title';
			}
			$order = $sort_field ? "ORDER BY {$this->marshal($sort_field, 'field')} $dir " : '';

			//var_dump("SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}");
			return "SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}";
		}

		function populate( int $document_id, &$document )
		{
			if ($document == null)
			{
				$document = new rental_document($document_id);
				$document->set_title($this->unmarshal($this->db->f('document_title', true), 'string'));
				$document->set_description($this->unmarshal($this->db->f('description', true), 'string'));
				$document->set_name($this->unmarshal($this->db->f('name', true), 'string'));
				$document->set_type($this->unmarshal($this->db->f('type_title', true), 'string'));
				$document->set_contract_id($this->unmarshal($this->db->f('contract_id', true), 'int'));
				$document->set_party_id($this->unmarshal($this->db->f('party_id', true), 'int'));
			}
			return $document;
		}

		public function add( &$document )
		{
			$cols = array(
				'title',
				'description',
				'name',
				'party_id',
				'contract_id',
				'type_id'
			);

			$party_id = $this->marshal($document->get_party_id(), 'int');
			$contract_id = $this->marshal($document->get_contract_id(), 'int');
			$party_id = $party_id > 0 ? $party_id : 'NULL';
			$contract_id = $contract_id > 0 ? $contract_id : 'NULL';


			$values = array(
				$this->marshal($document->get_title(), 'string'),
				$this->marshal($document->get_description(), 'string'),
				$this->marshal($document->get_name(), 'string'),
				$party_id,
				$contract_id,
				$this->marshal($document->get_type_id(), 'int')
			);

			$query = "INSERT INTO rental_document (" . join(',', $cols) . ") VALUES (" . join(',', $values) . ")";
			$result = $this->db->query($query);

			$document_id = $this->db->get_last_insert_id('rental_document', 'id');
			$document->set_id($document_id);
			return $document;
		}

		public function update( $document )
		{
			$id = intval($document->get_id());

			$name_value_pairs = array(
				"title = {$this->marshal($document->get_title(), 'string')}",
				"description = {$this->marshal($document->get_description(), 'string')}",
				"name = {$this->marshal($document->get_name(), 'string')}",
				"party_id = {$this->marshal($document->get_party_id(), 'int')}",
				"contract_id = {$this->marshal($document->get_contract_id(), 'int')}",
				"type_id = {$this->marshal($document->get_type_id(), 'int')}"
			);

			$query = "UPDATE rental_document SET " . join(',', $name_value_pairs) . " WHERE id = {$id}";
			$result = $this->db->query($query);
			return $result != null;
		}

		public function get_document_types()
		{
			if ($this->document_types == null)
			{
				$sql = "SELECT id, title FROM rental_document_types";
				$this->db->query($sql, __LINE__, __FILE__);
				$results = array();
				while ($this->db->next_record())
				{
					$location_id = $this->db->f('id', true);
					$results[$location_id] = $this->db->f('title', true);
				}
				$this->document_types = $results;
			}
			return $this->document_types;
		}

		private function get_document_path( string $document_type, $id )
		{
			$root_directory = self::$ROOT_FOR_DOCUMENTS;
			$type_directory;
			if ($document_type == self::$PARTY_DOCUMENTS)
			{
				$type_directory = self::$PARTY_DOCUMENTS;
			}
			else if ($document_type == self::$CONTRACT_DOCUMENTS)
			{
				$type_directory = self::$CONTRACT_DOCUMENTS;
			}
			else
			{
				return false;
			}

			$vfs = CreateObject('phpgwapi.vfs');
			$vfs->override_acl = 1;

			$path = "/{$root_directory}";
			$dir = array('string' => $path, 'relatives' => array( RELATIVE_NONE));
			if (!$vfs->file_exists($dir))
			{
				if (!$vfs->mkdir($dir))
				{
					return false;
				}
			}

			$path .= "/{$type_directory}";
			$dir = array('string' => $path, 'relatives' => array( RELATIVE_NONE));
			if (!$vfs->file_exists($dir))
			{
				if (!$vfs->mkdir($dir))
				{
					return false;
				}
			}

			$path .= "/{$id}";
			$dir = array('string' => $path, 'relatives' => array( RELATIVE_NONE));
			if (!$vfs->file_exists($dir))
			{
				if (!$vfs->mkdir($dir))
				{
					return false;
				}
			}

			return "/{$root_directory}/{$type_directory}/{$id}";
		}

		public function write_document_to_vfs( string $document_type, $temporary_name, $id, $name )
		{

			$path = $this->get_document_path($document_type, $id);

			if (!$path)
			{
				return false;
			}

			$vfs = CreateObject('phpgwapi.vfs');
			$vfs->override_acl = 1;
			$path .= "/{$name}";
			$file = array('string' => $path, 'relatives' => array( RELATIVE_NONE));

			return $vfs->write
					(
					array
						(
						'string' => $path,
						'relatives' => array( RELATIVE_NONE),
						'content' => file_get_contents($temporary_name)
					)
			);
		}

		public function read_document_from_vfs( string $document_type, $id, $name )
		{
			$path = $this->get_document_path($document_type, $id);

			$path .= "/{$name}";

			$vfs = CreateObject('phpgwapi.vfs');
			$vfs->override_acl = 1;

			return $vfs->read
					(
					array
						(
						'string' => $path,
						'relatives' => array( RELATIVE_NONE)
					)
			);
		}

		public function delete_document_from_vfs( string $document_type, $id, $name )
		{
			$path = $this->get_document_path($document_type, $id);

			$path .= "/{$name}";

			$vfs = CreateObject('phpgwapi.vfs');
			$vfs->override_acl = 1;

			return $vfs->rm
					(
					array
						(
						'string' => $path,
						'relatives' => array( RELATIVE_NONE)
					)
			);
		}

		public function delete_document( $id )
		{
			$sql = "DELETE FROM rental_document WHERE id = {$id}";

			$result = $this->db->query($sql, __LINE__, __FILE__);
			if ($result)
			{
				return true;
			}
			return false;
		}
	}