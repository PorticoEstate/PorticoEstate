<?php

	namespace AppBundle\Entity;

	use Doctrine\ORM\Mapping as ORM;
	//use Doctrine\ORM\ArrayCollection
	use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;

	/**
	 * FmStreetaddress
	 *
	 * @ORM\Table(name="fm_streetaddress")
	 * @ORM\Entity
	 */
	class FmStreetaddress
	{
		/**
		 * @var integer
		 *
		 * @ORM\Column(name="id", type="integer")
		 * @ORM\Id
		 * @ORM\GeneratedValue(strategy="SEQUENCE")
		 * @ORM\SequenceGenerator(sequenceName="fm_streetaddress_id_seq", allocationSize=1, initialValue=1)
		 */
		private $id;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="descr", type="string", length=150, nullable=false)
		 */
		private $descr;

		/**
		 * @ORM\OneToMany(targetEntity="FmLocation2", mappedBy="street")
		 */
		private $buildings;

		/**
		 * FmStreetaddress constructor.
		 */
		public function __construct()
		{
			$this->buildings = new ArrayCollection();
		}

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
		 * Get descr
		 *
		 * @return string
		 */
		public function getDescr()
		{
			return $this->descr;
		}

		/**
		 * Get buildings
		 *
		 * @return \Doctrine\Common\Collections\Collection
		 */
		public function getBuildings(): \Doctrine\Common\Collections\Collection
		{
			return $this->buildings;
		}
	}
