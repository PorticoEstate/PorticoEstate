<?php
	/**
	 * Created by PhpStorm.
	 * User: eskil.saatvedt
	 * Date: 20.04.2018
	 * Time: 15:14
	 */

	namespace AppBundle\Entity;

	use Doctrine\ORM\Mapping as ORM;

	/**
	 * FmTtsTicket
	 *
	 * @ORM\Table(name="fm_gab_location")
	 * @ORM\Entity
	 */
	class FmGab
	{
		//region Properties
		/**
		 * @var string
		 * @ORM\Id	 *
		 * @ORM\Column(name="location_code", type="string", nullable=false, length=20))
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
		protected  $loc2;
		/**
		 * @var string
		 *
		 * @ORM\Column(name="address", type="string", length=150)
		 */
		protected $address;
		/**
		 * @var string
		 *
		 * @ORM\Column(name="gab_id", type="string", length=20)
		 */
		protected $gab_id;
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
		public function getAddress(): string
		{
			return $this->address;
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
		public function getGabId(): string
		{
			return $this->gab_id;
		}

		/**
		 * @param string $gab_id
		 */
		public function setGabId(string $gab_id)
		{
			$this->gab_id = $gab_id;
		}

		/**
		 * @return array Gab code on the form xxxxx/xxxx/xxxx/xxx
		 */
		public function getFormattedGab(): array
		{
			return array('gnr'=>substr($this->gab_id, 4, 5), 'bnr'=>substr($this->gab_id, 9, 4),'fnr'=>substr($this->gab_id, 13, 4),'sek'=>substr($this->gab_id, 17, 3));
		}
		//endregion
	}