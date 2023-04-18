<?php

namespace App\Form;

use App\Entity\Shippingoption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ShippingoptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('carrier')
            ->add('shippingspeed', ChoiceType::class, [
                'choices' => [
                    '1-3 business days' => '1-3 business days',
                    '3-5 business days' => '3-5 business days',
                    '5-7 business days' => '5-7 business days',
                    '7-14 business days' => '7-14 business days',
                ],
            ])
            ->add('shippingfee', NumberType::class, [
                'attr' => [
                    'min' => 0,
                ],
            ])
            ->add('availableregions')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Shippingoption::class,
        ]);
    }
}
