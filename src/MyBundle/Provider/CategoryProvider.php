<?php

namespace MyBundle\Provider;

class CategoryProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * @return array
     */
    public function getWithJobs()
    {
        return $this->manager->getWithJobs();
    }

    /**
     * @param $slug
     * @return mixed
     */
    public function findOneBySlug($slug)
    {
        return $this->manager->findOneBySlug($slug);
    }
}
