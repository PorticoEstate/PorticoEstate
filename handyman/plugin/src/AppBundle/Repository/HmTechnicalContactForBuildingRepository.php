<?php
	/**
	 * Created by PhpStorm.
	 * User: eskil.saatvedt
	 * Date: 23.04.2018
	 * Time: 13:12
	 */

	namespace AppBundle\Repository;

	use Doctrine\ORM\EntityRepository;
	use AppBundle\Entity\HmTechnicalContactForBuildingView;

	class HmTechnicalContactForBuildingRepository extends EntityRepository
	{
		public function findAllIncludingAccount()
		{
			$result = $this->getEntityManager()
				->createQuery(
					'SELECT m,a FROM AppBundle:HmTechnicalContactForBuildingView m JOIN m.account a ORDER BY m.location_code ASC'
				)->getResult();
			return $result;
		}

		public function find($id)
		{
			$result = $this->getEntityManager()
				->createQuery(
					'SELECT m,a FROM AppBundle:HmTechnicalContactForBuildingView m JOIN m.account a WHERE m.location_code = :id ORDER BY m.location_code ASC'
				)->setParameter('id', $id)
				->getSingleResult();
			return $result;
		}
	}