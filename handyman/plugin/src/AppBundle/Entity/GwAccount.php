<?php
	/**
	 * Created by PhpStorm.
	 * User: eskil.saatvedt
	 * Date: 23.04.2018
	 * Time: 08:39
	 */

	namespace AppBundle\Entity;

	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\ORM\Mapping as ORM;

	/**
	 * FmTenant
	 *
	 * @ORM\Table(name="phpgw_accounts")
	 * @ORM\Entity
	 */
	class GwAccount
	{
		/**
		 * @var integer
		 *
		 * @ORM\Column(name="account_id", type="integer")
		 * @ORM\Id
		 */
		protected $account_id;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="account_firstname", type="string", length=50)
		 */
		protected $account_firstname;
		/**
		 * @var string
		 *
		 * @ORM\Column(name="account_lastname", type="string", length=50)
		 */
		protected $account_lastname;
		/**
		 * @var integer
		 *
		 * @ORM\Column(name="person_id", type="integer")
		 */
		protected $person_id;

		/**
		 * @return int
		 */
		public function getAccountId(): int
		{
			return $this->account_id;
		}

		/**
		 * @param int $account_id
		 */
		public function setAccountId(int $account_id)
		{
			$this->account_id = $account_id;
		}

		/**
		 * @return string
		 */
		public function getAccountFirstname(): string
		{
			return $this->account_firstname;
		}

		/**
		 * @param string $account_firstname
		 */
		public function setAccountFirstname(string $account_firstname)
		{
			$this->account_firstname = $account_firstname;
		}

		/**
		 * @return string
		 */
		public function getAccountLastname(): string
		{
			return $this->account_lastname;
		}

		/**
		 * @param string $account_lastname
		 */
		public function setAccountLastname(string $account_lastname)
		{
			$this->account_lastname = $account_lastname;
		}

		/**
		 * @return int
		 */
		public function getPersonId(): int
		{
			return $this->person_id;
		}

		/**
		 * @param int $person_id
		 */
		public function setPersonId(int $person_id)
		{
			$this->person_id = $person_id;
		}
	}