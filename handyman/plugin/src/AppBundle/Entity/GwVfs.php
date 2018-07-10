<?php
	/**
	 * Created by PhpStorm.
	 * User: eskil.saatvedt
	 * Date: 18.04.2018
	 * Time: 15:22
	 */

	namespace AppBundle\Entity;

	use DateTime;
	use Doctrine\ORM\Mapping as ORM;


	/**
	 * GwVfs
	 *
	 * @ORM\Table(name="phpgw_vfs")
	 * @ORM\Entity
	 */
	class GwVfs
	{

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="file_id", type="integer")
		 * @ORM\Id
		 * @ORM\GeneratedValue(strategy="SEQUENCE")
		 * @ORM\SequenceGenerator(sequenceName="seq_phpgw_vfs", allocationSize=1, initialValue=1)
		 */
		protected $file_id;

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="owner_id", type="integer", nullable=false)
		 */
		protected $owner_id;
		/**
		 * @var integer
		 *
		 * @ORM\Column(name="createdby_id", type="integer", nullable=true)
		 */
		protected $createdby_id;
	//	protected $modifiedby_id;
		/**
		 * @var datetime
		 *
		 * @ORM\Column(name="created", type="date", nullable=false)
		 */
		protected $created;
	//	protected $modified;
		/**
		 * @var integer
		 *
		 * @ORM\Column(name="size", type="integer", nullable=true)
		 */
		protected $size;
		/**
		 * @var string
		 *
		 * @ORM\Column(name="mime_type", type="string", length=150, nullable=true)
		 */
		protected $mime_type;
	//	protected $deleteable = 'Y';
		/**
		 * @var string
		 *
		 * @ORM\Column(name="comment", type="text", nullable=true)
		 */
		protected $comment;
		/**
		 * @var string
		 *
		 * @ORM\Column(name="app", type="string", length=25, nullable=true)
		 */
		protected $app = 'property';
		/**
		 * @var string
		 *
		 * @ORM\Column(name="directory", type="text", nullable=true)
		 */
		protected $directory;
		/**
		 * @var string
		 *
		 * @ORM\Column(name="name", type="text", nullable=false)
		 */
		protected $name;
	//	protected $link_directory;
	//	protected $link_name;
		/**
		 * @var string
		 *
		 * @ORM\Column(name="version", type="string", length=30, nullable=false)
		 */
		protected $version = '0.0.0.0';
	//	protected $content='';
	//	protected $external_id;
	//	protected $md5_sum;

		/**
		 * @return int
		 */
		public function getFileId(): int
		{
			return $this->file_id;
		}

		/**
		 * @param int $file_id
		 */
		public function setFileId(int $file_id)
		{
			$this->file_id = $file_id;
		}

		/**
		 * @return int
		 */
		public function getOwnerId(): int
		{
			return $this->owner_id;
		}

		/**
		 * @param int $owner_id
		 */
		public function setOwnerId(int $owner_id)
		{
			$this->owner_id = $owner_id;
		}

		/**
		 * @return int
		 */
		public function getCreatedbyId(): int
		{
			return $this->createdby_id;
		}

		/**
		 * @param int $createdby_id
		 */
		public function setCreatedbyId(int $createdby_id)
		{
			$this->createdby_id = $createdby_id;
		}

		/**
		 * @return datetime
		 */
		public function getCreated(): datetime
		{
			return $this->created;
		}

		/**
		 * @param datetime $created
		 */
		public function setCreated(datetime $created)
		{
			$this->created = $created;
		}

		/**
		 * @return int
		 */
		public function getSize(): int
		{
			return $this->size;
		}

		/**
		 * @param int $size
		 */
		public function setSize(int $size)
		{
			$this->size = $size;
		}

		/**
		 * @return string
		 */
		public function getMimeType(): string
		{
			return $this->mime_type;
		}

		/**
		 * @param string $mime_type
		 */
		public function setMimeType(string $mime_type)
		{
			$this->mime_type = $mime_type;
		}

		/**
		 * @return string
		 */
		public function getComment(): string
		{
			return $this->comment;
		}

		/**
		 * @param string $comment
		 */
		public function setComment(string $comment)
		{
			$this->comment = $comment;
		}

		/**
		 * @return string
		 */
		public function getApp(): string
		{
			return $this->app;
		}

		/**
		 * @param string $app
		 */
		public function setApp(string $app)
		{
			$this->app = $app;
		}

		/**
		 * @return string
		 */
		public function getDirectory(): string
		{
			return $this->directory;
		}

		/**
		 * @param string $directory
		 */
		public function setDirectory(string $directory)
		{
			$this->directory = $directory;
		}

		/**
		 * @return string
		 */
		public function getName(): string
		{
			return $this->name;
		}

		/**
		 * @param string $name
		 */
		public function setName(string $name)
		{
			$this->name = $name;
		}

		/**
		 * @return string
		 */
		public function getVersion(): string
		{
			return $this->version;
		}

		/**
		 * @param string $version
		 */
		public function setVersion(string $version)
		{
			$this->version = $version;
		}
	}