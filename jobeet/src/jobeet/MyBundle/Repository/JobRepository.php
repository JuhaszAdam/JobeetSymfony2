<?php

namespace jobeet\MyBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use jobeet\MyBundle\Entity\Job;

class JobRepository extends EntityRepository
{
    /**
     * @param null $category_id
     * @param null $max
     * @param null $offset
     * @param null $affiliate_id
     * @return array
     */
    public function getActiveJobs($category_id = null, $max = null, $offset = null, $affiliate_id = null)
    {
        $qb = $this->createQueryBuilder('j')
            ->where('j.expires_at > :date')
            ->setParameter('date', date('Y-m-d H:i:s', time()))
            ->andWhere('j.is_activated = :activated')
            ->setParameter('activated', 1)
            ->orderBy('j.expires_at', 'DESC');

        if ($max) {
            $qb->setMaxResults($max);
        }

        if ($offset) {
            $qb->setFirstResult($offset);
        }

        if ($category_id) {
            $qb->andWhere('j.category = :category_id')
                ->setParameter('category_id', $category_id);
        }
        // j.category c, c.affiliate a
        if ($affiliate_id) {
            $qb->leftJoin('j.category', 'c')
                ->leftJoin('c.category_affiliates', 'a')
                ->andWhere('a.id = :affiliate_id')
                ->setParameter('affiliate_id', $affiliate_id);
        }

        $query = $qb->getQuery();

        return $query->getResult();
    }

    /**
     * @param null $category_id
     * @return mixed
     */
    public function countActiveJobs($category_id = null)
    {
        $qb = $this->createQueryBuilder('j')
            ->select('count(j.id)')
            ->where('j.expires_at > :date')
            ->setParameter('date', date('Y-m-d H:i:s', time()))
            ->andWhere('j.is_activated = :activated')
            ->setParameter('activated', 1);

        if ($category_id) {
            $qb->andWhere('j.category = :category_id')
                ->setParameter('category_id', $category_id);
        }

        $query = $qb->getQuery();

        return $query->getSingleScalarResult();
    }

    /**
     * @param $id
     * @return mixed|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getActiveJob($id)
    {
        $query = $this->createQueryBuilder('j')
            ->where('j.id = :id')
            ->setParameter('id', $id)
            ->andWhere('j.expires_at > :date')
            ->setParameter('date', date('Y-m-d H:i:s', time()))
            ->andWhere('j.is_activated = :activated')
            ->setParameter('activated', 1)
            ->setMaxResults(1)
            ->getQuery();

        try {
            $job = $query->getSingleResult();
        } catch (NoResultException $e) {
            $job = null;
        }

        return $job;
    }

    /**
     * @param $days
     * @return mixed
     */
    public function cleanup($days)
    {
        $query = $this->createQueryBuilder('j')
            ->delete()
            ->where('j.is_activated IS NULL')
            ->andWhere('j.created_at < :created_at')->setParameter('created_at', date('Y-m-d', time() - 86400 * $days))
            ->getQuery();

        return $query->execute();
    }

    /**
     * @return mixed|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getLatestPost()
    {
        $query = $this->createQueryBuilder('j')
            ->where('j.expires_at > :date')
            ->setParameter('date', date('Y-m-d H:i:s', time()))
            ->andWhere('j.is_activated = :activated')
            ->setParameter('activated', 1)
            ->orderBy('j.expires_at', 'DESC')
            ->setMaxResults(1)
            ->getQuery();

        try {
            $job = $query->getSingleResult();
        } catch (NoResultException $e) {
            $job = null;
        }

        return $job;
    }

    /**
     * @param $query
     * @return array
     */
    public function getForLuceneQuery($query)
    {
        $hits = Job::getLuceneIndex()->find($query);

        $pks = array();
        foreach ($hits as $hit) {
            $pks[] = $hit->pk;
        }

        if (empty($pks)) {
            return array();
        }

        $q = $this->createQueryBuilder('j')
            ->where('j.id IN (:pks)')
            ->setParameter('pks', $pks)
            ->andWhere('j.is_activated = :active')
            ->setParameter('active', 1)
            ->setMaxResults(20)
            ->getQuery();

        return $q->getResult();
    }
}
