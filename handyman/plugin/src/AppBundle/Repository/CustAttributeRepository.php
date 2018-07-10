<?php
	/**
	 * Created by PhpStorm.
	 * User: eskil.saatvedt
	 * Date: 23.02.2018
	 * Time: 13:39
	 */

	namespace AppBundle\Repository;

	use Doctrine\ORM\EntityRepository;

	class CustAttributeRepository extends EntityRepository
	{
		public function find_properties(int $location_id)
		{
			// CH Multiple checkbox, LB Listbox, R = Multiple Radio
			return $this->getEntityManager()
				->createQuery(
					'SELECT a FROM AppBundle:CustAttribute a WHERE a.location_id = :location_id AND (a.dataType = \'CH\' OR a.dataType = \'LB\' OR a.dataType = \'R\')'
				)
				->setParameter('location_id', $location_id)
				->getResult();
		}
	}
