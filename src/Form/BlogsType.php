<?php

namespace App\Form;

use App\Entity\Blogcategories;
use App\Entity\Blogs;
use App\Entity\User;
use App\Form\Type\BlogCategoryType;
use App\Repository\BlogcategoriesRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlogsType extends AbstractType
{
    private UserRepository $UserRepository;
    private BlogcategoriesRepository $blogcategoriesRepository;

    public function __construct(UserRepository $UserRepository, BlogcategoriesRepository $blogcategoriesRepository)
    {
        $this->UserRepository = $UserRepository;
        $this->blogcategoriesRepository = $blogcategoriesRepository;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('content')
            ->add('categories', BlogCategoryType::class, [
                'mapped' => false,
            ])
            ->add('rating')
            ->add('nbViews')
            ->add('author', EntityType::class, [
                'class' => User::class,
                'choices' => $this->UserRepository->findAll(),
                'choice_label' => 'name',
                'placeholder' => 'Choose an option', // optional
            ])
            ->add('file', FileType::class, [
                'label' => 'Choose a file',
                'mapped' => false,
                'required' => true,
            ])
            ->getForm();
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Blogs::class,
        ]);
    }
}
