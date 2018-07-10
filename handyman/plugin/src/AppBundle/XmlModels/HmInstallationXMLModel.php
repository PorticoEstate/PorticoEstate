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
		/* @var string */
		protected $InstallationID;

		/**
		 *
		 * $InstallationOrigin 0 = default(Own) 1 = preinstalled
		 * @var int
		 */
		protected $InstallationOrigin = 0;
		/* @var string */
		protected $Name = '';
		/**
		 * /Site 0=Equipment, 1=Site
		 * @var int
		 */
		protected $Site = 1;
		/**
		 * Address is a handyman_address_xml_model
		 * @var HmAddressXMLModel
		 */
		protected $Address;//
		/**
		 * ID of the employee responsible for the installation, must exist in Handyman
		 * @var int
		 */
		protected $ResponsibleNo;
		/**
		 * Status 0=New (default for equipment), 1=Installed (default for site), 2=Paused, 3=Historical
		 * @var int
		 */
		protected $Status = 1;
		/* @var $Customer HmCustomerXMLModel */
		protected $Customer;
		/* @var int */
		protected $HSDepartmentID;

		/**
		 * HmInstallationXMLModel constructor
		 */
		public function __construct()
		{
			$this->Customer = new HmCustomerXMLModel;
		}

		/* @var $building FmBuildingExportView */
		public static function constructFromBuildingExport(FmBuildingExportView $building, array $user_agressoid_in_groupid)
		{
			$instance = new self();
			$instance->Address = HmAddressXMLModel::construct_building_export($building);
			$instance->Name = $building->getLoc2Name() ?? '';
			$instance->InstallationID = $building->getLocationCode() ?? '';
			$instance->ResponsibleNo = HmInstallationXMLModel::findResponsible($building);
			$instance->HSDepartmentID = HmInstallationXMLModel::findDepartment($building, $user_agressoid_in_groupid);
			return $instance;
		}

		/**
		 * @param FmBuildingExportView $building
		 * @return int
		 */
		private static function findResponsible(FmBuildingExportView $building): int
		{
			$result = 35919; // Bjørn Østrem
			if ($building->getManagerAgressoId()) {
				$result = $building->getManagerAgressoId();
			}
			return $result;
		}

		/**
		 * @param FmBuildingExportView $building
		 * @param array $user_agressoid_in_groupid
		 * @return int
		 */
		private static function findDepartment(FmBuildingExportView $building, array $user_agressoid_in_groupid): int
		{
			$result = 12;

			if ($building->getManagerAgressoId()) {
				if (isset($user_agressoid_in_groupid[$building->getManagerAgressoId()])) {
					$result = $user_agressoid_in_groupid[$building->getManagerAgressoId()];
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
