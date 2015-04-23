<?php

namespace jobeet\MyBundle\Manager;

use Doctrine\Entity;

interface ManagerInterface
{
    /**
     * @param Entity[] $saveList
     */
    public function pushToDatabase(array $saveList);

    /**
     * @return Entity[]
     */
    public function findFromDatabase();

    /**
     * @param array $criteria
     * @return Entity
     */
    public function findBy($criteria);

    /**
     * @return Entity
     */
    public function createNew();
}
