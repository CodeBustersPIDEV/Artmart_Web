<?php

namespace App\Form;

use App\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity', NumberType::class)
            ->add('shippingaddress')
            ->add('orderdate', null, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
            ])
            ->add('totalcost', NumberType::class)
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
