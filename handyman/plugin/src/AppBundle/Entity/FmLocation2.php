<?php

	namespace AppBundle\Entity;

	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Serializer\Annotation\Groups;
	use AppBundle\Entity\FmStreetaddress;


	/**
	 * FmLocation2
	 *
	 * @ORM\Table(name="fm_location2", indexes={@ORM\Index(name="location_code_fm_location2_idx", columns={"location_code"})})
	 * @ORM\Entity(repositoryClass="AppBundle\Repository\FmLocation2Repository")
	 */
	class FmLocation2
	{
		//region Properties
		/**
		 * @var string
		 *
		 * @ORM\Column(name="loc1", type="string", length=8)
		 * @ORM\GeneratedValue(strategy="NONE")
		 * @Groups({"rest"})
		 */
		private $loc1;

		/**
		 * @ORM\ManyToOne(targetEntity="FmLocation1", inversedBy="buildings")
		 * @ORM\JoinColumn(name="loc1", referencedColumnName="loc1")
		 * @Groups({"rest"})
		 */
		private $location1;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="loc2", type="string", length=4)
		 * @ORM\GeneratedValue(strategy="NONE")
		 * @Groups({"rest"})
		 */
		private $loc2;

		/**
		 * @var string
		 * @ORM\Id
		 * @ORM\Column(name="location_code", type="string", length=13, nullable=false)
		 * @Groups({"rest"})
		 */
		private $locationCode;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="loc2_name", type="string", length=200, nullable=true)
		 * @Groups({"rest"})
		 */
		private $loc2Name;

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="entry_date", type="integer", nullable=true)
		 * @Groups({"rest"})
		 */
		private $entryDate;

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="category", type="integer", nullable=true)
		 * @Groups({"rest"})
		 */
		private $category;

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="user_id", type="integer", nullable=true)
		 * @Groups({"rest"})
		 */
		private $userId;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="merknader", type="text", nullable=true)
		 * @Groups({"rest"})
		 */
		private $merknader;

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="change_type", type="integer", nullable=true)
		 * @Groups({"rest"})
		 */
		private $changeType;

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="byggeaar", type="integer", nullable=true)
		 * @Groups({"rest"})
		 */
		private $byggeaar;

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="kostra_id", type="integer", nullable=true)
		 * @Groups({"rest"})
		 */
		private $kostraId;

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="eierform", type="integer", nullable=true)
		 * @Groups({"rest"})
		 */
		private $eierform;

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="street_id", type="integer", nullable=true)
		 * @Groups({"rest"})
		 */
		private $streetId;

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="searskilt_objekt", type="integer", nullable=true)
		 * @Groups({"rest"})
		 */
		private $searskiltObjekt;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="merknader_1", type="text", nullable=true)
		 * @Groups({"rest"})
		 */
		private $merknader1;

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="svommebasseng", type="integer", nullable=true)
		 * @Groups({"rest"})
		 */
		private $svommebasseng;

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="brannklasse", type="integer", nullable=true)
		 * @Groups({"rest"})
		 */
		private $brannklasse;

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="risikoklasse", type="integer", nullable=true)
		 * @Groups({"rest"})
		 */
		private $risikoklasse;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="merknader_3", type="text", nullable=true)
		 * @Groups({"rest"})
		 */
		private $merknader3;

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="status", type="integer", nullable=true)
		 * @Groups({"rest"})
		 */
		private $status;

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="antikvar_status", type="integer", nullable=true)
		 * @Groups({"rest"})
		 */
		private $antikvarStatus;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="street_number", type="string", length=5, nullable=true)
		 * @Groups({"rest"})
		 */
		private $streetNumber;

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="modified_by", type="integer", nullable=true)
		 * @Groups({"rest"})
		 */
		private $modifiedBy;

		/**
		 * @var \DateTime
		 *
		 * @ORM\Column(name="modified_on", type="datetime", nullable=true)
		 * @Groups({"rest"})
		 */
		private $modifiedOn = 'now()';

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="id", type="integer", nullable=true)
		 * @Groups({"rest"})
		 */
		private $id;

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="holtekategori", type="integer", nullable=true)
		 * @Groups({"rest"})
		 */
		private $holtekategori;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="bygningsnummer", type="string", length=15, nullable=true)
		 * @Groups({"rest"})
		 */
		private $bygningsnummer;

		/**
		 * @ORM\ManyToOne(targetEntity="FmStreetaddress", inversedBy="buildings")
		 * @ORM\JoinColumn(name="street_id", referencedColumnName="id")
		 * @Groups({"rest"})
		 */
		private $street;
		//endregion

		//region Getters and Setters
		/**
		 * Set loc1
		 *
		 * @param string $loc1
		 *
		 * @return FmLocation2
		 */
		public function setLoc1($loc1)
		{
			$this->loc1 = $loc1;

			return $this;
		}

		/**
		 * Get loc1
		 *
		 * @return string
		 */
		public function getLoc1()
		{
			return $this->loc1;
		}

		/**
		 * Set loc2
		 *
		 * @param string $loc2
		 *
		 * @return FmLocation2
		 */
		public function setLoc2($loc2)
		{
			$this->loc2 = $loc2;

			return $this;
		}

		/**
		 * Get loc2
		 *
		 * @return string
		 */
		public function getLoc2()
		{
			return $this->loc2;
		}

		/**
		 * Set locationCode
		 *
		 * @param string $locationCode
		 *
		 * @return FmLocation2
		 */
		public function setLocationCode($locationCode)
		{
			$this->locationCode = $locationCode;

			return $this;
		}

		/**
		 * Get locationCode
		 *
		 * @return string
		 */
		public function getLocationCode()
		{
			return $this->locationCode;
		}

		/**
		 * Set loc2Name
		 *
		 * @param string $loc2Name
		 *
		 * @return FmLocation2
		 */
		public function setLoc2Name($loc2Name)
		{
			$this->loc2Name = $loc2Name;

			return $this;
		}

		/**
		 * Get loc2Name
		 *
		 * @return string
		 */
		public function getLoc2Name()
		{
			return $this->loc2Name;
		}

		/**
		 * Set entryDate
		 *
		 * @param integer $entryDate
		 *
		 * @return FmLocation2
		 */
		public function setEntryDate($entryDate)
		{
			$this->entryDate = $entryDate;

			return $this;
		}

		/**
		 * Get entryDate
		 *
		 * @return integer
		 */
		public function getEntryDate()
		{
			return $this->entryDate;
		}

		/**
		 * Set category
		 *
		 * @param integer $category
		 *
		 * @return FmLocation2
		 */
		public function setCategory($category)
		{
			$this->category = $category;

			return $this;
		}

		/**
		 * Get category
		 *
		 * @return integer
		 */
		public function getCategory()
		{
			return $this->category;
		}

		/**
		 * Set userId
		 *
		 * @param integer $userId
		 *
		 * @return FmLocation2
		 */
		public function setUserId($userId)
		{
			$this->userId = $userId;

			return $this;
		}

		/**
		 * Get userId
		 *
		 * @return integer
		 */
		public function getUserId()
		{
			return $this->userId;
		}

		/**
		 * Set merknader
		 *
		 * @param string $merknader
		 *
		 * @return FmLocation2
		 */
		public function setMerknader($merknader)
		{
			$this->merknader = $merknader;

			return $this;
		}

		/**
		 * Get merknader
		 *
		 * @return string
		 */
		public function getMerknader()
		{
			return $this->merknader;
		}

		/**
		 * Set changeType
		 *
		 * @param integer $changeType
		 *
		 * @return FmLocation2
		 */
		public function setChangeType($changeType)
		{
			$this->changeType = $changeType;

			return $this;
		}

		/**
		 * Get changeType
		 *
		 * @return integer
		 */
		public function getChangeType()
		{
			return $this->changeType;
		}

		/**
		 * Set byggeaar
		 *
		 * @param integer $byggeaar
		 *
		 * @return FmLocation2
		 */
		public function setByggeaar($byggeaar)
		{
			$this->byggeaar = $byggeaar;

			return $this;
		}

		/**
		 * Get byggeaar
		 *
		 * @return integer
		 */
		public function getByggeaar()
		{
			return $this->byggeaar;
		}

		/**
		 * Set kostraId
		 *
		 * @param integer $kostraId
		 *
		 * @return FmLocation2
		 */
		public function setKostraId($kostraId)
		{
			$this->kostraId = $kostraId;

			return $this;
		}

		/**
		 * Get kostraId
		 *
		 * @return integer
		 */
		public function getKostraId()
		{
			return $this->kostraId;
		}

		/**
		 * Set eierform
		 *
		 * @param integer $eierform
		 *
		 * @return FmLocation2
		 */
		public function setEierform($eierform)
		{
			$this->eierform = $eierform;

			return $this;
		}

		/**
		 * Get eierform
		 *
		 * @return integer
		 */
		public function getEierform()
		{
			return $this->eierform;
		}

		/**
		 * Set streetId
		 *
		 * @param integer $streetId
		 *
		 * @return FmLocation2
		 */
		public function setStreetId($streetId)
		{
			$this->streetId = $streetId;

			return $this;
		}

		/**
		 * Get streetId
		 *
		 * @return integer
		 */
		public function getStreetId()
		{
			return $this->streetId;
		}

		/**
		 * Set loc2Old
		 *
		 * @param string $loc2Old
		 *
		 * @return FmLocation2
		 */
		public function setLoc2Old($loc2Old)
		{
			$this->loc2Old = $loc2Old;

			return $this;
		}

		/**
		 * Get loc2Old
		 *
		 * @return string
		 */
		public function getLoc2Old()
		{
			return $this->loc2Old;
		}

		/**
		 * Set searskiltObjekt
		 *
		 * @param integer $searskiltObjekt
		 *
		 * @return FmLocation2
		 */
		public function setSearskiltObjekt($searskiltObjekt)
		{
			$this->searskiltObjekt = $searskiltObjekt;

			return $this;
		}

		/**
		 * Get searskiltObjekt
		 *
		 * @return integer
		 */
		public function getSearskiltObjekt()
		{
			return $this->searskiltObjekt;
		}

		/**
		 * Set merknader1
		 *
		 * @param string $merknader1
		 *
		 * @return FmLocation2
		 */
		public function setMerknader1($merknader1)
		{
			$this->merknader1 = $merknader1;

			return $this;
		}

		/**
		 * Get merknader1
		 *
		 * @return string
		 */
		public function getMerknader1()
		{
			return $this->merknader1;
		}

		/**
		 * Set svommebasseng
		 *
		 * @param integer $svommebasseng
		 *
		 * @return FmLocation2
		 */
		public function setSvommebasseng($svommebasseng)
		{
			$this->svommebasseng = $svommebasseng;

			return $this;
		}

		/**
		 * Get svommebasseng
		 *
		 * @return integer
		 */
		public function getSvommebasseng()
		{
			return $this->svommebasseng;
		}

		/**
		 * Set brannklasse
		 *
		 * @param integer $brannklasse
		 *
		 * @return FmLocation2
		 */
		public function setBrannklasse($brannklasse)
		{
			$this->brannklasse = $brannklasse;

			return $this;
		}

		/**
		 * Get brannklasse
		 *
		 * @return integer
		 */
		public function getBrannklasse()
		{
			return $this->brannklasse;
		}

		/**
		 * Set risikoklasse
		 *
		 * @param integer $risikoklasse
		 *
		 * @return FmLocation2
		 */
		public function setRisikoklasse($risikoklasse)
		{
			$this->risikoklasse = $risikoklasse;

			return $this;
		}

		/**
		 * Get risikoklasse
		 *
		 * @return integer
		 */
		public function getRisikoklasse()
		{
			return $this->risikoklasse;
		}

		/**
		 * Set merknader3
		 *
		 * @param string $merknader3
		 *
		 * @return FmLocation2
		 */
		public function setMerknader3($merknader3)
		{
			$this->merknader3 = $merknader3;

			return $this;
		}

		/**
		 * Get merknader3
		 *
		 * @return string
		 */
		public function getMerknader3()
		{
			return $this->merknader3;
		}

		/**
		 * Set status
		 *
		 * @param integer $status
		 *
		 * @return FmLocation2
		 */
		public function setStatus($status)
		{
			$this->status = $status;

			return $this;
		}

		/**
		 * Get status
		 *
		 * @return integer
		 */
		public function getStatus()
		{
			return $this->status;
		}

		/**
		 * Set antikvarStatus
		 *
		 * @param integer $antikvarStatus
		 *
		 * @return FmLocation2
		 */
		public function setAntikvarStatus($antikvarStatus)
		{
			$this->antikvarStatus = $antikvarStatus;

			return $this;
		}

		/**
		 * Get antikvarStatus
		 *
		 * @return integer
		 */
		public function getAntikvarStatus()
		{
			return $this->antikvarStatus;
		}


		/**
		 * Set streetNumber
		 *
		 * @param string $streetNumber
		 *
		 * @return FmLocation2
		 */
		public function setStreetNumber($streetNumber)
		{
			$this->streetNumber = $streetNumber;

			return $this;
		}

		/**
		 * Get streetNumber
		 *
		 * @return string
		 */
		public function getStreetNumber()
		{
			return $this->streetNumber;
		}

		/**
		 * Set modifiedBy
		 *
		 * @param integer $modifiedBy
		 *
		 * @return FmLocation2
		 */
		public function setModifiedBy($modifiedBy)
		{
			$this->modifiedBy = $modifiedBy;

			return $this;
		}

		/**
		 * Get modifiedBy
		 *
		 * @return integer
		 */
		public function getModifiedBy()
		{
			return $this->modifiedBy;
		}

		/**
		 * Set modifiedOn
		 *
		 * @param \DateTime $modifiedOn
		 *
		 * @return FmLocation2
		 */
		public function setModifiedOn($modifiedOn)
		{
			$this->modifiedOn = $modifiedOn;

			return $this;
		}

		/**
		 * Get modifiedOn
		 *
		 * @return \DateTime
		 */
		public function getModifiedOn()
		{
			return $this->modifiedOn;
		}

		/**
		 * Set id
		 *
		 * @param integer $id
		 *
		 * @return FmLocation2
		 */
		public function setId($id)
		{
			$this->id = $id;

			return $this;
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
		 * Set holtekategori
		 *
		 * @param integer $holtekategori
		 *
		 * @return FmLocation2
		 */
		public function setHoltekategori($holtekategori)
		{
			$this->holtekategori = $holtekategori;

			return $this;
		}

		/**
		 * Get holtekategori
		 *
		 * @return integer
		 */
		public function getHoltekategori()
		{
			return $this->holtekategori;
		}

		/**
		 * @param mixed $location1
		 */
		public function setLocation1(FmLocation1 $location1)
		{
			$this->location1 = $location1;
		}

		/**
		 * @return /FmLocation1
		 */
		public function getLocation1(): FmLocation1
		{
			return $this->location1;
		}

		/**
		 * @return FmStreetaddress
		 */
		public function getStreet(): FmStreetaddress
		{

			if(empty($this->street)){
				return new FmStreetaddress();
			}
			return $this->street;
		}

		/**
		 * @return string
		 */
		public function getBygningsnummer()
		{
			return $this->bygningsnummer;
		}

		/**
		 * @param string $bygningsnummer
		 */
		public function setBygningsnummer(string $bygningsnummer)
		{
			$this->bygningsnummer = $bygningsnummer;
		}
		//endregion
	}
