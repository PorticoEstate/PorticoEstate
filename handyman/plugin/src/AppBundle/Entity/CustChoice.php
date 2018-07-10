<?php
	/**
	 * Created by PhpStorm.
	 * User: eskil.saatvedt
	 * Date: 23.02.2018
	 * Time: 14:59
	 */

	namespace AppBundle\Entity;

	use Doctrine\ORM\Mapping as ORM;
	use Doctrine\ORM\ArrayCollection as ArrayCollection;

	/**
	 * FmOwner
	 * @ORM\Entity
	 * @ORM\Table(name="phpgw_cust_choice")
	 */
	class CustChoice
	{
		//region Properties
		/**
		 * @var integer
		 *
		 * @ORM\Column(name="location_id", type="integer")
		 * @ORM\Id
		 */
		private $location_id;
		/**
		 * @var integer
		 *
		 * @ORM\Column(name="attrib_id", type="integer")
		 * @ORM\Id
		 */
		private $attribId;
		/**
		 * @var integer
		 *
		 * @ORM\Column(name="id", type="integer")
		 * @ORM\Id
		 */
		private $id;
		/**
		 * @var string
		 *
		 * @ORM\Column(name="value", type="text")
		 */
		private $value;
		/**
		 * @var integer
		 *
		 * @ORM\Column(name="choice_sort", type="integer")
		 */
		private $sort;
		/**
		 * @var string
		 *
		 * @ORM\Column(name="title", type="text")
		 */
		private $title;

		/**
		 * @ORM\ManyToOne(
		 *      targetEntity="CustAttribute",
		 *      inversedBy="custChoices",
		 * )
		 * @ORM\JoinColumns({
		 *   @ORM\JoinColumn(name="attrib_id", referencedColumnName="id"),
		 *   @ORM\JoinColumn(name="location_id", referencedColumnName="location_id")
		 * })
		 * @ORM\OrderBy({"sort" = "ASC"})
		 */
		private $custAttribute;
		//endregion

		//region Getters and setters
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
		 * Get locationId
		 *
		 * @return integer
		 */
		public function getLocationId()
		{
			return $this->location_id;
		}

		/**
		 * Get attribId
		 *
		 * @return integer
		 */
		public function getAttribId()
		{
			return $this->attribId;
		}

		/**
		 * @return string
		 */
		public function getValue(): string
		{
			return $this->value;
		}

		/**
		 * @return integer
		 */
		public function getSort(): int
		{
			return $this->sort;
		}

		/**
		 * @return string
		 */
		public function getTitle(): string
		{
			return $this->title;
		}

		/**
		 * @return CustAttribute
		 */
		public function getCustAttribute(): CustAttribute
		{
			return $this->custAttribute;
		}
		//endregion
	}