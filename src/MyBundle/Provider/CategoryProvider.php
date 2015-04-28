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
}