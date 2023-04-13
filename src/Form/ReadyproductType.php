<?php

namespace App\Form;

use App\Entity\Readyproduct;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReadyproductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('price')
            ->add('productId', ProductType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Readyproduct::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
