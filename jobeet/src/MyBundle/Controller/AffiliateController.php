<?php

namespace MyBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use MyBundle\Entity\Affiliate;
use MyBundle\Form\AffiliateType;
use MyBundle\Manager\Manager;

class AffiliateController extends Controller
{
    /**
     * @var  Manager
     */
    private $manager;

    /**
     * @param Manager $manager
     */
    public function __construct($manager)
    {
        $this->manager = $manager;
    }

    /**
     * @return Response
     */
    public function newAction()
    {
        $entity = new Affiliate();
        $form = $this->createForm(new AffiliateType(), $entity);

        return $this->render('MyBundle:Affiliate:affiliate_new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * @param Request $request
     * @return RedirectResponse | Response
     */
    public function createAction(Request $request)
    {
        $affiliate = new Affiliate();
        $form = $this->createForm(new AffiliateType(), $affiliate);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $formData = $request->get('affiliate');
            $affiliate->setUrl($formData['url']);
            $affiliate->setEmail($formData['email']);
            $affiliate->setIsActive(false);

            // TODO: this persist crashes, something is wrong with the object we're trying to persist.
            // $this->manager->saveList(array($affiliate));

            return $this->redirect($this->generateUrl('ens_affiliate_wait'));
        }

        return $this->render('MyBundle:Affiliate:affiliate_new.html.twig', array(
            'entity' => $affiliate,
            'form' => $form->createView(),
        ));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function waitAction()
    {
        return $this->render('MyBundle:Affiliate:wait.html.twig');
    }
}
