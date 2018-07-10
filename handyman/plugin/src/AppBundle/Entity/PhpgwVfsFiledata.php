<?php

	namespace AppBundle\Entity;

	use Doctrine\ORM\Mapping as ORM;
	use Doctrine\Common\Collections\ArrayCollection;
	use Symfony\Component\Serializer\Annotation\Groups;

	/**
	 * PhpgwVfsFiledata
	 * @ORM\Entity
	 */

	/**
	 * FmLocation1
	 *
	 * @ORM\Table(name="phpgw_vfs_filedata")
	 * @ORM\Entity
	 */
	class PhpgwVfsFiledata
	{
		/**
		 * @ORM\Column(name="file_id", type="integer")
		 * @ORM\Id
		 * @ORM\GeneratedValue(strategy="AUTO")
		 * @Groups({"rest"})
		 */
		private $id;

		/**
		 * @ORM\Column(name="metadata", type="text")
		 * @Groups({"rest"})
		 * @var string
		 */
		private $metadata;


		/**
		 * Get id
		 *
		 * @return int
		 */
		public function getId()
		{
			return $this->id;
		}

		/**
		 * Set metadata
		 *
		 * @param string $metadata
		 * @return PhpgwVfsFiledata
		 */
		public function setMetadata($metadata)
		{
			$this->metadata = $metadata;

			return $this;
		}

		/**
		 * Get metadata
		 *
		 * @return string
		 */
		public function getMetadata()
		{
			return $this->metadata;
		}
	}