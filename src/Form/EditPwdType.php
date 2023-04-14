<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\NotBlank;


class EditPwdType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => 'The password fields must match.',
            'options' => ['attr' => ['class' => 'password-field']],
            'required' => true,
            'first_options'  => [
                'label' => 'Password',
                'attr' => ['class' => 'form-control', 'id' => 'password'],
                'empty_data' => 'default_password'
            ],
            'second_options' => [
                'label' => 'Confirm Password',
                'attr' => ['class' => 'form-control', 'id' => 'confirm_password'],
                'empty_data' => 'default_password'
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Please enter a password',
                ]),
            ],
        ]);
        
        
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
           
        ]);
    }
}
