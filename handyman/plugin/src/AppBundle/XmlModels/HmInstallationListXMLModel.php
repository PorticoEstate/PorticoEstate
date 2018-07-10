<?php
	/**
	 * Created by PhpStorm.
	 * User: eskil.saatvedt
	 * Date: 27.02.2018
	 * Time: 13:04
	 */

	namespace AppBundle\XmlModels;

	use AppBundle\Entity\FmLocation1;
	use AppBundle\Entity\FmBuildingExportView;

	class HmInstallationListXMLModel
	{
		/**
		 * @var \HMInstallationXMLModel[]
		 **/
		private $Installation;

		private $user_agressoid_in_groupid;

		/* @var $fmBuildings \FmLocation2[] */
		private function addBuildings(array $fmBuildings)
		{
			if (sizeof($fmBuildings) == 0) return;
			/* @var $building FmLocation2 */
			foreach ($fmBuildings as $building) {
				$this->Installation[] = HmInstallationXMLModel::constructFromBuilding($building);
			}
		}

		public function __construct(array $user_agressoid_in_groupid)
		{
			$this->Installation = array();
			$this->user_agressoid_in_groupid = $user_agressoid_in_groupid;
		}

		/**
		 * @param array $fm_building_exports
		 * @param array $user_agressoid_in_groupid
		 * @return HmInstallationListXMLModel
		 */
		public static function construct_from_building_export(array $fm_building_exports, array $user_agressoid_in_groupid)
		{
			$instance = new self($user_agressoid_in_groupid);
			$instance->parseBuldingExports($fm_building_exports);
			return $instance;
		}

		/* @var $fm_building_exports \FmBuildingExportView[] */
		public function parseBuldingExports(array $fm_building_exports)
		{
			/* @var $building FmBuildingExportView */
			foreach ($fm_building_exports as $building) {
				$this->Installation[] = HmInstallationXMLModel::constructFromBuildingExport($building, $this->user_agressoid_in_groupid);
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
