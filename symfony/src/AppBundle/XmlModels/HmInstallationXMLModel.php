<?php
/**
 * Created by PhpStorm.
 * User: eskil.saatvedt
 * Date: 27.02.2018
 * Time: 13:38
 */

namespace AppBundle\XmlModels;

use AppBundle\Entity\FmLocation1;
use AppBundle\Entity\FmLocation2;

class HmInstallationXMLModel
{
    /* @var string */
    protected $InstallationID;
    /* @var int */
    protected $InstallationOrigin = 0; // $InstallationOrigin 0 = default(Own) 1 = preinstalled
    /* @var string */
    protected $Name = '';
    /* @var int */
    protected $Site = 1; //Site 0=Equipment, 1=Site

    /* @var HmAddressXMLModel */
    protected $Address;// Address is a handyman_address_xml_model

    /* @var string */
    protected $InstallationIDParent = 0;
    // ID of the employee responsible for the installation, must exist in Handyman
    /* @var int */
    protected $ResponsibleNo = 0;
    /* @var int */
    protected $Status = 1;// Status 0=New (default for equipment), 1=Installed (default for site), 2=Paused, 3=Historical
//    protected $Customer; // handyman_customer_xml_model

    /**
     * HmInstallationXMLModel constructor
     */
    public function __construct()
    {
        // allocate your stuff
    }
    /* @var $building FmLocation2 */
    public static function constructFromBuilding(FmLocation2 $building)
    {
        $instance = new self();
        $instance->Name = $building->getLoc2Name() ?? '';
        $instance->InstallationID = $building->getLocationCode() ?? '';
        $instance->InstallationIDParent = $building->getLoc1() ?? '';
        $instance->Address = HmAddressXMLModel::constructFromBuilding($building);
        return $instance;
    }
    /* @var $loc FmLocation1 */
    public static function constructFromLocation(FmLocation1 $loc)
    {
        $instance = new self();
        $instance->Address = HmAddressXMLModel::constructFromLocation($loc);
        $instance->Name = $loc->getLoc1Name() ?? '';
        $instance->InstallationID = $loc->getLocationCode() ?? '';
        return $instance;
    }

    /**
     * @return string
     */
    public function getInstallationID(): string
    {
        return $this->InstallationID;
    }

    /**
     * @return int
     */
    public function getInstallationOrigin(): int
    {
        return $this->InstallationOrigin;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->Name;
    }

    /**
     * @return int
     */
    public function getSite(): int
    {
        return $this->Site;
    }

    /**
     * @return HmAddressXMLModel
     */
    public function getAddress(): HmAddressXMLModel
    {
        return $this->Address;
    }

    /**
     * @return int
     */
    public function getResponsibleNo(): int
    {
        return $this->ResponsibleNo;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->Status;
    }

    /**
     * @return string
     */
    public function getInstallationIDParent(): string
    {
        return $this->InstallationIDParent;
    }



}
