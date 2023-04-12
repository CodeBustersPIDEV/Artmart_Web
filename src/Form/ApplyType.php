<?php

namespace App\Form;
use App\Entity\Customproduct;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Entity\Apply;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApplyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('status', ChoiceType::class, [
            'choices' => [
                'in progress' => 'in progress',
                'Pending' => 'Pending',
                'done' => 'done',
                'refused' => 'refused',
                'waiting for admin' => 'waiting for admin',
            ],
        ])
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
