<?php
	/**
	 * Created by PhpStorm.
	 * User: eskil.saatvedt
	 * Date: 23.02.2018
	 * Time: 11:39
	 */

	namespace AppBundle\Entity;

	use Doctrine\ORM\Mapping as ORM;

	/**
	 * FmOwner
	 *
	 * @ORM\Table(name="phpgw_locations")
	 * @ORM\Entity
	 */
	class GwLocation
	{
		/**
		 * @var integer
		 *
		 * @ORM\Column(name="location_id", type="integer")
		 * @ORM\Id
		 * @ORM\GeneratedValue(strategy="SEQUENCE")
		 */
		private $id;

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="app_id", type="integer", nullable=false)
		 */
		private $appId;

		/**
		 * @ORM\ManyToOne(targetEntity="GwApplication", inversedBy="locations", fetch="EAGER")
		 * @ORM\JoinColumn(name="app_id", referencedColumnName="app_id")
		 */
		private $gwApp;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="name", type="string", length=50, nullable=false)
		 */
		private $name;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="descr", type="string", length=100, nullable=false)
		 */
		private $descr;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="c_attrib_table", type="string", length=25, nullable=true)
		 */
		private $attribTable;

		public function __construct()
		{
			$this->custChoice = new ArrayCollection();
		}

		/**
		 * Get appId
		 *
		 * @return integer
		 */
		public function getAppId(): int
		{
			return $this->appId;
		}

		/**
		 * @return /GwApplication
		 */
		public function getGwApp(): GwApplication
		{
			return $this->gwApp;
		}

		/**
		 * @return string
		 */
		public function getName(): string
		{
			return $this->name;
		}

		/**
		 * @return string
		 */
		public function getDescr(): string
		{
			return $this->descr;
		}

		/**
		 * @return string
		 */
		public function getAttribTable(): string
		{
			return $this->attribTable;
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
	}