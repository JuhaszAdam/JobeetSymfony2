<?php

namespace MyBundle\Controller;

use MyBundle\Entity\Category;
use MyBundle\Manager\Manager;
use MyBundle\Provider\CategoryProvider;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use MyBundle\Entity\Job;
use MyBundle\Form\JobType;

class JobController extends Controller
{
    /**
     * @var  Manager
     */
    private $jobManager;

    /**
     * @var Manager
     */
    private $categoryProvider;

    /**
     * @param Manager $jobManager
     * @param CategoryProvider $categoryProvider
     */
    public function __construct($jobManager, $categoryProvider)
    {
        $this->jobManager = $jobManager;
        $this->categoryProvider = $categoryProvider;
    }

    /**
     * @return Response
     */
    public function indexAction()
    {
        $categories = iterator_to_array($this->categoryProvider->provide());

        /**@var Category category */
        foreach ($categories as $category) {
            $activeJobs = $this->jobManager->findBy(array("category" => $category->getId()), null, $this->container->getParameter('max_jobs_on_homepage'));
            //$category->setActiveJobs($em->getRepository('MyBundle:Job')->getActiveJobs($category->getId(), $this->container->getParameter('max_jobs_on_homepage')));
            $category->setActiveJobs($activeJobs);

           /*  $category->setMoreJobs($em->getRepository('MyBundle:Job')->countActiveJobs($category->getId())
                 - $this->container->getParameter('max_jobs_on_homepage'));*/
        }

        $format = $this->getRequest()->getRequestFormat();

        return $this->render('MyBundle:Job:index.' . $format . '.twig', array(
            'categories' => $categories,
            // 'lastUpdated' => $em->getRepository('MyBundle:Job')->getLatestPost()->getCreatedAt()->format(DATE_ATOM),
            'feedId' => sha1($this->get('router')->generate('ens_job', array('_format' => 'atom'), true)),
        ));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction()
    {
        $entity = new Job();
        $request = $this->getRequest();
        $form = $this->createForm(new JobType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('ens_job_preview', array(
                'company' => $entity->getCompanySlug(),
                'location' => $entity->getLocationSlug(),
                'token' => $entity->getToken(),
                'position' => $entity->getPositionSlug()
            )));
        }

        return $this->render('MyBundle:Job:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        ));
    }

    /**
     * @param Job $entity
     *
     * @return \Symfony\Component\Form\Form
     */
    private function createCreateForm(Job $entity)
    {
        $form = $this->createForm(new JobType(), $entity, array(
            'action' => $this->generateUrl('ens_job_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * @return Response
     */
    public function newAction()
    {
        $entity = new Job();
        $entity->setType('full-time');
        $form = $this->createForm(new JobType(), $entity);

        return $this->render('MyBundle:Job:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        ));
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('MyBundle:Job')->getActiveJob($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Job entity.');
        }

        $session = $this->getRequest()->getSession();
        $jobs = $session->get('job_history', array());
        $job = array('id' => $entity->getId(), 'position' => $entity->getPosition(), 'company' => $entity->getCompany(),
            'companyslug' => $entity->getCompanySlug(), 'locationslug' => $entity->getLocationSlug(),
            'positionslug' => $entity->getPositionSlug());

        if (!in_array($job, $jobs)) {
            array_unshift($jobs, $job);
            $session->set('job_history', array_slice($jobs, 0, 3));
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('MyBundle:Job:show.html.twig', array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @param Token $token
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction($token)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('MyBundle:Job')->findOneByToken($token);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Job entity.');
        }

        if ($entity->getIsActivated()) {
            throw $this->createNotFoundException('Job is activated and cannot be edited.');
        }

        $editForm = $this->createForm(new JobType(), $entity);
        $deleteForm = $this->createDeleteForm($token);

        return $this->render('MyBundle:Job:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @param Job $entity
     *
     * @return \Symfony\Component\Form\Form
     */
    private function createEditForm(Job $entity)
    {
        $form = $this->createForm(new JobType(), $entity, array(
            'action' => $this->generateUrl('ens_job_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * @param $token
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updateAction($token)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('MyBundle:Job')->findOneByToken($token);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Job entity.');
        }

        $editForm = $this->createForm(new JobType(), $entity);
        $deleteForm = $this->createDeleteForm($token);

        $request = $this->getRequest();

        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('ens_job_preview', array(
                'company' => $entity->getCompanySlug(),
                'location' => $entity->getLocationSlug(),
                'token' => $entity->getToken(),
                'position' => $entity->getPositionSlug()
            )));
        }

        return $this->redirect($this->generateUrl('ens_job_preview', array(
            'company' => $entity->getCompanySlug(),
            'location' => $entity->getLocationSlug(),
            'token' => $entity->getToken(),
            'position' => $entity->getPositionSlug()
        )));
    }

    /**
     * @param $token
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($token)
    {
        $form = $this->createDeleteForm($token);
        $request = $this->getRequest();

        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('MyBundle:Job')->findOneByToken($token);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Job entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('ens_job'));
    }

    /**
     * @param $token
     * @return \Symfony\Component\Form\Form
     */
    private function createDeleteForm($token)
    {
        return $this->createFormBuilder(array('token' => $token))
            ->add('token', 'hidden')
            ->getForm();
    }

    /**
     * @param $token
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function previewAction($token)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('MyBundle:Job')->findOneByToken($token);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Job entity.');
        }

        $deleteForm = $this->createDeleteForm($entity->getId());
        $publishForm = $this->createPublishForm($entity->getToken());
        $extendForm = $this->createExtendForm($entity->getToken());

        return $this->render('MyBundle:Job:show.html.twig', array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
            'publish_form' => $publishForm->createView(),
            'extend_form' => $extendForm->createView(),
        ));
    }

    /**
     * @param $token
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function publishAction($token)
    {
        $form = $this->createPublishForm($token);
        $request = $this->getRequest();

        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('MyBundle:Job')->findOneByToken($token);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Job entity.');
            }

            $entity->publish();
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('notice', 'Your job is now online for 30 days.');
        }

        return $this->redirect($this->generateUrl('ens_job_preview', array(
            'company' => $entity->getCompanySlug(),
            'location' => $entity->getLocationSlug(),
            'token' => $entity->getToken(),
            'position' => $entity->getPositionSlug()
        )));
    }

    /**
     * @param $token
     * @return \Symfony\Component\Form\Form
     */
    private function createPublishForm($token)
    {
        return $this->createFormBuilder(array('token' => $token))
            ->add('token', 'hidden')
            ->getForm();
    }

    /**
     * @param $token
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function extendAction($token)
    {
        $form = $this->createExtendForm($token);
        $request = $this->getRequest();

        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('MyBundle:Job')->findOneByToken($token);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Job entity.');
            }

            if (!$entity->extend()) {
                throw $this->createNotFoundException('Unable to find extend the Job.');
            }

            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('notice', sprintf('Your job validity has been extended until %s.',
                $entity->getExpiresAt()->format('m/d/Y')));
        }

        return $this->redirect($this->generateUrl('ens_job_preview', array(
            'company' => $entity->getCompanySlug(),
            'location' => $entity->getLocationSlug(),
            'token' => $entity->getToken(),
            'position' => $entity->getPositionSlug()
        )));
    }

    /**
     * @param $token
     * @return \Symfony\Component\Form\Form
     */
    private function createExtendForm($token)
    {
        return $this->createFormBuilder(array('token' => $token))
            ->add('token', 'hidden')
            ->getForm();
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $this->getRequest()->get('query');

        if (!$query) {
            if (!$request->isXmlHttpRequest()) {
                return $this->redirect($this->generateUrl('ens_job'));
            } else {
                return new Response('No results.');
            }
        }

        $jobs = $em->getRepository('MyBundle:Job')->getForLuceneQuery($query);

        if ($request->isXmlHttpRequest()) {
            if ('*' == $query || !$jobs || $query == '') {
                return new Response('No results.');
            }
            return $this->render('MyBundle:Job:list.html.twig', array('jobs' => $jobs));
        }
        return $this->render('MyBundle:Job:search.html.twig', array('jobs' => $jobs));
    }
}
