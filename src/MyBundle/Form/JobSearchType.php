<?php

namespace MyBundle\Form;

use MyBundle\Model\JobSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class JobSearchType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('company', null, array(
                'required' => false,
            ))
            ->add('dateFrom', 'date', array(
                'required' => false,
                'widget' => 'single_text',
            ))
            ->add('dateTo', 'date', array(
                'required' => false,
                'widget' => 'single_text',
            ))
            ->add('is_activated', 'choice', array(
                'choices' => array('false' => 'no', 'true' => 'yes'),
                'required' => false,
            ))
            ->add('search', 'submit');
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'data_class' => 'MyBundle\Model\ElasticJobSearch'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'job_search_type';
    }
}
