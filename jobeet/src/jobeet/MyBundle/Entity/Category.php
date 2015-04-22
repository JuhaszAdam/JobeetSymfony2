<?php

namespace jobeet\MyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use jobeet\MyBundle\Utils\Jobeet;

/**
 * Category
 */
class Category
{
    /**
     * @var Job[]
     */
    private $more_jobs;

    /**
     * @return Job[]
     */
    public function getMoreJobs()
    {
        return $this->more_jobs;
    }

    /**
     * @param Job[] $more_jobs
     */
    public function setMoreJobs($more_jobs)
    {
        $this->more_jobs = $more_jobs;
    }

    /**
     * @var Job[]
     */
    private $active_jobs;

    /**
     * @param Job[] $jobs
     */
    public function setActiveJobs($jobs)
    {
        $this->active_jobs = $jobs;
    }

    /**
     * @var string $slug
     */
    private $slug;

    /**
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @ORM\PrePersist
     */
    public function setSlugValue()
    {
        $this->slug = Jobeet::slugify($this->getName());
    }

    /**
     * @return Job[]
     */
    public function getActiveJobs()
    {
        return $this->active_jobs;
    }

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $jobs;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $category_affiliates;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->jobs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->category_affiliates = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param \jobeet\MyBundle\Entity\Job $jobs
     * @return Category
     */
    public function addJob(\jobeet\MyBundle\Entity\Job $jobs)
    {
        $this->jobs[] = $jobs;

        return $this;
    }

    /**
     * Remove jobs
     *
     * @param \jobeet\MyBundle\Entity\Job $jobs
     */
    public function removeJob(\jobeet\MyBundle\Entity\Job $jobs)
    {
        $this->jobs->removeElement($jobs);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getJobs()
    {
        return $this->jobs;
    }

    /**
     * @param \jobeet\MyBundle\Entity\CategoryAffiliate $categoryAffiliates
     * @return Category
     */
    public function addCategoryAffiliate(\jobeet\MyBundle\Entity\CategoryAffiliate $categoryAffiliates)
    {
        $this->category_affiliates[] = $categoryAffiliates;

        return $this;
    }

    /**
     * @param \jobeet\MyBundle\Entity\CategoryAffiliate $categoryAffiliates
     */
    public function removeCategoryAffiliate(\jobeet\MyBundle\Entity\CategoryAffiliate $categoryAffiliates)
    {
        $this->category_affiliates->removeElement($categoryAffiliates);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategoryAffiliates()
    {
        return $this->category_affiliates;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}
