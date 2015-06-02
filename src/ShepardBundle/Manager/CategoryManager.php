<?php

namespace ShepardBundle\Manager;

class CategoryManager extends Manager implements ManagerInterface
{
    /**
     * @return array
     */
    public function getWithJobs()
    {
        return $this->repository->getWithJobs();
    }

    /**
     * @param $slug
     * @return mixed
     */
    public function findOneBySlug($slug)
    {
        return $this->repository->findOneBySlug($slug);
    }
}
