<?php

namespace jobeet\MyBundle\Provider;

use Doctrine\Entity;

interface ProviderInterface
{
    /**
     * @return Entity[]
     */
    public function provide();
}
