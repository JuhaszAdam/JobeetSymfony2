<?php

namespace MyBundle\Manager;

class JobManager extends Manager implements ManagerInterface
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
        return $this->repository->getActiveJobs($category_id, $max, $offset, $affiliate_id);
    }

    /**
     * @param null $category_id
     * @return mixed
     */
    public function countActiveJobs($category_id = null)
    {
        return $this->repository->countActiveJobs($category_id);
    }

    /**
     * @param $id
     * @return mixed|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getActiveJob($id)
    {
        return $this->repository->getActiveJob($id);
    }

    /**
     * @return mixed|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getLatestPost()
    {
        return $this->repository->getLatestPost();
    }

    /**
     * @param $token
     * @return Job
     */
    public function findOneByToken($token)
    {
        return $this->repository->findOneByToken($token);
    }

    /**
     * @param $query
     * @return array
     */
    public function getForLuceneQuery($query)
    {
        return $this->repository->getForLuceneQuery($query);
    }

    /**
     * @param $days
     * @return mixed
     */
    public function cleanup($days)
    {
        return $this->repository->cleanup($days);
    }
}
