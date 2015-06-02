<?php

namespace ShepardBundle\Controller;

use ShepardBundle\Provider\CategoryAffiliateProvider;
use ShepardBundle\Provider\JobProvider;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use ShepardBundle\Entity\Job;
use ShepardBundle\Provider\Provider;
use ShepardBundle\Repository\JobRepository;

class ApiController extends Controller
{
    /**
     * @var CategoryAffiliateProvider
     */
    private $affiliateProvider;

    /**
     * @var JobProvider
     */
    private $jobProvider;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * @param CategoryAffiliateProvider $affiliateProvider
     * @param JobProvider               $jobProvider
     * @param Router                    $router
     * @param EngineInterface           $templating
     */
    public function __construct(
        CategoryAffiliateProvider $affiliateProvider,
        JobProvider $jobProvider,
        Router $router,
        EngineInterface $templating)
    {
        $this->affiliateProvider = $affiliateProvider;
        $this->jobProvider = $jobProvider;
        $this->router = $router;
        $this->templating = $templating;
    }

    /**
     * @param Request $request
     * @param         $token
     * @return Response
     */
    public function listAction(Request $request, $token)
    {
        $jobs = [];

        $affiliate = $this->affiliateProvider->getForToken($token);
        if (!$affiliate) {
            throw $this->createNotFoundException('This affiliate account does not exist!');
        }
        /** @var JobRepository $rep */

        $active_jobs = $this->jobProvider->getActiveJobs(null, null, null, $affiliate->getId());
        /** @var Job $job */
        foreach ($active_jobs as $job) {
            $jobs[$this->router->generate('ShepardBundle_job_show',
                ['company' => $job->getCompanySlug(),
                    'location' => $job->getLocationSlug(),
                    'id' => $job->getId(),
                    'position' => $job->getPositionSlug()],
                true)] = $job->asArray($request->getHost());
        }
        $format = $request->getRequestFormat();
        $jsonData = json_encode($jobs);
        if ($format == "json") {
            $headers = ['Content-Type' => 'application/json'];
            $response = new Response($jsonData, 200, $headers);

            return $response;
        }

        return new Response($this->templating->render(
            'ShepardBundle:Api:jobs.' . $format . '.twig', ['jobs' => $jobs]));
    }
}
