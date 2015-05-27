<?php

namespace MyBundle\Controller;

use Doctrine\Entity;
use Monolog\Handler\ElasticSearchHandler;
use MyBundle\Entity\Category;
use MyBundle\Form\JobSearchType;
use MyBundle\Model\ElasticJobSearch;
use MyBundle\Manager\JobManager;
use MyBundle\Provider\CategoryProvider;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Form\FormFactory;
use MyBundle\Entity\Job;
use MyBundle\Form\JobType;

class JobController extends Controller
{
    /**
     * @var  JobManager
     */
    private $jobManager;

    /**
     * @var CategoryProvider
     */
    private $categoryProvider;

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
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var int
     */
    private $maxJobsOnPage;

    /**
     * @var int
     */
    private $maxCategoriesOnPage;

    /**
     * @param JobManager $jobManager
     * @param CategoryProvider $categoryProvider
     * @param FormFactory $formFactory
     * @param EngineInterface $templating
     * @param Router $router
     * @param RequestStack $requestStack
     * @param int $maxJobsOnPage
     * @param int $maxCategoriesOnPage
     */
    public function __construct(
        $jobManager,
        $categoryProvider,
        $formFactory,
        $templating,
        $router,
        $requestStack,
        $maxJobsOnPage,
        $maxCategoriesOnPage
    )
    {
        $this->jobManager = $jobManager;
        $this->categoryProvider = $categoryProvider;
        $this->formFactory = $formFactory;
        $this->templating = $templating;
        $this->router = $router;
        $this->requestStack = $requestStack;
        $this->maxJobsOnPage = $maxJobsOnPage;
        $this->maxCategoriesOnPage = $maxCategoriesOnPage;
    }

    /**
     * @return Response
     */
    public function indexAction()
    {
        $categories = $this->categoryProvider->getWithJobs();

        /**@var Category $category */
        foreach ($categories as $category) {
            $category->setActiveJobs($this->jobManager->getActiveJobs($category->getId(), $this->maxJobsOnPage));
            $category->setMoreJobs($this->jobManager->countActiveJobs($category->getId())
                - $this->maxJobsOnPage);
        }

        $format = $this->requestStack->getCurrentRequest()->getRequestFormat();

        return new Response($this->templating->render('MyBundle:Job:index.' . $format . '.twig', array(
            'categories' => $categories,
            'lastUpdated' => $this->jobManager->getLatestPost()->getCreatedAt()->format(DATE_ATOM),
            'feedId' => sha1($this->router->generate('ens_job', array('_format' => 'atom'), true)),
        )));
    }

    /**
     * @return RedirectResponse|Response
     */
    public function createAction()
    {
        $entity = new Job();
        $request = $this->requestStack->getCurrentRequest();
        $form = $this->formFactory->create(new JobType(), $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var Entity $entity */
            $this->jobManager->save($entity);
            /** @var Job $entity */
            return $this->redirect($this->router->generate('ens_job_preview', array(
                'company' => $entity->getCompanySlug(),
                'location' => $entity->getLocationSlug(),
                'token' => $entity->getToken(),
                'position' => $entity->getPositionSlug()
            )));
        }

        return new Response($this->templating->render('MyBundle:Job:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        )));
    }

    /**
     * @return Response
     */
    public function newAction()
    {
        $entity = new Job();
        $entity->setType('full-time');
        $form = $this->formFactory->create(new JobType(), $entity);

        return new Response($this->templating->render('MyBundle:Job:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        )));
    }

    /**
     * @param $id
     * @return Response
     */
    public function showAction($id)
    {

        $entity = $this->jobManager->getActiveJob($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Job entity.');
        }

        $session = $this->requestStack->getCurrentRequest()->getSession();
        $jobs = $session->get('job_history', array());
        $job = array('id' => $entity->getId(), 'position' => $entity->getPosition(), 'company' => $entity->getCompany(),
            'companyslug' => $entity->getCompanySlug(), 'locationslug' => $entity->getLocationSlug(),
            'positionslug' => $entity->getPositionSlug());

        if (!in_array($job, $jobs)) {
            array_unshift($jobs, $job);

            $session->set('job_history', array_slice($jobs, 0, 3));
        }

        $deleteForm = $this->createGenericForm($id);

        return new Response($this->templating->render('MyBundle:Job:show.html.twig', array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        )));
    }

    /**
     * @param Token $token
     * @return Response
     */
    public function editAction($token)
    {
        /** @var Job $entity */
        $entity = $this->jobManager->findOneByToken($token);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Job entity.');
        }


        if ($entity->getIsActivated()) {
            throw $this->createNotFoundException('Job is activated and cannot be edited.');
        }

        $editForm = $this->formFactory->create(new JobType(), $entity);
        $deleteForm = $this->createGenericForm($token);

        return new Response($this->templating->render('MyBundle:Job:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        )));
    }

    /**
     * @param $token
     * @return RedirectResponse|Response
     */
    public function updateAction($token)
    {
        /** @var Job $entity */
        $entity = $this->jobManager->findOneByToken($token);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Job entity.');
        }

        $editForm = $this->formFactory->create(new JobType(), $entity);
        $request = $this->requestStack->getCurrentRequest();

        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            /** @var Entity $entity */
            $this->jobManager->save($entity);
            /** @var Job $entity */
            return $this->redirect($this->router->generate('ens_job_preview', array(
                'company' => $entity->getCompanySlug(),
                'location' => $entity->getLocationSlug(),
                'token' => $entity->getToken(),
                'position' => $entity->getPositionSlug()
            )));
        }

        return $this->redirect($this->router->generate('ens_job_preview', array(
            'company' => $entity->getCompanySlug(),
            'location' => $entity->getLocationSlug(),
            'token' => $entity->getToken(),
            'position' => $entity->getPositionSlug()
        )));
    }

    /**
     * @param $token
     * @return RedirectResponse
     */
    public function deleteAction($token)
    {
        $form = $this->createGenericForm($token);
        $request = $this->requestStack->getCurrentRequest();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity = $this->jobManager->findOneByToken($token);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Job entity.');
            }

            $this->jobManager->remove($entity);
        }

        return $this->redirect($this->router->generate('ens_job'));
    }

    /**
     * @param $token
     * @return Form
     */
    private function createGenericForm($token)
    {
        return $this->formFactory->create('form', array('token' => $token))
            ->add('token', 'hidden');
    }

    /**
     * @param $token
     * @return Response
     */
    public function previewAction($token)
    {
        /** @var Job $entity */
        $entity = $this->jobManager->findOneByToken($token);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Job entity.');
        }

        $deleteForm = $this->createGenericForm($entity->getId());
        $publishForm = $this->createGenericForm($entity->getToken());
        $extendForm = $this->createGenericForm($entity->getToken());

        return new Response($this->templating->render('MyBundle:Job:show.html.twig', array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
            'publish_form' => $publishForm->createView(),
            'extend_form' => $extendForm->createView(),
        )));
    }

    /**
     * @param $token
     * @return RedirectResponse
     */
    public function publishAction($token)
    {
        /** @var Job $entity */

        $form = $this->createGenericForm($token);
        $request = $this->requestStack->getCurrentRequest();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity = $this->jobManager->findOneByToken($token);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Job entity.');
            }

            $entity->publish();
            /** @var Entity $entity */
            $this->jobManager->save($entity);
        }

        return $this->redirect($this->router->generate('ens_job_preview', array(
            'company' => $entity->getCompanySlug(),
            'location' => $entity->getLocationSlug(),
            'token' => $entity->getToken(),
            'position' => $entity->getPositionSlug()
        )));
    }

    /**
     * @param $token
     * @return RedirectResponse
     */
    public function extendAction($token)
    {
        $form = $this->createGenericForm($token);
        $request = $this->requestStack->getCurrentRequest();
        $form->handleRequest($request);
        /** @var Job $entity */

        if ($form->isValid()) {
            $entity = $this->jobManager->findOneByToken($token);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Job entity.');
            }

            if (!$entity->extend()) {
                throw $this->createNotFoundException('Unable to extend the Job.');
            }
            /** @var Entity $entity */
            $this->jobManager->save($entity);
            /** @var Job $entity */
        }
        $entity = null;
        return $this->redirect($this->router->generate('ens_job_preview', array(
            'company' => $entity->getCompanySlug(),
            'location' => $entity->getLocationSlug(),
            'token' => $entity->getToken(),
            'position' => $entity->getPositionSlug()
        )));
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function searchAction(Request $request)
    {
        $jobSearch = new ElasticJobSearch;
        $query = $this->requestStack->getCurrentRequest()->get('query');

        $jobSearchForm = $this->formFactory
            ->createNamed(
                '',
                new JobSearchType(),
                $jobSearch,
                array(
                    'action' => $this->router->generate('my_job_search'),
                    'method' => 'GET'
                )
            );
        $jobSearchForm->handleRequest($request);
        $jobSearch = $jobSearchForm->getData();

        //TODO: returns void result.
        //var_dump($jobSearch);

        /** @var ElasticJobSearch $jobSearch */
        $jobs = $this->jobManager->findBy(array(
            "company" => $jobSearch->getCompany(),
            "is_activated" => $jobSearch->isActivated()
        ));

        if ('*' == $query || !$jobs || $query == '') {
            return new Response('No results.');
        }
        return new Response($this->templating->render('MyBundle:Job:list.html.twig', array('jobs' => $jobs)));
    }
}
