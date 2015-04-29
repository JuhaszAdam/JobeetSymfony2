<?php

namespace MyBundle\Provider;

class JobProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * @param null $category_id
     * @param null $max
     * @param null $offset
     * @param null $affiliate_id
     * @return array
     */
    public function getActiveJobs($category_id = null, $max = null, $offset = null, $affiliate_id = null)
    {
        return $this->manager->getActiveJobs($category_id, $max, $offset, $affiliate_id);
    }

    /**
     * @param null $category_id
     * @return mixed
     */
    public function countActiveJobs($category_id = null)
    {
        return $this->manager->countActiveJobs($category_id);
    }

    /**
     * @param $id
     * @return mixed|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getActiveJob($id)
    {
        return $this->manager->getActiveJob($id);
    }

    /**
     * @return mixed|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getLatestPost()
    {
        return $this->manager->getLatestPost();
    }
}
