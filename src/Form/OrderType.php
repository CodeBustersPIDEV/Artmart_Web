<?php

namespace App\Form;

use Symfony\Component\Validator\Constraints\Regex;
use App\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity')
            ->add('shippingaddress')
            ->add('orderdate')
            ->add('userid')
            ->add('productid')
            ->add('shippingmethod')
            ->add('paymentmethod')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
