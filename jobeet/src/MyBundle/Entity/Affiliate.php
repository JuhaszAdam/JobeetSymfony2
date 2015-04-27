<?php

namespace MyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Affiliate
 */
class Affiliate
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $token;

    /**
     * @var \DateTime
     */
    private $created_at;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $category_affiliates;

    /**
     * @var boolean
     */
    private $is_active;

    /**
     * Constructor
     */
    public function __construct()
    {
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
     * @param string $url
     * @return Affiliate
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $email
     * @return Affiliate
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param \DateTime $createdAt
     * @return Affiliate
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param \MyBundle\Entity\CategoryAffiliate $categoryAffiliates
     * @return Affiliate
     */
    public function addCategoryAffiliate(\MyBundle\Entity\CategoryAffiliate $categoryAffiliates)
    {
        $this->category_affiliates[] = $categoryAffiliates;

        return $this;
    }

    /**
     * @param \MyBundle\Entity\CategoryAffiliate $categoryAffiliates
     */
    public function removeCategoryAffiliate(\MyBundle\Entity\CategoryAffiliate $categoryAffiliates)
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

    public function getCategories()
    {
        return $this->category_affiliates;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {

    }

    /**
     * @ORM\PrePersist
     */
    public function setTokenValue()
    {
        if (!$this->getToken()) {
            $token = sha1($this->getEmail() . rand(11111, 99999));
            $this->token = $token;
        }

        return $this;
    }

    /**
     * @param string $token
     * @return Affiliate
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->is_active;
    }

    /**
     * @param $a
     */
    public function setIsActive($isActive)
    {
        $this->is_active = $isActive;
    }

    /**
     * @param $c
     */
    public function setCategories($c)
    {
        $this->category_affiliates->add($c);
    }
}
