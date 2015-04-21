<?php

namespace jobeet\MyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use jobeet\MyBundle\Form\AffiliateType;
use Symfony\Component\HttpFoundation\Request;

use jobeet\MyBundle\Entity\Category;
use jobeet\MyBundle\Entity\Affiliate;

class AffiliateController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $affiliate = new Affiliate();
        $form = $this->createForm(new AffiliateType(), $affiliate);
        $form->bind($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isValid()) {

            $formData = $request->get('affiliate');
            $affiliate->setUrl($formData['url']);
            $affiliate->setEmail($formData['email']);
            $affiliate->setIsActive(false);

           // $em->persist($affiliate);
            $em->flush();

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
