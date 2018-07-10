<?php

	namespace AppBundle\Entity;

	use Doctrine\ORM\Mapping as ORM;

	/**
	 * FmOwnerCategory
	 *
	 * @ORM\Table(name="fm_owner_category")
	 * @ORM\Entity
	 */
	class FmOwnerCategory
	{
		/**
		 * @var integer
		 *
		 * @ORM\Column(name="id", type="integer")
		 * @ORM\Id
		 * @ORM\GeneratedValue(strategy="SEQUENCE")
		 * @ORM\SequenceGenerator(sequenceName="fm_owner_category_id_seq", allocationSize=1, initialValue=1)
		 */
		private $id;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="descr", type="string", length=255, nullable=false)
		 */
		private $descr;



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
		 * Set descr
		 *
		 * @param string $descr
		 *
		 * @return FmOwnerCategory
		 */
		public function setDescr($descr)
		{
			$this->descr = $descr;

			return $this;
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
	}
