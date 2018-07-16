<?php

namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class ProjectRepository extends EntityRepository
{
    public function createFindAllQuery()
    {
        return $this->_em->getRepository('AppBundle:Project')->createQueryBuilder('bp');
    }

    public function createFindOneByIdQuery(int $id)
    {
        $query = $this->_em->createQuery(
            "
            SELECT bp
            FROM AppBundle:Project bp
            WHERE bp.id = :id
            "
        );
        $query->setParameter('id', $id);
        return $query;
    }
}