<?php

namespace App\Form;

use App\Entity\Orderrefund;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

class OrderrefundType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('refundamount', NumberType::class, [
            'constraints' => [
                new NotBlank(),
                new PositiveOrZero(),
            ],
        ])
        ->add('reason')
        ->add('date')
        ->add('orderid')
    ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Orderrefund::class,
        ]);
    }
}
