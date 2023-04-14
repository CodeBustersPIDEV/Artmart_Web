<?php

namespace App\Form;

use App\Entity\Orderstatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class OrderstatusType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('status', ChoiceType::class, [
            'choices' => [
                'New' => 'NEW',
                'In Progress' => 'IN PROGRESS',
                'Completed' => 'COMPLETED',
                'Cancelled' => 'CANCELED',
                'Refunded' => 'REFUNDED',
            ],
        ])
            ->add('date')
            ->add('orderid')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Orderstatus::class,
        ]);
    }
}
