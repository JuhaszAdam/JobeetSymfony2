<?php

namespace MyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use MyBundle\Entity\Affiliate;
use MyBundle\Entity\Job;
use MyBundle\Repository\AffiliateRepository;
use MyBundle\Provider\Provider;
use MyBundle\Repository\JobRepository;

class ApiController extends Controller
{
    /**
     * @var Provider
     */
    private $affiliate_provider;

    /**
     * @var  Provider
     */
    private $job_provider;

    /**
     * @param Provider $affiliate_provider
     * @param Provider $job_provider
     */
    public function __construct($affiliate_provider = NULL, $job_provider = NULL)
    {
        $this->affiliate_provider = $affiliate_provider;
        $this->job_provider = $job_provider;
    }

    public function listAction(Request $request, $token)
    {
        //TODO: What should the provider provide in this scenario?
        $em = $this->getDoctrine()->getManager();
        $jobs = array();
        /** @var Affiliate $affiliate */
        /** @var AffiliateRepository $rep */
        $rep = $em->getRepository('MyBundle:Affiliate');
        $affiliate = $rep->getForToken($token);
        if (!$affiliate) {
            throw $this->createNotFoundException('This affiliate account does not exist!');
        }
        /** @var JobRepository $rep */
        $rep = $em->getRepository('MyBundle:Job');
        $active_jobs = $rep->getActiveJobs(null, null, null, $affiliate->getId());
        /** @var Job $job */
        foreach ($active_jobs as $job) {
            $jobs[$this->get('router')->generate('ens_job_show',
                array('company' => $job->getCompanySlug(),
                    'location' => $job->getLocationSlug(),
                    'id' => $job->getId(),
                    'position' => $job->getPositionSlug()),
                true)] = $job->asArray($request->getHost());
        }
        $format = $request->getRequestFormat();
        $jsonData = json_encode($jobs);
        if ($format == "json") {
            $headers = array('Content-Type' => 'application/json');
            $response = new Response($jsonData, 200, $headers);
            return $response;
        }
        return $this->render('MyBundle:Api:jobs.' . $format . '.twig', array('jobs' => $jobs));
    }
}