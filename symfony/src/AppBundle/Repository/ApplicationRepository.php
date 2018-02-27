<?php
/**
 * Created by PhpStorm.
 * User: eskil.saatvedt
 * Date: 23.02.2018
 * Time: 13:39
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ApplicationRepository extends EntityRepository
{


    public function findAppForProperties()
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT a, g FROM AppBundle:Application a JOIN a.locations g WHERE a.name = \'property\' AND g.name = \'.location.1\''
            )
            ->setMaxResults(1)
            ->getSingleResult();
    }

    public function findAppForBuildings()
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT a, g FROM AppBundle:Application a JOIN a.locations g WHERE a.name = \'property\' AND g.name = \'.location.2\''
            )
            ->setMaxResults(1)
            ->getSingleResult();
    }
}