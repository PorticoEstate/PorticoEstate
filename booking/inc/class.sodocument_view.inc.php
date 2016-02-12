<?php
	phpgw::import_class('booking.socommon');
	phpgw::import_class('booking.sodocument');

	class booking_sodocument_view extends booking_socommon
	{

		protected static
			$document_so_objects;

		function __construct()
		{
			//CREATE VIEW bb_document_view AS SELECT bb_document.id AS id,bb_document.name AS name,bb_document.owner_id AS owner_id,bb_document.category AS category,bb_document.description AS description,bb_document.type AS type FROM ((SELECT *, 'building' as type from bb_document_building) UNION ALL (SELECT *, 'resource' as type from bb_document_resource)) as bb_document;

			parent::__construct('bb_document_view', array(
				'id' => array('type' => 'string', 'expression' => '%%table%%.type || \'::\' || %%table%%.id'),
				'type' => array('type' => 'string'),
				'name' => array('type' => 'string', 'query' => true),
				'owner_id' => array('type' => 'int', 'required' => true),
				'category' => array('type' => 'string', 'required' => true),
				'description' => array('type' => 'string', 'required' => false),
				)
			);
		}

		public static function get_document_storage_objects()
		{
			if (!is_array(self::$document_so_objects))
			{
				self::$document_so_objects = array();
				foreach (booking_sodocument::get_document_owners() as $owner_type)
				{
					self::$document_so_objects[$owner_type] = CreateObject('booking.sodocument_' . $owner_type);
				}
			}

			return self::$document_so_objects;
		}

		public function get_categories()
		{
			return $this->document_so->get_categories();
		}

		public function read_single( $id )
		{
			list($type, $id_value) = explode('::', $id, 2);

			if (!$id_value || !$type)
			{
				throw new LogicException('Missing type or id');
			}

			$document_storage_objects = self::get_document_storage_objects();

			if (!isset($document_storage_objects[$type]))
			{
				throw new LogicException(sprintf('Could not determine document storage for document type "%s"', $type));
			}

			return $document_storage_objects[$type]->read_single($id_value);
		}
	}