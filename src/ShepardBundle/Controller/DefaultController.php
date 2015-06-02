<?php

namespace ShepardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Templating\EngineInterface;

class DefaultController extends Controller
{
    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * @var int
     */
    private $jobsPerCategory;

    /**
     * @param EngineInterface $templating
     * @param int             $jobsPerCategory
     */
    public function __construct(
        EngineInterface $templating,
        $jobsPerCategory)
    {
        $this->templating = $templating;
        $this->jobsPerCategory = $jobsPerCategory;
    }

    /**
     * @param string $name
     * @return Response
     */
    public function indexAction($name)
    {
        return new Response($this->templating->render('ShepardBundle:Default:index.html.twig', ['name' => $name]));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function loginAction(Request $request)
    {
        $session = $request->getSession();

        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render('ShepardBundle:Default:login.html.twig', [
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error' => $error,
        ]);
    }
}
