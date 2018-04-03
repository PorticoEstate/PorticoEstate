<?php
/**
 * Created by PhpStorm.
 * User: eskil.saatvedt
 * Date: 27.02.2018
 * Time: 13:04
 */

namespace AppBundle\XmlModels;

use AppBundle\Entity\FmLocation1;

class HmInstallationListXMLModel
{
    /**
     * @var \HMInstallationXMLModel[]
     **/
    private $Installation;

    /* @var $fmBuildings \FmLocation2[] */
    private function addBuildings(array $fmBuildings)
    {
        if (sizeof($fmBuildings) == 0) return;
        /* @var $building FmLocation2 */
        foreach ($fmBuildings as $building) {
            $this->Installation[] = HmInstallationXMLModel::constructFromBuilding($building);
        }
    }

    public function __construct()
    {
        $this->Installation = array();
    }

	/* @var $fm_building_exports \FmBuildingExportView[] */
    public static function construct_from_building_export(array $fm_building_exports){
		$instance = new self();
		$instance->parseBuldingExports($fm_building_exports);
		return $instance;
	}

	/* @var $fm_building_exports \FmBuildingExportView[]  */
	public function parseBuldingExports(array $fm_building_exports){
		/* @var $building FmBuildingExportView */
		foreach ($fm_building_exports as $building) {
			$this->Installation[] = HmInstallationXMLModel::constructFromBuildingExport($building);
		}
	}

    /**
     * @return \HMInstallationXMLModel[]
     */
    public function getInstallation(): array
    {
        return $this->Installation;
    }
}
