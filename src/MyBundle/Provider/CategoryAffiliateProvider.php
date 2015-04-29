<?php

namespace MyBundle\Provider;

class CategoryAffiliateProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * @return array
     */
    public function getWithJobs()
    {
        return $this->manager->getWithJobs();
    }

    /**
     * @param $token
     * @return mixed|null
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getForToken($token)
    {
        return $this->manager->getForToken($token);
    }
}
