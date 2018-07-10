<?php
	/**
	 * Created by PhpStorm.
	 * User: eskil.saatvedt
	 * Date: 23.02.2018
	 * Time: 10:36
	 */

	namespace AppBundle\Entity;

	use Doctrine\ORM\Mapping as ORM;
	use Doctrine\ORM\ArrayCollection as ArrayCollection;

	/**
	 * FmOwner
	 *
	 * @ORM\Table(name="phpgw_applications")
	 * @ORM\Entity(repositoryClass="AppBundle\Repository\GwApplicationRepository")
	 */
	class GwApplication
	{
		/**
		 * @var integer
		 *
		 * @ORM\Column(name="app_id", type="integer")
		 * @ORM\Id
		 * @ORM\GeneratedValue(strategy="SEQUENCE")
		 */
		private $id;

		/**
		 *
		 * @ORM\OneToMany(targetEntity="GwLocation", mappedBy="gwApp", fetch="EAGER")
		 */
		private $locations;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="app_name", type="string", length=25, nullable=false)
		 */
		private $name;

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="app_enabled", type="integer", nullable=false)
		 */
		private $enabled;

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="app_order", type="integer", nullable=false)
		 */
		private $order;

		/**
		 * @var array
		 *
		 * @ORM\Column(name="app_tables", type="simple_array", nullable=false)
		 */
		private $tables;

		public function __construct()
		{
			$this->locations = new ArrayCollection();
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
		 * Get name
		 *
		 * @return string
		 */
		public function getName()
		{
			return $this->name;
		}

		/**
		 * Get enabled
		 *
		 * @return integer
		 */
		public function getEnabled()
		{
			return $this->enabled;
		}

		/**
		 * Get order
		 *
		 * @return integer
		 */
		public function getOrder()
		{
			return $this->order;
		}

		/**
		 * Get category
		 *
		 * @return array
		 */
		public function getTables(): array
		{
			return $this->tables;
		}

		/**
		 * Get locations
		 *
		 * @return \Doctrine\ORM\ArrayCollection
		 */
		public function getLocations()
		{
			return $this->locations;
		}
	}
