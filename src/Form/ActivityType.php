<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\Event;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActivityType extends AbstractType
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
            ->add('date', DateType::class)
            ->add('title')
            ->add('host')
            ->add('event', EntityType::class, [
                'class' => Event::class,
                'choice_label' => 'name',
                'placeholder' => 'Choose an option',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->where('e.user = :user')
                        ->setParameter('user', $this->connectedUser);
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Activity::class,
        ]);
    }
}
