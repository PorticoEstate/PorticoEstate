<?php
	/**
	 * Created by PhpStorm.
	 * User: eskil.saatvedt
	 * Date: 27.02.2018
	 * Time: 14:01
	 */

	namespace AppBundle\Repository;

	use Doctrine\ORM\EntityRepository;

	class FmLocation1Repository extends EntityRepository
	{
		public function findAllFmLocation1()
		{
	//        return $this->getEntityManager()
	//            ->createQuery(
	//                'SELECT l, b FROM AppBundle:FmLocation1 l JOIN l.buildings b WHERE l.category < 99 ORDER BY l.locationCode ASC, b.loc2 ASC'
	//            )->getResult();

			return $this->getEntityManager()
				->createQuery(
					'SELECT l, b, s FROM AppBundle:FmLocation1 l JOIN l.buildings b JOIN b.street s WHERE l.category < 99 AND b.category < 99 ORDER BY l.locationCode ASC, b.loc2 ASC'
				)->getResult();
		}
	}