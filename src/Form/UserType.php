<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Client;
use App\Entity\Artist;
use App\Entity\Admin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use App\Validator\NotFutureDate;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $clientAttributes = $options['client_attributes'];
        $artistAttributes = $options['artist_attributes'];
        $adminAttributes = $options['admin_attributes'];

        $builder
            ->add('name', TextType::class, [
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'required' => true,
            ])
            ->add('birthday', DateType::class, [
                'constraints' => [
                    new NotFutureDate(),
                ],
            ])
            ->add('phonenumber')
            
            ->add('username', TextType::class, [
                'required' => true,
            ])
            ->add('file', FileType::class, [
                'label' => 'Choose a file',
                'mapped' => false,
                'required' => true,
            ]);
        if (!$options['is_edit'])  {
            $builder->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Confirm Password'],
            ])
            ->add('role', ChoiceType::class, [
                'choices' => [
                    'Admin' => 'admin',
                    'Artist' => 'artist',
                    'Client' => 'client',
                ],
                'required' => true,
            ]);
        }
        if ($options['is_edit']) {
            if (isset($clientAttributes['nbrOrders']) && $clientAttributes['nbrOrders'] !== null) {
                $builder->add('client', ClientType::class, [
                    'data' => new Client(),
                    'mapped' => false,
                    'attr' => [
                        'class' => 'client-form',
                    ],
                ]);
            }

            if (isset($artistAttributes['bio'] )&& $artistAttributes['bio']!== null) {
                $builder->add('bio', TextType::class, [
                    'mapped' => false,
                    'attr' => [
                        'class' => 'artist-form',
                    ],
                ]);
            }

            if (isset($adminAttributes['department']) && $adminAttributes['department']!== null) {
                $builder->add('department', TextType::class, [
                    'mapped' => false,
                    'attr' => [
                        'class' => 'admin-form',
                    ],
                ]);
            }
        }
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'client_attributes' => [],
            'artist_attributes' => [],
            'admin_attributes' => [],
            'is_edit' => false,
        ]);
    }
}
