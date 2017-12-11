<?php
	phpgw::import_class('booking.uicommon');

//        phpgw::import_class('booking.uidocument_building');
//	phpgw::import_class('booking.uipermission_building');
//	phpgw::import_class('phpgwapi.uicommon_jquery');

	abstract class booking_uidocument extends booking_uicommon
	{

		protected
			$documentOwnerType = null,
			$module;
		public
			$public_functions = array(
			'index' => true,
			'query' => true,
			'show' => true,
			'add' => true,
			'edit' => true,
			'download' => true,
			'delete' => true,
		);

		public function __construct()
		{
			parent::__construct();

//			Analizar esta linea de permiso self::process_booking_unauthorized_exceptions();

			$this->set_business_object();

			//'name' is not in fields as it will always be generated from the uploaded filename
			$this->fields = array('category', 'description', 'owner_id', 'owner_name');

			$this->module = 'booking';
		}

		protected function set_business_object( booking_bodocument $bo = null )
		{
			$this->bo = is_null($bo) ? $this->create_business_object() : $bo;
		}

		protected function create_business_object()
		{
			return CreateObject(sprintf('booking.bodocument_%s', $this->get_document_owner_type()));
		}

		protected function get_document_owner_type()
		{
			if (!$this->documentOwnerType)
			{
				$this->set_document_owner_type();
			}
			return $this->documentOwnerType;
		}

		protected function set_document_owner_type( $type = null )
		{
			if (is_null($type))
			{
				$class = get_class($this);
				$r = new ReflectionObject($this);
				while (__CLASS__ != ($current_class = $r->getParentClass()->getName()))
				{
					$class = $current_class;
					$r = $r->getParentClass();
				}
				$type = substr($class, 19);
			}

			$this->documentOwnerType = $type;
		}

		public function get_parent_url_link_params()
		{
			$inlineParams = $this->get_inline_params();
			return array('menuaction' => sprintf($this->module . '.ui%s.show', $this->get_document_owner_type()),
				'id' => $inlineParams['filter_owner_id']);
		}

		public function redirect_to_parent_if_inline()
		{
			if ($this->is_inline())
			{
				$this->redirect($this->get_parent_url_link_params());
			}

			return false;
		}

		public function get_owner_typed_link_params( $action, $params = array() )
		{
			$action = sprintf($this->module . '.uidocument_%s.%s', $this->get_document_owner_type(), $action);
			return array_merge(array('menuaction' => $action), $this->apply_inline_params($params));
		}

		public function get_owner_typed_link( $action, $params = array() )
		{
			return $this->link($this->get_owner_typed_link_params($action, $params));
		}

		public function apply_inline_params( &$params )
		{
			if ($this->is_inline())
			{
				$params['filter_owner_id'] = intval(phpgw::get_var('filter_owner_id'));
			}
			return $params;
		}

		protected function get_parent_if_inline()
		{
			return $this->is_inline() ? $this->bo->read_parent($this->get_parent_id()) : null;
		}

		public function get_parent_id()
		{
			$inlineParams = $this->get_inline_params();
			return $inlineParams['filter_owner_id'];
		}

		public function get_inline_params()
		{
			return array('filter_owner_id' => phpgw::get_var('filter_owner_id', 'int'));
		}

		public function is_inline()
		{
			return false != phpgw::get_var('filter_owner_id', 'int', 'REQUEST', false);
		}

		public static function generate_inline_link( $documentOwnerType, $documentOwnerId, $action )
		{
			return self::link(array('menuaction' => sprintf('booking.uidocument_%s.%s', $documentOwnerType, $action),
					'filter_owner_id' => $documentOwnerId));
		}

		public function index()
		{
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			$this->redirect_to_parent_if_inline();

			$data = array(
				'form' => array(
					'toolbar' => array(
					),
				),
				'datatable' => array(
					'source' => $this->get_owner_typed_link('index', array('phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'name',
							'label' => lang('Document Name'),
							'formatter' => 'JqueryPortico.formatLink',
						),
						array(
							'key' => 'owner_name',
							'label' => lang($this->get_document_owner_type()),
						),
						array(
							'key' => 'description',
							'label' => lang('Description'),
						),
						array(
							'key' => 'category',
							'label' => lang('Category'),
						),
						array(
							'key' => 'option_edit',
							'label' => lang('Edit'),
							'formatter' => 'JqueryPortico.formatLinkGeneric',
							'sortable' => false
						),
						array(
							'key' => 'option_delete',
							'label' => lang('Delete'),
							'formatter' => 'JqueryPortico.formatLinkGeneric',
							'sortable' => false
						),
						array(
							'key' => 'link',
							'hidden' => true
						)
					)
				)
			);

			$data['datatable']['actions'][] = array();
			if ($this->bo->allow_create())
			{
				$data['datatable']['new_item'] = $this->get_owner_typed_link('add');
			}

			self::render_template_xsl('datatable_jquery', $data);
		}

		public function query()
		{
			$documents = $this->bo->read();
			foreach ($documents['results'] as &$document)
			{
				$document['link'] = $this->get_owner_typed_link('download', array('id' => $document['id']));
				$document['category'] = lang(self::humanize($document['category']));
				#$document['active'] = $document['active'] ? lang('Active') : lang('Inactive');
//				$document_actions = array();
//				if ($this->bo->allow_write($document))  $document_actions[] = $this->get_owner_typed_link('edit', array('id' => $document['id']));
//				if ($this->bo->allow_delete($document)) $document_actions[] = $this->get_owner_typed_link('delete', array('id' => $document['id']));
//				
//				$document['actions'] = $document_actions;

				if ($this->bo->allow_write($document))
				{
					$document['option_edit'] = $this->get_owner_typed_link('edit', array('id' => $document['id']));
				}
				if ($this->bo->allow_delete($document))
				{
					$document['option_delete'] = $this->get_owner_typed_link('delete', array('id' => $document['id']));
				}
			}
			if (phpgw::get_var('no_images'))
			{
				$documents['results'] = array_filter($documents['results'], array($this, 'is_image'));
				// the array_filter function preserves the array keys. The javascript that later iterates over the resultset don't like gaps in the array keys
				// reindexing the results array solves the problem
				$doc_backup = $documents;
				unset($documents['results']);
				foreach ($doc_backup['results'] as $doc)
				{
					$documents['results'][] = $doc;
				}
				$documents['total_records'] = count($documents['results']);
			}
			return $this->jquery_results($documents);
		}

		private function is_image( $document )
		{
			return $document['is_image'] == false;
		}

		public function index_images()
		{
			$images = $this->bo->read_images();

			foreach ($images['results'] as &$image)
			{
				$image['src'] = $this->get_owner_typed_link('download', array('id' => $image['id']));
			}

			return $this->yui_results($images);
		}

		protected function get_document_categories()
		{
			$types = array();
			foreach ($this->bo->get_categories() as $type)
			{
				$types[$type] = self::humanize($type);
			}
			return $types;
		}

		protected function add_default_display_data( &$document_data )
		{
			$document_data['owner_pathway'] = $this->get_owner_pathway($document_data);
			$document_data['owner_type'] = $this->get_document_owner_type();
			$document_data['owner_type_label'] = ucfirst($document_data['owner_type']);
			$document_data['inline'] = $this->is_inline();
			$document_data['document_types'] = $this->get_document_categories();
			$document_data['documents_link'] = $this->get_owner_typed_link('index');
			$document_data['cancel_link'] = $this->get_owner_typed_link('index');
		}

		public function show()
		{
			$id = phpgw::get_var('id', 'int');
			$document = $this->bo->read_single($id);
			$this->add_default_display_data($document);
			self::render_template('document', array('document' => $document));
		}

		public function add()
		{
			$errors = array();
			$document = array();

			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$document = extract_values($_POST, $this->fields);
				$document['files'] = $this->get_files();
				$errors = $this->bo->validate($document);
				if (!$errors)
				{
					try
					{

						$receipt = $this->bo->add($document);
						$this->redirect_to_parent_if_inline();
						$this->redirect($this->get_owner_typed_link_params('index'));
					}
					catch (booking_unauthorized_exception $e)
					{
						$errors['global'] = lang('Could not add object due to insufficient permissions');
					}
				}
			}

			self::add_javascript('booking', 'base', 'document.js');
			phpgwapi_jquery::load_widget('autocomplete');

			$this->add_default_display_data($document);

			if (is_array($parentData = $this->get_parent_if_inline()))
			{
				$document['owner_id'] = $parentData['id'];
				$document['owner_name'] = $parentData['name'];
			}

			$this->flash_form_errors($errors);

			$tabs = array();
			$tabs['generic'] = array('label' => lang('Document New'), 'link' => '#document');
			$active_tab = 'generic';

			$document['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			$document['validator'] = phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file'));

			self::render_template_xsl('document_form', array('document' => $document));
		}

		public function edit()
		{
			$id = phpgw::get_var('id', 'int');
			$document = $this->bo->read_single($id);

			$errors = array();
			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$document = array_merge($document, extract_values($_POST, $this->fields));
				$errors = $this->bo->validate($document);
				if (!$errors)
				{
					try
					{
						$receipt = $this->bo->update($document);
						$this->redirect_to_parent_if_inline();
						$this->redirect($this->get_owner_typed_link_params('index'));
					}
					catch (booking_unauthorized_exception $e)
					{
						$errors['global'] = lang('Could not update object due to insufficient permissions');
					}
				}
			}

			self::add_javascript('booking', 'base', 'document.js');
			phpgwapi_jquery::load_widget('autocomplete');

			$this->add_default_display_data($document);

			$this->flash_form_errors($errors);

			$tabs = array();
			$tabs['generic'] = array('label' => lang('Document Edit'), 'link' => '#document');
			$active_tab = 'generic';

			$document['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			$document['validator'] = phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file'));

			self::render_template_xsl('document_form', array('document' => $document));
		}

		public function download()
		{
			$id = phpgw::get_var('id', 'int');

			$document = $this->bo->read_single($id);

			self::send_file($document['filename'], array('filename' => $document['name']));
		}

		public function delete()
		{
			$id = phpgw::get_var('id', 'int');
			$this->bo->delete($id);

			$this->redirect_to_parent_if_inline();
			$this->redirect($this->get_owner_typed_link_params('index'));
		}

		/**
		 * Implement to return the full hierarchical pathway to this documents owner(s).
		 *
		 * @param int $document_id
		 *
		 * @return array of url(s) to owner(s) in order of hierarchy.
		 */
		protected function get_owner_pathway( array $forDocumentData )
		{
			return array();
		}
	}