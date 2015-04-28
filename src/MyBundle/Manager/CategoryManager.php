<?php

namespace MyBundle\Manager;

class CategoryManager extends Manager implements ManagerInterface
{
    /**
     * @return array
     */
    public function getWithJobs()
    {
        return $this->repository->getWithJobs();
    }
}