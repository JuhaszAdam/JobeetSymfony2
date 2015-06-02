<?php

namespace ShepardBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CategoryAffiliate
 */
class CategoryAffiliate
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var Category
     */
    private $category;

    /**
     * @var Affiliate
     */
    private $affiliate;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param Category $category
     * @return CategoryAffiliate
     */
    public function setCategory(Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Affiliate $affiliate
     * @return CategoryAffiliate
     */
    public function setAffiliate(Affiliate $affiliate = null)
    {
        $this->affiliate = $affiliate;

        return $this;
    }

    /**
     * @return Affiliate
     */
    public function getAffiliate()
    {
        return $this->affiliate;
    }
}
