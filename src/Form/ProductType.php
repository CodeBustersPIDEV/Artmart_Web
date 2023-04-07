<?php

namespace App\Form;
use App\Entity\Categories;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\NotBlank;
class ProductType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
    
$builder
->add('category', EntityType::class, [
    'class' => Categories::class,
    'choice_label' => 'name',
])
->add('name', null, [
    'constraints' => [
        new NotBlank([
            'message' => 'Please enter a name'
        ])
    ]
])
->add('description', null, [
    'constraints' => [
        new NotBlank([
            'message' => 'Please enter a description'
        ])
    ]
])
->add('dimensions', null, [
    'constraints' => [
        new NotBlank([
            'message' => 'Please enter a dimension'
        ])
    ]
])
->add('weight', null, [
    'constraints' => [
        new NotBlank([
            'message' => 'Please enter a weight'
        ])
    ]
])
->add('material', null, [
    'constraints' => [
        new NotBlank([
            'message' => 'Please enter a material'
        ])
    ]
])
->add('image', FileType::class, [
    'label' => 'Product Image',
    'mapped' => false,
    'required' => false,
    'constraints' => [
        new NotBlank([
            'message' => 'Please enter an image'
            ]) ],
    'attr' => [
        'accept' => 'image/*',
    ],
]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
