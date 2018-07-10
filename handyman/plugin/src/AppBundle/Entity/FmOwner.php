<?php

	namespace AppBundle\Entity;

	use Doctrine\ORM\Mapping as ORM;

	/**
	 * FmOwner
	 *
	 * @ORM\Table(name="fm_owner")
	 * @ORM\Entity
	 */
	class FmOwner
	{
		/**
		 * @var integer
		 *
		 * @ORM\Column(name="id", type="integer")
		 * @ORM\Id
		 * @ORM\GeneratedValue(strategy="SEQUENCE")
		 * @ORM\SequenceGenerator(sequenceName="fm_owner_id_seq", allocationSize=1, initialValue=1)
		 */
		private $id;

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="abid", type="integer", nullable=true)
		 */
		private $abid;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="org_name", type="string", length=50, nullable=true)
		 */
		private $orgName;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="contact_name", type="string", length=50, nullable=true)
		 */
		private $contactName;

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="category", type="integer", nullable=false)
		 */
		private $category;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="member_of", type="string", length=255, nullable=true)
		 */
		private $memberOf;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="remark", type="string", length=255, nullable=true)
		 */
		private $remark;

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="entry_date", type="integer", nullable=true)
		 */
		private $entryDate;

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="owner_id", type="integer", nullable=true)
		 */
		private $ownerId;



		/**
		 * Get id
		 *
		 * @return integer
		 */
		public function getId()
		{
			return $this->id;
		}

		/**
		 * Set abid
		 *
		 * @param integer $abid
		 *
		 * @return FmOwner
		 */
		public function setAbid($abid)
		{
			$this->abid = $abid;

			return $this;
		}

		/**
		 * Get abid
		 *
		 * @return integer
		 */
		public function getAbid()
		{
			return $this->abid;
		}

		/**
		 * Set orgName
		 *
		 * @param string $orgName
		 *
		 * @return FmOwner
		 */
		public function setOrgName($orgName)
		{
			$this->orgName = $orgName;

			return $this;
		}

		/**
		 * Get orgName
		 *
		 * @return string
		 */
		public function getOrgName()
		{
			return $this->orgName;
		}

		/**
		 * Set contactName
		 *
		 * @param string $contactName
		 *
		 * @return FmOwner
		 */
		public function setContactName($contactName)
		{
			$this->contactName = $contactName;

			return $this;
		}

		/**
		 * Get contactName
		 *
		 * @return string
		 */
		public function getContactName()
		{
			return $this->contactName;
		}

		/**
		 * Set category
		 *
		 * @param integer $category
		 *
		 * @return FmOwner
		 */
		public function setCategory($category)
		{
			$this->category = $category;

			return $this;
		}

		/**
		 * Get category
		 *
		 * @return integer
		 */
		public function getCategory()
		{
			return $this->category;
		}

		/**
		 * Set memberOf
		 *
		 * @param string $memberOf
		 *
		 * @return FmOwner
		 */
		public function setMemberOf($memberOf)
		{
			$this->memberOf = $memberOf;

			return $this;
		}

		/**
		 * Get memberOf
		 *
		 * @return string
		 */
		public function getMemberOf()
		{
			return $this->memberOf;
		}

		/**
		 * Set remark
		 *
		 * @param string $remark
		 *
		 * @return FmOwner
		 */
		public function setRemark($remark)
		{
			$this->remark = $remark;

			return $this;
		}

		/**
		 * Get remark
		 *
		 * @return string
		 */
		public function getRemark()
		{
			return $this->remark;
		}

		/**
		 * Set entryDate
		 *
		 * @param integer $entryDate
		 *
		 * @return FmOwner
		 */
		public function setEntryDate($entryDate)
		{
			$this->entryDate = $entryDate;

			return $this;
		}

		/**
		 * Get entryDate
		 *
		 * @return integer
		 */
		public function getEntryDate()
		{
			return $this->entryDate;
		}

		/**
		 * Set ownerId
		 *
		 * @param integer $ownerId
		 *
		 * @return FmOwner
		 */
		public function setOwnerId($ownerId)
		{
			$this->ownerId = $ownerId;

			return $this;
		}

		/**
		 * Get ownerId
		 *
		 * @return integer
		 */
		public function getOwnerId()
		{
			return $this->ownerId;
		}
	}
