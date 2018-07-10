<?php
	/**
	 * Created by PhpStorm.
	 * User: eskil.saatvedt
	 * Date: 24.04.2018
	 * Time: 09:34
	 */

	namespace AppBundle\Entity;

	use DateTime;
	use Doctrine\ORM\Mapping as ORM;

	/**
	 * FmTtsTicket
	 *
	 * @ORM\Table(name="fm_handyman_log")
	 * @ORM\Entity(repositoryClass="AppBundle\Repository\FmHandymanLogRepository")
	 */
	class FmHandymanLog
	{
		//region Properties
		/**
		 * @var integer
		 * @ORM\Id
		 * @ORM\Column(name="id", type="integer", nullable=false))
		 * @ORM\GeneratedValue(strategy="SEQUENCE")
		 * @ORM\SequenceGenerator(sequenceName="seq_fm_handyman_log", allocationSize=1, initialValue=1)
		 */
		protected $id;
		/**
		 * @var string
		 *
		 * @ORM\Column(name="comment", type="text")
		 */
		protected $comment;
		/**
		 * @var DateTime
		 *
		 * @ORM\Column(name="log_date", type="datetime", nullable=true)
		 */
		protected $log_date;
		/**
		 * @var bool
		 *
		 * @ORM\Column(name="success", type="boolean", nullable=false)
		 */
		protected $success;
		/**
		 * @var integer
		 *
		 * @ORM\Column(name="num_of_messages", type="integer")
		 */
		protected $num_of_messages;
		//endregion

		/**
		 * FmHandymanLog constructor.
		 */
		public function __construct()
		{
			$this->log_date = new DateTime();
		}
		//region Getters and setters

		/**
		 * @return int
		 */
		public function getId(): int
		{
			return $this->id;
		}

		/**
		 * @param int $id
		 */
		public function setId(int $id)
		{
			$this->id = $id;
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
		 * @return DateTime
		 */
		public function getLogDate(): DateTime
		{
			return $this->log_date;
		}

		/**
		 * @param DateTime $log_date
		 */
		public function setLogDate(DateTime $log_date)
		{
			$this->log_date = $log_date;
		}

		/**
		 * @return bool
		 */
		public function isSuccess(): bool
		{
			return !empty($this->success);
		}

		/**
		 * @param bool $success
		 */
		public function setSuccess(bool $success)
		{
			$this->success = $success;
		}

		/**
		 * @return int
		 */
		public function getNumOfMessages(): int
		{
			return $this->num_of_messages;
		}

		/**
		 * @param int $num_of_messages
		 */
		public function setNumOfMessages(int $num_of_messages)
		{
			$this->num_of_messages = $num_of_messages;
		}
		//endregion

	}