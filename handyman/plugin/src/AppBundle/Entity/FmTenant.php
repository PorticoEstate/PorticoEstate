<?php

	namespace AppBundle\Entity;

	use Doctrine\ORM\Mapping as ORM;

	/**
	 * FmTenant
	 *
	 * @ORM\Table(name="fm_tenant")
	 * @ORM\Entity
	 */
	class FmTenant
	{
		/**
		 * @var integer
		 *
		 * @ORM\Column(name="id", type="integer")
		 * @ORM\Id
		 * @ORM\GeneratedValue(strategy="SEQUENCE")
		 * @ORM\SequenceGenerator(sequenceName="fm_tenant_id_seq", allocationSize=1, initialValue=1)
		 */
		private $id;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="member_of", type="string", length=255, nullable=true)
		 */
		private $memberOf;

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="entry_date", type="integer", nullable=true)
		 */
		private $entryDate;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="first_name", type="string", length=50, nullable=true)
		 */
		private $firstName;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="last_name", type="string", length=50, nullable=true)
		 */
		private $lastName;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="contact_phone", type="string", length=15, nullable=true)
		 */
		private $contactPhone;

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="category", type="integer", nullable=true)
		 */
		private $category;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="account_lid", type="string", length=50, nullable=true)
		 */
		private $accountLid;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="account_pwd", type="string", length=32, nullable=true)
		 */
		private $accountPwd;

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="phpgw_account_id", type="integer", nullable=true)
		 */
		private $phpgwAccountId;

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="account_status", type="integer", nullable=true)
		 */
		private $accountStatus = '1';

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="owner_id", type="integer", nullable=true)
		 */
		private $ownerId;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="email", type="string", length=64, nullable=true)
		 */
		private $email;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="contact_email", type="string", length=64, nullable=true)
		 */
		private $contactEmail;



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
		 * Set memberOf
		 *
		 * @param string $memberOf
		 *
		 * @return FmTenant
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
		 * Set entryDate
		 *
		 * @param integer $entryDate
		 *
		 * @return FmTenant
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
		 * Set firstName
		 *
		 * @param string $firstName
		 *
		 * @return FmTenant
		 */
		public function setFirstName($firstName)
		{
			$this->firstName = $firstName;

			return $this;
		}

		/**
		 * Get firstName
		 *
		 * @return string
		 */
		public function getFirstName()
		{
			return $this->firstName;
		}

		/**
		 * Set lastName
		 *
		 * @param string $lastName
		 *
		 * @return FmTenant
		 */
		public function setLastName($lastName)
		{
			$this->lastName = $lastName;

			return $this;
		}

		/**
		 * Get lastName
		 *
		 * @return string
		 */
		public function getLastName()
		{
			return $this->lastName;
		}

		/**
		 * Set contactPhone
		 *
		 * @param string $contactPhone
		 *
		 * @return FmTenant
		 */
		public function setContactPhone($contactPhone)
		{
			$this->contactPhone = $contactPhone;

			return $this;
		}

		/**
		 * Get contactPhone
		 *
		 * @return string
		 */
		public function getContactPhone()
		{
			return $this->contactPhone;
		}

		/**
		 * Set category
		 *
		 * @param integer $category
		 *
		 * @return FmTenant
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
		 * Set accountLid
		 *
		 * @param string $accountLid
		 *
		 * @return FmTenant
		 */
		public function setAccountLid($accountLid)
		{
			$this->accountLid = $accountLid;

			return $this;
		}

		/**
		 * Get accountLid
		 *
		 * @return string
		 */
		public function getAccountLid()
		{
			return $this->accountLid;
		}

		/**
		 * Set accountPwd
		 *
		 * @param string $accountPwd
		 *
		 * @return FmTenant
		 */
		public function setAccountPwd($accountPwd)
		{
			$this->accountPwd = $accountPwd;

			return $this;
		}

		/**
		 * Get accountPwd
		 *
		 * @return string
		 */
		public function getAccountPwd()
		{
			return $this->accountPwd;
		}

		/**
		 * Set phpgwAccountId
		 *
		 * @param integer $phpgwAccountId
		 *
		 * @return FmTenant
		 */
		public function setPhpgwAccountId($phpgwAccountId)
		{
			$this->phpgwAccountId = $phpgwAccountId;

			return $this;
		}

		/**
		 * Get phpgwAccountId
		 *
		 * @return integer
		 */
		public function getPhpgwAccountId()
		{
			return $this->phpgwAccountId;
		}

		/**
		 * Set accountStatus
		 *
		 * @param integer $accountStatus
		 *
		 * @return FmTenant
		 */
		public function setAccountStatus($accountStatus)
		{
			$this->accountStatus = $accountStatus;

			return $this;
		}

		/**
		 * Get accountStatus
		 *
		 * @return integer
		 */
		public function getAccountStatus()
		{
			return $this->accountStatus;
		}

		/**
		 * Set ownerId
		 *
		 * @param integer $ownerId
		 *
		 * @return FmTenant
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

		/**
		 * Set email
		 *
		 * @param string $email
		 *
		 * @return FmTenant
		 */
		public function setEmail($email)
		{
			$this->email = $email;

			return $this;
		}

		/**
		 * Get email
		 *
		 * @return string
		 */
		public function getEmail()
		{
			return $this->email;
		}

		/**
		 * Set contactEmail
		 *
		 * @param string $contactEmail
		 *
		 * @return FmTenant
		 */
		public function setContactEmail($contactEmail)
		{
			$this->contactEmail = $contactEmail;

			return $this;
		}

		/**
		 * Get contactEmail
		 *
		 * @return string
		 */
		public function getContactEmail()
		{
			return $this->contactEmail;
		}
	}
