<?php

namespace MyBundle\Model;

use Symfony\Component\HttpFoundation\Request;

class ElasticJobSearch
{
    /**
     * @var \DateTime
     */
    protected $dateFrom;

    /**
     * @var \DateTime
     */
    protected $dateTo;

    /**
     * @var bool
     */
    protected $is_activated;

    /**
     * @var string
     */
    private $company;

    public function __construct()
    {
        $date = new \DateTime();
        $month = new \DateInterval('P1Y');
        $date->sub($month);
        $date->setTime('00', '00', '00');

        $this->dateFrom = $date;
        $this->dateTo = new \DateTime();
        $this->dateTo->setTime('23', '59', '59');
    }

    /**
     * @param \DateTime $dateFrom
     * @return $this
     */
    public function setDateFrom(\DateTime $dateFrom)
    {
        if ($dateFrom != "") {
            $dateFrom->setTime('00', '00', '00');
            $this->dateFrom = $dateFrom;
        }

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateFrom()
    {
        return $this->dateFrom;
    }

    /**
     * @param \DateTime $dateTo
     * @return $this
     */
    public function setDateTo(\DateTime $dateTo)
    {
        if ($dateTo != "") {
            $dateTo->setTime('23', '59', '59');
            $this->dateTo = $dateTo;
        }

        return $this;
    }

    public function clearDates()
    {
        $this->dateTo = null;
        $this->dateFrom = null;
    }

    /**
     * @return \DateTime
     */
    public function getDateTo()
    {
        return $this->dateTo;
    }

    /**
     * @return bool
     */
    public function isActivated()
    {
        return $this->is_activated;
    }

    /**
     * @param bool $is_activated
     * @return $this
     */
    public function setActivated($is_activated)
    {
        $this->is_activated = $is_activated;

        return $this;
    }

    /**
     * @param string $company
     * @return Job
     */
    public function setCompany($company)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }
}
