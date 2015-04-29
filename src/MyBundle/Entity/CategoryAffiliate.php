<?php

namespace MyBundle\Entity;

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
     * @var \MyBundle\Entity\Category
     */
    private $category;

    /**
     * @var \MyBundle\Entity\Affiliate
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
     * @param \MyBundle\Entity\Category $category
     * @return CategoryAffiliate
     */
    public function setCategory(\MyBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return \MyBundle\Entity\Category 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param \MyBundle\Entity\Affiliate $affiliate
     * @return CategoryAffiliate
     */
    public function setAffiliate(\MyBundle\Entity\Affiliate $affiliate = null)
    {
        $this->affiliate = $affiliate;

        return $this;
    }

    /**
     * @return \MyBundle\Entity\Affiliate 
     */
    public function getAffiliate()
    {
        return $this->affiliate;
    }
}
