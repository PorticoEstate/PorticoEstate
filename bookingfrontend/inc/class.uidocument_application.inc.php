<?php
	phpgw::import_class('booking.uidocument_application');

	class bookingfrontend_uidocument_application extends booking_uidocument_application
	{

		public $public_functions = array
			(
			'download' => true,
			'index' => true,
			'index_images' => true,
		);
		protected $module;

		public function __construct()
		{
			parent::__construct();
			$this->module = "bookingfrontend";
		}

		public function index()
		{
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			phpgw::no_access();
		}

		public function query()
		{
			$secret = phpgw::get_var('filter_secret', 'string');
			$documents = $this->bo->read();
			$validated_doc = array('results' => array());
			foreach ($documents['results'] as &$document)
			{
				if($secret != $document['secret'])
				{
					continue;
				}

				$document['link'] = $this->get_owner_typed_link('download', array('id' => $document['id'], 'secret' => $document['secret']));
				$document['category'] = lang(self::humanize($document['category']));

				$validated_doc['results'][] = $document;
			}

			$validated_doc['total_records'] = count($validated_doc['results']);

			if (phpgw::get_var('no_images'))
			{
				$validated_doc['results'] = array_filter($validated_doc['results'], array($this, 'is_image'));
				// the array_filter function preserves the array keys. The javascript that later iterates over the resultset don't like gaps in the array keys
				// reindexing the results array solves the problem
				$doc_backup = $validated_doc;
				unset($validated_doc['results']);
				foreach ($doc_backup['results'] as $doc)
				{
					$validated_doc['results'][] = $doc;
				}
				$validated_doc['total_records'] = count($validated_doc['results']);
			}
			return $this->jquery_results($validated_doc);
		}

		public function download()
		{
			$id = phpgw::get_var('id', 'int');

			$document = $this->bo->read_single($id);
			if (!empty($document) && $document['secret'] == phpgw::get_var('secret', 'string'))
			{
				self::send_file($document['filename'], array('filename' => $document['name']));
			}
			else
			{
				phpgw::no_access();
			}
		}
	}