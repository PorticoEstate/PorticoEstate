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

			$system_user_lid = 'LRS_system';
			$system_user_id	 = $GLOBALS['phpgw']->accounts->name2id($system_user_lid);
			if ($system_user_id)
			{
				$GLOBALS['phpgw_info']['user']['account_id'] = $system_user_id;
				$GLOBALS['phpgw']->preferences->set_account_id($system_user_id, true);
			}

			$this->function_name = get_class($this);
			$this->sub_location	 = lang('helpdesk');
			$this->function_msg	 = 'Hent epost fra postmottak til LRS';
			$this->db			 = & $GLOBALS['phpgw']->db;
			$this->join			 = & $this->db->join;

			$this->config = CreateObject('admin.soconfig', $GLOBALS['phpgw']->locations->get_id('helpdesk', '.admin'));

			$GLOBALS['phpgw_info']['server']['enforce_ssl'] = true;
		}

		function execute()
		{
			$start						 = time();
			$this->process_messages();
			$msg						 = 'Tidsbruk: ' . (time() - $start) . ' sekunder';
			$this->cron_log($msg, $cron);
			echo "$msg\n";
			$this->receipt['message'][]	 = array('msg' => $msg);
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
			$host		 = !empty($this->config->config_data['xPortico']['ews_server']) ? $this->config->config_data['xPortico']['ews_server'] : 'epost.bergen.kommune.no';
			$username	 = !empty($this->config->config_data['xPortico']['username']) ? $this->config->config_data['xPortico']['username'] : 'xLRS';
			$password	 = $this->config->config_data['xPortico']['password'];
			$version	 = Client::VERSION_2016;

			$filter_ulest = !empty($this->config->config_data['xPortico']['filter_ulest']) ? true : false;

			$client = new Client($host, $username, $password, $version);

			//move messages to this folder.
			$movet_to_folder_info = $this->find_folder($client, 'Importert til database');

			$IsEqualTo_isread										 = new IsEqualToType();
			$IsEqualTo_isread->FieldURI								 = new PathToUnindexedFieldType();
			$IsEqualTo_isread->FieldURI->FieldURI					 = 'message:IsRead';
			$IsEqualTo_isread->FieldURIOrConstant					 = new FieldURIOrConstantType();
			$IsEqualTo_isread->FieldURIOrConstant->Constant			 = new ConstantValueType();
			$IsEqualTo_isread->FieldURIOrConstant->Constant->Value	 = "false";


			/**
			 * Regelsett 1
			 */
			$folder_list = array
				(
				'Innboks'							 => array(),
				'Firewall-Fakturaavvik'				 => array
					(
					'message_cat_id' => 319, // 24 Firewall-Fakturaavvik
					'group_id'		 => 4253, //LRS-Drift_Regnskap
					'subject'		 => ''
				),
				'Arbeidsflyt og ehandel'			 => array
					(
					'message_cat_id' => 280, // 24 Faktura fra leverandør
					'group_id'		 => 4253, //LRS-Drift_Regnskap
					'subject'		 => 'Arbeidsflyt og ehandel'
				),
				'Innkassokrav'						 => array
					(
					'message_cat_id' => 321, // 24 Purringer/Inkasso
					'group_id'		 => 4253, //LRS-Drift_Regnskap
					'subject'		 => 'Innkassokrav',
					'priority'		 => 1
				),
				'Purring/Inkassovarsel'				 => array
					(
					'message_cat_id' => 321, // 24 Purringer/Inkasso
					'group_id'		 => 4253, //LRS-Drift_Regnskap
					'subject'		 => 'Purring/Inkassovarsel'
				),
				'Spørsmål fra leverandører'			 => array
					(
					'message_cat_id' => 280, // 24 Faktura fra leverandør
					'group_id'		 => 4253, //LRS-Drift_Regnskap
					'subject'		 => 'Spørsmål fra leverandører'
				),
				'Lønn'								 => array
					(
					'message_cat_id' => 249, // LRS-Lønn::Lønn
					'group_id'		 => 3159, //LRS-DRIFT_Lønn
					'subject'		 => '',
				),
				'Refusjon'							 => array
					(
					'message_cat_id' => 265, // LRS-Refusjon::Annet
					'group_id'		 => 3233, //LRS-DRIFT_Refusjon
					'subject'		 => '',
				),
				/**
				 * Lagt til 7. juni 2019
				 */
				'Hjelp til annet'					 => array
					(
					'message_cat_id' => 357, // 16 Hjelp til annet
					'group_id'		 => 4169, //LRS-SERVICE_Regnskap
					'subject'		 => 'Hjelp til annet'
				),
				'Hjelp til eHandel'					 => array
					(
					'message_cat_id' => 269, // 10 Hjelp til eHandel
					'group_id'		 => 4169, // LRS-SERVICE_Regnskap
					'subject'		 => 'Hjelp til eHandel'
				),
				'Hjelp til inngående faktura'		 => array
					(
					'message_cat_id' => 343, // 11 Hjelp til inngående faktura
					'group_id'		 => 4169, // LRS-SERVICE_Regnskap
					'subject'		 => 'Hjelp til inngående faktura'
				),
				'Hjelp til internordre'				 => array
					(
					'message_cat_id' => 340, // 14 Hjelp til internordre
					'group_id'		 => 4169, // LRS-SERVICE_Regnskap
					'subject'		 => 'Hjelp til internordre'
				),
				'Hjelp til omkontering'				 => array
					(
					'message_cat_id' => 323, // 12 Hjelp til omkontering
					'group_id'		 => 4169, // LRS-SERVICE_Regnskap
					'subject'		 => 'Hjelp til omkontering'
				),
				'Hjelp til rapportering'			 => array
					(
					'message_cat_id' => 342, // 15 Hjelp til rapportering
					'group_id'		 => 4169, // LRS-SERVICE_Regnskap
					'subject'		 => 'Hjelp til rapportering'
				),
				'Hjelp til salgsordre/utg faktura'	 => array
					(
					'message_cat_id' => 282, // 13 Hjelp til salgsordre/utg faktura
					'group_id'		 => 4169, // LRS-SERVICE_Regnskap
					'subject'		 => 'Hjelp til salgsordre/utg faktura'
				),
				'Digilev'							 => array
					(
					'message_cat_id' => 319, // 24 Firewall-Fakturaavvik
					'group_id'		 => 4253, // LRS-DRIFT_Regnskap
					'subject'		 => 'Digilev'
				),
				'Kategori 22 Innbet. fra kunde'		 => array
					(
					'message_cat_id' => 284, // 22 Innbetalinger fra kunde
					'group_id'		 => 4253, //LRS-Drift_Regnskap
					'subject'		 => ''
				),
				'Kategori 20 Fakturering til kunde'	 => array
					(
					'message_cat_id' => 281, // 20 Fakturering til kunde
					'group_id'		 => 4253, //LRS-Drift_Regnskap
					'subject'		 => ''
				),
			);

			foreach ($folder_list as $folder_name => $folder_rules)
			{
				//read messages from this folder
				$root_folder = $this->find_folder($client, $folder_name);

				$request					 = new FindItemType();
				$request->ParentFolderIds	 = new NonEmptyArrayOfBaseFolderIdsType();

				// Build the restriction.
				if ($filter_ulest)
				{
					$request->Restriction			 = new RestrictionType();
					$request->Restriction->IsEqualTo = $IsEqualTo_isread;
				}

				// Return all message properties.
				$request->ItemShape				 = new ItemResponseShapeType();
				$request->ItemShape->BaseShape	 = DefaultShapeNamesType::ALL_PROPERTIES;

				// Search in another user's inbox.
				$folder_id				 = new jamesiarmes\PhpEws\Type\FolderIdType();
				$folder_id->ChangeKey	 = $root_folder['changekey'];
				$folder_id->Id			 = $root_folder['id'];

				$request->ParentFolderIds->FolderId[] = $folder_id;

				$response = $client->FindItem($request);

				// Iterate over the results, printing any error messages or message subjects.
				$response_messages = $response->ResponseMessages->FindItemResponseMessage;

				if ($this->debug)
				{
					_debug_array(array($folder_name, $folder_id->ChangeKey, $folder_id->Id));
					_debug_array(count($response_messages[0]->RootFolder->Items->Message));
				}

				foreach ($response_messages as $response_message)
				{
					// Make sure the request succeeded.
					if ($response_message->ResponseClass != ResponseClassType::SUCCESS)
					{
						$code	 = $response_message->ResponseCode;
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
						$message_id						 = $item->ItemId->Id;
						$request2						 = new GetItemType();
						$request2->ItemShape			 = new ItemResponseShapeType();
						$request2->ItemShape->BaseShape	 = DefaultShapeNamesType::ALL_PROPERTIES;
						$request2->ItemIds				 = new NonEmptyArrayOfBaseItemIdsType();

						// Add the message id to the request.
						$item2						 = new ItemIdType();
						$item2->Id					 = $message_id;
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
								$code	 = $response_message2->ResponseCode;
								$message = $response_message2->MessageText;
								fwrite(STDERR, "Failed to get message with \"$code: $message\"\n");
								continue;
							}

							// Iterate over the messages, getting the attachments for each.
							$attachments = array();
							foreach ($response_message2->Items->Message as $item3)
							{
								$target = $this->handle_message($item3, $folder_rules);

								// If there are no attachments for the item, move on to the next
								// message.
								if (empty($item3->Attachments) || empty($target['id']))
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
							if ($attachments)
							{
								$saved_attachments = $this->handle_attachments($client, $attachments, $response_message2);
							}

							if (!empty($target['id']) && $saved_attachments)
							{
								$this->add_attacthment_to_target($target, $saved_attachments);
							}

							if ($saved_attachments)
							{
								$this->clean_attacthment_from_temp($saved_attachments);
							}

							foreach ($this->items_to_move as $item4)
							{
								$this->update_message($client, $item4);
								$this->move_message($client, $item4, $movet_to_folder_info);
							}

							$this->items_to_move = array();
						}
					}
				}
			}
		}

		function find_folder( $client, $folder_name = 'Importert til database' )
		{
			// Build the request.
			$request						 = new FindFolderType();
			$request->FolderShape			 = new FolderResponseShapeType();
			$request->FolderShape->BaseShape = DefaultShapeNamesType::ALL_PROPERTIES;
			$request->ParentFolderIds		 = new NonEmptyArrayOfBaseFolderIdsType();
			$request->Restriction			 = new RestrictionType();

			// Search recursively.
			$request->Traversal = FolderQueryTraversalType::DEEP;

			// Search within the root folder. Combined with the traversal set above, this
			// should search through all folders in the user's mailbox.
			$parent		 = new DistinguishedFolderIdType();
			$parent->Id	 = DistinguishedFolderIdNameType::ROOT;

			// New Properties:
			$_mailbox						 = !empty($this->config->config_data['xPortico']['mailbox']) ? $this->config->config_data['xPortico']['mailbox'] : 'lrs@bergen.kommune.no';
			$parent->Mailbox				 = new StdClass;
			$parent->Mailbox->EmailAddress	 = $_mailbox;
			// End of new Props.

			$request->ParentFolderIds->DistinguishedFolderId[] = $parent;

			// Build the restriction that will search for folders containing "Cal".
			$contains						 = new \jamesiarmes\PhpEws\Type\ContainsExpressionType();
			$contains->FieldURI				 = new PathToUnindexedFieldType();
			$contains->FieldURI->FieldURI	 = UnindexedFieldURIType::FOLDER_DISPLAY_NAME;
			$contains->Constant				 = new ConstantValueType();
			$contains->Constant->Value		 = $folder_name;
			$contains->ContainmentComparison = ContainmentComparisonType::EXACT;
			$contains->ContainmentMode		 = ContainmentModeType::EXACT_PHRASE;
			$request->Restriction->Contains	 = $contains;

			$response = $client->FindFolder($request);

			// Iterate over the results, printing any error messages or folder names and
			// ids.
			$response_messages = $response->ResponseMessages->FindFolderResponseMessage;
			foreach ($response_messages as $response_message)
			{
				// Make sure the request succeeded.
				if ($response_message->ResponseClass != ResponseClassType::SUCCESS)
				{
					$code	 = $response_message->ResponseCode;
					$message = $response_message->MessageText;
					fwrite(STDERR, "Failed to find folders with \"$code: $message\"\n");
					continue;
				}

				$folders = $response_message->RootFolder->Folders->Folder;

				$folder_info = array(
					'name'		 => $folders[0]->DisplayName,
					'id'		 => $folders[0]->FolderId->Id,
					'changekey'	 => $folders[0]->FolderId->ChangeKey
				);
			}

			return $folder_info;
		}

		function handle_message( $item3, $folder_rules = array() )
		{
			$sender		 = $item3->Sender->Mailbox->EmailAddress;
			$target		 = array();
			$subject	 = $item3->Subject;
			$body		 = $item3->Body->_;
			$body_type	 = $item3->Body->BodyType; //'HTML' or 'Text'
//			_debug_array($body_type);
//			echo $this->clean_html( $body );
//			return;

			/**
			 * Regelsett 1
			 */
			if ($folder_rules)
			{
				$message_cat_id	 = $folder_rules['message_cat_id'];
				$group_id		 = $folder_rules['group_id'];
				$priority		 = !empty($folder_rules['priority']) ? (int)$folder_rules['priority'] : 3;

				if (!empty($folder_rules['subject']))
				{
					$subject = "{$folder_rules['subject']}::{$subject}";
				}

				$ticket_id = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type, $priority);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			/**
			 * Regelsett 2
			 */
			else if (preg_match("/\[PorticoTicket/", $subject))
			{
				preg_match_all("/\[[^\]]*\]/", $subject, $matches);
				$identificator_str	 = trim($matches[0][0], "[]");
				$identificator_arr	 = explode("::", $identificator_str);

				$ticket_id = $this->update_external_communication($identificator_arr, $body, $sender, $body_type);

				if ($ticket_id)
				{
					$target['type']	 = 'helpdesk';
					$target['id']	 = $ticket_id;
				}
			}
			else if (preg_match("/helpdesk@bergen.kommune.no/i", $sender))
			{

				$message_cat_id	 = 302; // Fra postmottak LRS
				$group_id		 = 4169; //LRS-SERVICE_Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/(penger@prest.no|trekkliste@forskerforbundet.no|post@akademikerforbundet.no|trekklister@nito.no|post@ergoterapeutene.org|trekklister@parat.com|skolenes@skolenes.no|post@dnmf.no|trekklister@bibforb.no|trekklister@lederne.no|rune.nielsen11@gmail.com|medlemsservice@musikerorg.no|post@skolelederforbundet.no|anne@matomsorg.no|sekretariatet@samfunnsokonomene.no|firmapost@elogitbergen.com|hege.tollefsen@lederne.no|fana.fagforbundet@gmail.com|Solfrid.Alfredsen@fo.no)/i", $sender))
			{
				//Send til Lønn- Trekk - Emnefelt=Fagforening
				$message_cat_id	 = 254; // Trekk (IKKE ferie)
				$group_id		 = 4253; //LRS-Drift_Regnskap
				$ticket_id		 = $this->create_ticket("Fagforening::{$subject}", $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/(@SPK.no|@klp.no|@bergenkp.no)/i", $sender))
			{
				$message_cat_id	 = 244; // til Lønn -Pensjon
				$group_id		 = 3159; //LRS-DRIFT_Lønn
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Manglende informasjon på mottatt/i", $subject))
			{

				$message_cat_id	 = 319; // Faktura til Bg. Kommune- underkategori: Firewall (aut opprettede meldinger).
				$group_id		 = 4169; //LRS-Saksbehandler-Økonomi
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/(Innkassokrav|Inkassokrav)/i", $subject))
			{
				$message_cat_id	 = 321; // 24 Purringer/Inkasso
				$group_id		 = 4253; //LRS-Drift_Regnskap
				$priority		 = 1;
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type, $priority);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Purring\/Inkassovarsel/i", $subject))
			{
				$message_cat_id	 = 321; // 24 Purringer/Inkasso
				$group_id		 = 4253; //LRS-Drift_Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Mottatt faktura blir ikke behandlet/i", $subject))
			{

				$message_cat_id	 = 319; // Faktura til Bg. Kommune- underkategori: Firewall (aut opprettede meldinger).
				$group_id		 = 4169; //LRS-Saksbehandler-Økonomi
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Faktura avvises og vil ikke bli betalt/i", $subject))
			{

				$message_cat_id	 = 319; // Faktura til Bg. Kommune- underkategori: Firewall (aut opprettede meldinger).
				$group_id		 = 4169; //LRS-Saksbehandler-Økonomi
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/gangs purring/i", $subject))
			{

				$message_cat_id	 = 319; // Faktura til Bg. Kommune- underkategori: Firewall (aut opprettede meldinger).
				$group_id		 = 4169; //LRS-Saksbehandler-Økonomi
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Kreditnota/i", $subject))
			{

				$message_cat_id	 = 319; // Faktura til Bergen kommune- underkategori: Firewall-Fakturaavvik (Automatisk generert fra Firewall).
				$group_id		 = 4169; // LRS-SERVICE_Økonomi
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/ny leverandør/i", $subject))
			{
				$message_cat_id	 = 319; // LRS-Regnskap- underkategori: 24 Firewall-Fakturaavvik
				$group_id		 = 4253; //LRS-Drift_Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/endring av leverandør/i", $subject))
			{
				$message_cat_id	 = 319; // LRS-Regnskap- underkategori: 24 Firewall-Fakturaavvik
				$group_id		 = 4253; //LRS-Drift_Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/nye leverandører /i", $subject))
			{
				$message_cat_id	 = 319; // LRS-Regnskap- underkategori: 24 Firewall-Fakturaavvik
				$group_id		 = 4253; //LRS-Drift_Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Varsel om mulig motregning mellom kunde - lev for utbetaling av Vrakpant/i", $subject))
			{
				$message_cat_id	 = 281; // LRS-Regnskap  -  20 Fakturering til kunde
				$group_id		 = 4253; //LRS- Drift _Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Betalingsplan på tvers av oppdragsgivere/i", $subject))
			{
				$message_cat_id	 = 281; // LRS-Regnskap  -  20 Fakturering til kunde
				$group_id		 = 4253; //LRS- Drift _Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Din salgsordre til EHF-kunde - mangler utfylling i feltet \"Ekstern referanse - Deres ref/i", $subject))
			{
				$message_cat_id	 = 281; // LRS-Regnskap  -  20 Fakturering til kunde
				$group_id		 = 4253; //LRS- Drift _Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Salgsordre med Kostra art 690 fordelte utgifter - internsalg til eksterne kunder/i", $subject))
			{
				$message_cat_id	 = 281; // LRS-Regnskap  -  20 Fakturering til kunde
				$group_id		 = 4253; //LRS- Drift _Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Salgsordre er registrert med feil Kontaktinfo - Kontakt Bergen kommune/i", $subject))
			{
				$message_cat_id	 = 281; // LRS-Regnskap  -  20 Fakturering til kunde
				$group_id		 = 4253; //LRS- Drift _Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Salgsordre er registrert med feil tlf 5556 5556/i", $subject))
			{
				$message_cat_id	 = 281; // LRS-Regnskap  -  20 Fakturering til kunde
				$group_id		 = 4253; //LRS- Drift _Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Salgsordre har mangler i feltet Beskrivelse på artikkel/i", $subject))
			{
				$message_cat_id	 = 281; // LRS-Regnskap  -  20 Fakturering til kunde
				$group_id		 = 4253; //LRS- Drift _Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Manglende informasjon på kunde - må rettes av Agresso kunde/i", $subject))
			{
				$message_cat_id	 = 281; // LRS-Regnskap  -  20 Fakturering til kunde
				$group_id		 = 4253; //LRS- Drift _Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Kreditering av salgsordre er registrert feil/i", $subject))
			{
				$message_cat_id	 = 281; // LRS-Regnskap  -  20 Fakturering til kunde
				$group_id		 = 4253; //LRS- Drift _Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Kunder med utenlandsk adresse som har post nr eller sted/i", $subject))
			{
				$message_cat_id	 = 281; // LRS-Regnskap  -  20 Fakturering til kunde
				$group_id		 = 4253; //LRS- Drift _Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Varsel om mulig motregning mellom kunde/i", $subject))
			{
				$message_cat_id	 = 284; // LRS-Regnskap- underkategori: 22 Innbetalinger fra kunde
				$group_id		 = 4253; //LRS-Drift_Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Utbetalingsvedtak av/i", $subject))
			{
				$message_cat_id	 = 284; // LRS-Regnskap- underkategori: 22 Innbetalinger fra kunde
				$group_id		 = 4253; //LRS-Drift_Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Fakturaavvik: EDI, returnert/i", $subject))
			{
				$message_cat_id	 = 281; // LRS-Regnskap- underkategori: 20 Fakturering til kunde
				$group_id		 = 4253; //LRS-Drift_Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Elektronisk Adresseoppdatering fra Posten/i", $subject))
			{
				$message_cat_id	 = 281; // LRS-Regnskap- underkategori: 20 Fakturering til kunde
				$group_id		 = 4253; //LRS-Drift_Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/E-post fra DNB BANK ASA/i", $subject))
			{
				$message_cat_id	 = 281; // LRS-Regnskap- underkategori: 20 Fakturering til kunde
				$group_id		 = 4253; //LRS-Drift_Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Utbetalingsrapport/i", $subject))
			{
				$message_cat_id	 = 284; // LRS-Regnskap- underkategori: 22 Innbetalinger fra kunde
				$group_id		 = 4253; //LRS-Drift_Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Fakturaavvik: Ukjent bestilling, returnert /i", $subject))
			{
				$message_cat_id	 = 281; // LRS-Regnskap- underkategori: 20 Fakturering til kunde
				$group_id		 = 4253; //LRS-Drift_Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Faktura må behandles – Purring mottatt/i", $subject))
			{
				$message_cat_id	 = 321; // LRS-Regnskap- underkategori: 24 Purringer/Inkasso
				$group_id		 = 4253; //LRS-Drift_Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Fakturakopi etterspørres - Bergen kommune/i", $subject))
			{
				$message_cat_id	 = 321; // LRS-Regnskap- underkategori: 24 Purringer/Inkasso
				$group_id		 = 4253; //LRS-Drift_Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			/**
			 * Nye regler 14. februar 2020
			 */
			else if (preg_match("/BOEi filer/i", $subject))
			{
				$message_cat_id	 = 362; // LRS-Intern- underkategori: filer til bokføring(IA varsel)
				$group_id		 = 4253; //LRS-Drift_Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Innlesing av lønnsfil/i", $subject))
			{
				$message_cat_id	 = 362; // LRS-Intern- underkategori: filer til bokføring(IA varsel)
				$group_id		 = 4253; //LRS-Drift_Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Innlesing av BOEI hovedboksfil/i", $subject))
			{
				$message_cat_id	 = 362; // LRS-Intern- underkategori: filer til bokføring(IA varsel)
				$group_id		 = 4253; //LRS-Drift_Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Innlesing av fil fra PARKA/i", $subject))
			{
				$message_cat_id	 = 362; // LRS-Intern- underkategori: filer til bokføring(IA varsel)
				$group_id		 = 4253; //LRS-Drift_Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Innlesing av fil fra PILEN/i", $subject))
			{
				$message_cat_id	 = 362; // LRS-Intern- underkategori: filer til bokføring(IA varsel)
				$group_id		 = 4253; //LRS-Drift_Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Innlesing av refusjonsfil/i", $subject))
			{
				$message_cat_id	 = 362; // LRS-Intern- underkategori: filer til bokføring(IA varsel)
				$group_id		 = 4253; //LRS-Drift_Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Ikke bokførte bunter fra Sosio pr/i", $subject))
			{
				$message_cat_id	 = 362; // LRS-Intern- underkategori: filer til bokføring(IA varsel)
				$group_id		 = 4253; //LRS-Drift_Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Ikke bokførte bunter fra SoSys pr/i", $subject))
			{
				$message_cat_id	 = 362; // LRS-Intern- underkategori: filer til bokføring(IA varsel)
				$group_id		 = 4253; //LRS-Drift_Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Det er registrert duplikater eller feil i prekonteringsdetaljer/i", $subject))
			{
				$message_cat_id	 = 363; // LRS-Intern- underkategori: Prekontering
				$group_id		 = 4253; //LRS-Drift_Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Doble begrepsverdier/i", $subject))
			{
				$message_cat_id	 = 358; // LRS-System  -  Intel Agent
				$group_id		 = 4252; //LRS-System_Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Ressurser i Agresso med samme personnummer/i", $subject))
			{
				$message_cat_id	 = 358; // LRS-System  -  Intel Agent
				$group_id		 = 4252; //LRS-System_Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/EI02 - varsling ved nye forekomster i acrxmlimport/i", $subject))
			{
				$message_cat_id	 = 358; // LRS-System  -  Intel Agent
				$group_id		 = 4252; //LRS-System_Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Det er nye filer i AbwError/i", $subject))
			{
				$message_cat_id	 = 358; // LRS-System  -  Intel Agent
				$group_id		 = 4252; //LRS-System_Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Varsel om feilid 4 for EI02 variant 31/i", $subject))
			{
				$message_cat_id	 = 358; // LRS-System  -  Intel Agent
				$group_id		 = 4252; //LRS-System_Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Varsel om PR43 som feiler pga flere rader på samme ressurs/i", $subject))
			{
				$message_cat_id	 = 358; // LRS-System  -  Intel Agent
				$group_id		 = 4252; //LRS-System_Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Varsel om feil i kvitteringsfil fra Multikanal/i", $subject))
			{
				$message_cat_id	 = 358; // LRS-System  -  Intel Agent
				$group_id		 = 4252; //LRS-System_Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Prosjektregisteret- feil oppdatering av hovedprosjekt/i", $subject))
			{
				$message_cat_id	 = 358; // LRS-System  -  Intel Agent
				$group_id		 = 4252; //LRS-System_Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Manglende adresseoppsett nye landkoder/i", $subject))
			{
				$message_cat_id	 = 358; // LRS-System  -  Intel Agent
				$group_id		 = 4252; //LRS-System_Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Varsling - brukere som mangler overordnet/i", $subject))
			{
				$message_cat_id	 = 357; // LRS-Regnskap- underkategori: 16 Hjelp til annet
				$group_id		 = 4169; //LRS-Service_Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Ressurser som ligger igjen i ajourhold ressurser etter PR43/i", $subject))
			{
				$message_cat_id	 = 357; // LRS-Regnskap- underkategori: 16 Hjelp til annet
				$group_id		 = 4169; //LRS-Service_Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Faktura med status ulik N som må kontrolleres og leses inn/i", $subject))
			{
				$message_cat_id	 = 280; // LRS-Regnskap- underkategori: 24 Faktura fra leverandør
				$group_id		 = 4253; //LRS-Drift_Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Tilskudd uten motregning klar for bokføring/i", $subject))
			{
				$message_cat_id	 = 280; // LRS-Regnskap- underkategori: 24 Spørsmål fra leverandører
				$group_id		 = 4253; //LRS- Drift _Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Godkjente innkjøpsfaktura mangler mva-linjer/i", $subject))
			{
				$message_cat_id	 = 280; // LRS-Regnskap- underkategori: 24 Faktura fra leverandør
				$group_id		 = 4253; //LRS- Drift _Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Ajourhold faktura har poster som må behandles/i", $subject))
			{
				$message_cat_id	 = 280; // LRS-Regnskap- underkategori: 24 Faktura fra leverandør
				$group_id		 = 4253; //LRS- Drift _Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Innkjøpsfaktura med feil leverandør ifht ordre/i", $subject))
			{
				$message_cat_id	 = 280; // LRS-Regnskap- underkategori: 24 Faktura fra leverandør
				$group_id		 = 4253; //LRS- Drift _Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Mottatt kredtitnota med feil fortegn/i", $subject))
			{
				$message_cat_id	 = 281; // LRS-Regnskap  -  20 Fakturering til kunde
				$group_id		 = 4253; //LRS- Drift _Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Varsel om nye forekomster av bilag uten MVA/i", $subject))
			{
				$message_cat_id	 = 280; // LRS-Regnskap- underkategori: 24 Faktura fra leverandør
				$group_id		 = 4253; //LRS- Drift _Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Varsling om faktura med arbeidsflyt på reskontrolinje - mva-transaksjoner/i", $subject))
			{
				$message_cat_id	 = 280; // LRS-Regnskap- underkategori: 24 Faktura fra leverandør
				$group_id		 = 4253; //LRS- Drift _Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Varsling om mottaksregistrete faktura over 200 000 (hver time)/i", $subject))
			{
				$message_cat_id	 = 280; // LRS-Regnskap- underkategori: 24 Faktura fra leverandør
				$group_id		 = 4253; //LRS- Drift _Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Digifrid trenger hjelp til å behandle purring/i", $subject))
			{
				$message_cat_id	 = 321; // LRS-Regnskap- underkategori: 24 Purringer/Inkasso
				$group_id		 = 4253; //LRS-Drift_Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Purring på faktura mottatt – Bilagsnummer/i", $subject))
			{
				$message_cat_id	 = 321; // LRS-Regnskap- underkategori: 24 Purringer/Inkasso
				$group_id		 = 4253; //LRS-Drift_Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Purring på betalt faktura - Bergen kommune/i", $subject))
			{
				$message_cat_id	 = 321; // LRS-Regnskap- underkategori: 24 Purringer/Inkasso
				$group_id		 = 4253; //LRS-Drift_Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Purring på faktura mottatt – Motregnet/i", $subject))
			{
				$message_cat_id	 = 321; // LRS-Regnskap- underkategori: 24 Purringer/Inkasso
				$group_id		 = 4253; //LRS-Drift_Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Doble ansvar i ansvarstreet/i", $subject))
			{
				$message_cat_id	 = 357; // LRS-Regnskap- underkategori: 16 Hjelp til annet
				$group_id		 = 4169; //LRS-Service_Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Varsel om behov for korrigering i leverandørregister fane/i", $subject))
			{
				$message_cat_id	 = 280; // LRS-Regnskap- underkategori: 24 Spørsmål fra leverandører
				$group_id		 = 4253; //LRS- Drift _Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Varsling - brukere der overordnet ikke er aktiv /i", $subject))
			{
				$message_cat_id	 = 357; // LRS-Regnskap- underkategori: 16 Hjelp til annet
				$group_id		 = 4253; //LRS- Drift _Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Fare for duplikate faktura/i", $subject))
			{
				$message_cat_id	 = 317; // LRS-System- underkategori: Avvik
				$group_id		 = 4252; //LRS-System
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if(preg_match("/Salgsordre med Kostra art 1790 fordelte utgifte/i" , $subject ))
			{
				$message_cat_id	 = 281; // LRS-DRIFT_Regnskap - underkategori: 20 Fakturering til kunde
				$group_id		 = 4253; //LRS-DRIFT_Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Prekontering Print, Manuell Håndtering/i", $subject))
			{
				$message_cat_id	 = 334; // LRS-Regnskap- underkategori: 28 Kostfordeling/prekontering
				$group_id		 = 4253; //LRS-DRIFT_Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}
			else if (preg_match("/Prekontering, Manuell Håndtering/i", $subject))
			{
				$message_cat_id	 = 334; // LRS-Regnskap- underkategori: 28 Kostfordeling/prekontering
				$group_id		 = 4253; //LRS-DRIFT_Regnskap
				$ticket_id		 = $this->create_ticket($subject, $body, $message_cat_id, $group_id, $sender, $body_type);
				if ($ticket_id)
				{
					$this->receipt['message'][]	 = array('msg' => "Melding #{$ticket_id} er opprettet");
					$target['type']				 = 'helpdesk';
					$target['id']				 = $ticket_id;
				}
			}

			/**
			 * Ticket created / updated
			 */
			if ($target)
			{
				$this->items_to_move[] = $item3;
			}
			return $target;
		}

		function update_external_communication( $identificator_arr, $body, $sender, $body_type )
		{
			$ticket_id	 = (int)$identificator_arr[1];
			$msg_id		 = (int)$identificator_arr[2];


			if (!$msg_id)
			{
				return false;
			}

			if ($body_type == 'HTML')
			{
				$message_arr = explode('========', $body);
				$message	 = $this->clean_html($message_arr[0]);
			}
			else
			{
				$html2text	 = createObject('phpgwapi.html2text', $body);
				$text		 = $html2text->getText();
				$message_arr = explode('========', $text);
				$message	 = phpgw::clean_value($message_arr[0]);
			}

			if (!$message)
			{
				return false;
			}

			$sender = phpgw::clean_value($sender);

			$soexternal = createObject('helpdesk.soexternal_communication');

			if ($soexternal->add_msg($msg_id, $message, $sender))
			{
				$sql		 = "SELECT assignedto"
					. " FROM phpgw_helpdesk_tickets"
					. " WHERE id = {$ticket_id}";
				$this->db->query($sql, __LINE__, __FILE__);
				$this->db->next_record();
				$assignedto	 = $this->db->f('assignedto');
				if ($assignedto)
				{
					createObject('helpdesk.boexternal_communication')->alert_assigned($msg_id);
				}

				createObject('helpdesk.sotts')->reset_views($ticket_id);
				return $ticket_id;
			}
		}

		function get_ticket( $subject )
		{
			//ISS vedlegg: vedlegg til #ID: <din WO ID>
			$subject_arr		 = explode('#', $subject);
			$id_arr				 = explode(':', $subject_arr[1]);
			$external_ticket_id	 = (int)($id_arr[1]);
			$sql				 = "SELECT id"
				. " FROM phpgw_helpdesk_tickets"
				. " WHERE external_ticket_id = {$external_ticket_id}";
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			$ticket_id			 = $this->db->f('id');
			return $ticket_id;
		}

		function clean_html( $test )
		{
			$tidy_options = array(
				'indent'						 => 2,
				'output-xhtml'					 => true,
				'drop-font-tags'				 => true,
				'clean'							 => true,
				'merge-spans'					 => true,
				'drop-proprietary-attributes'	 => true,
				'char-encoding'					 => 'utf8'
			);

//			if(!mb_check_encoding($test, 'UTF-8'))
//			{
//				$test = utf8_encode($test);
//			}

			$test = str_replace('>&nbsp;<', '><', $test);

//			if (class_exists('tidy'))
//			{
//				$tidy	 = new tidy;
//				$test	 = $tidy->repairString($test);
//				$tidy->parseString($test, $tidy_options, 'utf8');
//				$test	 = $tidy->html();
//			}

			$dom			 = new DOMDocument();
			$dom->recover	 = true;
			$dom->loadHTML($test);//, LIBXML_NOBLANKS | LIBXML_XINCLUDE  );
			$xpath			 = new DOMXPath($dom);
			$nodes			 = $xpath->query('//*[@style]');  // Find elements with a style attribute
			foreach ($nodes as $node)
			{
				$node->removeAttribute('style'); // Remove style attribute
			}
			unset($node);
			$nodes = $xpath->query('//*[@class]');  // Find elements with a class attribute
			foreach ($nodes as $node)
			{
				$node->removeAttribute('class'); // Remove class attribute
			}
			unset($node);
			$nodes = $xpath->query('//*[@lang]');  // Find elements with a lang attribute
			foreach ($nodes as $node)
			{
				$node->removeAttribute('lang'); // Remove lang attribute
			}
			unset($node);
			$nodes = $xpath->query('//*[@align]');  // Find elements with a align attribute
			foreach ($nodes as $node)
			{
				$node->removeAttribute('align'); // Remove align attribute
			}
			unset($node);
			$nodes = $xpath->query('//*[@size]');  // Find elements with a size attribute
			foreach ($nodes as $node)
			{
				$node->removeAttribute('size'); // Remove size attribute
			}
			unset($node);

			$test = $dom->saveHTML();

//			if (class_exists('tidy'))
//			{
//				$tidy	 = new tidy;
//				$tidy->parseString($test);
//				$test	 = $tidy->body();
//		//		$test =  phpgw::clean_html($test);
//			}

			return $test;
			/**
			 * HTMLpurifier is sometimes scrambling tables
			 * Need tidy...
			 */
//			return phpgw::clean_html($test);
		}

		function create_ticket( $subject, $body, $message_cat_id, $group_id, $sender, $body_type, $priority = 3 )
		{

			$pattern = "/{$sender}/i";

			if ($body_type == 'HTML')
			{
				$message_details = $this->clean_html($body);
				if (!preg_match($pattern, $message_details))
				{
					$message_details .= "\n<br/>Avsender: {$sender}";
				}
			}
			else
			{
				$html2text			 = createObject('phpgwapi.html2text', $body);
				$text				 = trim($html2text->getText());
				$textAr				 = explode(PHP_EOL, $text);
				$textAr				 = array_filter($textAr, 'trim'); // remove any extra \r characters left behind
				$message_details_arr = array($subject);
				foreach ($textAr as $line)
				{
					if (preg_match("/Untitled document/", $line))
					{
						continue;
					}

					$message_details_arr[] = trim($line);
				}
				$message_details = implode(PHP_EOL, $message_details_arr);

				if (!preg_match($pattern, $message_details))
				{
					$message_details .= "\n\nAvsender: {$sender}";
				}

				$message_details = phpgw::clean_value($message_details);
			}

			if (!$message_cat_id)
			{
				return false;
			}

			$ticket_id = false;

			$subject_arr		 = explode('#', $subject);
			$id_arr				 = explode(':', $subject_arr[1]);
			$external_ticket_id	 = trim($id_arr[1]);

			$subject = phpgw::clean_value($subject);
			$sender	 = phpgw::clean_value($sender);

			if (!$message_details)
			{
				_debug_array($body);
				return false;
			}

			if ($ticket_id)
			{
				$historylog = CreateObject('phpgwapi.historylog', 'helpdesk');
				$historylog->add('C', $ticket_id, $message_details);
			}
			else
			{
				$ticket = array
					(
					'assignedto'			 => false,
					'group_id'				 => $group_id,
					'cat_id'				 => $message_cat_id,
					'priority'				 => $priority, //valgfri (1-3)
					'title'					 => $subject,
					'details'				 => $message_details,
					'external_ticket_id'	 => $external_ticket_id,
					'external_origin_email'	 => $sender
				);

				$ticket_id = CreateObject('helpdesk.botts')->add_ticket($ticket);

				try
				{
					$external_message = array(
						'type_id'			 => 1,
						'ticket_id'			 => $ticket_id,
						'subject'			 => $subject,
						'message'			 => $message_details,
						'sender'			 => $sender,
						'mail_recipients'	 => array($sender)
					);

					CreateObject('helpdesk.soexternal_communication')->add($external_message);
				}
				catch (Exception $exc)
				{
					echo $exc->getTraceAsString();
				}
			}
			return $ticket_id;
		}

		function clean_attacthment_from_temp( $saved_attachments )
		{
			foreach ($saved_attachments as $saved_attachment)
			{
				unlink($saved_attachment['tmp_name']);
			}
		}

		function add_attacthment_to_target( $target, $saved_attachments )
		{

			$target['type'];
			$target['id'];

			$bofiles = CreateObject('property.bofiles', '/helpdesk');
			foreach ($saved_attachments as $saved_attachment)
			{
				$file_name = str_replace(array(' ', '..'), array('_', '.'), $saved_attachment['name']);

				if ($file_name && $target['id'])
				{
					$to_file = "{$bofiles->fakebase}/{$target['id']}/{$file_name}";

					if ($bofiles->vfs->file_exists(array(
							'string'	 => $to_file,
							'relatives'	 => array(RELATIVE_NONE)
						)))
					{
						$this->receipt['error'][] = array('msg' => lang('This file already exists !'));
					}
					else
					{
						$bofiles->create_document_dir("{$target['id']}");
						$bofiles->vfs->override_acl = 1;

						if (!$bofiles->vfs->cp(array(
								'from'		 => $saved_attachment['tmp_name'],
								'to'		 => $to_file,
								'relatives'	 => array(RELATIVE_NONE | VFS_REAL, RELATIVE_ALL))))
						{
							$this->receipt['error'][] = array('msg' => lang('Failed to upload file !'));
						}
						$bofiles->vfs->override_acl = 0;
					}
				}
			}
		}

		function update_message( $client, $item3 )
		{
			$message_id			 = $item3->ItemId->Id;
			$message_change_key	 = $item3->ItemId->ChangeKey;

			$request = new UpdateItemType();

			$request->SendMeetingInvitationsOrCancellations	 = 'SendToNone';
			$request->MessageDisposition					 = 'SaveOnly';
			$request->ConflictResolution					 = 'AlwaysOverwrite';
			$request->ItemChanges							 = array();

			// Build out item change request.
			$change						 = new ItemChangeType();
			$change->ItemId				 = new ItemIdType();
			$change->ItemId->Id			 = $message_id;
			$change->ItemId->ChangeKey	 = $message_change_key;

			// Build the set item field object and set the item on it.
			$field						 = new SetItemFieldType();
			$field->FieldURI			 = new PathToUnindexedFieldType();
			$field->FieldURI->FieldURI	 = "message:IsRead";
			$field->Message				 = new MessageType();
			$field->Message->IsRead		 = true;

			$change->Updates->SetItemField[] = $field;
			$request->ItemChanges[]			 = $change;

			$response = $client->UpdateItem($request);
		}

		function move_message( $client, $item3, $folder_info )
		{
			$request = new MoveItemType();

			$request->ToFolderId->FolderId->Id			 = $folder_info['id'];
			$request->ToFolderId->FolderId->ChangeKey	 = $folder_info['changekey'];

			$request->ItemIds->ItemId->Id		 = $item3->ItemId->Id;
			$request->ItemIds->ItemId->ChangeKey = $item3->ItemId->ChangeKey;

			$response = $client->MoveItem($request);
		}

		function handle_attachments( $client, $attachments, $response_message2 )
		{
			$saved_attachments = array();

			$temp_dir = sys_get_temp_dir();

			// Build the request to get the attachments.
			$request3				 = new GetAttachmentType();
			$request3->AttachmentIds = new NonEmptyArrayOfRequestAttachmentIdsType();

			// Iterate over the attachments for the message.
			foreach ($attachments as $attachment_id)
			{
				$id										 = new RequestAttachmentIdType();
				$id->Id									 = $attachment_id;
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
				if ($attachment_response_message->ResponseClass != ResponseClassType::SUCCESS)
				{
					$code	 = $response_message2->ResponseCode;
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
					$tmp_name	 = tempnam($temp_dir, "xPortico");
					$handle		 = fopen($tmp_name, "w");
					fwrite($handle, $attachment->Content);
					fclose($handle);

					$saved_attachments[] = array(
						'tmp_name'	 => $tmp_name,
						'name'		 => $attachment->Name,
					);
				}
			}

			return $saved_attachments;
		}
	}