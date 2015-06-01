<?php

namespace MyBundle\Manager;

use Doctrine\Entity;

interface ManagerInterface
{
    /**
     * @param Entity[] $saveList
     */
    public function saveList(array $saveList);

    /**
     * @return Entity[]
     */
    public function findAll();

    /**
     * @param array      $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     * @return Entity[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @return Entity
     */
    public function createNew();

    /**
     * @param Entity $entity
     */
    public function save($entity);
}
