<?php

	namespace AppBundle\Repository;

	use Doctrine\ORM\EntityRepository;

	class ProductRepository extends EntityRepository
	{
		public function findAllOrderedByName()
		{
			return $this->getEntityManager()
				->createQuery(
					'SELECT p, c FROM AppBundle:Product p JOIN p.category c ORDER BY p.name ASC'
				)
				->getResult();
		}
	}