<?php

namespace App\Form;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use App\Entity\Event;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
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
                'placeholder' => 'Choose an option',
                'choices' => [
                    'Auction' => 'Auction',
                    'Art Fair' => 'Art Fair',
                    'Exhibition' => 'Exhibition',
                    'Open Gallery' => 'Open Gallery',
                ],
            ])
            ->add('description')
            ->add('entryfee')
            ->add('capacity')
            ->add('startdate', DateType::class)
            ->add('enddate', DateType::class)
            ->add('image', FileType::class, [
                'label' => 'Product Image',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'accept' => 'image/*',
                ],
                
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Scheduled' => 'Scheduled',
                    'Started' => 'Started',
                    'Finished' => 'Finished',
                    'Postponed' => 'Postponed',
                    'Cancelled' => 'Cancelled',
                ],
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'placeholder' => 'Choose an option',
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