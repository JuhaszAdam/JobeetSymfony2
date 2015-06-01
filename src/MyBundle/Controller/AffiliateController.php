<?php

namespace MyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use MyBundle\Entity\Affiliate;
use MyBundle\Form\AffiliateType;
use MyBundle\Manager\Manager;
use Symfony\Component\Routing\Router;
use Symfony\Component\Templating\EngineInterface;

class AffiliateController extends Controller
{
    /**
     * @var  Manager
     */
    private $manager;

    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var EngineInterface;
     */
    private $templating;

    /**
     * @var Router
     */
    private $router;

    /**
     * @param Manager         $manager
     * @param FormFactory     $formFactory
     * @param EngineInterface $templating
     * @param Router          $router
     */
    public function __construct(
        Manager $manager,
        FormFactory $formFactory,
        EngineInterface $templating,
        Router $router)
    {
        $this->manager = $manager;
        $this->formFactory = $formFactory;
        $this->templating = $templating;
        $this->router = $router;
    }

    /**
     * @return Response
     */
    public function newAction()
    {
        $entity = new Affiliate();
        $form = $this->formFactory->create(new AffiliateType(), $entity);

        return new Response($this->templating->render('MyBundle:Affiliate:affiliate_new.html.twig', [
            'entity' => $entity,
            'form' => $form->createView(),
        ]));
    }

    /**
     * @param Request $request
     * @return RedirectResponse | Response
     */
    public function createAction(Request $request)
    {
        $affiliate = new Affiliate();
        $form = $this->formFactory->create(new AffiliateType(), $affiliate);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $formData = $request->get('affiliate');
            $affiliate->setUrl($formData['url']);
            $affiliate->setEmail($formData['email']);
            $affiliate->setIsActive(false);

            return $this->redirect($this->router->generate('ens_affiliate_wait'));
        }

        return new Response($this->templating->render('MyBundle:Affiliate:affiliate_new.html.twig', [
            'entity' => $affiliate,
            'form' => $form->createView(),
        ]));
    }

    /**
     * @return Response
     */
    public function waitAction()
    {
        return new Response($this->templating->render('MyBundle:Affiliate:wait.html.twig'));
    }
}
