<?php
	phpgw::import_class('rental.uicommon');
	phpgw::import_class('rental.sodocument');

	class rental_uidocument extends rental_uicommon
	{	
		public $public_functions = array
		(
				'query'			=> true,
				'add'			=> true,
				'download'		=> true,
				'delete'		=> true
			);
		
		public function __construct()
		{
			parent::__construct();
		}
		
		public function query()
		{
			// YUI variables for paging and sorting
			$start_index	= phpgw::get_var('startIndex', 'int');
			$num_of_objects	= phpgw::get_var('results', 'int', 'GET', 1000);
			$sort_field		= phpgw::get_var('sort');
			$sort_ascending	= phpgw::get_var('dir') == 'desc' ? false : true;
			// Form variables
			$search_for 	= phpgw::get_var('query');
			$search_type	= phpgw::get_var('search_option');
			// Create an empty result set
			$result_objects = array();
			$result_count = 0;
			
			$type = phpgw::get_var('type');
			switch($type)
			{
				case 'documents_for_contract':
					$filters = array('contract_id' => phpgw::get_var('contract_id'), 'type_id' => phpgw::get_var('type_id'));
					break;
				case 'documents_for_party':
					$filters = array('party_id' => phpgw::get_var('party_id'), 'type_id' => phpgw::get_var('type_id'));
					break;
			}

			$result_objects = rental_sodocument::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
			$result_count = rental_sodocument::get_instance()->get_count($search_for, $search_type, $filters);
			
			
			
			//Serialize the documents found
			$rows = array();
			foreach ($result_objects as $result) {
				if(isset($result))
				{
					if($result->has_permission(PHPGW_ACL_READ)) // check for read permission
					{
						$rows[] = $result->serialize();
					}
				}
			}
			
		
			//Add context menu columns (actions and labels)
			array_walk($rows, array($this, 'add_actions'), array($type));
				
			
			//Build a YUI result from the data
			$result_data = array('results' => $rows, 'total_records' => $result_count);	
			return $this->yui_results($result_data, 'total_records', 'results');
		}
		
		/**
		 * Add data for context menu
		 *
		 * @param $value pointer to
		 * @param $key ?
		 * @param $params [type of query, editable]
		 */
		public function add_actions(&$value, $key, $params)
		{
			
			$value['ajax'] = array();
			$value['actions'] = array();
			$value['labels'] = array();

			//view/download
			$value['ajax'][] = false;
			$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uidocument.view', 'id' => $value['id'])));
			$value['labels'][] = lang('view_document');
			
			$type = $params[0];
			
			switch($type)
			{
				case 'documents_for_contract':
						//Delete, check for contract permissions
						$value['ajax'][] = false;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uidocument.delete', 'id' => $value['id'])));
						$value['labels'][] = lang('delete_document');	
					break;
				case 'documents_for_party':
						//Delete, check for executive officer / administrator
						$value['ajax'][] = false;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uidocument.delete', 'id' => $value['id'])));
						$value['labels'][] = lang('delete_document');	
					break;
			}
		}
		
		public function add()
		{	
			// 1. Check permissions
			$contract_id = intval(phpgw::get_var('contract_id'));
			$party_id = intval(phpgw::get_var('party_id'));
			
			if(isset($contract_id) && $contract_id > 0)
			{
				$contract = rental_socontract::get_instance()->get_single($contract_id);
				if(!$contract->has_permission(PHPGW_ACL_EDIT))
				{
					render('permission_denied.php');
					return;
				}
			}
			
			if(isset($party_id) && $party_id > 0)
			{
				$party = rental_socontract::get_instance()->get_single($party_id);
				if($this->isAdministrator() || $this->isExecutiveOfficer())
				{
					render('permission_denied.php');
					return;
				}
			}
			
			if(!(isset($party) || isset($contract)))
			{
				render('permission_denied.php');
				return;
			}
			
			
			
			// 2. Move file from temporary storage to vfs
			
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				
			}
			
			// 3. Forward user to either party or contract view on documents 
			
			
			
			
			$errors = array();
			$document = array();
			
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$document = extract_values($_POST, $this->fields);	
				$document['files'] = $this->get_files();
				$errors = $this->bo->validate($document);
				if(!$errors)
				{
					try {
						$receipt = $this->bo->add($document);
						$this->redirect_to_parent_if_inline();
						$this->redirect($this->get_owner_typed_link_params('index'));
					} catch (rental_unauthorized_exception $e) {
						$errors['global'] = lang('Could not add object due to insufficient permissions');
					}

				}
			}
			
			self::add_javascript('rental', 'rental', 'document.js');
			
			$this->add_default_display_data($document);
			
			if (is_array($parentData = $this->get_parent_if_inline()))
			{
				$document['owner_id'] = $parentData['id'];
				$document['owner_name'] = $parentData['name'];
			}
			
			$this->flash_form_errors($errors);

			self::render_template('document_form', array('document' => $document));
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
			
			$this->redirect_to_parent_if_inline();
			$this->redirect($this->get_owner_typed_link_params('index'));
		}
	}