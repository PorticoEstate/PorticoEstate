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
	 *
	 * @ORM\Table(name="phpgw_cust_attribute")
	 * @ORM\Entity(repositoryClass="AppBundle\Repository\CustAttributeRepository")
	 */
	class CustAttribute
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
		 * @ORM\Column(name="id", type="integer")
		 * @ORM\Id
		 */
		private $id;
		/**
		 * @var string
		 *
		 * @ORM\Column(name="column_name", type="string", length=50, nullable=false)
		 */
		private $columnName;
		/**
		 * @var string
		 *
		 * @ORM\Column(name="input_text", type="string", length=255, nullable=false)
		 */
		private $inputText;
		/**
		 * @var string
		 *
		 * @ORM\Column(name="statustext", type="string", length=255, nullable=false)
		 */
		private $statusText;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="datatype", type="string", length=10, nullable=false)
		 */
		private $dataType;

		/**
		 * @ORM\OneToMany(
		 *     targetEntity="CustChoice",
		 *     mappedBy="custAttribute",
		 *     fetch="EAGER"
		 * )
		 */
		private $custChoices;

		//endregion
		public function __construct()
		{
			$this->custChoices = new ArrayCollection();
		}

		//region Getters and setters
		/**
		 * @return int
		 */
		public function getLocationId(): int
		{
			return $this->location_id;
		}

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
		public function getColumnName(): string
		{
			return $this->columnName;
		}

		/**
		 * @return string
		 */
		public function getInputText(): string
		{
			return $this->inputText;
		}

		/**
		 * @return string
		 */
		public function getStatusText(): string
		{
			return $this->statusText;
		}

		/**
		 * @return string
		 */
		public function getDataType(): string
		{
			return $this->dataType;
		}

		/**
		 * Get custChoices
		 *
		 * @return \Doctrine\ORM\ArrayCollection
		 */
		public function getCustChoices()
		{
			return $this->custChoices;
		}
		//endregion

		public function getMinfiedArray(): array
		{
			$values = array();
			/* @var $choice CustChoice */
			foreach ($this->custChoices as $choice) {
				$values[$choice->getId()] = $choice->getValue();
			}
			return array($this->getColumnName() => array('type' => $this->getDataType(), 'values' => $values));
		}

		public static function getDefaultValue(string $datatype){
			switch($datatype){
				case 'LB': // Listbox
					return '';
					break;
				case 'R': // Multiple radio
				case 'CH': // Multiple checkbox
					return array();
					break;
				default:
					'';
			}
		}
	}