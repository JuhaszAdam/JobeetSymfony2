<?php

namespace jobeet\MyBundle\Controller;

use Doctrine\ORM\EntityManager;
use jobeet\MyBundle\Manager\Manager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use jobeet\MyBundle\Form\AffiliateType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use jobeet\MyBundle\Entity\Affiliate;

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
        $form->bind($request);
        //$em = $this->getDoctrine()->getManager();

        if ($form->isValid()) {

            $formData = $request->get('affiliate');
            $affiliate->setUrl($formData['url']);
            $affiliate->setEmail($formData['email']);
            $affiliate->setIsActive(false);

            // TODO: this persist crashes, something is wrong with the object we're trying to persist.
            // $this-#manager->persist($affiliate);

            //$this->manager->flush();

            $this->manager->pushToDatabase(array($affiliate));

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
