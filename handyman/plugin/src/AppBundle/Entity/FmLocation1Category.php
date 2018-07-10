<?php

	namespace AppBundle\Entity;

	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Serializer\Annotation\Groups;

	/**
	 * FmLocation1Category
	 *
	 * @ORM\Table(name="fm_location1_category")
	 * @ORM\Entity
	 */
	class FmLocation1Category
	{
		/**
		 * @var integer
		 *
		 * @ORM\Column(type="integer")
		 * @ORM\Id
		 * @ORM\GeneratedValue(strategy="AUTO")
		 * @Groups({"rest"})
		 */
		private $id;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="descr", type="string", length=255, nullable=false)
		 * @Groups({"rest"})
		 */
		private $descr;

		/**
		 * @return int
		 */
		public function getId(): int
		{
			return $this->id;
		}

		/**
		 * @return string
		 */
		public function getDescr(): string
		{
			return $this->descr;
		}

		/**
		 * @param string $descr
		 *@return FmLocation1Category
		 */
		public function setDescr(string $descr)
		{
			$this->descr = $descr;
			return $this;
		}

		/**
		 * Add location
		 *
		 * @param \AppBundle\Entity\FmLocation1 $fmlocation1
		 *
		 * @return FmLocation1
		 */
		public function addLocation(\AppBundle\Entity\FmLocation1 $fmlocation1)
		{
			$this->locations[] = $fmlocation1;

			return $this;
		}

		/**
		 * Remove product
		 *
		 * @param \AppBundle\Entity\FmLocation1 $fmlocation1
		 */
		public function removeLocation(\AppBundle\Entity\FmLocation1 $fmlocation1)
		{
			$this->locations->removeElement($fmlocation1);
		}

		/**
		 * Get products
		 *
		 * @return \Doctrine\Common\Collections\Collection
		 */
		public function getLocations()
		{
			return $this->locations;
		}

		/**
		* @ORM\OneToMany(targetEntity="FmLocation1", mappedBy="category1")
		*/
		private $locations;

		public function __construct()
		{
			$this->locations = new ArrayCollection();
		}

	}
