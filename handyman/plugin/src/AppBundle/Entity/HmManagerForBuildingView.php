<?php
	/**
	 * Created by PhpStorm.
	 * User: eskil.saatvedt
	 * Date: 27.03.2018
	 * Time: 09:54
	 */

	namespace AppBundle\Entity;

	use Doctrine\ORM\Mapping as ORM;

	/**
	 * FmTtsTicket
	 *
	 * @ORM\Table(name="hm_manager_for_building")
	 * @ORM\Entity(repositoryClass="AppBundle\Repository\HmManagerForBuildingRepository")
	 */
	class HmManagerForBuildingView
	{
		/**
		 * @var string
		 *
		 * @ORM\Column(name="location_code", type="string", length=8)
		 * @ORM\Id
		 */
		protected $location_code;
		/**
		 * @var string
		 *
		 * @ORM\Column(name="loc1_name", type="string", length=50)
		 */
		protected $loc1_name;
		/**
		 * @var integer
		 *
		 * @ORM\Column(name="contact_id", type="integer")
		 */
		protected $contact_id;
		/**
		 * @var string
		 *
		 * @ORM\Column(name="first_name", type="string", length=50)
		 */
		protected $first_name;
		/**
		 * @var string
		 *
		 * @ORM\Column(name="last_name", type="string", length=50)
		 */
		protected $last_name;

		/**
		 * @ORM\ManyToOne(targetEntity = "GwAccount")
		 * @ORM\JoinColumn(name = "contact_id", referencedColumnName = "person_id")
		 */
		protected $account;

		/* @var string $agresso_id */
		protected $agresso_id;

		/**
		 * @return int
		 */
		public function getContactId(): int
		{
			return $this->contact_id;
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
		public function getLoc1Name(): string
		{
			return $this->loc1_name;
		}

		/**
		 * @param string $loc1_name
		 */
		public function setLoc1Name(string $loc1_name)
		{
			$this->loc1_name = $loc1_name;
		}

		/**
		 * @return string
		 */
		public function getFirstName(): string
		{
			return $this->first_name;
		}

		/**
		 * @param string $first_name
		 */
		public function setFirstName(string $first_name)
		{
			$this->first_name = $first_name;
		}

		/**
		 * @return string
		 */
		public function getLastName(): string
		{
			return $this->last_name;
		}

		/**
		 * @param string $last_name
		 */
		public function setLastName(string $last_name)
		{
			$this->last_name = $last_name;
		}

		/**
		 * @return GwAccount
		 */
		public function getAccount(): GwAccount
		{
			return $this->account;
		}

		/**
		 * @return string
		 */
		public function getAgressoId(): string
		{
			return $this->agresso_id ?? '';
		}

		/**
		 * @param string $agresso_id
		 */
		public function setAgressoId(string $agresso_id)
		{
			$this->agresso_id = $agresso_id;
		}
	}