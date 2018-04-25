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
use AppBundle\Entity\FmBuildingExportView;

class HmInstallationXMLModel
{
	// From Handyman 12 - Kontroll pilot, 2 - Etat for bygg og eiendom
	/**
	 * agressoIdToDepartmentNo is a key value array with managers agressoID and the department they belong to in Handyman
	 */
	const agressoIdToDepartmentNo = array('35919'=>'12');

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

    // ID of the employee responsible for the installation, must exist in Handyman
    /* @var int */
    protected $ResponsibleNo;
    /* @var int */
    protected $Status = 1;// Status 0=New (default for equipment), 1=Installed (default for site), 2=Paused, 3=Historical

    /* @var $Customer HmCustomerXMLModel */
    protected $Customer;

    protected $HSDepartmentID;

    /**
     * HmInstallationXMLModel constructor
     */
    public function __construct()
    {
        // allocate your stuff
        $this->Customer = new HmCustomerXMLModel;
    }

	/* @var $building FmBuildingExportView */
	public static function constructFromBuildingExport(FmBuildingExportView $building)
	{
		$instance = new self();
		$instance->Address = HmAddressXMLModel::constructBuildingExport($building);
		$instance->Name = $building->getLoc2Name() ?? '';
		$instance->InstallationID = $building->getLocationCode() ?? '';
		$instance->ResponsibleNo = HmInstallationXMLModel::findResponsible($building);
		$instance->HSDepartmentID = HmInstallationXMLModel::findDepartment($building);
		return $instance;
	}

	/**
	 * @param FmBuildingExportView $building
	 * @return int
	 */
	private static function findResponsible(FmBuildingExportView $building): int
	{
		$result = 35919; // Bjørn Østrem
		if ($building->getManagerAgressoId()){
			$result = $building->getManagerAgressoId();
		}
		return $result;
	}

	/**
	 * @param FmBuildingExportView $building
	 * @return int
	 */
	private static function findDepartment(FmBuildingExportView $building): int
	{
		$result = 12;

		if($building->getManagerAgressoId()){
			if(isset(HmInstallationXMLModel::agressoIdToDepartmentNo[$building->getManagerAgressoId()])){
				$result = HmInstallationXMLModel::agressoIdToDepartmentNo[$building->getManagerAgressoId()];
			}
		}

		return $result;
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

//    /**
//     * @return string
//     */
//    public function getInstallationIDParent(): string
//    {
//        return $this->InstallationIDParent;
//    }

    /**
     * @return HmCustomerXMLModel
     */
    public function getCustomer(): HmCustomerXMLModel
    {
        return $this->Customer;
    }

    /**
     * @return int
     */
    public function getHSDepartmentID(): int
    {
        return $this->HSDepartmentID;
    }



}
