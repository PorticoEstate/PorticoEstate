<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * FmLocation2
 *
 * @ORM\Table(name="fm_location2", indexes={@ORM\Index(name="location_code_fm_location2_idx", columns={"location_code"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FmLocation2Repository")
 */
class FmLocation2
{
    /**
     * @var string
     *
     * @ORM\Column(name="loc1", type="string", length=8)
     * @ORM\GeneratedValue(strategy="NONE")
     * @Groups({"rest"})
     */
    private $loc1;



// ORM\ManyToOne(targetEntity="FmLocation1", inversedBy="buildings", fetch="EAGER")
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
     * @var string
     *
     * @ORM\Column(name="tips_objekt", type="string", length=8, nullable=true)
     * @Groups({"rest"})
     */
    private $tipsObjekt;

    /**
     * @var string
     *
     * @ORM\Column(name="tips_bygg", type="string", length=8, nullable=true)
     * @Groups({"rest"})
     */
    private $tipsBygg;

    /**
     * @var integer
     *
     * @ORM\Column(name="street_id", type="integer", nullable=true)
     * @Groups({"rest"})
     */
    private $streetId;

    /**
     * @var string
     *
     * @ORM\Column(name="googlemap", type="string", length=255, nullable=true)
     * @Groups({"rest"})
     */
    private $googlemap;

    /**
     * @var integer
     *
     * @ORM\Column(name="tenant_id", type="integer", nullable=true)
     * @Groups({"rest"})
     */
    private $tenantId;

    /**
     * @var string
     *
     * @ORM\Column(name="location_code_old", type="string", length=8, nullable=true)
     * @Groups({"rest"})
     */
    private $locationCodeOld;

    /**
     * @var string
     *
     * @ORM\Column(name="loc1_old", type="string", length=6, nullable=true)
     * @Groups({"rest"})
     */
    private $loc1Old;

    /**
     * @var string
     *
     * @ORM\Column(name="loc2_old", type="string", length=4, nullable=true)
     * @Groups({"rest"})
     */
    private $loc2Old;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="garanti_dato", type="datetime", nullable=true)
     * @Groups({"rest"})
     */
    private $garantiDato;

    /**
     * @var integer
     *
     * @ORM\Column(name="searskilt_objekt", type="integer", nullable=true)
     * @Groups({"rest"})
     */
    private $searskiltObjekt;

    /**
     * @var integer
     *
     * @ORM\Column(name="offentlig_paalegg", type="integer", nullable=true)
     * @Groups({"rest"})
     */
    private $offentligPaalegg;

    /**
     * @var integer
     *
     * @ORM\Column(name="bygg_kostnad_1", type="integer", nullable=true)
     * @Groups({"rest"})
     */
    private $byggKostnad1;

    /**
     * @var integer
     *
     * @ORM\Column(name="bygg_kostnad_2", type="integer", nullable=true)
     * @Groups({"rest"})
     */
    private $byggKostnad2;

    /**
     * @var integer
     *
     * @ORM\Column(name="bygg_kostnad_3", type="integer", nullable=true)
     * @Groups({"rest"})
     */
    private $byggKostnad3;

    /**
     * @var integer
     *
     * @ORM\Column(name="bygg_kostnad_4", type="integer", nullable=true)
     * @Groups({"rest"})
     */
    private $byggKostnad4;

    /**
     * @var integer
     *
     * @ORM\Column(name="bygg_kostnad_5", type="integer", nullable=true)
     * @Groups({"rest"})
     */
    private $byggKostnad5;

    /**
     * @var integer
     *
     * @ORM\Column(name="bygg_kostnad_6", type="integer", nullable=true)
     * @Groups({"rest"})
     */
    private $byggKostnad6;

    /**
     * @var string
     *
     * @ORM\Column(name="merknader_1", type="text", nullable=true)
     * @Groups({"rest"})
     */
    private $merknader1;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sistombygget", type="datetime", nullable=true)
     * @Groups({"rest"})
     */
    private $sistombygget;

    /**
     * @var integer
     *
     * @ORM\Column(name="inneklimagodkjent", type="integer", nullable=true)
     * @Groups({"rest"})
     */
    private $inneklimagodkjent;

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
     * @ORM\Column(name="sdanlegg", type="integer", nullable=true)
     * @Groups({"rest"})
     */
    private $sdanlegg;

    /**
     * @var array
     *
     * @ORM\Column(name="oppvarming", type="simple_array", nullable=true)
     * @Groups({"rest"})
     */
    private $oppvarming;

    /**
     * @var string
     *
     * @ORM\Column(name="tjenesteomraadet_1", type="string", length=50, nullable=true)
     * @Groups({"rest"})
     */
    private $tjenesteomraadet1;

    /**
     * @var string
     *
     * @ORM\Column(name="tjenesteomraadet_2", type="string", length=50, nullable=true)
     * @Groups({"rest"})
     */
    private $tjenesteomraadet2;

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
     * @var integer
     *
     * @ORM\Column(name="brannobjekt", type="integer", nullable=true)
     * @Groups({"rest"})
     */
    private $brannobjekt;

    /**
     * @var integer
     *
     * @ORM\Column(name="asbest", type="integer", nullable=true)
     * @Groups({"rest"})
     */
    private $asbest;

    /**
     * @var integer
     *
     * @ORM\Column(name="roykluker", type="integer", nullable=true)
     * @Groups({"rest"})
     */
    private $roykluker;

    /**
     * @var integer
     *
     * @ORM\Column(name="sprinkelanlegg", type="integer", nullable=true)
     * @Groups({"rest"})
     */
    private $sprinkelanlegg;

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
     * @ORM\Column(name="area_gross", type="decimal", precision=20, scale=2, nullable=true)
     * @Groups({"rest"})
     */
    private $areaGross = '0.00';

    /**
     * @var string
     *
     * @ORM\Column(name="area_heated", type="decimal", precision=20, scale=2, nullable=true)
     * @Groups({"rest"})
     */
    private $areaHeated = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="area_usable", type="decimal", precision=20, scale=2, nullable=true)
     * @Groups({"rest"})
     */
    private $areaUsable = '0.00';

    /**
     * @var string
     *
     * @ORM\Column(name="street_number", type="string", length=5, nullable=true)
     * @Groups({"rest"})
     */
    private $streetNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="energiklasse", type="text", nullable=true)
     * @Groups({"rest"})
     */
    private $energiklasse;

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
     * @var integer
     *
     * @ORM\Column(name="b_info_sist_endret", type="integer", nullable=true)
     * @Groups({"rest"})
     */
    private $bInfoSistEndret;

    /**
     * @var integer
     *
     * @ORM\Column(name="areal_sist_endret", type="integer", nullable=true)
     * @Groups({"rest"})
     */
    private $arealSistEndret;

    /**
     * @var integer
     *
     * @ORM\Column(name="areal_netto_endring", type="integer", nullable=true)
     * @Groups({"rest"})
     */
    private $arealNettoEndring;

    /**
     * @ORM\ManyToOne(targetEntity="FmStreetaddress", inversedBy="buildings")
     * @ORM\JoinColumn(name="street_id", referencedColumnName="id")
     * @Groups({"rest"})
     */
    private $street;

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
     * Set tipsObjekt
     *
     * @param string $tipsObjekt
     *
     * @return FmLocation2
     */
    public function setTipsObjekt($tipsObjekt)
    {
        $this->tipsObjekt = $tipsObjekt;

        return $this;
    }

    /**
     * Get tipsObjekt
     *
     * @return string
     */
    public function getTipsObjekt()
    {
        return $this->tipsObjekt;
    }

    /**
     * Set tipsBygg
     *
     * @param string $tipsBygg
     *
     * @return FmLocation2
     */
    public function setTipsBygg($tipsBygg)
    {
        $this->tipsBygg = $tipsBygg;

        return $this;
    }

    /**
     * Get tipsBygg
     *
     * @return string
     */
    public function getTipsBygg()
    {
        return $this->tipsBygg;
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
     * Set googlemap
     *
     * @param string $googlemap
     *
     * @return FmLocation2
     */
    public function setGooglemap($googlemap)
    {
        $this->googlemap = $googlemap;

        return $this;
    }

    /**
     * Get googlemap
     *
     * @return string
     */
    public function getGooglemap()
    {
        return $this->googlemap;
    }

    /**
     * Set tenantId
     *
     * @param integer $tenantId
     *
     * @return FmLocation2
     */
    public function setTenantId($tenantId)
    {
        $this->tenantId = $tenantId;

        return $this;
    }

    /**
     * Get tenantId
     *
     * @return integer
     */
    public function getTenantId()
    {
        return $this->tenantId;
    }

    /**
     * Set locationCodeOld
     *
     * @param string $locationCodeOld
     *
     * @return FmLocation2
     */
    public function setLocationCodeOld($locationCodeOld)
    {
        $this->locationCodeOld = $locationCodeOld;

        return $this;
    }

    /**
     * Get locationCodeOld
     *
     * @return string
     */
    public function getLocationCodeOld()
    {
        return $this->locationCodeOld;
    }

    /**
     * Set loc1Old
     *
     * @param string $loc1Old
     *
     * @return FmLocation2
     */
    public function setLoc1Old($loc1Old)
    {
        $this->loc1Old = $loc1Old;

        return $this;
    }

    /**
     * Get loc1Old
     *
     * @return string
     */
    public function getLoc1Old()
    {
        return $this->loc1Old;
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
     * Set garantiDato
     *
     * @param \DateTime $garantiDato
     *
     * @return FmLocation2
     */
    public function setGarantiDato($garantiDato)
    {
        $this->garantiDato = $garantiDato;

        return $this;
    }

    /**
     * Get garantiDato
     *
     * @return \DateTime
     */
    public function getGarantiDato()
    {
        return $this->garantiDato;
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
     * Set offentligPaalegg
     *
     * @param integer $offentligPaalegg
     *
     * @return FmLocation2
     */
    public function setOffentligPaalegg($offentligPaalegg)
    {
        $this->offentligPaalegg = $offentligPaalegg;

        return $this;
    }

    /**
     * Get offentligPaalegg
     *
     * @return integer
     */
    public function getOffentligPaalegg()
    {
        return $this->offentligPaalegg;
    }

    /**
     * Set byggKostnad1
     *
     * @param integer $byggKostnad1
     *
     * @return FmLocation2
     */
    public function setByggKostnad1($byggKostnad1)
    {
        $this->byggKostnad1 = $byggKostnad1;

        return $this;
    }

    /**
     * Get byggKostnad1
     *
     * @return integer
     */
    public function getByggKostnad1()
    {
        return $this->byggKostnad1;
    }

    /**
     * Set byggKostnad2
     *
     * @param integer $byggKostnad2
     *
     * @return FmLocation2
     */
    public function setByggKostnad2($byggKostnad2)
    {
        $this->byggKostnad2 = $byggKostnad2;

        return $this;
    }

    /**
     * Get byggKostnad2
     *
     * @return integer
     */
    public function getByggKostnad2()
    {
        return $this->byggKostnad2;
    }

    /**
     * Set byggKostnad3
     *
     * @param integer $byggKostnad3
     *
     * @return FmLocation2
     */
    public function setByggKostnad3($byggKostnad3)
    {
        $this->byggKostnad3 = $byggKostnad3;

        return $this;
    }

    /**
     * Get byggKostnad3
     *
     * @return integer
     */
    public function getByggKostnad3()
    {
        return $this->byggKostnad3;
    }

    /**
     * Set byggKostnad4
     *
     * @param integer $byggKostnad4
     *
     * @return FmLocation2
     */
    public function setByggKostnad4($byggKostnad4)
    {
        $this->byggKostnad4 = $byggKostnad4;

        return $this;
    }

    /**
     * Get byggKostnad4
     *
     * @return integer
     */
    public function getByggKostnad4()
    {
        return $this->byggKostnad4;
    }

    /**
     * Set byggKostnad5
     *
     * @param integer $byggKostnad5
     *
     * @return FmLocation2
     */
    public function setByggKostnad5($byggKostnad5)
    {
        $this->byggKostnad5 = $byggKostnad5;

        return $this;
    }

    /**
     * Get byggKostnad5
     *
     * @return integer
     */
    public function getByggKostnad5()
    {
        return $this->byggKostnad5;
    }

    /**
     * Set byggKostnad6
     *
     * @param integer $byggKostnad6
     *
     * @return FmLocation2
     */
    public function setByggKostnad6($byggKostnad6)
    {
        $this->byggKostnad6 = $byggKostnad6;

        return $this;
    }

    /**
     * Get byggKostnad6
     *
     * @return integer
     */
    public function getByggKostnad6()
    {
        return $this->byggKostnad6;
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
     * Set sistombygget
     *
     * @param \DateTime $sistombygget
     *
     * @return FmLocation2
     */
    public function setSistombygget($sistombygget)
    {
        $this->sistombygget = $sistombygget;

        return $this;
    }

    /**
     * Get sistombygget
     *
     * @return \DateTime
     */
    public function getSistombygget()
    {
        return $this->sistombygget;
    }

    /**
     * Set inneklimagodkjent
     *
     * @param integer $inneklimagodkjent
     *
     * @return FmLocation2
     */
    public function setInneklimagodkjent($inneklimagodkjent)
    {
        $this->inneklimagodkjent = $inneklimagodkjent;

        return $this;
    }

    /**
     * Get inneklimagodkjent
     *
     * @return integer
     */
    public function getInneklimagodkjent()
    {
        return $this->inneklimagodkjent;
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
     * Set sdanlegg
     *
     * @param integer $sdanlegg
     *
     * @return FmLocation2
     */
    public function setSdanlegg($sdanlegg)
    {
        $this->sdanlegg = $sdanlegg;

        return $this;
    }

    /**
     * Get sdanlegg
     *
     * @return integer
     */
    public function getSdanlegg()
    {
        return $this->sdanlegg;
    }

    /**
     * Set oppvarming
     *
     * @param array $oppvarming
     *
     * @return FmLocation2
     */
    public function setOppvarming(array $oppvarming)
    {
        $this->oppvarming = $oppvarming;

        return $this;
    }

    /**
     * Get oppvarming
     *
     * @return array
     */
    public function getOppvarming()
    {
        return $this->oppvarming;
    }

    /**
     * Set tjenesteomraadet1
     *
     * @param string $tjenesteomraadet1
     *
     * @return FmLocation2
     */
    public function setTjenesteomraadet1($tjenesteomraadet1)
    {
        $this->tjenesteomraadet1 = $tjenesteomraadet1;

        return $this;
    }

    /**
     * Get tjenesteomraadet1
     *
     * @return string
     */
    public function getTjenesteomraadet1()
    {
        return $this->tjenesteomraadet1;
    }

    /**
     * Set tjenesteomraadet2
     *
     * @param string $tjenesteomraadet2
     *
     * @return FmLocation2
     */
    public function setTjenesteomraadet2($tjenesteomraadet2)
    {
        $this->tjenesteomraadet2 = $tjenesteomraadet2;

        return $this;
    }

    /**
     * Get tjenesteomraadet2
     *
     * @return string
     */
    public function getTjenesteomraadet2()
    {
        return $this->tjenesteomraadet2;
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
     * Set brannobjekt
     *
     * @param integer $brannobjekt
     *
     * @return FmLocation2
     */
    public function setBrannobjekt($brannobjekt)
    {
        $this->brannobjekt = $brannobjekt;

        return $this;
    }

    /**
     * Get brannobjekt
     *
     * @return integer
     */
    public function getBrannobjekt()
    {
        return $this->brannobjekt;
    }

    /**
     * Set asbest
     *
     * @param integer $asbest
     *
     * @return FmLocation2
     */
    public function setAsbest($asbest)
    {
        $this->asbest = $asbest;

        return $this;
    }

    /**
     * Get asbest
     *
     * @return integer
     */
    public function getAsbest()
    {
        return $this->asbest;
    }

    /**
     * Set roykluker
     *
     * @param integer $roykluker
     *
     * @return FmLocation2
     */
    public function setRoykluker($roykluker)
    {
        $this->roykluker = $roykluker;

        return $this;
    }

    /**
     * Get roykluker
     *
     * @return integer
     */
    public function getRoykluker()
    {
        return $this->roykluker;
    }

    /**
     * Set sprinkelanlegg
     *
     * @param integer $sprinkelanlegg
     *
     * @return FmLocation2
     */
    public function setSprinkelanlegg($sprinkelanlegg)
    {
        $this->sprinkelanlegg = $sprinkelanlegg;

        return $this;
    }

    /**
     * Get sprinkelanlegg
     *
     * @return integer
     */
    public function getSprinkelanlegg()
    {
        return $this->sprinkelanlegg;
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
     * Set areaGross
     *
     * @param string $areaGross
     *
     * @return FmLocation2
     */
    public function setAreaGross($areaGross)
    {
        $this->areaGross = $areaGross;

        return $this;
    }

    /**
     * Get areaGross
     *
     * @return string
     */
    public function getAreaGross()
    {
        return $this->areaGross;
    }

    /**
     * Set areaHeated
     *
     * @param string $areaHeated
     *
     * @return FmLocation2
     */
    public function setAreaHeated($areaHeated)
    {
        $this->areaHeated = $areaHeated;

        return $this;
    }

    /**
     * Get areaHeated
     *
     * @return string
     */
    public function getAreaHeated()
    {
        return $this->areaHeated;
    }

    /**
     * Set areaUsable
     *
     * @param string $areaUsable
     *
     * @return FmLocation2
     */
    public function setAreaUsable($areaUsable)
    {
        $this->areaUsable = $areaUsable;

        return $this;
    }

    /**
     * Get areaUsable
     *
     * @return string
     */
    public function getAreaUsable()
    {
        return $this->areaUsable;
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
     * Set energiklasse
     *
     * @param string $energiklasse
     *
     * @return FmLocation2
     */
    public function setEnergiklasse($energiklasse)
    {
        $this->energiklasse = $energiklasse;

        return $this;
    }

    /**
     * Get energiklasse
     *
     * @return string
     */
    public function getEnergiklasse()
    {
        return $this->energiklasse;
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
     * Set bInfoSistEndret
     *
     * @param integer $bInfoSistEndret
     *
     * @return FmLocation2
     */
    public function setBInfoSistEndret($bInfoSistEndret)
    {
        $this->bInfoSistEndret = $bInfoSistEndret;

        return $this;
    }

    /**
     * Get bInfoSistEndret
     *
     * @return integer
     */
    public function getBInfoSistEndret()
    {
        return $this->bInfoSistEndret;
    }

    /**
     * Set arealSistEndret
     *
     * @param integer $arealSistEndret
     *
     * @return FmLocation2
     */
    public function setArealSistEndret($arealSistEndret)
    {
        $this->arealSistEndret = $arealSistEndret;

        return $this;
    }

    /**
     * Get arealSistEndret
     *
     * @return integer
     */
    public function getArealSistEndret()
    {
        return $this->arealSistEndret;
    }

    /**
     * Set arealNettoEndring
     *
     * @param integer $arealNettoEndring
     *
     * @return FmLocation2
     */
    public function setArealNettoEndring($arealNettoEndring)
    {
        $this->arealNettoEndring = $arealNettoEndring;

        return $this;
    }

    /**
     * Get arealNettoEndring
     *
     * @return integer
     */
    public function getArealNettoEndring()
    {
        return $this->arealNettoEndring;
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
     * @return FmStreetadress
     */
    public function getStreet()
    {
        return $this->street;
    }
}
