<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Service\FmLocationService;

/**
 * FmLocation1
 *
 * @ORM\Table(name="fm_location1", indexes={@ORM\Index(name="location_code_fm_location1_idx", columns={"location_code"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class FmLocation1
{
    /**
     * @var string
     *
     * @ORM\Column(name="loc1", type="string", length=8)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="fm_location1_loc1_seq", allocationSize=1, initialValue=1)
     * @Groups({"rest"})
     */
    private $loc1;

    /**
     * @var string
     *
     * @ORM\Column(name="location_code", type="string", length=8, nullable=true)
     * @Groups({"rest"})
     */
    private $locationCode;

    /**
     * @var string
     *
     * @ORM\Column(name="loc1_name", type="string", length=50, nullable=true)
     * @Groups({"rest"})
     */
    private $loc1Name;

    /**
     * @var integer
     *
     * @ORM\Column(name="part_of_town_id", type="integer", nullable=true)
     * @Groups({"rest"})
     */
    private $partOfTownId;

    /**
     * @var integer
     *
     * @ORM\Column(name="entry_date", type="integer", nullable=true)
     * @Groups({"rest"})
     */
    private $entryDate;


    /**
     * @ORM\ManyToOne(targetEntity="FmLocation1Category", inversedBy="locations")
     * @ORM\JoinColumn(name="category", referencedColumnName="id")
     * @Groups({"rest"})
     */
    private $category1;

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
     * @var integer
     *
     * @ORM\Column(name="owner_id", type="integer", nullable=true)
     * @Groups({"rest"})
     */
    private $ownerId;

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
     * @var string
     *
     * @ORM\Column(name="tips_objekt", type="string", length=8, nullable=true)
     * @Groups({"rest"})
     */
    private $tipsObjekt;

    /**
     * @var string
     *
     * @ORM\Column(name="merknader_2", type="text", nullable=true)
     * @Groups({"rest"})
     */
    private $merknader2;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse1", type="string", length=50, nullable=true)
     * @Groups({"rest"})
     */
    private $adresse1;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse2", type="string", length=50, nullable=true)
     * @Groups({"rest"})
     */
    private $adresse2;

    /**
     * @var integer
     *
     * @ORM\Column(name="postnummer", type="integer", nullable=true)
     * @Groups({"rest"})
     */
    private $postnummer;

    /**
     * @var string
     *
     * @ORM\Column(name="poststed", type="string", length=20, nullable=true)
     * @Groups({"rest"})
     */
    private $poststed;

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
     * @ORM\Column(name="aktiv", type="integer", nullable=true)
     * @Groups({"rest"})
     */
    private $aktiv;

    /**
     * @var integer
     *
     * @ORM\Column(name="olje_tank", type="integer", nullable=true)
     * @Groups({"rest"})
     */
    private $oljeTank;

    /**
     * @var integer
     *
     * @ORM\Column(name="gass_tank", type="integer", nullable=true)
     */
    private $gassTank;

    /**
     * @var integer
     *
     * @ORM\Column(name="septik_tank", type="integer", nullable=true)
     * @Groups({"rest"})
     */
    private $septikTank;

    /**
     * @var integer
     *
     * @ORM\Column(name="brann_hydrant", type="integer", nullable=true)
     */
    private $brannHydrant;

    /**
     * @var string
     *
     * @ORM\Column(name="area_gross", type="decimal", precision=20, scale=2, nullable=true)
     * @Groups({"rest"})
     */
    private $areaGross = '0.00';

    /**
     * @var integer
     *
     * @ORM\Column(name="bronn", type="integer", nullable=true)
     */
    private $bronn;

    /**
     * @var integer
     *
     * @ORM\Column(name="fett_avskiller", type="integer", nullable=true)
     */
    private $fettAvskiller;

    /**
     * @var integer
     *
     * @ORM\Column(name="slam_avskiller", type="integer", nullable=true)
     */
    private $slamAvskiller;

    /**
     * @var integer
     *
     * @ORM\Column(name="mva", type="integer", nullable=true)
     */
    private $mva;

    /**
     * @var integer
     *
     * @ORM\Column(name="modified_by", type="integer", nullable=true)
     */
    private $modifiedBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified_on", type="datetime", nullable=true)
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
     * @var string
     *
     * @ORM\Column(name="delivery_address", type="text", nullable=true)
     */
    private $deliveryAddress;


    /**
     * @ORM\OneToMany(targetEntity="FmLocation2", mappedBy="location1")
     * @Groups({"rest"})
     */
    private $buildings;

    private $customAttributes;

    public function __construct()
    {
        $this->buildings = new ArrayCollection();
        $this->customAttributes = array();
    }

    /**
     * @return string
     */
    public function getLoc1()
    {
        return $this->loc1;
    }

    /**
     * @param string $loc1
     */
    public function setLoc1($loc1)
    {
        $this->loc1 = $loc1;
    }

    /**
     * @return string
     */
    public function getLocationCode()
    {
        return $this->locationCode;
    }

    /**
     * @param string $locationCode
     */
    public function setLocationCode($locationCode)
    {
        $this->locationCode = $locationCode;
    }

    /**
     * @return string
     */
    public function getLoc1Name()
    {
        return $this->loc1Name;
    }

    /**
     * @param string $loc1Name
     */
    public function setLoc1Name($loc1Name)
    {
        $this->loc1Name = $loc1Name;
    }

    /**
     * @return int
     */
    public function getPartOfTownId()
    {
        return $this->partOfTownId;
    }

    /**
     * @param int $partOfTownId
     */
    public function setPartOfTownId($partOfTownId)
    {
        $this->partOfTownId = $partOfTownId;
    }

    /**
     * @return int
     */
    public function getEntryDate()
    {
        return $this->entryDate;
    }

    /**
     * @param int $entryDate
     */
    public function setEntryDate($entryDate)
    {
        $this->entryDate = $entryDate;
    }

    /**
     * @return /FmLocation1Category
     */
    public function getCategory1()
    {
        return $this->category1;
    }

    /**
     * @param mixed $category1
     */
    public function setCategory1(FmLocation1Category $category1)
    {
        $this->category1 = $category1;
    }

    /**
     * @return int
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param int $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return int
     */
    public function getOwnerId()
    {
        return $this->ownerId;
    }

    /**
     * @param int $ownerId
     */
    public function setOwnerId($ownerId)
    {
        $this->ownerId = $ownerId;
    }

    /**
     * @return string
     */
    public function getMerknader()
    {
        return $this->merknader;
    }

    /**
     * @param string $merknader
     */
    public function setMerknader($merknader)
    {
        $this->merknader = $merknader;
    }

    /**
     * @return int
     */
    public function getChangeType()
    {
        return $this->changeType;
    }

    /**
     * @param int $changeType
     */
    public function setChangeType($changeType)
    {
        $this->changeType = $changeType;
    }

    /**
     * @return string
     */
    public function getTipsObjekt()
    {
        return $this->tipsObjekt;
    }

    /**
     * @param string $tipsObjekt
     */
    public function setTipsObjekt($tipsObjekt)
    {
        $this->tipsObjekt = $tipsObjekt;
    }

    /**
     * @return string
     */
    public function getMerknader2()
    {
        return $this->merknader2;
    }

    /**
     * @param string $merknader2
     */
    public function setMerknader2($merknader2)
    {
        $this->merknader2 = $merknader2;
    }

    /**
     * @return string
     */
    public function getAdresse1()
    {
        return $this->adresse1;
    }

    /**
     * @param string $adresse1
     */
    public function setAdresse1($adresse1)
    {
        $this->adresse1 = $adresse1;
    }

    /**
     * @return string
     */
    public function getAdresse2()
    {
        return $this->adresse2;
    }

    /**
     * @param string $adresse2
     */
    public function setAdresse2($adresse2)
    {
        $this->adresse2 = $adresse2;
    }

    /**
     * @return int
     */
    public function getPostnummer()
    {
        return $this->postnummer;
    }

    /**
     * @param int $postnummer
     */
    public function setPostnummer($postnummer)
    {
        $this->postnummer = $postnummer;
    }

    /**
     * @return string
     */
    public function getPoststed()
    {
        return $this->poststed;
    }

    /**
     * @param string $poststed
     */
    public function setPoststed($poststed)
    {
        $this->poststed = $poststed;
    }

    /**
     * @return string
     */
    public function getMerknader1()
    {
        return $this->merknader1;
    }

    /**
     * @param string $merknader1
     */
    public function setMerknader1($merknader1)
    {
        $this->merknader1 = $merknader1;
    }

    /**
     * @return int
     */
    public function getAktiv()
    {
        return $this->aktiv;
    }

    /**
     * @param int $aktiv
     */
    public function setAktiv($aktiv)
    {
        $this->aktiv = $aktiv;
    }

    /**
     * @return int
     */
    public function getOljeTank()
    {
        return $this->oljeTank;
    }

    /**
     * @param int $oljeTank
     */
    public function setOljeTank($oljeTank)
    {
        $this->oljeTank = $oljeTank;
    }

    /**
     * @return int
     */
    public function getGassTank()
    {
        return $this->gassTank;
    }

    /**
     * @param int $gassTank
     */
    public function setGassTank($gassTank)
    {
        $this->gassTank = $gassTank;
    }

    /**
     * @return int
     */
    public function getSeptikTank()
    {
        return $this->septikTank;
    }

    /**
     * @param int $septikTank
     */
    public function setSeptikTank($septikTank)
    {
        $this->septikTank = $septikTank;
    }

    /**
     * @return int
     */
    public function getBrannHydrant()
    {
        return $this->brannHydrant;
    }

    /**
     * @param int $brannHydrant
     */
    public function setBrannHydrant($brannHydrant)
    {
        $this->brannHydrant = $brannHydrant;
    }

    /**
     * @return string
     */
    public function getAreaGross()
    {
        return $this->areaGross;
    }

    /**
     * @param string $areaGross
     */
    public function setAreaGross($areaGross)
    {
        $this->areaGross = $areaGross;
    }

    /**
     * @return int
     */
    public function getBronn()
    {
        return $this->bronn;
    }

    /**
     * @param int $bronn
     */
    public function setBronn($bronn)
    {
        $this->bronn = $bronn;
    }

    /**
     * @return int
     */
    public function getFettAvskiller()
    {
        return $this->fettAvskiller;
    }

    /**
     * @param int $fettAvskiller
     */
    public function setFettAvskiller($fettAvskiller)
    {
        $this->fettAvskiller = $fettAvskiller;
    }

    /**
     * @return int
     */
    public function getSlamAvskiller()
    {
        return $this->slamAvskiller;
    }

    /**
     * @param int $slamAvskiller
     */
    public function setSlamAvskiller($slamAvskiller)
    {
        $this->slamAvskiller = $slamAvskiller;
    }

    /**
     * @return int
     */
    public function getMva()
    {
        return $this->mva;
    }

    /**
     * @param int $mva
     */
    public function setMva($mva)
    {
        $this->mva = $mva;
    }

    /**
     * @return int
     */
    public function getModifiedBy()
    {
        return $this->modifiedBy;
    }

    /**
     * @param int $modifiedBy
     */
    public function setModifiedBy($modifiedBy)
    {
        $this->modifiedBy = $modifiedBy;
    }

    /**
     * @return \DateTime
     */
    public function getModifiedOn()
    {
        return $this->modifiedOn;
    }

    /**
     * @param \DateTime $modifiedOn
     */
    public function setModifiedOn($modifiedOn)
    {
        $this->modifiedOn = $modifiedOn;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getDeliveryAddress()
    {
        return $this->deliveryAddress;
    }

    /**
     * @param string $deliveryAddress
     */
    public function setDeliveryAddress($deliveryAddress)
    {
        $this->deliveryAddress = $deliveryAddress;
    }

    /**
     * Add building
     *
     * @param \AppBundle\Entity\FmLocation2 $fmlocation2
     *
     * @return FmLocation1
     */
    public function addBuilding(\AppBundle\Entity\FmLocation2 $fmlocation2)
    {
        $this->buildings[] = $fmlocation2;

        return $this;
    }

    /**
     * Remove building
     *
     * @param \AppBundle\Entity\FmLocation2 $fmlocation2
     */
    public function removeBuilding(\AppBundle\Entity\FmLocation2 $fmlocation2)
    {
        $this->buildings->removeElement($fmlocation2);
    }

    /**
     * Get buildings
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBuildings()
    {
        return $this->buildings;
    }

    /**
     * Get customAttributes
     *
     * @return array
     */
    public function getCustomAttributes():array
    {
        return $this->customAttributes;
    }

    /**
     * Get customAttributes
     * @param array $customAttributes
     * @return FmLocation1
     */
    public function setCustomAttributes(array $customAttributes): FmLocation1
    {
        $this->customAttributes = $customAttributes;
        return $this;
    }

    public function getValue(string $property)
    {
        return FmLocationService::getValue($property, get_object_vars($this), $this->customAttributes);
    }
}
