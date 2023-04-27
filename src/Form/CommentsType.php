<?php

namespace App\Form;

use App\Entity\Comments;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use blackknight467\StarRatingBundle\Form\RatingType;


class CommentsType extends AbstractType
{
    private UserRepository $UserRepository;


    public function __construct(UserRepository $UserRepository)
    {
        $this->UserRepository = $UserRepository;
    }


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content')
            ->add('rating');
        // ->add('author', EntityType::class, [
        //     'class' => User::class,
        //     'choices' => $this->UserRepository->findAll(),
        //     'choice_label' => 'name',
        //     'placeholder' => 'Choose an option',
        //     'choice_attr' => function ($choice, $key, $value) {
        //         return ['style' => 'color:black;'];
        //     }
        // ])

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comments::class,
        ]);
    }
}
