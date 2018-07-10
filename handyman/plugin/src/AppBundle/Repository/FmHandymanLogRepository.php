<?php
	/**
	 * Created by PhpStorm.
	 * User: eskil.saatvedt
	 * Date: 24.04.2018
	 * Time: 10:17
	 */

	namespace AppBundle\Repository;

	use Doctrine\ORM\EntityRepository;
	use Doctrine\ORM\NonUniqueResultException;
	use Doctrine\ORM\NoResultException;

	class FmHandymanLogRepository extends EntityRepository
	{
		/**
		 * @return mixed|null
		 */
		public function findLast()
		{
			$qb = $this->getEntityManager()->createQueryBuilder();
			$qb->select('lo');
			$qb->from('AppBundle:FmHandymanLog', 'lo');
			$qb->setMaxResults(1);
			$qb->orderBy('lo.log_date', 'DESC');
			try {
				return $qb->getQuery()->getSingleResult();
			} catch (NoResultException $e) {
				return null;
			}
		}
	}