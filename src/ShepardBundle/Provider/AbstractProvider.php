<?php

namespace ShepardBundle\Provider;

use ShepardBundle\Manager\Manager;

abstract class AbstractProvider implements ProviderInterface
{
    /**
     * @var Manager
     */
    protected $manager;

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

    /**
     * {@inheritdoc}
     */
    public function provideBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->manager->findBy($criteria, $orderBy, $limit, $offset);
    }
}
