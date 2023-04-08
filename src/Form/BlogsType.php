<?php

namespace App\Form;

use App\Entity\Blogcategories;
use App\Entity\Blogs;
use App\Entity\Tags;
use App\Entity\User;
use App\Repository\BlogcategoriesRepository;
use App\Repository\TagsRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlogsType extends AbstractType
{
    private UserRepository $UserRepository;
    private BlogcategoriesRepository $blogcategoriesRepository;
    private TagsRepository $tagsRepository;


    public function __construct(UserRepository $UserRepository, BlogcategoriesRepository $blogcategoriesRepository, TagsRepository $tagsRepository)
    {
        $this->UserRepository = $UserRepository;
        $this->blogcategoriesRepository = $blogcategoriesRepository;
        $this->tagsRepository = $tagsRepository;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('content')
            ->add('category',  EntityType::class, [
                'mapped' => false,
                'class' => Blogcategories::class,
                'choice_label' => 'name',
                'choices' => $this->blogcategoriesRepository->findAll(),
                'placeholder' => 'Choose an option', // optional

            ])
            ->add('tags', EntityType::class, [
                'mapped' => false,
                'class' => Tags::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'choices' => $this->tagsRepository->findAll(),
                'placeholder' => 'Choose an option', // optional
                'choice_attr' => function ($choice, $key, $value) {
                    return ['class' => 'form-check-input'];
                }
            ])
            ->add('addTags', null, ['mapped' => false])
            // ->add('addedTags', null, ['mapped' => false, 'attr'  =>  ['style' => 'display:none;', 'value']])
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
