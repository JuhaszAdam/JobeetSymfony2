<?php

namespace ShepardBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use ShepardBundle\Entity\Job;

class JobType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('category');
        $builder->add('type', 'choice', ['choices' => Job::getTypes(), 'expanded' => true]);
        $builder->add('company');
        $builder->add('file', 'file', ['label' => 'Company logo', 'required' => false]);
        $builder->add('url');
        $builder->add('position');
        $builder->add('location');
        $builder->add('description');
        $builder->add('how_to_apply', null, ['label' => 'How to apply?']);
        $builder->add('is_public', null, ['label' => 'Public?']);
        $builder->add('email');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'job';
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'ShepardBundle\Entity\Job'
        ]);
    }
}
