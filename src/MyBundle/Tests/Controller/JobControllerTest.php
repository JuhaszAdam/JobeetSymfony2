<?php

namespace MyBundle\Tests\Controller;

use MyBundle\Controller\JobController;
use MyBundle\Entity\Category;
use MyBundle\Entity\Job;
use MyBundle\Manager\Manager;
use MyBundle\Provider\Provider;

class JobControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var  Manager
     */
    private $jobManager;

    /**
     * @var Provider
     */
    private $categoryProvider;

    /**
     * @var JobController
     */
    private $jobController;

    public function setUp()
    {
        $this->jobManager = $this->mockJobManager();
        $this->categoryProvider = $this->mockCategoryProvider();
        $this->jobController = $this->getJobControllerInstance();
    }

    /**
     * @return JobController
     */
    private function getJobControllerInstance()
    {
        if ($this->jobController === null) {
            $this->jobController = new JobController($this->jobManager, $this->categoryProvider);
        }

        return $this->jobController;
    }


    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function mockJobManager()
    {
        return $this
            ->getMockBuilder('MyBundle\Manager\Manager')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function mockCategoryProvider()
    {
        return $this
            ->getMockBuilder('MyBundle\Provider\Provider')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \Traversable
     */
    private function getCategories()
    {
        for ($i = 0; $i < 10; $i++) {
            $category = new Category();
            $category->setName("Asd");
            yield $category;
        }
    }

    /**
     * @return \Traversable
     */
    private function getJobs()
    {
        for ($i = 0; $i < 10; $i++) {
            $job = new Job();
            $job->setIsActivated(true);
            yield $job;
        }
    }

    public function testIndex()
    {
        $this->categoryProvider->expects($this->once())
            ->method('provide')
            ->will($this->returnValue($this->getCategories()));

        $this->jobManager->expects($this->any())
            ->method('findBy')
            ->will($this->returnValue($this->getJobs()));

        $this->jobController->indexAction();

        $categories = iterator_to_array($this->categoryProvider->provide());

        $this->assertContainsOnly(Category::class, $categories);
    }
}
