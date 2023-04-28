<?php

namespace App\Form;

use App\Entity\Productreview;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductreviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('readyProductId')
            ->add('userId')
            ->add('title')
            ->add('text')
            ->add('rating')
            ->add('date');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Productreview::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
