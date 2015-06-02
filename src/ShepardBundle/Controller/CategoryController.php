<?php

namespace ShepardBundle\Controller;

use ShepardBundle\Provider\CategoryProvider;
use ShepardBundle\Provider\JobProvider;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
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
     * @param EngineInterface  $templating
     * @param JobProvider      $jobProvider
     * @param CategoryProvider $categoryProvider
     * @param Router           $router
     * @param int              $jobsPerCategory
     */
    public function __construct(
        EngineInterface $templating,
        JobProvider $jobProvider,
        CategoryProvider $categoryProvider,
        Router $router,
        $jobsPerCategory)
    {
        $this->templating = $templating;
        $this->jobProvider = $jobProvider;
        $this->categoryProvider = $categoryProvider;
        $this->router = $router;
        $this->jobsPerCategory = $jobsPerCategory;
    }

    /**
     * @param Request $request
     * @param string  $slug
     * @param int     $page
     * @return Response
     */
    public function showAction(Request $request, $slug, $page)
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

        $format = $request->getRequestFormat();

        return new Response($this->templating->render('ShepardBundle:Category:show.' . $format . '.twig', [
            'category' => $category,
            'last_page' => $last_page,
            'previous_page' => $previous_page,
            'current_page' => $page,
            'next_page' => $next_page,
            'total_jobs' => $total_jobs,
            'feedId' => sha1($this->router->generate('ShepardBundle_category',
                ['slug' => $category->getSlug(), '_format' => 'atom'], true)),
        ]));
    }
}
