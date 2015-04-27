<?php

namespace MyBundle\Provider;

use MyBundle\Manager\Manager;

class Provider implements ProviderInterface
{
    /**
     * @var Manager
     */
    private $manager;

    /**
     * @param Manager $manager
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function provide()
    {
        $jobs = $this->manager->findAll();

        foreach ($jobs as $job) {
            yield $job;
        }
    }
}
