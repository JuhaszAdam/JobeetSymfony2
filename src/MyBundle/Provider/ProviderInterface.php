<?php

namespace MyBundle\Provider;

use Doctrine\Entity;

interface ProviderInterface
{
    /**
     * @return Entity[]
     */
    public function provide();

    /**
     * @param array      $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     * @return Entity[]
     */
    public function provideBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);
}
