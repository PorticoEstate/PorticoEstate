<?php
	phpgw::import_class('rental.uicommon');
	phpgw::import_class('rental.sodocument');
	phpgw::import_class('rental.socontract');
	phpgw::import_class('rental.soparty');
	include_class('rental', 'document', 'inc/model/');

	class rental_uidocument extends rental_uicommon
	{

		public $public_functions = array
			(
			'query' => true,
			'add' => true,
			'view' => true,
			'delete' => true
		);

		public function __construct()
		{
			parent::__construct();
		}

		public function query()
		{
			if ($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] > 0)
			{
				$user_rows_per_page = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$user_rows_per_page = 10;
			}

			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');

			$start_index = phpgw::get_var('start', 'int', 'REQUEST', 0);
			$num_of_objects = (phpgw::get_var('length', 'int') <= 0) ? $user_rows_per_page : phpgw::get_var('length', 'int');
			$sort_field = 'id';
			switch ($columns[$order[0]['column']]['data'])
			{
				case 'type':
					$sort_field = 'rental_document.type_id';
					Break;
				default :
					$sort_field = 'rental_document.' . $columns[$order[0]['column']]['data'];
			}
			$sort_ascending = ($order[0]['dir'] == 'desc') ? false : true;
			// Form variables
			$search_for = $search['value'];
			$search_type = phpgw::get_var('search_option', 'string', 'REQUEST', 'all');

			// YUI variables for paging and sorting
			/* $start_index	= phpgw::get_var('startIndex', 'int');
			  $num_of_objects	= phpgw::get_var('results', 'int', 'GET', 10);
			  $sort_field		= phpgw::get_var('sort');
			  $sort_ascending	= phpgw::get_var('dir') == 'desc' ? false : true;
			  // Form variables
			  $search_for 	= phpgw::get_var('query');
			  $search_type	= phpgw::get_var('search_option'); */
			// Create an empty result set
			$result_objects = array();
			$result_count = 0;

			//Retrieve a contract identifier and load corresponding contract
			$contract_id = phpgw::get_var('contract_id');
			if (isset($contract_id))
			{
				$contract = rental_socontract::get_instance()->get_single($contract_id);
			}

			$type = phpgw::get_var('type');
			switch ($type)
			{
				case 'documents_for_contract':
					$filters = array('contract_id' => $contract_id, 'document_type' => phpgw::get_var('document_type'));
					break;
				case 'documents_for_party':
					$filters = array('party_id' => phpgw::get_var('party_id'), 'document_type' => phpgw::get_var('document_type'));
					break;
			}

			$result_objects = rental_sodocument::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
			$result_count = rental_sodocument::get_instance()->get_count($search_for, $search_type, $filters);

			//Serialize the documents found
			$rows = array();
			foreach ($result_objects as $result)
			{
				if (isset($result))
				{
					$rows[] = $result->serialize();
				}
			}


			$result_data = array('results' => $rows);
			$result_data['total_records'] = $result_count;
			$result_data['draw'] = $draw;

			return $this->jquery_results($result_data);
		}

		/**
		 * Add data for context menu
		 *
		 * @param $value pointer to
		 * @param $key ?
		 * @param $params [type of query, editable]
		 */
		public function add_actions( &$value, $key, $params )
		{

			$value['ajax'] = array();
			$value['actions'] = array();
			$value['labels'] = array();

			//view/download
			$value['ajax'][] = false;
			$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uidocument.view',
					'id' => $value['id'])));
			$value['labels'][] = lang('view');

			$type = $params[0];
			$edit_permission = $params[1];
			$user_is = $params[2];
			$editable = $params[3];

			switch ($type)
			{
				case 'documents_for_contract':
					if ($edit_permission && $editable)
					{
						$value['ajax'][] = true;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uidocument.delete',
								'id' => $value['id'])));
						$value['labels'][] = lang('delete');
					}
					break;
				case 'documents_for_party':
					if ($user_is[EXECUTIVE_OFFICER] && $editable)
					{
						$value['ajax'][] = true;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uidocument.delete',
								'id' => $value['id'])));
						$value['labels'][] = lang('delete');
					}
					break;
			}
		}

		/**
		 * Public function to add a document.
		 * 
		 * @param HTTP::contract_id	the contract id
		 * @param HTTP::party_id	the party id
		 * @return unknown_type
		 */
		public function add()
		{
			// Get target ids
			$contract_id = intval(phpgw::get_var('contract_id'));
			$party_id = intval(phpgw::get_var('party_id'));

			$message = array();

			// Check permissions if contract id is set
			if (isset($contract_id) && $contract_id > 0)
			{
				//Load contract
				$contract = rental_socontract::get_instance()->get_single($contract_id);
				if (!$contract->has_permission(PHPGW_ACL_EDIT))
				{
					$message['error'][] = array('msg' => lang('permission_denied_add_document'));
					return $message;
				}
			}

			// Check permissions if party id is set
			if (isset($party_id) && $party_id > 0)
			{
				//Load party
				$party = rental_soparty::get_instance()->get_single($party_id);
				if (!($this->isAdministrator() || $this->isExecutiveOfficer()))
				{
					$message['error'][] = array('msg' => lang('permission_denied_add_document'));
					return $message;
				}
			}

			// If no contract or party is loaded
			if (!(isset($party) || isset($contract)))
			{
				$message['error'][] = array('msg' => lang('permission_denied_add_document'));
				return $message;
			}

			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				//Create a document object
				$document = new rental_document();
				$document->set_title(phpgw::get_var('document_title'));
				$document->set_name($_FILES["file_path"]["name"]);
				$document->set_type_id(phpgw::get_var('document_type'));
				$document->set_contract_id($contract_id);
				$document->set_party_id($party_id);

				//Retrieve the document properties
				$document_properties = $this->get_type_and_id($document);

				// Move file from temporary storage to vfs
				$result = rental_sodocument::get_instance()->write_document_to_vfs
					(
					$document_properties['document_type'], $_FILES["file_path"]["tmp_name"], $document_properties['id'], $_FILES["file_path"]["name"]
				);

				if ($result)
				{
					if (rental_sodocument::get_instance()->store($document))
					{
						$message['message'][] = array('msg' => lang('document has been added'));
					}
					else
					{

						$message['error'][] = array('msg' => lang('document not added'));
					}
				}
				else
				{
					$message['error'][] = array('msg' => lang('failed to upload file') . ': ' . $_FILES["file_path"]["name"]);
				}

				return $message;
			}
		}

		/**
		 * Public function for viewing/downloading a document.
		 * 
		 * @param HTTP::id	the document id
		 * @return document on success, error message on failure
		 */
		public function view()
		{
			$document_id = intval(phpgw::get_var('id'));
			$document = rental_sodocument::get_instance()->get_single($document_id);
			if ($document->has_permission(PHPGW_ACL_READ))
			{
				$document_properties = $this->get_type_and_id($document);

				header("Content-Disposition: attachment; filename={$document->get_name()}");
				header("Content-Type: $file_type");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

				echo rental_sodocument::get_instance()->read_document_from_vfs
					(
					$document_properties['document_type'], $document_properties['id'], $document->get_name()
				);
			}
			else
			{
				$this->redirect($document, $document_properties, lang('no_access'), '');
			}
			exit;
		}

		/**
		 * Public function for deleting a document. Deletes the document from
		 * the database and the virtual file system (vfs).
		 * 
		 * @param HTTP::id	the document id
		 * @return true if successful, false if error, permission denied message on
		 * 			not enough privileges
		 */
		/* public function delete()
		  {
		  $document_id = intval(phpgw::get_var('id'));
		  $document = rental_sodocument::get_instance()->get_single($document_id);
		  $document_properties = $this->get_type_and_id($document);

		  if(!$this->check_permissions($document,$document_properties))
		  {
		  phpgw::no_access();
		  }

		  $result = rental_sodocument::get_instance()->delete_document_from_vfs
		  (
		  $document_properties['document_type'],
		  $document_properties['id'],
		  $document->get_name()
		  );

		  if($result)
		  {
		  return rental_sodocument::get_instance()->delete_document($document_id);
		  }
		  // TODO: communicate error/message to user
		  return false;
		  } */

		public function delete()
		{
			$list_document_id = phpgw::get_var('id');
			$message = array();

			foreach ($list_document_id as $document_id)
			{
				$document = rental_sodocument::get_instance()->get_single($document_id);
				$document_properties = $this->get_type_and_id($document);

				if (!$this->check_permissions($document, $document_properties))
				{
					$message['error'][] = array('msg' => lang('permission_denied') . ' to remove ' . $document->get_name());
					continue;
				}

				$result = rental_sodocument::get_instance()->delete_document_from_vfs
					(
					$document_properties['document_type'], $document_properties['id'], $document->get_name()
				);

				if ($result)
				{
					if (rental_sodocument::get_instance()->delete_document($document_id))
					{
						$message['message'][] = array('msg' => lang('document %1 has been removed', $document->get_name()));
					}
					else
					{
						$message['error'][] = array('msg' => lang('document %1 not removed', $document->get_name()));
					}
				}
				else
				{
					$message['error'][] = array('msg' => lang('failed to delete file') . ': ' . $document->get_name());
				}
			}
			// TODO: communicate error/message to user
			return $message;
		}

		/**
		 * Utitity function for redirecting to correct edit mode (contract/party)
		 * 
		 * @param $document	the target document
		 * @param $document_properties	the document properies (name/value array)
		 * @param $error	an error message
		 * @param $message	a user message
		 */
		public function redirect( $document, $document_properties, $error, $message )
		{
			if ($document_properties['document_type'] == rental_sodocument::$CONTRACT_DOCUMENTS)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uiparty.edit',
					'id' => $document_properties['id'], 'error' => $error, 'message' => $message));
			}
			else if ($document_properties['document_type'] == rental_sodocument::$PARTY_DOCUMENTS)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uiparty.edit',
					'id' => $document_properties['id'], 'error' => $error, 'message' => $message));
			}
		}

		/**
		 * Utiity method for checking the users permission on this document. If the
		 * document is bound to a contract, then the user must have edit privileges
		 * on the given contract. If no contract, the user must be an executive 
		 * officer or an administrator.
		 * 
		 * @param $document	the document in question
		 * @param $document_properties	the document type and object id
		 * @return true if correct privileges, false otherwise
		 */
		private function check_permissions( $document, $document_properties )
		{
			if ($document_properties == rental_sodocument::$CONTRACT_DOCUMENTS)
			{
				$contract = rental_socontract::get_instance()->get_single($document_properties['id']);
				if (!$contract->has_permission(PHPGW_ACL_EDIT))
				{
					return false;
				}
			}
			else
			{
				if (!($this->isExecutiveOfficer() || $this->isAdministrator()))
				{
					return false;
				}
			}
			return true;
		}

		/**
		 * Utility method for finding out whether a document is bound to a
		 * contract or a party.
		 * 
		 * @param $document	the given document
		 * @return name/value array ('document_type','id')
		 */
		private function get_type_and_id( $document )
		{
			$document_type;
			$id;
			$contract_id = $document->get_contract_id();
			$party_id = $document->get_party_id();
			if (isset($contract_id) && $contract_id > 0)
			{
				$document_type = rental_sodocument::$CONTRACT_DOCUMENTS;
				$id = $contract_id;
			}
			else if (isset($party_id) && $party_id > 0)
			{
				$document_type = rental_sodocument::$PARTY_DOCUMENTS;
				$id = $party_id;
			}
			return array
				(
				'document_type' => $document_type,
				'id' => $id
			);
		}
	}