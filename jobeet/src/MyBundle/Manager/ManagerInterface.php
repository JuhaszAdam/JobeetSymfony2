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
     * @param array $criteria
     * @return Entity
     */
    public function findBy($criteria);

    /**
     * @return Entity
     */
    public function createNew();

    /**
     * @param Entity $entity
     */
    public function save($entity);
}
