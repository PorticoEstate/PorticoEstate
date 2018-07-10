<?php
	/**
	 * Created by PhpStorm.
	 * User: eskil.saatvedt
	 * Date: 12.04.2018
	 * Time: 14:06
	 */

	namespace AppBundle\Entity;

	use DateTime;
	use Doctrine\ORM\Mapping as ORM;

	/**
	 * FmOwner
	 *
	 * @ORM\Table(name="fm_handyman_documents")
	 * @ORM\Entity
	 */
	class FmHandymanDocument
	{
		//region Properties
		/**
		 * @var integer
		 *
		 * @ORM\Column(name="id", type="integer")
		 * @ORM\Id
		 * @ORM\GeneratedValue(strategy="SEQUENCE")
		 * @ORM\SequenceGenerator(sequenceName="seq_fm_handyman_documents", allocationSize=1, initialValue=1)
		 */
		protected $id;
		/**
		 * @var integer
		 *
		 * @ORM\Column(name="hs_document_id", type="integer", nullable=false)
		 */
		protected $hs_document_id;
		/**
		 * @var string
		 *
		 * @ORM\Column(name="name", type="string", length=50, nullable=false)
		 */
		protected $name;
		/**
		 * @var string
		 *
		 * @ORM\Column(name="file_path", type="string", length=200, nullable=false)
		 */
		protected $file_path;
		/**
		 * @var string
		 *
		 * @ORM\Column(name="file_extension", type="string", length=20, nullable=false)
		 */
		protected $file_extension;
		/**
		 * @var string
		 *
		 * @ORM\Column(name="hm_installation_id", type="string", length=20, nullable=false)
		 */
		protected $hm_installation_id;
		/**
		 * @var DateTime
		 *
		 * @ORM\Column(name="created_date", type="datetime", nullable=true)
		 */
		protected $created_date;
		/**
		 * @var int
		 *
		 * @ORM\Column(name="retrieved_from_handyman", type="smallint", nullable=false)
		 */
		protected $retrieved_from_handyman = 0;
		/**
		 * @var DateTime
		 *
		 * @ORM\Column(name="retrieved_date", type="datetime", nullable=true)
		 */
		protected $retrieved_date;

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="message_id", type="integer")
		 */
		protected $message_id;
		/**
		 * @var integer
		 *
		 * @ORM\Column(name="hs_order_number", type="integer", nullable=true)
		 */
		protected $hs_order_number;
		/**
		 * @var integer
		 *
		 * @ORM\Column(name="hs_checklist_id", type="integer", nullable=true)
		 */
		protected $hs_checklist_id;
		//endregion

		//region Getters and setters
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
		public function getHsDocumentId(): int
		{
			return $this->hs_document_id;
		}

		/**
		 * @param int $hs_document_id
		 */
		public function setHsDocumentId(int $hs_document_id)
		{
			$this->hs_document_id = $hs_document_id;
		}

		/**
		 * @return string
		 */
		public function getName(): string
		{
			return $this->name;
		}

		/**
		 * @param string $name
		 */
		public function setName(string $name)
		{
			$this->name = $name;
		}

		/**
		 * @return string
		 */
		public function getFilePath(): string
		{
			return $this->file_path;
		}

		/**
		 * @param string $file_path
		 */
		public function setFilePath(string $file_path)
		{
			$this->file_path = $file_path;
		}

		/**
		 * @return string
		 */
		public function getFileExtension(): string
		{
			return $this->file_extension;
		}

		/**
		 * @param string $file_extension
		 */
		public function setFileExtension(string $file_extension)
		{
			$this->file_extension = $file_extension;
		}

		/**
		 * @return string
		 */
		public function getHmInstallationId(): string
		{
			return $this->hm_installation_id;
		}

		/**
		 * @param string $hm_installation_id
		 */
		public function setHmInstallationId(string $hm_installation_id)
		{
			$this->hm_installation_id = $hm_installation_id;
		}

		/**
		 * @return DateTime
		 */
		public function getCreatedDate(): DateTime
		{
			return $this->created_date;
		}

		/**
		 * @param DateTime $created_date
		 */
		public function setCreatedDate(DateTime $created_date)
		{
			$this->created_date = $created_date;
		}

		/**
		 * @return bool
		 */
		public function isRetrievedFromHandyman(): bool
		{
			return (bool)$this->retrieved_from_handyman;
		}

		/**
		 * @param bool $retrieved_from_handyman
		 */
		public function setRetrievedFromHandyman(bool $retrieved_from_handyman)
		{
			$this->retrieved_from_handyman = $retrieved_from_handyman ? 1 : 0;
		}

		/**
		 * @return DateTime
		 */
		public function getRetrievedDate(): DateTime
		{
			return $this->retrieved_date;
		}

		/**
		 * @param DateTime $retrieved_date
		 */
		public function setRetrievedDate(DateTime $retrieved_date)
		{
			$this->retrieved_date = $retrieved_date;
		}

		/**
		 * @return int
		 */
		public function getMessageId(): int
		{
			return $this->message_id;
		}

		/**
		 * @param int $message_id
		 */
		public function setMessageId(int $message_id)
		{
			$this->message_id = $message_id;
		}

		/**
		 * @return int
		 */
		public function getHsOrderNumber(): int
		{
			return $this->hs_order_number;
		}

		/**
		 * @param int $hs_order_number
		 */
		public function setHsOrderNumber(int $hs_order_number)
		{
			$this->hs_order_number = $hs_order_number;
		}

		/**
		 * @return int
		 */
		public function getHsChecklistId(): int
		{
			return $this->hs_checklist_id;
		}

		/**
		 * @param int $hs_checklist_id
		 */
		public function setHsChecklistId(int $hs_checklist_id)
		{
			$this->hs_checklist_id = $hs_checklist_id;
		}
		//endregion
	}