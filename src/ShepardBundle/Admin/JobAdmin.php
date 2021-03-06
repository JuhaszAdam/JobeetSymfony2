<?php
namespace ShepardBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Validator;
use Sonata\AdminBundle\Form\FormMapper;
use ShepardBundle\Entity\Job;

class JobAdmin extends Admin
{

    /**
     * @var array
     */
    protected $datagridValues = [
        '_sort_order' => 'DESC',
        '_sort_by' => 'created_at'
    ];

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('category')
            ->add('type', 'choice', ['choices' => Job::getTypes(), 'expanded' => true])
            ->add('company')
            ->add('file', 'file', ['label' => 'Company logo', 'required' => false])
            ->add('url')
            ->add('position')
            ->add('location')
            ->add('description')
            ->add('how_to_apply')
            ->add('is_public')
            ->add('email')
            ->add('is_activated');
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('category')
            ->add('company')
            ->add('position')
            ->add('description')
            ->add('is_activated')
            ->add('is_public')
            ->add('email')
            ->add('expires_at');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('company')
            ->add('position')
            ->add('location')
            ->add('url')
            ->add('is_activated')
            ->add('email')
            ->add('category')
            ->add('expires_at')
            ->add('_action', 'actions', [
                'actions' => [
                    'view' => [],
                    'edit' => [],
                    'delete' => [],
                ]
            ]);
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('category')
            ->add('type')
            ->add('company')
            ->add('webPath', 'string', ['template' => 'ShepardBundle:JobAdmin:list_image.html.twig'])
            ->add('url')
            ->add('position')
            ->add('location')
            ->add('description')
            ->add('how_to_apply')
            ->add('is_public')
            ->add('is_activated')
            ->add('token')
            ->add('email')
            ->add('expires_at');
    }

    /**
     * @return array
     */
    public function getBatchActions()
    {
        $actions = parent::getBatchActions();

        if ($this->hasRoute('edit') &&
            $this->isGranted('EDIT') &&
            $this->hasRoute('delete') &&
            $this->isGranted('DELETE')
        ) {
            $actions['extend'] = [
                'label' => 'Extend',
                'ask_confirmation' => true
            ];

            $actions['deleteNeverActivated'] = [
                'label' => 'Delete never activated jobs',
                'ask_confirmation' => true
            ];
        }

        return $actions;
    }
}
