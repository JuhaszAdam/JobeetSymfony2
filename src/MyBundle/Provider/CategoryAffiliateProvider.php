<?php

namespace MyBundle\Provider;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

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
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getForToken($token)
    {
        return $this->manager->getForToken($token);
    }
}
