<?php

namespace App\Form;
use App\Entity\Customproduct;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Apply;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApplyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status')
            ->add('artist')
            ->add('customproduct', EntityType::class, [
                'class' => customproduct::class,
                'choice_label' => 'product.name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Apply::class,
        ]);
    }
}
