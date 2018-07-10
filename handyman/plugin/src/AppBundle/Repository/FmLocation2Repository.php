<?php
	/**
	 * Created by PhpStorm.
	 * User: eskil.saatvedt
	 * Date: 19.03.2018
	 * Time: 15:46
	 */

	namespace AppBundle\Repository;

	use Doctrine\ORM\EntityRepository;

	class FmLocation2Repository extends EntityRepository
	{
		public function findLocationWithAddress(string $location_code)
		{
			return $this->getEntityManager()
				->createQuery(
					'SELECT b, l, s FROM AppBundle:FmLocation2 b LEFT JOIN b.location1 l LEFT JOIN b.street s WHERE b.locationCode = :location_code'
				)->setParameter('location_code', $location_code)->getSingleResult();
		}
	}