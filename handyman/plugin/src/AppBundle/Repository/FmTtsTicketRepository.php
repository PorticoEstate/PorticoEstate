<?php
	/**
	 * Created by PhpStorm.
	 * User: eskil.saatvedt
	 * Date: 20.03.2018
	 * Time: 13:19
	 */

	namespace AppBundle\Repository;

	use Doctrine\ORM\EntityRepository;

	class FmTtsTicketRepository extends EntityRepository
	{
		public function findTicketsWithHandymanOrderIDasArray(array $ids)
		{
			if (count($ids) == 0) {
				return array();
			}
			$qb = $this->getEntityManager()->createQueryBuilder();
			$qb->select('t.id, t.handyman_order_number');
			$qb->from('AppBundle:FmTtsTicket', 't');
			$qb->where($qb->expr()->in('t.handyman_order_number', $ids));
			return $qb->getQuery()->getResult();
		}
	}
