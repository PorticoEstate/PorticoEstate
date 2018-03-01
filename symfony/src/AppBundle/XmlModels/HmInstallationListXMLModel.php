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

    public function __construct(array $fmLocations)
    {
        $this->Installation = array();
        /* @var $loc FmLocation1 */
        foreach ($fmLocations as $loc) {
            $this->Installation[] = HmInstallationXMLModel::constructFromLocation($loc);
            $fmBuildings = $loc->getBuildings()->toArray();
            $this->addBuildings($fmBuildings);
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
