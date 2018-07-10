<?php
	/**
	 * Created by PhpStorm.
	 * User: eskil.saatvedt
	 * Date: 26.03.2018
	 * Time: 08:28
	 */

	namespace AppBundle\Repository;

	use Doctrine\ORM\EntityRepository;
	use AppBundle\Entity\GwPreference;

	class GwPreferenceRepository extends EntityRepository
	{
		public function findUsersByAgressoID(array $user_ids_from_agresso)
		{
			// Filter out items with a matching "ressursnr" in the db text
			//s:9:"ressursnr";s:6:"119510";

			$search_strings = array();
			/* @var string $user_id */
			foreach ($user_ids_from_agresso as $user_id) {
				if ($user_id && strlen($user_id) > 3) { // Agresso_ids is at least 4 characters long
					$search_strings[] = 'p.preference_value LIKE \'%:9:"ressursnr";s:' . strlen($user_id) . ':"' . $user_id . '";%\'';
				}
			}

			$selection_string = implode(' OR ', $search_strings);

			$result = $this->getEntityManager()
				->createQuery(
					'SELECT p,a FROM AppBundle:GwPreference p JOIN p.account a WHERE p.preference_app LIKE \'property\' AND (' . $selection_string . ') ORDER BY p.preference_owner ASC'
				)->getResult();

			/* @var GwPreference $pref */
			foreach ($result as $pref) {
				if (isset($pref->getPreferenceValue()['ressursnr'])) {
					$pref->setResourceNumber($pref->getPreferenceValue()['ressursnr']);
				}
			}
			return $result;
		}

		public function findUsersWithPropertyResourceNr()
		{
			$result = $this->getEntityManager()
				->createQuery(
					'SELECT p,a FROM AppBundle:GwPreference p JOIN p.account a WHERE p.preference_app LIKE \'property\' AND p.preference_value LIKE \'%:9:"ressursnr";s:%\' ORDER BY p.preference_owner ASC'
				)->getResult();

			/* @var GwPreference $pref */
			foreach ($result as $pref) {
				if (isset($pref->getPreferenceValue()['ressursnr'])) {
					$pref->setResourceNumber($pref->getPreferenceValue()['ressursnr']);
				}
			}

			/* @var GwPreference $pref */
			foreach ($result as $key => $pref) {
				if (empty($pref->getResourceNumber())) {
					unset($result[$key]);
				}
			}
			return $result;
		}
	}