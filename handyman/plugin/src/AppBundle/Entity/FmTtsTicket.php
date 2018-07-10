<?php
	/**
	 * Created by PhpStorm.
	 * User: eskil.saatvedt
	 * Date: 06.03.2018
	 * Time: 11:35
	 */

	namespace AppBundle\Entity;

	use Doctrine\ORM\Mapping as ORM;

	/**
	 * FmTtsTicket
	 *
	 * @ORM\Table(name="fm_tts_tickets")
	 * @ORM\Entity(repositoryClass="AppBundle\Repository\FmTtsTicketRepository")
	 */
	class FmTtsTicket
	{
		/**
		 * @var integer
		 *
		 * @ORM\Column(name="id", type="integer")
		 * @ORM\Id
		 * @ORM\GeneratedValue(strategy="SEQUENCE")
		 * @ORM\SequenceGenerator(sequenceName="seq_fm_tts_tickets", allocationSize=1, initialValue=1)
		 */
		protected $id;
		/**
		 * @var integer
		 *
		 * @ORM\Column(name="group_id", type="integer")
		 */
		protected $group_id;
		/**
		 * @var integer
		 *
		 * @ORM\Column(name="priority", type="integer")
		 */
		protected $priority;
		/**
		 * @var integer
		 *
		 * @ORM\Column(name="user_id", type="integer")
		 */
		protected $user_id;
		/**
		 * @var integer
		 *
		 * @ORM\Column(name="assignedto", type="integer")
		 */
		protected $assignedto;
		/**
		 * @var string
		 *
		 * @ORM\Column(name="subject", type="string", length=255)
		 */
		protected $subject;
		/**
		 * @var integer
		 *
		 * @ORM\Column(name="cat_id", type="integer")
		 */
		protected $cat_id;
	//protected $billable_hours;
		/**
		 * @var string
		 *
		 * @ORM\Column(name="status", type="string", length=3)
		 */
		protected $status;
		/**
		 * @var string
		 *
		 * @ORM\Column(name="details", type="text")
		 */
		protected $details = '';
		/**
		 * @var string
		 *
		 * @ORM\Column(name="location_code", type="string", length=28)
		 */
		protected $location_code;
	//protected $p_num;
	//protected $p_entry_id;
	//protected $p_cat_id;
		/**
		 * @var string
		 *
		 * @ORM\Column(name="loc1", type="string", length=8)
		 */
		protected $loc1;
		/**
		 * @var string
		 *
		 * @ORM\Column(name="loc2", type="string", length=4)
		 */
		protected $loc2;
	//protected $loc3;
	//protected $loc4;
	//protected $floor;
	//	/**
	//	 * @var string
	//	 *
	//	 * @ORM\Column(name="address", type="string", length=255)
	//	 */
	//	protected $address;
	//protected $contact_phone;
	//protected $tenanant_id;
		/**
		 * @var integer
		 *
		 * @ORM\Column(name="entry_date", type="integer")
		 */
		protected $entry_date;

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="finnish_date", type="integer")
		 */
		protected $finnish_date;
	//protected $finnish_date2;
	//protected $loc5;
		/**
		 * @var integer
		 *
		 * @ORM\Column(name="contact_id", type="integer")
		 */
		protected $contact_id;
	//protected $order_id;
	//protected $vendor_id;
	//protected $order_descr;
	//protected $b_account_id;
	//protected $ecodimb;
	//protected $budget;
	//protected $actual_cost;
	//protected $contact_email;
	//protected $order_cat_id;
	//protected $building_part;
	//protected $order_dim1;
	//protected $publish_note;
	//protected $branch_id;
		/**
		 * @var integer
		 *
		 * @ORM\Column(name="modified_date", type="integer")
		 */
		protected $modified_date;
	//protected $external_project_id;
	//protected $actual_cost_year;
	//protected $contract_id;
	//protected $service_id;
	//protected $tax_code;
	//protected $unspsc_code;
	//protected $order_sent;
	//protected $order_recieved;
	//protected $order_recived_amount;
	//protected $order_by;
	//protected $mail_recipients;
	//protected $file_attachments;
	//protected $delivery_address;
	//protected $continuous;
	//protected $order_deadeline;
	//protected $invoice_remark;

		/**
		 * Added with property version 0.9.17.727
		 * @var integer
		 *
		 * @ORM\Column(name="handyman_checklist_id", type="integer")
		 */
		protected $handyman_checklist_id;

		/**
		 * Added with property version 0.9.17.727
		 * @var integer
		 *
		 * @ORM\Column(name="handyman_order_number", type="integer")
		 */
		protected $handyman_order_number;

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="document_required", type="integer")
		 */
		protected $document_required;

		public function set_default_values()
		{
			// 14 = Teknisk drift
			$this->group_id = 14;
			// 10 Bygg Teknisk
			$this->cat_id = 10;
			// status = 'O': ny melding, 4: Hos teknisk person på bygget
			$this->status = 'O'; // as in Oscar for Open ticket
			$this->priority = 1; // 1= High, 2 = Medium, 3 = Low
			$this->entry_date = time();
			$this->finnish_date = 0;
		}

		/**
		 * @return int
		 */
		public function getId(): int
		{
			return $this->id;
		}

		/**
		 * @param int $id
		 */
		public function setId(int $id)
		{
			$this->id = $id;
		}

		/**
		 * @return int
		 */
		public function getGroupId(): int
		{
			return $this->group_id;
		}

		/**
		 * @param int $group_id
		 */
		public function setGroupId(int $group_id)
		{
			$this->group_id = $group_id;
		}

		/**
		 * @return int
		 */
		public function getPriority(): int
		{
			return $this->priority;
		}

		/**
		 * @param int $priority
		 */
		public function setPriority(int $priority)
		{
			$this->priority = $priority;
		}

		/**
		 * @return int
		 */
		public function getUserId(): int
		{
			return $this->user_id;
		}

		/**
		 * @param int $user_id
		 */
		public function setUserId(int $user_id)
		{
			$this->user_id = $user_id;
		}

		/**
		 * @return int
		 */
		public function getAssignedto(): int
		{
			return $this->assignedto;
		}

		/**
		 * @param int $assignedto
		 */
		public function setAssignedto(int $assignedto)
		{
			$this->assignedto = $assignedto;
		}

		/**
		 * @return string
		 */
		public function getSubject(): string
		{
			return $this->subject;
		}

		/**
		 * @param string $subject
		 */
		public function setSubject(string $subject)
		{
			$this->subject = $subject;
		}

		/**
		 * @return int
		 */
		public function getCatId(): int
		{
			return $this->cat_id;
		}

		/**
		 * @param int $cat_id
		 */
		public function setCatId(int $cat_id)
		{
			$this->cat_id = $cat_id;
		}

		/**
		 * @return string
		 */
		public function getStatus(): string
		{
			return $this->status;
		}

		/**
		 * @param string $status
		 */
		public function setStatus(string $status)
		{
			$this->status = $status;
		}

		/**
		 * @return string
		 */
		public function getDetails(): string
		{
			return $this->details;
		}

		/**
		 * @param string $details
		 */
		public function setDetails(string $details)
		{
			$this->details = $details;
		}

		/**
		 * @return string
		 */
		public function getLocationCode(): string
		{
			return $this->location_code;
		}

		/**
		 * @param string $location_code
		 */
		public function setLocationCode(string $location_code)
		{
			$this->location_code = $location_code;
		}

		/**
		 * @return string
		 */
		public function getLoc1(): string
		{
			return $this->loc1;
		}

		/**
		 * @param string $loc1
		 */
		public function setLoc1(string $loc1)
		{
			$this->loc1 = $loc1;
		}

		/**
		 * @return string
		 */
		public function getLoc2()
		{
			return $this->loc2;
		}

		/**
		 * @param string $loc2
		 */
		public function setLoc2(string $loc2)
		{
			$this->loc2 = $loc2;
		}

		/**
		 * @return int
		 */
		public function getEntryDate(): int
		{
			return $this->entry_date;
		}

		/**
		 * @param int $entry_date
		 */
		public function setEntryDate(int $entry_date)
		{
			$this->entry_date = $entry_date;
		}

		/**
		 * @return int
		 */
		public function getFinnishDate(): int
		{
			return $this->finnish_date;
		}

		/**
		 * @param int $finnish_date
		 */
		public function setFinnishDate(int $finnish_date)
		{
			$this->finnish_date = $finnish_date;
		}

		/**
		 * @return int
		 */
		public function getContactId(): int
		{
			return $this->contact_id;
		}

		/**
		 * @param int $contact_id
		 */
		public function setContactId(int $contact_id)
		{
			$this->contact_id = $contact_id;
		}

		/**
		 * @return int
		 */
		public function getModifiedDate(): int
		{
			return $this->modified_date;
		}

		/**
		 * @param int $modified_date
		 */
		public function setModifiedDate(int $modified_date)
		{
			$this->modified_date = $modified_date;
		}

		/**
		 * @return int
		 */
		public function getHandymanChecklistId(): int
		{
			return $this->handyman_checklist_id;
		}

		/**
		 * @param int $handyman_checklist_id
		 */
		public function setHandymanChecklistId(int $handyman_checklist_id)
		{
			$this->handyman_checklist_id = $handyman_checklist_id;
		}

		/**
		 * @return int
		 */
		public function getHandymanOrderNumber(): int
		{
			return $this->handyman_order_number;
		}

		/**
		 * @param int $handyman_order_number
		 */
		public function setHandymanOrderNumber(int $handyman_order_number)
		{
			$this->handyman_order_number = $handyman_order_number;
		}

		/**
		 * @return int
		 */
		public function getDocumentRequired()
		{
			return $this->document_required;
		}

		/**
		 * @param int $document_required
		 */
		public function setDocumentRequired(int $document_required)
		{
			$this->document_required = $document_required;
		}
	}
