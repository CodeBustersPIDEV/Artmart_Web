<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Feedback;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

class FeedbackType extends AbstractType
{
    private User $connectedUser;
 
    public function __construct(SessionInterface $session, UserRepository $userRepository)
    {
        if ($session != null) {
            $connectedUserID = $session->get('user_id');
            if (is_int($connectedUserID)) {
                $this->connectedUser = $userRepository->find((int) $connectedUserID);
            }
        }
    }
    
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
        ->add('comment');

        // Add the user field only if connectedUser has the admin role
        if ($this->connectedUser->getRole() === 'admin') {
            $builder->add('event', EntityType::class, [
                'class' => Event::class,
                'choice_label' => 'name',
                'placeholder' => 'Choose an event',
            ]);
            $builder->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'name',
                'placeholder' => 'Choose a user',
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Feedback::class,
        ]);
    }
}
