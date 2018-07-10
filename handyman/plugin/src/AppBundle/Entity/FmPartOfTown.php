<?php

	namespace AppBundle\Entity;

	use Doctrine\ORM\Mapping as ORM;

	/**
	 * FmPartOfTown
	 *
	 * @ORM\Table(name="fm_part_of_town")
	 * @ORM\Entity
	 */
	class FmPartOfTown
	{
		/**
		 * @var integer
		 *
		 * @ORM\Column(name="id", type="integer")
		 * @ORM\Id
		 * @ORM\GeneratedValue(strategy="SEQUENCE")
		 * @ORM\SequenceGenerator(sequenceName="fm_part_of_town_id_seq", allocationSize=1, initialValue=1)
		 */
		private $id = 'seq_fm_part_of_town';

		/**
		 * @var string
		 *
		 * @ORM\Column(name="name", type="string", length=20, nullable=true)
		 */
		private $name;

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="district_id", type="smallint", nullable=true)
		 */
		private $districtId;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="delivery_address", type="text", nullable=true)
		 */
		private $deliveryAddress;



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
		 * Set name
		 *
		 * @param string $name
		 *
		 * @return FmPartOfTown
		 */
		public function setName($name)
		{
			$this->name = $name;

			return $this;
		}

		/**
		 * Get name
		 *
		 * @return string
		 */
		public function getName()
		{
			return $this->name;
		}

		/**
		 * Set districtId
		 *
		 * @param integer $districtId
		 *
		 * @return FmPartOfTown
		 */
		public function setDistrictId($districtId)
		{
			$this->districtId = $districtId;

			return $this;
		}

		/**
		 * Get districtId
		 *
		 * @return integer
		 */
		public function getDistrictId()
		{
			return $this->districtId;
		}

		/**
		 * Set deliveryAddress
		 *
		 * @param string $deliveryAddress
		 *
		 * @return FmPartOfTown
		 */
		public function setDeliveryAddress($deliveryAddress)
		{
			$this->deliveryAddress = $deliveryAddress;

			return $this;
		}

		/**
		 * Get deliveryAddress
		 *
		 * @return string
		 */
		public function getDeliveryAddress()
		{
			return $this->deliveryAddress;
		}
	}
