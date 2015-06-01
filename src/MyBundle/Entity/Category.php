<?php

namespace MyBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use MyBundle\Utils\Jobeet;

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
     * @var Collection
     */
    private $jobs;

    /**
     * @var Collection
     */
    private $category_affiliates;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->jobs = new ArrayCollection();
        $this->category_affiliates = new ArrayCollection();
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
     * @param Job $jobs
     * @return Category
     */
    public function addJob(Job $jobs)
    {
        $this->jobs[] = $jobs;

        return $this;
    }

    /**
     * Remove jobs
     *
     * @param Job $jobs
     */
    public function removeJob(Job $jobs)
    {
        $this->jobs->removeElement($jobs);
    }

    /**
     * @return Collection
     */
    public function getJobs()
    {
        return $this->jobs;
    }

    /**
     * @param CategoryAffiliate $categoryAffiliates
     * @return Category
     */
    public function addCategoryAffiliate(CategoryAffiliate $categoryAffiliates)
    {
        $this->category_affiliates[] = $categoryAffiliates;

        return $this;
    }

    /**
     * @param CategoryAffiliate $categoryAffiliates
     */
    public function removeCategoryAffiliate(CategoryAffiliate $categoryAffiliates)
    {
        $this->category_affiliates->removeElement($categoryAffiliates);
    }

    /**
     * @return Collection
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
