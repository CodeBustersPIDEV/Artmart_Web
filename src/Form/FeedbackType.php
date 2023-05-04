<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Feedback;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

class FeedbackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('rating', ChoiceType::class, [
            'expanded' => true,
            'attr' => ['class' => 'rating-inline'],
            'choices' => [
                '1' => 1,
                '2' => 2,
                '3' => 3,
                '4' => 4,
                '5' => 5,
            ],
            'constraints' => [
                new Range([
                    'min' => 1,
                    'max' => 5,
                    'notInRangeMessage' => 'Rating must be between {{ min }} and {{ max }}',
                ]),
            ],
        ])
        ->add('comment')
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Feedback::class,
        ]);
    }
}
