<?php
	phpgw::import_class('booking.uicommon');

	class booking_uidocumentation extends booking_uicommon
	{
		protected
			$documentOwnerType = null,
			$module;
		
		public 
			$public_functions = array(
				'index'			=> true,
				'show'			=> true,
				'add'			=> true,
				'edit'			=> true,
				'download'		=> true,
				'delete'		=> true,
			);
		
		public function __construct()
		{
			parent::__construct();
			$this->bo = CreateObject('booking.bodocumentation');
			$this->fields = array('category', 'description');
			$this->module = 'booking';
		}
		
		public function index()
		{
			if(phpgw::get_var('phpgw_return_as') == 'json') {
				return $this->index_json();
			}
			
			self::add_javascript('booking', 'booking', 'datatable.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');
			
			// if($_SESSION['showall'])
			// {
			// 	$active_botton = lang('Show only active');
			// }else{
			// 	$active_botton = lang('Show all');
			// }
			
						
			$data = array(
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array(
								'type' => 'text', 
								'name' => 'query'
							),
							array(
								'type' => 'submit',
								'name' => 'search',
								'value' => lang('Search')
							),
							// array(
							// 	'type' => 'link',
							// 	'value' => $active_botton,
							// 	'href' => self::link(array('menuaction' => $this->get_owner_typed_link('active')))
							// ),
						)
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'booking.uidocumentation.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'name',
							'label' => lang('Document Name'),
							'formatter' => 'YAHOO.booking.formatLink',
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
							'key' => 'actions',
							'label' => lang('Actions'),
							'formatter' => 'YAHOO.booking.'.sprintf('formatGenericLink(\'%s\', \'%s\')', lang('edit'), lang('delete')),
						),
						array(
							'key' => 'link',
							'hidden' => true
						)
					)
				)
			);
			
			
				array_unshift($data['form']['toolbar']['item'], array(
					'type' => 'link',
					'value' => lang('New document'),
					'href' => self::link(array('menuaction' => $this->module.'.uidocumentation.add')),
			 	));
			
			self::render_template('datatable', $data);
		}

		public function index_json()
		{
			$documents = $this->bo->read();
			
			foreach($documents['results'] as &$document)
			{
				$document['link'] = self::link(array('menuaction' => $this->module.'.uidocumentation.download', 'id' => $document['id']));
				$document['category'] = lang(self::humanize($document['category']));
				#$document['active'] = $document['active'] ? lang('Active') : lang('Inactive');
				
				$document_actions = array();
				$document_actions[] = self::link(array('menuaction' => $this->module.'.uidocumentation.edit', 'id' => $document['id']));
				$document_actions[] = self::link(array('menuaction' => $this->module.'.uidocumentation.delete', 'id' => $document['id']));
				
				$document['actions'] = $document_actions;
			}
			return $this->yui_results($documents);
		}

		public function index_images()
		{
			$images = $this->bo->read_images();
			
			foreach($images['results'] as &$image) {
				$image['src'] = $this->get_owner_typed_link('download', array('id' => $image['id']));
			}
			
			return $this->yui_results($images);
		}
		
		protected function get_document_categories()
		{
			$types = array();
			foreach($this->bo->get_categories() as $type) { $types[$type] = self::humanize($type); }
			return $types;
		}
		
		protected function add_default_display_data(&$document_data)
		{
#			$document_data['owner_pathway'] 	= $this->get_owner_pathway($document_data);
#			$document_data['owner_type']  		= lang('manual');
#			$document_data['owner_type_label'] 	= ucfirst($document_data['owner_type']);
#			$document_data['inline'] 			= $this->is_inline();
			$document_data['document_types'] 	= $this->get_document_categories();
			$document_data['documents_link'] 	= self::link(array('menuaction' => $this->module.'.uidocumentation.index'));
			$document_data['cancel_link'] 		= self::link(array('menuaction' => $this->module.'.uidocumentation.index'));
		}
		
		public function show()
		{
			$id = intval(phpgw::get_var('id', 'GET'));
			$document = $this->bo->read_single($id);
			$this->add_default_display_data($document);
			self::render_template('documentation', array('document' => $document));
		}
		
		public function add()
		{	
			$errors = array();
			$document = array();
			
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$document = extract_values($_POST, $this->fields);	
				$document['files'] = $this->get_files();
				$errors = $this->bo->validate($document);
				if(!$errors)
				{
					$receipt = $this->bo->add($document);
					$this->redirect('booking.uidocumentation.index');
				}
			}
			
			self::add_javascript('booking', 'booking', 'document.js');

			$this->add_default_display_data($document);
			
			$this->flash_form_errors($errors);

			self::render_template('documentation_form', array('document' => $document));
		}
		
		public function edit()
		{
			$id = intval(phpgw::get_var('id', 'GET'));
			$document = $this->bo->read_single($id);
			
			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$document = array_merge($document, extract_values($_POST, $this->fields));
				$errors = $this->bo->validate($document);
				if(!$errors)
				{
					$receipt = $this->bo->update($document);	
					$this->redirect('booking.uidocumentation.index');
				}
			}
			
			self::add_javascript('booking', 'booking', 'document.js');
			$this->flash_form_errors($errors);
			$this->add_default_display_data($document);
			
			self::render_template('documentation_form', array('document' => $document));
		}
		
		public function download()
		{
			$id = intval(phpgw::get_var('id', 'GET'));
			
			$document = $this->bo->read_single($id);
			
			self::send_file($document['filename'], array('filename' => $document['name']));
		}
		
		public function delete()
		{
			$id = intval(phpgw::get_var('id', 'GET'));
			$this->bo->delete($id);
			
			$this->redirect('booking.uidocumentation.index');
		}
		
		
		/**
		 * Implement to return the full hierarchical pathway to this documents owner(s).
		 *
		 * @param int $document_id
		 *
		 * @return array of url(s) to owner(s) in order of hierarchy.
		 */
		protected function get_owner_pathway(array $forDocumentData) { return array(); }
	}
