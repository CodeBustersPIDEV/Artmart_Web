<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('location')
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Auction' => 'auction',
                    'Art Fair' => 'art fair',
                    'Exhibition' => 'exhibition',
                    'Open Gallery' => 'open gallery',
                ],
            ])
            ->add('description')
            ->add('entryfee')
            ->add('capacity')
            ->add('startdate', DateType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
            ])
            ->add('enddate', DateType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
            ])
            ->add('image')
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Scheduled' => 'scheduled',
                    'Started' => 'started',
                    'Finished' => 'finshed',
                    'Postponed' => 'postponed',
                    'Cancelled' => 'cancelled',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
