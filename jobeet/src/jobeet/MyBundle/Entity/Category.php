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
     * Set slug
     *
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }
    /**
     * Get slug
     *
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add jobs
     *
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
     * Get jobs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getJobs()
    {
        return $this->jobs;
    }

    /**
     * Add category_affiliates
     *
     * @param \jobeet\MyBundle\Entity\CategoryAffiliate $categoryAffiliates
     * @return Category
     */
    public function addCategoryAffiliate(\jobeet\MyBundle\Entity\CategoryAffiliate $categoryAffiliates)
    {
        $this->category_affiliates[] = $categoryAffiliates;

        return $this;
    }

    /**
     * Remove category_affiliates
     *
     * @param \jobeet\MyBundle\Entity\CategoryAffiliate $categoryAffiliates
     */
    public function removeCategoryAffiliate(\jobeet\MyBundle\Entity\CategoryAffiliate $categoryAffiliates)
    {
        $this->category_affiliates->removeElement($categoryAffiliates);
    }

    /**
     * Get category_affiliates
     *
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
