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
	 * example cron : /usr/local/bin/php -q /var/www/html/phpgroupware/property/inc/cron/cron.php default hent_epost_fra_eksterne_BK
	 * @package property
	 */
	include_class('property', 'cron_parent', 'inc/cron/');
	require_once PHPGW_SERVER_ROOT . '/phpgwapi/inc/ews/autoload.php';
//	use \jamesiarmes\PhpEws\Client;
//	use \jamesiarmes\PhpEws\Request\FindItemType;
//	use \jamesiarmes\PhpEws\Type\ItemResponseShapeType;
//	use \jamesiarmes\PhpEws\Enumeration\DefaultShapeNamesType;
//	use \jamesiarmes\PhpEws\Enumeration\BodyTypeResponseType;
//	use \jamesiarmes\PhpEws\Type\IndexedPageViewType;
//	use \jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfBaseFolderIdsType;
//	use \jamesiarmes\PhpEws\Type\DistinguishedFolderIdType;
//	use \jamesiarmes\PhpEws\Enumeration\DistinguishedFolderIdNameType;
//	use \jamesiarmes\PhpEws\Enumeration\ItemQueryTraversalType;



	//test find
use \jamesiarmes\PhpEws\Client;
use \jamesiarmes\PhpEws\Request\FindItemType;

use \jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfBaseFolderIdsType;

use \jamesiarmes\PhpEws\Enumeration\DefaultShapeNamesType;
use \jamesiarmes\PhpEws\Enumeration\DistinguishedFolderIdNameType;
use \jamesiarmes\PhpEws\Enumeration\ResponseClassType;
use \jamesiarmes\PhpEws\Enumeration\UnindexedFieldURIType;

use \jamesiarmes\PhpEws\Type\AndType;
use \jamesiarmes\PhpEws\Type\ConstantValueType;
use \jamesiarmes\PhpEws\Type\DistinguishedFolderIdType;
use \jamesiarmes\PhpEws\Type\FieldURIOrConstantType;
use \jamesiarmes\PhpEws\Type\IsGreaterThanOrEqualToType;
use \jamesiarmes\PhpEws\Type\IsLessThanOrEqualToType;
use \jamesiarmes\PhpEws\Type\ItemResponseShapeType;
use \jamesiarmes\PhpEws\Type\PathToUnindexedFieldType;
use \jamesiarmes\PhpEws\Type\RestrictionType;

use \jamesiarmes\PhpEws\Request\GetItemType;
use \jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfBaseItemIdsType;
use \jamesiarmes\PhpEws\Type\ItemIdType;
use \jamesiarmes\PhpEws\Request\GetAttachmentType;
use \jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfRequestAttachmentIdsType;
use \jamesiarmes\PhpEws\Type\RequestAttachmentIdType;


	class hent_epost_fra_eksterne_BK extends property_cron_parent
	{

		public function __construct()
		{
			parent::__construct();

			$this->function_name = get_class($this);
			$this->sub_location = lang('property');
			$this->function_msg = 'Hent epost fra eksterne';
			$this->db = & $GLOBALS['phpgw']->db;
			$this->join = & $this->db->join;
		}

		function execute()
		{
			$start = time();

			$emails = $this->find_message();
			die();
	//		$emails = $this->get_email();

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



		function get_email()
		{
			$host = 'epost.bergen.kommune.no';
			$username = 'xPortico';
			$password = 'Bergen2018';
			$username = 'hc483';
			$password = 'Fmsigg08=';
			$version = Client::VERSION_2016;

			$ews = new Client($host, $username, $password, $version);

			$request = new FindItemType();
			$itemProperties = new ItemResponseShapeType();
			$itemProperties->BaseShape = DefaultShapeNamesType::ID_ONLY;
			$itemProperties->BodyType = BodyTypeResponseType::BEST;
			$request->ItemShape = $itemProperties;

			$request->IndexedPageItemView = new IndexedPageViewType();
			$request->IndexedPageItemView->BasePoint = "Beginning";
			$request->IndexedPageItemView->Offset = 0;

			$request->ParentFolderIds = new NonEmptyArrayOfBaseFolderIdsType();
			$request->ParentFolderIds->DistinguishedFolderId = new DistinguishedFolderIdType();
			$request->ParentFolderIds->DistinguishedFolderId->Id = DistinguishedFolderIdNameType::INBOX;

			$request->Traversal = ItemQueryTraversalType::SHALLOW;
			_debug_array($request);

			try
			{
				$result = $ews->FindItem($request);
			}
			catch(Exception $e)
			{
				echo $e->getMessage();
			}

			_debug_array($result);
			die();

			return $result;

		}



		function find_message()
		{
			// Replace with the date range you want to search in. As is, this will find all
			// messages within the current calendar year.
			$start_date = new DateTime('May 1 00:00:00');
			$end_date = new DateTime('December 31 23:59:59');
			$timezone = 'Eastern Standard Time';

			// Set connection information.
			$host = 'epost.bergen.kommune.no';
			$username = 'xPortico';
			$password = 'Bergen2018';
			$username = 'hc483';
			$password = 'Fmsigg08=';
			$version = Client::VERSION_2016;
$file_destination = sys_get_temp_dir() . '/attachments';

// Make sure the destination directory exists and is writeable.
if (!file_exists($file_destination)) {
    mkdir($file_destination, 0777, true);
}

if (!is_writable($file_destination)) {
    throw new Exception("Destination $file_destination is not writable.");
}

			$client = new Client($host, $username, $password, $version);
			$client->setTimezone($timezone);

			$request = new FindItemType();
			$request->ParentFolderIds = new NonEmptyArrayOfBaseFolderIdsType();

			// Build the start date restriction.
			$greater_than = new IsGreaterThanOrEqualToType();
			$greater_than->FieldURI = new PathToUnindexedFieldType();
			$greater_than->FieldURI->FieldURI = UnindexedFieldURIType::ITEM_DATE_TIME_RECEIVED;
			$greater_than->FieldURIOrConstant = new FieldURIOrConstantType();
			$greater_than->FieldURIOrConstant->Constant = new ConstantValueType();
			$greater_than->FieldURIOrConstant->Constant->Value = $start_date->format('c');

			// Build the end date restriction;
			$less_than = new IsLessThanOrEqualToType();
			$less_than->FieldURI = new PathToUnindexedFieldType();
			$less_than->FieldURI->FieldURI = UnindexedFieldURIType::ITEM_DATE_TIME_RECEIVED;
			$less_than->FieldURIOrConstant = new FieldURIOrConstantType();
			$less_than->FieldURIOrConstant->Constant = new ConstantValueType();
			$less_than->FieldURIOrConstant->Constant->Value = $end_date->format('c');

			// Build the restriction.
			$request->Restriction = new RestrictionType();
			$request->Restriction->And = new AndType();
			$request->Restriction->And->IsGreaterThanOrEqualTo = $greater_than;
			$request->Restriction->And->IsLessThanOrEqualTo = $less_than;

			// Return all message properties.
			$request->ItemShape = new ItemResponseShapeType();
			$request->ItemShape->BaseShape = DefaultShapeNamesType::ALL_PROPERTIES;

			// Search in the user's inbox.
			$folder_id = new DistinguishedFolderIdType();
			$folder_id->Id = DistinguishedFolderIdNameType::INBOX;
			$request->ParentFolderIds->DistinguishedFolderId[] = $folder_id;

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
//					_debug_array($item->ItemId->Id);
					_debug_array($item->Subject);

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
							//_debug_array((string)$item3->Body->_);
                                $rool =$item3->Body->_;
                                $text_message  = array('text' => $rool);
                                $newArray = array_map(function($v)
								{
									return trim(strip_tags($v));
                                 }, $text_message);


								 _debug_array($newArray['text']);
			//				die();
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
								$path = "$file_destination/" . $attachment->Name;
								file_put_contents($path, $attachment->Content);
								fwrite(STDOUT, "Created attachment $path\n");
							}
						}
					}
				}
			}
		}
	}