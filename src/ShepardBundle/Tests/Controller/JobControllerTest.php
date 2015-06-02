<?php

namespace ShepardBundle\Tests\Controller;

use ShepardBundle\Controller\JobController;
use ShepardBundle\Entity\Category;
use ShepardBundle\Entity\Job;
use ShepardBundle\Manager\JobManager;
use ShepardBundle\Provider\CategoryProvider;
use PHPUnit_Framework_MockObject_MockObject;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

class JobControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var  JobManager
     */
    private $jobManager;

    /**
     * @var CategoryProvider
     */
    private $categoryProvider;

    /**
     * @var JobController
     */
    private $jobController;

    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function setUp()
    {
        $this->jobManager = $this->mockJobManager();
        $this->categoryProvider = $this->mockCategoryProvider();
        $this->formFactory = $this->mockFormFactory();
        $this->templating = $this->mockTemplating();
        $this->router = $this->mockRouter();
        $this->requestStack = $this->mockRequestStack();
    }

    /**
     * @return JobController
     */
    private function getJobControllerInstance()
    {
        if ($this->jobController === null) {
            $this->jobController = new JobController(
                $this->jobManager,
                $this->categoryProvider,
                $this->formFactory,
                $this->templating,
                $this->router,
                $this->requestStack,
                10,
                10
            );
        }

        return $this->jobController;
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function mockJobManager()
    {
        return $this->getMockBuilder(JobManager::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function mockCategoryProvider()
    {
        return $this->getMockBuilder(CategoryProvider::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function mockFormFactory()
    {
        return $this->getMockBuilder(FormFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function mockTemplating()
    {
        return $this->getMockBuilder(EngineInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function mockRouter()
    {
        return $this->getMockBuilder(Router::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function mockRequestStack()
    {
        return $this->getMockBuilder(RequestStack::class)
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
            $category->setName("Example Category Name");

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

    /**
     * @return Job
     */
    private function getLatestJob()
    {
        $job = new Job();
        $job->setCreatedAt(new \DateTime());

        return $job;
    }

    public function testIndex()
    {
        $this->categoryProvider->expects($this->once())
            ->method('provide')
            ->will($this->returnValue($this->getCategories()));

        $this->categoryProvider->expects($this->once())
            ->method('getWithJobs')
            ->will($this->returnValue($this->getCategories()));

        $this->jobManager->expects($this->any())
            ->method('findBy')
            ->will($this->returnValue($this->getJobs()));

        $this->requestStack->expects($this->any())
            ->method('getCurrentRequest')
            ->will($this->returnValue(new Request()));

        $this->jobManager->expects($this->any())
            ->method('getLatestPost')
            ->will($this->returnValue($this->getLatestJob()));

        $this->getJobControllerInstance()->indexAction();

        $categories = iterator_to_array($this->categoryProvider->provide());

        $this->assertContainsOnly(Category::class, $categories);
        foreach ($categories as $category) {
            /** @var Category $category */
            $this->assertEquals($category->getName(), "Example Category Name");
        }
    }
}
