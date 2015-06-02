<?php

namespace ShepardBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class AffiliateRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function getWithJobs()
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT c FROM ShepardBundle:Category c LEFT JOIN c.jobs j WHERE j.expires_at > :date AND j.is_activated = :activated'
        )->setParameter('date', date('Y-m-d H:i:s', time()))->setParameter('activated', 1);

        return $query->getResult();
    }

    /**
     * @param $token
     * @return mixed|null
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getForToken($token)
    {
        $qb = $this->createQueryBuilder('a')
            ->where('a.is_active = :active')
            ->setParameter('active', 1)
            ->andWhere('a.token = :token')
            ->setParameter('token', $token)
            ->setMaxResults(1);

        try {
            $affiliate = $qb->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            $affiliate = null;
        }

        return $affiliate;
    }
}
