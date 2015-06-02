<?php

namespace ShepardBundle\Form;

use ShepardBundle\Model\JobSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class JobSearchType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('company', null, [
                'required' => false,
            ])
            ->add('dateFrom', 'date', [
                'required' => false,
                'widget' => 'single_text',
            ])
            ->add('dateTo', 'date', [
                'required' => false,
                'widget' => 'single_text',
            ])
            ->add('is_activated', 'choice', [
                'choices' => ['false' => 'no', 'true' => 'yes'],
                'required' => false,
            ])
            ->add('search', 'submit');
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => 'ShepardBundle\Model\ElasticJobSearch'
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'job_search_type';
    }
}
