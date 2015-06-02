<?php

namespace ShepardBundle\Manager;

class CategoryAffiliateManager extends Manager implements ManagerInterface
{
    /**
     * @return array
     */
    public function getWithJobs()
    {
        return $this->repository->getWithJobs();
    }

    /**
     * @param $token
     * @return mixed|null
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getForToken($token)
    {
        return $this->repository->getForToken($token);
    }
}
