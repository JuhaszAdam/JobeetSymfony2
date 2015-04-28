<?php

namespace MyBundle\Controller;

use MyBundle\Provider\CategoryProvider;
use MyBundle\Provider\JobProvider;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var JobProvider $jobProvider
     */
    private $jobProvider;

    /**
     * @var CategoryProvider $categoryProvider
     */
    private $categoryProvider;

    /**
     * @var Router $router
     */
    private $router;

    /**
     * @var int
     */
    private $jobsPerCategory;


    /**
     * @param EngineInterface $templating
     * @param RequestStack $requestStack
     * @param JobProvider $jobProvider
     * @param CategoryProvider $categoryProvider
     * @param Router $router
     * @param int $jobsPerCategory
     */
    public function __construct($templating, $requestStack, $jobProvider, $categoryProvider, $router, $jobsPerCategory)
    {
        $this->templating = $templating;
        $this->requestStack = $requestStack;
        $this->jobProvider = $jobProvider;
        $this->categoryProvider = $categoryProvider;
        $this->router = $router;
        $this->jobsPerCategory = $jobsPerCategory;
    }

    /**
     * @param $slug
     * @param $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($slug, $page)
    {
        $category = $this->categoryProvider->findOneBySlug($slug);

        if (!$category) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $total_jobs = $this->jobProvider->countActiveJobs($category->getId());
        $jobs_per_page = $this->jobsPerCategory;
        $last_page = ceil($total_jobs / $jobs_per_page);
        $previous_page = $page > 1 ? $page - 1 : 1;
        $next_page = $page < $last_page ? $page + 1 : $last_page;

        $category->setActiveJobs($this->jobProvider->getActiveJobs(
            $category->getId(), $jobs_per_page, ($page - 1) * $jobs_per_page));

        $format = $this->requestStack->getCurrentRequest()->getRequestFormat();

        return new Response($this->templating->render('MyBundle:Category:show.' . $format . '.twig', array(
            'category' => $category,
            'last_page' => $last_page,
            'previous_page' => $previous_page,
            'current_page' => $page,
            'next_page' => $next_page,
            'total_jobs' => $total_jobs,
            'feedId' => sha1($this->router->generate('EnsJobeetBundle_category',
                array('slug' => $category->getSlug(), '_format' => 'atom'), true)),
        )));
    }
}
