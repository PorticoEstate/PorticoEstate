<?php
	/**
	 * Created by PhpStorm.
	 * User: eskil.saatvedt
	 * Date: 26.02.2018
	 * Time: 15:07
	 */

	namespace AppBundle\Service;

	use AppBundle\Entity\FmBuildingExportView;
	use AppBundle\Entity\FmLocation1 as FmLocation1;
	use AppBundle\Entity\GwPreference;
	use AppBundle\Entity\HmManagerForBuildingView;
	use Doctrine\ORM\ArrayCollection as ArrayCollection;
	use Doctrine\ORM\EntityManager as EntityManager;
	use AppBundle\Entity\GwApplication;
	use AppBundle\Entity\CustAttribute;

	class FmLocationService
	{
		/* @var $em EntityManager */
		private $em;

		public function __construct(EntityManager $em)
		{
			$this->em = $em;
		}

		/**
		 * Return the value(s) for a given property, if the property is in both arrays it will look itself up in custom attributes and return its representation
		 *
		 * @param string $property
		 * @param array $objectVars
		 * @param array $customAttributes
		 * @return mixed
		 */
		public static function get_value(string $property, array $objectVars, array $customAttributes)
		{
			if (!array_key_exists($property, $objectVars)) {
				return null;
			}
			if (!array_key_exists($property, $customAttributes)) {
				return $objectVars[$property];
			}

			$index = $objectVars[$property];
			$type = $customAttributes[$property]['type'];
			if (!$index) {
				return CustAttribute::getDefaultValue($type);
			}

			switch ($type) {
				case 'LB':
					return $customAttributes[$property]['values'][$index];
					break;
				case 'R':
				case 'CH':
					if (!is_array($index)) {
						$index = array_filter(explode(',', $index));
					}
					return array_intersect_key($customAttributes[$property]['values'], array_flip($index));
					break;
				default:
					return CustAttribute::getDefaultValue($type);
			}
		}


		public function get_buildings(): array
		{
			$buildings = $this->em->getRepository('AppBundle:FmBuildingExportView')->findAll();
			$managers = $this->em->getRepository('AppBundle:HmManagerForBuildingView')->findAllIncludingAccount();
			$this->add_agresso_id_to_manager($managers);
			/* @var FmBuildingExportView $building */
			foreach ($buildings as $building) {
				$this->add_manager_data_to_building($building, $managers);
			}
			return $buildings;
		}

		private function add_manager_data_to_building(FmBuildingExportView &$building, array $managers)
		{
			/* @var HmManagerForBuildingView $manager */
			foreach ($managers as $manager) {
				if (empty($manager->getLocationCode())) {
					continue;
				}
				if (empty($manager->getAgressoId())) {
					continue;
				}
				if (empty($manager->getAccount())) {
					continue;
				}
				if ($building->getLoc1() == $manager->getLocationCode()) {
					$building->setManagerAgressoId($manager->getAgressoId() ?? '');
					$building->setManagerUserId($manager->getContactId() ?? '');
					$name = Trim(($manager->getFirstName() ?? '') . ' ' . ($manager->getLastName() ?? ''));
					$building->setManagerName($name);
					$building->setManagerAccountId($manager->getAccount()->getAccountId());
				}
			}
		}

		private function add_agresso_id_to_manager(array &$managers)
		{
			$users_with_agresso_id = $this->em->getRepository('AppBundle:GwPreference')->findUsersWithPropertyResourceNr();
			/* @var HmManagerForBuildingView $manager */
			foreach ($managers as $key => &$manager) {
				if (empty($manager->getContactId())) {
					unset($managers[$key]);
					continue;
				}

				/* @var GwPreference $pref_user */
				foreach ($users_with_agresso_id as $pref_user) {
					if ($pref_user->getPreferenceOwner() == $manager->getAccount()->getAccountId()) {
						$manager->setAgressoId($pref_user->getResourceNumber());
					}
				}
				if (empty($manager->getAgressoId())) {
					unset($managers[$key]);
				}
			}
		}
	}