<?php
	/**
	 * phpGroupWare - property: a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
	 * This file is part of phpGroupWare.
	 *
	 * phpGroupWare is free software; you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation; either version 2 of the License, or
	 * (at your option) any later version.
	 *
	 * phpGroupWare is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with phpGroupWare; if not, write to the Free Software
	 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	 *
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	 * @package property
	 * @subpackage cron
	 * @version $Id$
	 */
	/**
	 * Description
	 * example cron : /usr/local/bin/php -q /var/www/html/phpgroupware/property/inc/cron/cron.php default hent_epost_fra_eksterne_BK_LRS
	 * @package property
	 */
	include_class('property', 'cron_parent', 'inc/cron/');
	require_once PHPGW_SERVER_ROOT . '/phpgwapi/inc/ews/autoload.php';

	use \jamesiarmes\PhpEws\Client;
	use \jamesiarmes\PhpEws\Request\FindItemType;

	use \jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfBaseFolderIdsType;

	use \jamesiarmes\PhpEws\Enumeration\DefaultShapeNamesType;
	use \jamesiarmes\PhpEws\Enumeration\DistinguishedFolderIdNameType;
	use \jamesiarmes\PhpEws\Enumeration\ResponseClassType;

	use \jamesiarmes\PhpEws\Type\ConstantValueType;
	use \jamesiarmes\PhpEws\Type\DistinguishedFolderIdType;
	use \jamesiarmes\PhpEws\Type\FieldURIOrConstantType;
	use \jamesiarmes\PhpEws\Type\ItemResponseShapeType;
	use \jamesiarmes\PhpEws\Type\PathToUnindexedFieldType;
	use \jamesiarmes\PhpEws\Type\RestrictionType;

	use \jamesiarmes\PhpEws\Type\IsEqualToType;
	use \jamesiarmes\PhpEws\Request\GetItemType;
	use \jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfBaseItemIdsType;
	use \jamesiarmes\PhpEws\Type\ItemIdType;
	use \jamesiarmes\PhpEws\Request\GetAttachmentType;
	use \jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfRequestAttachmentIdsType;
	use \jamesiarmes\PhpEws\Type\RequestAttachmentIdType;

	use \jamesiarmes\PhpEws\Request\UpdateItemType;
	use \jamesiarmes\PhpEws\Type\ItemChangeType;
	use \jamesiarmes\PhpEws\Type\SetItemFieldType;
	use \jamesiarmes\PhpEws\Type\MessageType;

	// Folder info
	use \jamesiarmes\PhpEws\Request\FindFolderType;
	use \jamesiarmes\PhpEws\Enumeration\ContainmentComparisonType;
	use \jamesiarmes\PhpEws\Enumeration\ContainmentModeType;
	use \jamesiarmes\PhpEws\Enumeration\FolderQueryTraversalType;
	use \jamesiarmes\PhpEws\Enumeration\UnindexedFieldURIType;
	use \jamesiarmes\PhpEws\Type\FolderResponseShapeType;
	//

	use \jamesiarmes\PhpEws\Request\MoveItemType;

	class hent_epost_fra_eksterne_BK_LRS extends property_cron_parent
	{

		var $items_to_move = array();
		protected $config;

		public function __construct()
		{
			parent::__construct();

			$this->function_name = get_class($this);
			$this->sub_location = lang('helpdesk');
			$this->function_msg = 'Hent epost fra postmottak til LRS';
			$this->db = & $GLOBALS['phpgw']->db;
			$this->join = & $this->db->join;

			$this->config = CreateObject('admin.soconfig', $GLOBALS['phpgw']->locations->get_id('helpdesk', '.admin'));
		}

		function execute()
		{
			$start = time();
			$this->process_messages();
			$msg = 'Tidsbruk: ' . (time() - $start) . ' sekunder';
			$this->cron_log($msg, $cron);
			echo "$msg\n";
			$this->receipt['message'][] = array('msg' => $msg);

		}

		function cron_log( $receipt = '' )
		{

			$insert_values = array(
				$this->cron,
				date($this->db->datetime_format()),
				$this->function_name,
				$receipt
			);

			$insert_values = $this->db->validate_insert($insert_values);

			$sql = "INSERT INTO fm_cron_log (cron,cron_date,process,message) "
				. "VALUES ($insert_values)";
			$this->db->query($sql, __LINE__, __FILE__);
		}



		function process_messages()
		{
			// Set connection information.
			$host = !empty($this->config->config_data['xPortico']['ews_server']) ? $this->config->config_data['xPortico']['ews_server'] : 'epost.bergen.kommune.no';
			$username = !empty($this->config->config_data['xPortico']['username']) ? $this->config->config_data['xPortico']['username'] : 'xLRS';
			$password = $this->config->config_data['xPortico']['password'];
			$version = Client::VERSION_2016;

			$client = new Client($host, $username, $password, $version);

			//read messages from this folder
			$root_folder = $this->find_root_folder($client);

			//move messages to this folder.
			$folder_info = $this->find_folder($client);

			$IsEqualTo_isread = new IsEqualToType();
			$IsEqualTo_isread->FieldURI = new PathToUnindexedFieldType();
			$IsEqualTo_isread->FieldURI->FieldURI = 'message:IsRead';
			$IsEqualTo_isread->FieldURIOrConstant = new FieldURIOrConstantType();
			$IsEqualTo_isread->FieldURIOrConstant->Constant = new ConstantValueType();
			$IsEqualTo_isread->FieldURIOrConstant->Constant->Value = "false";

			$request = new FindItemType();
			$request->ParentFolderIds = new NonEmptyArrayOfBaseFolderIdsType();

			// Build the restriction.
			$request->Restriction = new RestrictionType();
			$request->Restriction->IsEqualTo = $IsEqualTo_isread;

			// Return all message properties.
			$request->ItemShape = new ItemResponseShapeType();
			$request->ItemShape->BaseShape = DefaultShapeNamesType::ALL_PROPERTIES;

			// Search in another user's inbox.
			$folder_id =new jamesiarmes\PhpEws\Type\FolderIdType();
			$folder_id->ChangeKey = $root_folder['changekey'];
			$folder_id->Id = $root_folder['id'];

			$request->ParentFolderIds->FolderId[] = $folder_id;

			$response = $client->FindItem($request);

			// Iterate over the results, printing any error messages or message subjects.
			$response_messages = $response->ResponseMessages->FindItemResponseMessage;
			foreach ($response_messages as $response_message)
			{
				// Make sure the request succeeded.
				if ($response_message->ResponseClass != ResponseClassType::SUCCESS)
				{
					$code = $response_message->ResponseCode;
					$message = $response_message->MessageText;
					fwrite(
						STDERR,
						"Failed to search for messages with \"$code: $message\"\n"
					);
					continue;
				}

				// Iterate over the messages that were found, printing the subject for each.
				$items = $response_message->RootFolder->Items->Message;
				foreach ($items as $item)
				{
					$message_id = $item->ItemId->Id;
					$request2 = new GetItemType();
					$request2->ItemShape = new ItemResponseShapeType();
					$request2->ItemShape->BaseShape = DefaultShapeNamesType::ALL_PROPERTIES;
					$request2->ItemIds = new NonEmptyArrayOfBaseItemIdsType();

					// Add the message id to the request.
					$item2 = new ItemIdType();
					$item2->Id = $message_id;
					$request2->ItemIds->ItemId[] = $item2;

					$response2 = $client->GetItem($request2);

					// Iterate over the results, printing any error messages or receiving
					// attachments.
					$response_messages2 = $response2->ResponseMessages->GetItemResponseMessage;


					foreach ($response_messages2 as $response_message2)
					{
						// Make sure the request succeeded.
						if ($response_message2->ResponseClass != ResponseClassType::SUCCESS)
						{
							$code = $response_message2->ResponseCode;
							$message = $response_message2->MessageText;
							fwrite(STDERR, "Failed to get message with \"$code: $message\"\n");
							continue;
						}

						// Iterate over the messages, getting the attachments for each.
						$attachments = array();
						foreach ($response_message2->Items->Message as $item3)
						{
							$target = $this->handle_message($item3);

							// If there are no attachments for the item, move on to the next
							// message.
							if (empty($item3->Attachments))
							{
								continue;
							}

							// Iterate over the attachments for the message.
							foreach ($item3->Attachments->FileAttachment as $attachment)
							{
								$attachments[] = $attachment->AttachmentId->Id;
							}
						}

						$saved_attachments = array();
						if($attachments)
						{
							$saved_attachments = $this->handle_attachments($client, $attachments, $response_message2);
						}

						if(!empty($target['id']) && $saved_attachments)
						{
							$this->add_attacthment_to_target($target, $saved_attachments);
						}

						foreach ($this->items_to_move as $item4)
						{
							$this->update_message($client, $item4);
							$this->move_message($client, $item4, $folder_info);
						}

						$this->items_to_move = array();
					}
				}
			}
		}

		function find_root_folder($client)
		{

			// Build the request.
			$request = new FindFolderType();
			$request->FolderShape = new FolderResponseShapeType();
			$request->FolderShape->BaseShape = DefaultShapeNamesType::ALL_PROPERTIES;
			$request->ParentFolderIds = new NonEmptyArrayOfBaseFolderIdsType();
			$request->Restriction = new RestrictionType();

			// Search recursively.
			$request->Traversal = FolderQueryTraversalType::DEEP;

			// Search within the root folder. Combined with the traversal set above, this
			// should search through all folders in the user's mailbox.
			$parent = new DistinguishedFolderIdType();
			$parent->Id = DistinguishedFolderIdNameType::ROOT;

			// New Properties:
			$_mailbox = !empty($this->config->config_data['xPortico']['mailbox']) ? $this->config->config_data['xPortico']['mailbox'] : 'lrs@bergen.kommune.no';
			$parent->Mailbox = new StdClass;
			$parent->Mailbox->EmailAddress = $_mailbox;
			// End of new Props.

			$request->ParentFolderIds->DistinguishedFolderId[] = $parent;

			// Build the restriction that will search for folders containing "Cal".
			$contains = new \jamesiarmes\PhpEws\Type\ContainsExpressionType();
			$contains->FieldURI = new PathToUnindexedFieldType();
			$contains->FieldURI->FieldURI = UnindexedFieldURIType::FOLDER_DISPLAY_NAME;
			$contains->Constant = new ConstantValueType();
			$contains->Constant->Value = 'Innboks';
			$contains->ContainmentComparison = ContainmentComparisonType::EXACT;
			$contains->ContainmentMode = ContainmentModeType::EXACT_PHRASE;
			$request->Restriction->Contains = $contains;

			$response = $client->FindFolder($request);

			// Iterate over the results, printing any error messages or folder names and
			// ids.
			$response_messages = $response->ResponseMessages->FindFolderResponseMessage;

			foreach ($response_messages as $response_message)
			{
				// Make sure the request succeeded.
				if ($response_message->ResponseClass != ResponseClassType::SUCCESS)
				{
					$code = $response_message->ResponseCode;
					$message = $response_message->MessageText;
					fwrite(STDERR, "Failed to find folders with \"$code: $message\"\n");
					continue;
				}

				$folders = $response_message->RootFolder->Folders->Folder;

				$folder_info = array(
					'name' => $folders[0]->DisplayName,
					'id' => $folders[0]->FolderId->Id,
					'changekey' => $folders[0]->FolderId->ChangeKey
				);
			}

			return $folder_info;
		}


		function find_folder($client)
		{
			// Build the request.
			$request = new FindFolderType();
			$request->FolderShape = new FolderResponseShapeType();
			$request->FolderShape->BaseShape = DefaultShapeNamesType::ALL_PROPERTIES;
			$request->ParentFolderIds = new NonEmptyArrayOfBaseFolderIdsType();
			$request->Restriction = new RestrictionType();

			// Search recursively.
			$request->Traversal = FolderQueryTraversalType::DEEP;

			// Search within the root folder. Combined with the traversal set above, this
			// should search through all folders in the user's mailbox.
			$parent = new DistinguishedFolderIdType();
			$parent->Id = DistinguishedFolderIdNameType::ROOT;

			// New Properties:
			$_mailbox = !empty($this->config->config_data['xPortico']['mailbox']) ? $this->config->config_data['xPortico']['mailbox'] : 'lrs@bergen.kommune.no';
			$parent->Mailbox = new StdClass;
			$parent->Mailbox->EmailAddress = $_mailbox;
			// End of new Props.

			$request->ParentFolderIds->DistinguishedFolderId[] = $parent;

			// Build the restriction that will search for folders containing "Cal".
			$contains = new \jamesiarmes\PhpEws\Type\ContainsExpressionType();
			$contains->FieldURI = new PathToUnindexedFieldType();
			$contains->FieldURI->FieldURI = UnindexedFieldURIType::FOLDER_DISPLAY_NAME;
			$contains->Constant = new ConstantValueType();
			$contains->Constant->Value = 'Importert til database';
			$contains->ContainmentComparison = ContainmentComparisonType::EXACT;
			$contains->ContainmentMode = ContainmentModeType::EXACT_PHRASE;
			$request->Restriction->Contains = $contains;

			$response = $client->FindFolder($request);

			// Iterate over the results, printing any error messages or folder names and
			// ids.
			$response_messages = $response->ResponseMessages->FindFolderResponseMessage;
			foreach ($response_messages as $response_message)
			{
				// Make sure the request succeeded.
				if ($response_message->ResponseClass != ResponseClassType::SUCCESS)
				{
					$code = $response_message->ResponseCode;
					$message = $response_message->MessageText;
					fwrite(STDERR, "Failed to find folders with \"$code: $message\"\n");
					continue;
				}

				$folders = $response_message->RootFolder->Folders->Folder;

				$folder_info = array(
					'name' => $folders[0]->DisplayName,
					'id' => $folders[0]->FolderId->Id,
					'changekey' => $folders[0]->FolderId->ChangeKey
				);
			}

			return $folder_info;
		}

		function handle_message( $item3 )
		{
			$sender = $item3->Sender->Mailbox->EmailAddress;
			$target = array();
			$subject = $item3->Subject;
			$rool =$item3->Body->_;
			$text_message  = array('text' => $rool);
			$newArray = array_map(function($v)
			{
				return trim(strip_tags($v));
			 }, $text_message);

			$body = $newArray['text'];

			if(preg_match("/helpdesk@bergen.kommune.no/i" , $sender ))
//			if(preg_match("/sigurd.nes@bergen.kommune.no/i" , $sender ))
			{

				$message_cat_id = 302; // Fra postmottak LRS
				$group_id = 4174; //LRS-EDD telefoni
				$ticket_id = $this->create_ticket($subject, $body, $message_cat_id, $group_id);
				if($ticket_id)
				{
					$this->receipt['message'][] = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type'] = 'helpdesk';
					$target['id'] = $ticket_id;
				}
			}
			else if (preg_match("/noreply@altinn.no/i" , $sender ))
			{
				if(preg_match("/Skatt/i" , $subject ))
				{
					$message_cat_id = 264; //LRS Lønn - Skatt
					$group_id = 3159; //LRS Lønn

				}
				if(preg_match("/Sykmelding/i" , $subject ) || preg_match("/sykepenger/i" , $subject ))
				{
					$message_cat_id = 306; //LRS Refusjon - Altinn
					$group_id = 3233; //LRS Refusjon
				}
				$ticket_id = $this->create_ticket($subject, $body, $message_cat_id, $group_id);
				if($ticket_id)
				{
					$this->receipt['message'][] = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type'] = 'helpdesk';
					$target['id'] = $ticket_id;
				}

			}
			else if(preg_match("/\[PorticoTicket/" , $subject ))
			{
				preg_match_all("/\[[^\]]*\]/", $subject, $matches);
				$identificator_str =  trim($matches[0][0],  "[]" );
				$identificator_arr = explode("::", $identificator_str);

				$ticket_id = $this->update_external_communication($identificator_arr, $body, $sender);

				if($ticket_id)
				{
					$target['type'] = 'helpdesk';
					$target['id'] = $ticket_id;
				}
			}

			/**
			 * Ticket created / updated
			 */
			if($target)
			{
				$this->items_to_move[] = $item3;

			}
			return $target;

		}

		function update_external_communication($identificator_arr, $body, $sender)
		{
			$ticket_id = (int)$identificator_arr[1];
			$msg_id	= (int)$identificator_arr[2];

			if(!$msg_id)
			{
				return false;
			}
			$soexternal = createObject('property.soexternal_communication');

			$message_arr = explode('========', $body);
			$message = $message_arr[0];
			if($soexternal->add_msg($msg_id, $message, $sender))
			{
				$sql = "SELECT assignedto"
					. " FROM phpgw_helpdesk_tickets"
					. " WHERE id = {$ticket_id}";
				$this->db->query($sql, __LINE__, __FILE__);
				$this->db->next_record();
				$assignedto = $this->db->f('assignedto');
				if($assignedto)
				{
					createObject('property.boexternal_communication')->alert_assigned($msg_id);
				}

				return $ticket_id;
			}
		}

		function get_ticket ($subject)
		{
			//ISS vedlegg: vedlegg til #ID: <din WO ID>
			$subject_arr = explode('#', $subject);
			$id_arr = explode(':', $subject_arr[1]);
			$external_ticket_id = (int)($id_arr[1]);
			$sql = "SELECT id"
				. " FROM phpgw_helpdesk_tickets"
				. " WHERE external_ticket_id = {$external_ticket_id}";
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			$ticket_id =  $this->db->f('id');
			return $ticket_id;
		}


		function create_ticket ($subject, $body, $message_cat_id, $group_id)
		{

			if(!$message_cat_id)
			{
				return false;
			}

//			$ticket_id = $this->get_ticket($subject);
			$ticket_id = false;

			$subject_arr = explode('#', $subject);
			$id_arr = explode(':', $subject_arr[1]);
			$external_ticket_id = trim($id_arr[1]);
			$text = trim($body);
			$textAr = explode(PHP_EOL, $text);
			$textAr = array_filter($textAr, 'trim'); // remove any extra \r characters left behind
			$message_details_arr = array($subject);
			foreach ($textAr as $line)
			{
				if(preg_match("/Untitled document/" , $line ))
				{
					continue;
				}

				$message_details_arr[] = trim($line);
			}
			$message_details = implode(PHP_EOL, $message_details_arr);

			if($ticket_id)
			{
				$historylog	= CreateObject('phpgwapi.historylog','helpdesk');
				$historylog->add('C', $ticket_id, $message_details);
			}
			else
			{
				$priority = 3;
				$ticket = array
				(
					'assignedto'=> false,
					'group_id' => $group_id,
					'cat_id' => $message_cat_id,
					'priority' => $priority, //valgfri (1-3)
					'title' => $subject,
					'details' => $message_details,
					'external_ticket_id'	=> $external_ticket_id
				);

				$ticket_id =  CreateObject('helpdesk.botts')->add_ticket($ticket);
			}
			return $ticket_id;
		}

		function add_attacthment_to_target( $target, $saved_attachments )
		{

			$target['type'];
			$target['id'];

			$bofiles = CreateObject('property.bofiles','/helpdesk');
			foreach ($saved_attachments as $saved_attachment)
			{
				$file_name = str_replace(array(' ', '..'), array('_', '.'), $saved_attachment['name']);

				if ($file_name && $target['id'])
				{
					$to_file = "{$bofiles->fakebase}/{$target['id']}/{$file_name}";

					if ($bofiles->vfs->file_exists(array(
							'string' => $to_file,
							'relatives' => array(RELATIVE_NONE)
						)))
					{
						$this->receipt['error'][] = array('msg' => lang('This file already exists !'));
					}
					else
					{
						$bofiles->create_document_dir("{$target['id']}");
						$bofiles->vfs->override_acl = 1;

						if (!$bofiles->vfs->cp(array(
								'from' => $saved_attachment['tmp_name'],
								'to' => $to_file,
								'relatives' => array(RELATIVE_NONE | VFS_REAL, RELATIVE_ALL))))
						{
							$this->receipt['error'][] = array('msg' => lang('Failed to upload file !'));
						}
						$bofiles->vfs->override_acl = 0;
					}
				}
			}
		}

		function update_message($client, $item3)
		{
			$message_id = $item3->ItemId->Id;
			$message_change_key = $item3->ItemId->ChangeKey;

			$request = new UpdateItemType();

			$request->SendMeetingInvitationsOrCancellations = 'SendToNone';
			$request->MessageDisposition = 'SaveOnly';
			$request->ConflictResolution = 'AlwaysOverwrite';
			$request->ItemChanges = array();

			// Build out item change request.
			$change = new ItemChangeType();
			$change->ItemId = new ItemIdType();
			$change->ItemId->Id = $message_id;
			$change->ItemId->ChangeKey = $message_change_key;

			// Build the set item field object and set the item on it.
			$field = new SetItemFieldType();
			$field->FieldURI = new PathToUnindexedFieldType();
			$field->FieldURI->FieldURI = "message:IsRead";
			$field->Message = new MessageType();
			$field->Message->IsRead = true;

			$change->Updates->SetItemField[] = $field;
			$request->ItemChanges[] = $change;

			$response = $client->UpdateItem($request);

		}

		function move_message($client, $item3, $folder_info)
		{
			$request = new MoveItemType();

			$request->ToFolderId->FolderId->Id = $folder_info['id'];
			$request->ToFolderId->FolderId->ChangeKey = $folder_info['changekey'];

			$request->ItemIds->ItemId->Id = $item3->ItemId->Id;
			$request->ItemIds->ItemId->ChangeKey = $item3->ItemId->ChangeKey;

			$response = $client->MoveItem($request);
		}

		function handle_attachments($client, $attachments, $response_message2)
		{
			$saved_attachments = array();

			$temp_dir = sys_get_temp_dir();

			// Build the request to get the attachments.
			$request3 = new GetAttachmentType();
			$request3->AttachmentIds = new NonEmptyArrayOfRequestAttachmentIdsType();

			// Iterate over the attachments for the message.
			foreach ($attachments as $attachment_id)
			{
				$id = new RequestAttachmentIdType();
				$id->Id = $attachment_id;
				$request3->AttachmentIds->AttachmentId[] = $id;
			}

			$response3 = $client->GetAttachment($request3);

			// Iterate over the response messages, printing any error messages or
			// saving the attachments.
			$attachment_response_messages = $response3->ResponseMessages
				->GetAttachmentResponseMessage;
			foreach ($attachment_response_messages as $attachment_response_message)
			{
				// Make sure the request succeeded.
				if ($attachment_response_message->ResponseClass
					!= ResponseClassType::SUCCESS)
				{
					$code = $response_message2->ResponseCode;
					$message = $response_message2->MessageText;
					fwrite(
						STDERR,
						"Failed to get attachment with \"$code: $message\"\n"
					);
					continue;
				}

				// Iterate over the file attachments, saving each one.
				$attachments = $attachment_response_message->Attachments
					->FileAttachment;
				foreach ($attachments as $attachment)
				{
					$tmp_name = tempnam($temp_dir, "xPortico");
					$handle = fopen($tmp_name, "w");
					fwrite($handle, $attachment->Content);
					fclose($handle);

					$saved_attachments[] = array(
						'tmp_name' => $tmp_name,
						'name' => $attachment->Name,
					);
				}
			}

			return $saved_attachments;
		}
	}