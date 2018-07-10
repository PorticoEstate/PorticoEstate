<?php
	/**
	 * Created by PhpStorm.
	 * User: eskil.saatvedt
	 * Date: 03.04.2018
	 * Time: 13:54
	 */

	namespace AppBundle\Entity;

	use Doctrine\ORM\Mapping as ORM;

	/**
	 * FmTtsTicket
	 *
	 * @ORM\Table(name="hm_building_export")
	 * @ORM\Entity
	 */
	class FmBuildingExportView
	{
		//region Properties
		/**
		 * @var string
		 *
		 * @ORM\Column(name="location_code", type="string", length=13)
		 * @ORM\Id
		 */
		protected $location_code;
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
		/**
		 * @var string
		 *
		 * @ORM\Column(name="loc2_name", type="string", length=200)
		 */
		protected $loc2_name;
		/**
		 * @var string
		 *
		 * @ORM\Column(name="merknader", type="text")
		 */
		protected $merknader;
		/**
		 * @var string
		 *
		 * @ORM\Column(name="street_number", type="string", length=5)
		 */
		protected $street_number;
		/**
		 * @var string
		 *
		 * @ORM\Column(name="address", type="string", length=150, nullable=true)
		 */
		protected $address;
		/**
		 * @var string
		 *
		 * @ORM\Column(name="category", type="string", length=100, nullable=true)
		 */
		protected $category;

		/**
		 * @var int
		 *
		 * @ORM\Column(name="postnummer", type="integer", nullable=true)
		 */
		protected $postnummer;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="poststed", type="string", length=20, nullable=true)
		 */
		protected $poststed;

		/**
		 * @var string
		 */
		protected $manager_name = '';
		/**
		 * @var string
		 */
		protected $manager_agresso_id = '';
		/**
		 * @var int
		 */
		protected $manager_user_id = null;
		/**
		 * @var int
		 */
		protected $manager_account_id = null;
		//endregion

		//region Getters and setters
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
		public function getLoc2(): string
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
		 * @return string
		 */
		public function getLoc2Name(): string
		{
			return html_entity_decode(html_entity_decode($this->loc2_name ?? ''));
		}

		/**
		 * @param string $loc2_name
		 */
		public function setLoc2Name(string $loc2_name)
		{
			$this->loc2_name = $loc2_name;
		}

		/**
		 * @return string
		 */
		public function getMerknader(): string
		{
			return $this->merknader ?? '';
		}

		/**
		 * @param string $merkander
		 */
		public function setMerknader(string $merknader)
		{
			$this->merknader = $merknader;
		}

		/**
		 * @return string
		 */
		public function getStreetNumber(): string
		{
			return $this->street_number ?? '';
		}

		/**
		 * @param string $street_number
		 */
		public function setStreetNumber(string $street_number)
		{
			$this->street_number = $street_number;
		}

		/**
		 * @return string
		 */
		public function getAddress(): string
		{
			return $this->address ?? '';
		}

		/**
		 * @param string $address
		 */
		public function setAddress(string $address)
		{
			$this->address = $address;
		}

		/**
		 * @return string
		 */
		public function getCategory(): string
		{
			return $this->category ?? '';
		}

		/**
		 * @param string $category
		 */
		public function setCategory(string $category)
		{
			$this->category = $category;
		}

		/**
		 * @return int
		 */
		public function getPostnummer()
		{
			return $this->postnummer;
		}

		/**
		 * @param int $postnummer
		 */
		public function setPostnummer(int $postnummer)
		{
			$this->postnummer = $postnummer;
		}

		/**
		 * @return string
		 */
		public function getPoststed(): string
		{
			return $this->poststed ?? '';
		}

		/**
		 * @param string $poststed
		 */
		public function setPoststed(string $poststed)
		{
			$this->poststed = $poststed;
		}

		/**
		 * @return string
		 */
		public function getManagerName(): string
		{
			return $this->manager_name;
		}

		/**
		 * @param string $manager_name
		 */
		public function setManagerName(string $manager_name)
		{
			$this->manager_name = $manager_name;
		}

		/**
		 * @return string
		 */
		public function getManagerAgressoId(): string
		{
			return $this->manager_agresso_id;
		}

		/**
		 * @param string $manager_agresso_id
		 */
		public function setManagerAgressoId(string $manager_agresso_id)
		{
			$this->manager_agresso_id = $manager_agresso_id;
		}

		/**
		 * @return int
		 */
		public function getManagerUserId()
		{
			return $this->manager_user_id;
		}

		/**
		 * @param int $manager_user_id
		 */
		public function setManagerUserId(int $manager_user_id)
		{
			$this->manager_user_id = $manager_user_id;
		}

		/**
		 * @return int
		 */
		public function getManagerAccountId()
		{
			return $this->manager_account_id;
		}

		/**
		 * @param int $manager_account_id
		 */
		public function setManagerAccountId(int $manager_account_id)
		{
			$this->manager_account_id = $manager_account_id;
		}
		//endregion
	}